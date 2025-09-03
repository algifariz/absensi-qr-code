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
                            <div id="reader" style="width:100%;"></div>
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

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="<?= base_url('assets/js/core/jquery-3.5.1.min.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    let audio = new Audio("<?= base_url('assets/audio/beep.mp3'); ?>");

    function onScanSuccess(decodedText, decodedResult) {
        console.log(`Code matched = ${decodedText}`, decodedResult);
        cekData(decodedText);
        html5QrcodeScanner.clear();
    }

    let config = {
        fps: 10,
        qrbox: {
            width: 250,
            height: 250
        },
        rememberLastUsedCamera: true,
        supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
    };

    let html5QrcodeScanner = new Html5QrcodeScanner(
        "reader", config, /* verbose= */ false);
    html5QrcodeScanner.render(onScanSuccess);


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
                setTimeout(() => {
                    location.reload();
                }, 1500)
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
