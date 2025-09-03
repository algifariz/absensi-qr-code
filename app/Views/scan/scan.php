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
                            <div class="previewParent">
                               <div class="text-center">
                                  <h4 class="d-none w-100" id="searching"><b>Mencari...</b></h4>
                               </div>
                               <div id="previewKamera"></div>
                            </div>
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

 <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js" integrity="sha512-r6rDA7W6ZeQhvl8S7yRVQUKVHdexq+GAlNkNNqVC7Yy9+L8yIOeGuH3AuH5SO_hRqrffUsnaxcNQouJda_G0qQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
 <script src="<?= base_url('assets/js/core/jquery-3.5.1.min.js') ?>"></script>
 <script type="text/javascript">
    let selectedDeviceId = null;
    let audio = new Audio("<?= base_url('assets/audio/beep.mp3'); ?>");
    let html5QrcodeScanner;

    const sourceSelect = $('#pilihKamera');

    function onScanSuccess(decodedText, decodedResult) {
        // handle the scanned code as you like, for example:
        console.log(`Code matched = ${decodedText}`, decodedResult);
        cekData(decodedText);
    }

    function onScanFailure(error) {
        // handle scan failure, usually better to ignore and keep scanning.
        // for example:
        // console.warn(`Code scan error = ${error}`);
    }

    function startScanner(deviceId) {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear();
        }

        html5QrcodeScanner = new Html5QrcodeScanner(
            "previewKamera", {
                fps: 10,
                qrbox: {
                    width: 350,
                    height: 350
                },
                rememberLastUsedCamera: true
            },
            /* verbose= */
            false);
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        $('#previewKamera').removeAttr('style');
    }


    $(document).on('change', '#pilihKamera', function() {
        selectedDeviceId = $(this).val();
        startScanner(selectedDeviceId);
    });


    // This method will trigger user permissions
    Html5Qrcode.getCameras().then(devices => {
        /**
         * devices would be an array of objects of type:
         * { id: "id", label: "label" }
         */
        if (devices && devices.length) {
            sourceSelect.html('');
            devices.forEach((element) => {
                const sourceOption = document.createElement('option')
                sourceOption.text = element.label
                sourceOption.value = element.id
                sourceSelect.append(sourceOption)
            });

            selectedDeviceId = devices[0].id;
            if (devices.length > 1) {
                selectedDeviceId = devices[1].id;
            }
            // start scanning.
            startScanner(selectedDeviceId);
        }
    }).catch(err => {
        // handle err
        console.error(err);
        alert('Cannot access camera.');
    });

    $(document).ready(function() {
        $.ajax({
            url: "<?= base_url('scan/getalldata'); ?>",
            type: 'get',
            success: function(response) {
                localStorage.setItem('app_data', JSON.stringify(response));
            },
            error: function(xhr, status, thrown) {
                console.log(thrown);
            }
        });
    });

    function findUser(code) {
        let app_data = JSON.parse(localStorage.getItem('app_data'));
        if (!app_data) {
            return null;
        }

        let user = app_data.students.find(student => student.unique_code === code);
        if (user) {
            user.type = 'Siswa';
            return user;
        }

        user = app_data.teachers.find(teacher => teacher.unique_code === code);
        if (user) {
            user.type = 'Guru';
            return user;
        }

        return null;
    }

    function displayResult(user) {
        let html = '';
        if (user) {
            html = `<div class="alert alert-success">Data ditemukan</div>
            <table class="table table-bordered">
                <tr>
                    <td>Nama</td>
                    <td>${user.name}</td>
                </tr>
                <tr>
                    <td>Tipe</td>
                    <td>${user.type}</td>
                </tr>
            </table>`;
        } else {
            html = `<div class="alert alert-danger">Data tidak ditemukan</div>`;
        }
        $('#hasilScan').html(html);
    }

    async function cekData(code) {
        let user = findUser(code);
        displayResult(user);

        if (navigator.onLine) {
            jQuery.ajax({
                url: "<?= base_url('scan/cek'); ?>",
                type: 'post',
                data: {
                    'unique_code': code,
                    'waktu': '<?= strtolower($waktu); ?>'
                },
                success: function(response, status, xhr) {
                    console.log('Data sent to server');
                },
                error: function(xhr, status, thrown) {
                    console.log('Failed to send data to server');
                }
            });
        }
    }

    function clearData() {
       $('#hasilScan').html('');
    }

    window.addEventListener('online', () => {
        console.log('Became online');
        $.ajax({
            url: "<?= base_url('scan/getalldata'); ?>",
            type: 'get',
            success: function(response) {
                localStorage.setItem('app_data', JSON.stringify(response));
                console.log('Data synced with server');
            },
            error: function(xhr, status, thrown) {
                console.log('Failed to sync data with server');
            }
        });
    });
 </script>

 <?= $this->endSection(); ?>
