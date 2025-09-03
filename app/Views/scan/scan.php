 <?= $this->extend('templates/starting_page_layout'); ?>

 <?= $this->section('navaction') ?>
 <?= $this->endSection() ?>

 <?= $this->section('content'); ?>
 <?php
   $oppBtn = '';

   $waktu == 'Masuk' ? $oppBtn = 'pulang' : $oppBtn = 'masuk';
   ?>
 <div class="main-panel">
    <div class="content">
       <div class="container-fluid">
          <div class="row mx-auto">
             <div class="col-lg-3">
                <div class="card">
                   <div class="card-body">
                      <h3 class="mt-2"><b>Tips</b></h3>
                      <ul class="pl-3">
                         <li>Tunjukkan qr code sampai terlihat jelas di kamera</li>
                         <li>Posisikan qr code tidak terlalu jauh maupun terlalu dekat</li>
                      </ul>
                   </div>
                </div>
                <div class="card">
                   <div class="card-body">
                      <h3 class="mt-2"><b>Penggunaan</b></h3>
                      <ul class="pl-3">
                         <li>Jika berhasil scan maka akan muncul data siswa/guru di samping kanan preview kamera</li>
                         <li>Klik tombol <b><span class="text-success">Absen masuk</span> / <span class="text-warning">Absen pulang</span></b> untuk mengubah waktu absensi</li>
   
                         <li>Untuk mengakses halaman petugas anda harus login terlebih dahulu</li>
                      </ul>
                   </div>
                </div>
             </div>
             <div class="col-lg-5">
                <div class="card">
                   <div class="col-10 mx-auto card-header card-header-primary">
                      <div class="row">
                         <div class="col">
                            <h4 class="card-title"><b>Absen <?= $waktu; ?></b></h4>
                            <p class="card-category">Silahkan tunjukkan QR Code anda</p>
                         </div>
                         <div class="col-md-auto">
                            <a href="<?= base_url("scan/$oppBtn"); ?>" class="btn btn-<?= $oppBtn == 'masuk' ? 'success' : 'warning'; ?>">
                               Absen <?= $oppBtn; ?>
                            </a>
                         </div>
                      </div>
                   </div>
                   <div class="card-body my-auto px-5">
                      <h4 class="d-inline">Pilih kamera</h4>

                      <select id="pilihKamera" class="custom-select w-50 ml-2" aria-label="Default select example" style="height: 35px;">
                         <option selected>Select camera devices</option>
                      </select>

                      <br>

                      <div class="row">
                         <div class="col-sm-12 mx-auto">
                            <div id="reader"></div>
                         </div>
                      </div>
                   </div>
                </div>
             </div>
             <div class="col-lg-4">
                <div class="card">
                   <div class="card-header card-header-info">
                      <h4 class="card-title"><b>Hasil Scan</b></h4>
                   </div>
                   <div class="card-body">
                      <div id="hasilScan" class="px-3"></div>
                   </div>
                </div>
             </div>
          </div>
       </div>
    </div>
 </div>

 <script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script>
 <script src="<?= base_url('assets/js/core/jquery-3.5.1.min.js') ?>"></script>
 <script>
    let selectedDeviceId = null;
    let audio = new Audio("<?= base_url('assets/audio/beep.mp3'); ?>");
    const sourceSelect = $('#pilihKamera');
    const html5QrCode = new Html5Qrcode("reader");

    function onScanSuccess(decodedText, decodedResult) {
        // handle the scanned code as you like, for example:
        console.log(`Code matched = ${decodedText}`, decodedResult);
        cekData(decodedText);

        // temporarily stop scanning
        html5QrCode.stop().then((ignore) => {
            // reset and restart scanning after a delay
            setTimeout(() => {
                startScanning();
            }, 1500);
        }).catch((err) => {
            console.log("Failed to stop scanning.", err);
        });
    }

    function onScanFailure(error) {
        // handle scan failure, usually better to ignore and keep scanning.
        // for example:
        // console.warn(`Code scan error = ${error}`);
    }

    function startScanning() {
        html5QrCode.start(
            selectedDeviceId, {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 250
                }
            },
            onScanSuccess,
            onScanFailure
        ).catch((err) => {
            console.log(`Unable to start scanning, error: ${err}`);
        });
    }

    function initScanner(cameras) {
        if (cameras && cameras.length) {

            if (selectedDeviceId == null) {
                if (cameras.length <= 1) {
                   selectedDeviceId = cameras[0].id
                } else {
                   selectedDeviceId = cameras[1].id
                }
             }

            sourceSelect.html('');
            cameras.forEach((camera) => {
                const sourceOption = document.createElement('option');
                sourceOption.text = camera.label;
                sourceOption.value = camera.id;
                if (camera.id == selectedDeviceId) {
                    sourceOption.selected = 'selected';
                }
                sourceSelect.append(sourceOption);
            });

            startScanning();
        }
    }

    // This method will trigger user permissions
    Html5Qrcode.getCameras().then(devices => {
        console.log("Cameras found", devices);
        if (devices && devices.length) {
            initScanner(devices);
        }
    }).catch(err => {
        // handle err
        console.log("Error getting cameras", err);
        alert('Cannot access camera.');
    });

    $(document).on('change', '#pilihKamera', function() {
        selectedDeviceId = $(this).val();
        if (html5QrCode.isScanning) {
            html5QrCode.stop().then(() => {
                startScanning();
            }).catch((err) => {
                console.log("Failed to stop and restart scanning.", err);
            });
        }
    });

    async function cekData(code) {
       jQuery.ajax({
          url: "<?= base_url('scan/cek'); ?>",
          type: 'post',
          data: {
             'unique_code': code,
             'waktu': '<?= strtolower($waktu); ?>'
          },
          success: function(response, status, xhr) {
             audio.play();
             console.log(response);
             $('#hasilScan').html(response);
          },
          error: function(xhr, status, thrown) {
             console.log(thrown);
             $('#hasilScan').html(thrown);
          }
       });
    }

    function clearData() {
       $('#hasilScan').html('');
    }
 </script>

 <?= $this->endSection(); ?>
