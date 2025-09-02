<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
    <div class="container-fluid">
        <!-- Stat Cards -->
        <div class="dashboard-grid">
            <!-- Card Siswa -->
             <a href="<?= base_url('admin/siswa'); ?>">
            <div class="stat-card">
                <div class="icon bg-blue">
                    <i class="material-icons">group</i>
                </div>
                <div class="info">
                    <div class="label">Jumlah Siswa</div>
                    <div class="value"><?= count($siswa); ?></div>
                    <div class="status">✓ Terdaftar</div>
                </div>
            </div>
            </a>
            <!-- Card Guru -->
             <a href="<?= base_url('admin/guru'); ?>">
            <div class="stat-card">
                <div class="icon bg-blue">
                    <i class="material-icons">groups</i>
                </div>
                <div class="info">
                    <div class="label">Jumlah Guru</div>
                    <div class="value"><?= count($guru); ?></div>
                    <div class="status">✓ Terdaftar</div>
                </div>
            </div>
</a>
            <!-- Card Kelas -->
             <a href="<?= base_url('admin/kelas'); ?>">
            <div class="stat-card">
                <div class="icon bg-blue">
                    <i class="material-icons">home</i>
                </div>
                <div class="info">
                    <div class="label">Jumlah Kelas</div>
                    <div class="value"><?= count($kelas); ?></div>
                    <div class="status">✓ Terdaftar</div>
                </div>
            </div>
</a>
            <!-- Card Petugas -->
             <a href="<?= base_url('admin/petugas'); ?>">
            <div class="stat-card">
                <div class="icon bg-blue">
                    <i class="material-icons">lock</i>
                </div>
                <div class="info">
                    <div class="label">Jumlah Petugas</div>
                    <div class="value"><?= count($petugas); ?></div>
                    <div class="status">✓ Terdaftar</div>
                </div>
            </div>
</a>
        </div>

        <!-- Attendance and Charts Section -->
        <div class="dashboard-section">
            <!-- Student Attendance -->
            <div class="attendance-block">
                <div class="header">
                    <div class="title">Absensi Siswa Hari Ini</div>
                    <div class="date"><?= $dateNow ?></div>
                </div>
                <div class="stats">
                    <div class="stat-item">
                        <div class="label hadir" style="background-color: #2ecc71;">Hadir</div>
                        <div class="value"><?= $jumlahKehadiranSiswa['hadir'] ?></div>
                    </div>
                    <div class="stat-item">
                        <div class="label sakit" style="background-color: #f1c40f;">Sakit</div>
                        <div class="value"><?= $jumlahKehadiranSiswa['sakit'] ?></div>
                    </div>
                    <div class="stat-item">
                        <div class="label izin" style="background-color: #e67e22;">Izin</div>
                        <div class="value"><?= $jumlahKehadiranSiswa['izin'] ?></div>
                    </div>
                    <div class="stat-item">
                        <div class="label alfa" style="background-color: #e74c3c;">Alfa</div>
                        <div class="value"><?= $jumlahKehadiranSiswa['alfa'] ?></div>
                    </div>
                </div>
            </div>

            <!-- Teacher Attendance -->
            <div class="attendance-block">
                <div class="header">
                    <div class="title">Absensi Guru Hari Ini</div>
                    <div class="date"><?= $dateNow ?></div>
                </div>
                <div class="stats">
                    <div class="stat-item">
                        <div class="label hadir" style="background-color: #2ecc71;">Hadir</div>
                        <div class="value"><?= $jumlahKehadiranGuru['hadir'] ?></div>
                    </div>
                    <div class="stat-item">
                        <div class="label sakit" style="background-color: #f1c40f;">Sakit</div>
                        <div class="value"><?= $jumlahKehadiranGuru['sakit'] ?></div>
                    </div>
                    <div class="stat-item">
                        <div class="label izin" style="background-color: #e67e22;">Izin</div>
                        <div class="value"><?= $jumlahKehadiranGuru['izin'] ?></div>
                    </div>
                    <div class="stat-item">
                        <div class="label alfa" style="background-color: #e74c3c;">Alfa</div>
                        <div class="value"><?= $jumlahKehadiranGuru['alfa'] ?></div>
                    </div>
                </div>
            </div>

            <!-- Student Chart -->
            <div class="chart-container">
                <div class="chart-title">Grafik Kehadiran Siswa (7 Hari Terakhir)</div>
                <canvas id="studentAttendanceChart"></canvas>
            </div>

            <!-- Teacher Chart -->
            <div class="chart-container">
                <div class="chart-title">Grafik Kehadiran Guru (7 Hari Terakhir)</div>
                <canvas id="teacherAttendanceChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const dateLabels = <?= json_encode($dateRange) ?>;
    const studentData = <?= json_encode($grafikKehadiranSiswa) ?>;
    const teacherData = <?= json_encode($grafikkKehadiranGuru) ?>;

    const createChartOptions = (maxValue) => ({
        scales: {
            y: {
                beginAtZero: true,
                min: 0,
                max: Math.max(1, maxValue),
                ticks: {
                    color: '#666',
                    stepSize: 1,
                    font: {
                        size: 12
                    }
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)',
                    borderDash: [2, 2],
                    drawBorder: false,
                }
            },
            x: {
                ticks: {
                    color: '#666',
                    font: {
                        size: 12
                    }
                },
                grid: {
                    display: false
                }
            }
        },
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    color: '#333',
                    font: {
                        size: 12,
                        weight: '600'
                    },
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: '#333',
                borderWidth: 1,
                cornerRadius: 6,
                displayColors: false
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        },
        responsive: true,
        maintainAspectRatio: false
    });

    const maxStudent = Math.max(...studentData, 0);
    const maxTeacher = Math.max(...teacherData, 0);

    // Student Attendance Chart
    new Chart(document.getElementById('studentAttendanceChart'), {
        type: 'line',
        data: {
            labels: dateLabels,
            datasets: [{
                label: 'Kehadiran Siswa',
                data: studentData,
                fill: true,
                backgroundColor: 'rgba(33, 150, 243, 0.1)',
                borderColor: '#2196f3',
                borderWidth: 3,
                tension: 0.4,
                pointBackgroundColor: '#2196f3',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 8,
                pointHoverBackgroundColor: '#1976d2',
                pointHoverBorderColor: '#ffffff'
            }]
        },
        options: createChartOptions(maxStudent)
    });

    // Teacher Attendance Chart
    new Chart(document.getElementById('teacherAttendanceChart'), {
        type: 'line',
        data: {
            labels: dateLabels,
            datasets: [{
                label: 'Kehadiran Guru',
                data: teacherData,
                fill: true,
                backgroundColor: 'rgba(76, 175, 80, 0.1)',
                borderColor: '#4caf50',
                borderWidth: 3,
                tension: 0.4,
                pointBackgroundColor: '#4caf50',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 8,
                pointHoverBackgroundColor: '#388e3c',
                pointHoverBorderColor: '#ffffff'
            }]
        },
        options: createChartOptions(maxTeacher)
    });
});
</script>
<?= $this->endSection() ?>
