<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$user_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = " . intval($_SESSION['user_id'])));

$pesan = '';
$tipe_pesan = '';

// Update Nama
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi_nama'])) {
    $nama_baru = mysqli_real_escape_string($conn, trim($_POST['nama']));
    if (strlen($nama_baru) < 3) {
        $pesan = "Nama minimal 3 karakter!";
        $tipe_pesan = "error";
    } else {
        mysqli_query($conn, "UPDATE users SET nama='$nama_baru' WHERE id=" . intval($_SESSION['user_id']));
        $_SESSION['user_nama'] = $nama_baru;
        $pesan = "Nama berhasil diubah!";
        $tipe_pesan = "sukses";
        $user_data['nama'] = $nama_baru;
    }
}

// Update Password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi_password'])) {
    $pw_lama = $_POST['password_lama'];
    $pw_baru = $_POST['password_baru'];
    $pw_konfirmasi = $_POST['password_konfirmasi'];

    if (!password_verify($pw_lama, $user_data['password'])) {
        $pesan = "Password lama salah!";
        $tipe_pesan = "error";
    } elseif (strlen($pw_baru) < 6) {
        $pesan = "Password baru minimal 6 karakter!";
        $tipe_pesan = "error";
    } elseif ($pw_baru !== $pw_konfirmasi) {
        $pesan = "Konfirmasi password tidak cocok!";
        $tipe_pesan = "error";
    } else {
        $hash = password_hash($pw_baru, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password='$hash' WHERE id=" . intval($_SESSION['user_id']));
        $pesan = "Password berhasil diubah!";
        $tipe_pesan = "sukses";
        $user_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = " . intval($_SESSION['user_id'])));
    }
}

// Statistik
$total_cafes = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM cafes"))['t'];
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM users"))['t'];
$total_kasir = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM users WHERE role='kasir'"))['t'];

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'overview';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Admin | Salatiga Coffee Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bg60: '#140F0A',
                        bg30: '#2C1E16',
                        bg20: '#3D2B1F',
                        accent10: '#D4A373',
                        textLight: '#FDF8F5',
                        textMuted: '#A89B91',
                        sukses: '#4ADE80',
                        error: '#F87171',
                        warning: '#FBBF24',
                        info: '#60A5FA'
                    }
                }
            }
        }
    </script>
    <style>
        * { scrollbar-width: thin; scrollbar-color: #D4A373 #2C1E16; }
        .tab-active { background: linear-gradient(135deg, #D4A373, #c09161); color: #140F0A; font-weight: 700; }
        .input-field { width: 100%; background: #140F0A; border: 1px solid #3D2B1F; border-radius: 0.5rem; padding: 0.625rem 1rem; font-size: 0.875rem; outline: none; color: #FDF8F5; transition: border-color 0.2s; }
        .input-field:focus { border-color: #D4A373; }
        .sop-item { counter-increment: sop; position: relative; padding-left: 4rem !important; }
        .sop-item::before { content: counter(sop); position: absolute; left: 1.25rem; top: 1.25rem; width: 2rem; height: 2rem; background: rgba(212,163,115,0.15); color: #D4A373; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem; }
    </style>
</head>
<body class="bg-bg60 text-textLight min-h-screen">

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed left-0 top-0 h-full w-64 bg-bg30 border-r border-bg20 z-50 flex flex-col -translate-x-full md:translate-x-0 transition-transform duration-300">
        <div class="p-6 border-b border-bg20">
            <a href="admin_dashboard.php" class="text-xl font-bold flex items-center gap-2">
                <i class="fas fa-mug-hot text-accent10"></i>
                Salatiga <span class="text-accent10">Coffee</span>
            </a>
            <p class="text-xs text-textMuted mt-1">Admin Panel</p>
        </div>

        <nav class="flex-1 p-4 space-y-1">
            <a href="admin_dashboard.php?tab=overview" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all text-textMuted hover:text-textLight hover:bg-bg20">
                <i class="fas fa-chart-pie w-5"></i> Overview
            </a>
            <a href="admin_dashboard.php?tab=cafes" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all text-textMuted hover:text-textLight hover:bg-bg20">
                <i class="fas fa-store w-5"></i> Kelola Cafe
            </a>
            <a href="admin_dashboard.php?tab=diskon" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all text-textMuted hover:text-textLight hover:bg-bg20">
                <i class="fas fa-tags w-5"></i> Kelola Diskon
            </a>
            <a href="admin_dashboard.php?tab=transaksi" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all text-textMuted hover:text-textLight hover:bg-bg20">
                <i class="fas fa-receipt w-5"></i> Transaksi
            </a>
            <a href="admin_dashboard.php?tab=users" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all text-textMuted hover:text-textLight hover:bg-bg20">
                <i class="fas fa-users w-5"></i> Kelola User
            </a>
            <a href="admin_dashboard.php?tab=ulasan" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all text-textMuted hover:text-textLight hover:bg-bg20">
                <i class="fas fa-comments w-5"></i> Kelola Ulasan
            </a>
            <a href="pengaturan_admin.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all tab-active">
                <i class="fas fa-cog w-5"></i> Pengaturan
            </a>
        </nav>

        <div class="p-4 border-t border-bg20">
            <div class="flex items-center gap-3 mb-3 px-2">
                <div class="w-9 h-9 rounded-full bg-accent10 flex items-center justify-center text-bg60 font-bold text-sm">A</div>
                <div>
                    <p class="text-sm font-semibold"><?= htmlspecialchars($_SESSION['user_nama']) ?></p>
                    <p class="text-xs text-accent10">Administrator</p>
                </div>
            </div>
            <a href="logout.php" class="flex items-center gap-2 px-4 py-2 text-sm text-error hover:bg-error/10 rounded-lg transition-all">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </aside>

    <!-- Mobile Header -->
    <div class="md:hidden fixed top-0 left-0 w-full bg-bg30 border-b border-bg20 z-40 px-5 py-4 flex items-center justify-between shadow-md">
        <a href="admin_dashboard.php" class="text-lg font-bold flex items-center gap-2">
            <i class="fas fa-mug-hot text-accent10"></i>
            Salatiga <span class="text-accent10">Coffee</span>
        </a>
        <button id="sidebarToggle" class="text-textLight hover:text-accent10 focus:outline-none">
            <i class="fas fa-bars text-2xl"></i>
        </button>
    </div>
    
    <!-- Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden backdrop-blur-sm"></div>

    <!-- Main Content -->
    <main class="md:ml-64 p-4 md:p-8 pt-24 md:pt-8">

        <?php if ($pesan): ?>
        <div class="mb-6 px-5 py-4 rounded-xl border <?= $tipe_pesan === 'sukses' ? 'bg-sukses/10 border-sukses/30 text-sukses' : 'bg-error/10 border-error/30 text-error' ?> flex items-center gap-3">
            <i class="fas <?= $tipe_pesan === 'sukses' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
            <?= $pesan ?>
        </div>
        <?php endif; ?>

        <div class="mb-8">
            <h1 class="text-3xl font-bold"><i class="fas fa-cog text-accent10 mr-3"></i>Pengaturan <span class="text-accent10">Admin</span></h1>
            <p class="text-textMuted mt-1">Kelola profil, keamanan, dan lihat SOP administrator</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

            <!-- Ubah Nama -->
            <div class="bg-bg30 rounded-2xl border border-bg20 p-6">
                <h2 class="font-bold text-lg mb-1 flex items-center gap-2"><i class="fas fa-user-edit text-accent10"></i> Ubah Nama</h2>
                <p class="text-textMuted text-xs mb-5">Nama yang ditampilkan di dashboard admin</p>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="aksi_nama" value="1">
                    <div>
                        <label class="text-xs text-textMuted block mb-1">Nama Saat Ini</label>
                        <div class="px-4 py-2.5 bg-bg60/50 border border-bg20 rounded-lg text-sm text-textMuted"><?= htmlspecialchars($user_data['nama']) ?></div>
                    </div>
                    <div>
                        <label class="text-xs text-textMuted block mb-1">Nama Baru</label>
                        <input type="text" name="nama" required minlength="3" placeholder="Masukkan nama baru..." class="input-field">
                    </div>
                    <button type="submit" class="px-6 py-2.5 bg-accent10 text-bg60 font-bold rounded-xl hover:bg-[#c09161] transition-all text-sm">
                        <i class="fas fa-save mr-2"></i>Simpan Nama
                    </button>
                </form>
            </div>

            <!-- Ubah Password -->
            <div class="bg-bg30 rounded-2xl border border-bg20 p-6">
                <h2 class="font-bold text-lg mb-1 flex items-center gap-2"><i class="fas fa-lock text-accent10"></i> Ubah Password</h2>
                <p class="text-textMuted text-xs mb-5">Pastikan password baru minimal 6 karakter</p>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="aksi_password" value="1">
                    <div class="relative">
                        <label class="text-xs text-textMuted block mb-1">Password Lama</label>
                        <input type="password" name="password_lama" id="pw1" required class="input-field pr-10">
                        <div class="absolute right-3 top-[1.85rem] cursor-pointer text-textMuted hover:text-accent10" onclick="togglePw('pw1', this)"><i class="fas fa-eye text-sm"></i></div>
                    </div>
                    <div class="relative">
                        <label class="text-xs text-textMuted block mb-1">Password Baru</label>
                        <input type="password" name="password_baru" id="pw2" required minlength="6" class="input-field pr-10">
                        <div class="absolute right-3 top-[1.85rem] cursor-pointer text-textMuted hover:text-accent10" onclick="togglePw('pw2', this)"><i class="fas fa-eye text-sm"></i></div>
                    </div>
                    <div class="relative">
                        <label class="text-xs text-textMuted block mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="password_konfirmasi" id="pw3" required minlength="6" class="input-field pr-10">
                        <div class="absolute right-3 top-[1.85rem] cursor-pointer text-textMuted hover:text-accent10" onclick="togglePw('pw3', this)"><i class="fas fa-eye text-sm"></i></div>
                    </div>
                    <button type="submit" class="px-6 py-2.5 bg-accent10 text-bg60 font-bold rounded-xl hover:bg-[#c09161] transition-all text-sm">
                        <i class="fas fa-key mr-2"></i>Ubah Password
                    </button>
                </form>
            </div>

        </div>

        <!-- Info Akun -->
        <div class="bg-bg30 rounded-2xl border border-bg20 p-6 mb-8">
            <h2 class="font-bold text-lg mb-4 flex items-center gap-2"><i class="fas fa-id-card text-accent10"></i> Informasi Akun</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-bg60/50 rounded-xl p-4 border border-bg20">
                    <p class="text-xs text-textMuted mb-1">Email</p>
                    <p class="text-sm font-semibold"><?= htmlspecialchars($user_data['email']) ?></p>
                </div>
                <div class="bg-bg60/50 rounded-xl p-4 border border-bg20">
                    <p class="text-xs text-textMuted mb-1">Role</p>
                    <p class="text-sm font-semibold"><span class="text-accent10"><i class="fas fa-crown mr-1"></i>Administrator</span></p>
                </div>
                <div class="bg-bg60/50 rounded-xl p-4 border border-bg20">
                    <p class="text-xs text-textMuted mb-1">User ID</p>
                    <p class="text-sm font-semibold font-mono">#<?= $user_data['id'] ?></p>
                </div>
                <div class="bg-bg60/50 rounded-xl p-4 border border-bg20">
                    <p class="text-xs text-textMuted mb-1">Total Cafe Dikelola</p>
                    <p class="text-sm font-semibold"><?= $total_cafes ?> cafe</p>
                </div>
                <div class="bg-bg60/50 rounded-xl p-4 border border-bg20">
                    <p class="text-xs text-textMuted mb-1">Total User Terdaftar</p>
                    <p class="text-sm font-semibold"><?= $total_users ?> user</p>
                </div>
                <div class="bg-bg60/50 rounded-xl p-4 border border-bg20">
                    <p class="text-xs text-textMuted mb-1">Kasir Aktif</p>
                    <p class="text-sm font-semibold"><?= $total_kasir ?> kasir</p>
                </div>
            </div>
        </div>

        <!-- SOP Admin -->
        <div class="bg-bg30 rounded-2xl border border-bg20 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 rounded-xl bg-accent10/15 flex items-center justify-center"><i class="fas fa-shield-alt text-accent10 text-xl"></i></div>
                <div>
                    <h2 class="font-bold text-lg">SOP Administrator — Salatiga Coffee Hub</h2>
                    <p class="text-textMuted text-xs">Standard Operating Procedure untuk administrator sistem</p>
                </div>
            </div>

            <div class="space-y-6" style="counter-reset: sop;">

                <!-- Bagian 1: Manajemen Platform -->
                <div>
                    <h3 class="text-accent10 font-bold text-sm uppercase tracking-wider mb-3 flex items-center gap-2">
                        <i class="fas fa-server"></i> Manajemen Platform
                    </h3>
                    <div class="space-y-3 ml-1">
                        <div class="sop-item bg-bg60/30 rounded-xl p-4 border border-bg20/50">
                            <p class="font-semibold text-sm">Monitoring Dashboard Harian</p>
                            <p class="text-textMuted text-xs mt-1">Periksa <strong class="text-accent10">Overview</strong> setiap pagi untuk memantau statistik platform: jumlah cafe, menu, transaksi, dan pendapatan. Identifikasi anomali atau penurunan aktivitas.</p>
                        </div>
                        <div class="sop-item bg-bg60/30 rounded-xl p-4 border border-bg20/50">
                            <p class="font-semibold text-sm">Kelola Data Cafe</p>
                            <p class="text-textMuted text-xs mt-1">Pastikan setiap cafe memiliki data lengkap: nama, lokasi, rating, deskripsi, dan gambar. Verifikasi data baru yang diinput. Cafe tanpa informasi lengkap harus ditandai dan dilengkapi dalam 24 jam.</p>
                        </div>
                        <div class="sop-item bg-bg60/30 rounded-xl p-4 border border-bg20/50">
                            <p class="font-semibold text-sm">Audit Menu Berkala</p>
                            <p class="text-textMuted text-xs mt-1">Lakukan audit menu setiap minggu melalui tab <strong class="text-accent10">Kelola Menu</strong>. Pastikan harga, deskripsi, dan gambar konsisten. Hapus menu duplikat atau menu dengan data tidak valid.</p>
                        </div>
                    </div>
                </div>

                <!-- Bagian 2: Manajemen User -->
                <div>
                    <h3 class="text-accent10 font-bold text-sm uppercase tracking-wider mb-3 flex items-center gap-2">
                        <i class="fas fa-users-cog"></i> Manajemen User & Kasir
                    </h3>
                    <div class="space-y-3 ml-1">
                        <div class="sop-item bg-bg60/30 rounded-xl p-4 border border-bg20/50">
                            <p class="font-semibold text-sm">Pembuatan Akun Kasir</p>
                            <p class="text-textMuted text-xs mt-1">Saat cafe baru bergabung, buatkan akun kasir melalui tab <strong class="text-accent10">Users</strong>. Pastikan: (1) role diset ke <em>kasir</em>, (2) <em>cafe_id</em> diisi sesuai cafe, (3) password default diberikan secara aman, (4) kasir diminta mengganti password saat login pertama.</p>
                        </div>
                        <div class="sop-item bg-bg60/30 rounded-xl p-4 border border-bg20/50">
                            <p class="font-semibold text-sm">Review Akses Kasir</p>
                            <p class="text-textMuted text-xs mt-1">Periksa daftar kasir setiap bulan. Nonaktifkan/hapus akun kasir yang sudah tidak aktif (resign atau cafe tutup). Pastikan setiap cafe aktif memiliki minimal 1 akun kasir.</p>
                        </div>
                        <div class="sop-item bg-bg60/30 rounded-xl p-4 border border-bg20/50">
                            <p class="font-semibold text-sm">Pengelolaan User Pelanggan</p>
                            <p class="text-textMuted text-xs mt-1">Monitor pertumbuhan user terdaftar. Tangani permintaan reset password dari user. Hapus akun spam atau akun yang melanggar ketentuan layanan. Jaga privasi data pelanggan.</p>
                        </div>
                    </div>
                </div>

                <!-- Bagian 3: Promo & Diskon -->
                <div>
                    <h3 class="text-accent10 font-bold text-sm uppercase tracking-wider mb-3 flex items-center gap-2">
                        <i class="fas fa-percentage"></i> Manajemen Promo & Diskon
                    </h3>
                    <div class="space-y-3 ml-1">
                        <div class="sop-item bg-bg60/30 rounded-xl p-4 border border-bg20/50">
                            <p class="font-semibold text-sm">Pembuatan Promo</p>
                            <p class="text-textMuted text-xs mt-1">Buat promo melalui tab <strong class="text-accent10">Kelola Diskon</strong>. Setiap promo harus memiliki: kode unik, judul menarik, potongan jelas, minimum pembelian, dan tanggal berlaku. Koordinasikan dengan pemilik cafe terkait.</p>
                        </div>
                        <div class="sop-item bg-bg60/30 rounded-xl p-4 border border-bg20/50">
                            <p class="font-semibold text-sm">Monitoring Promo Aktif</p>
                            <p class="text-textMuted text-xs mt-1">Periksa tanggal kedaluwarsa promo secara rutin. Hapus promo yang sudah expired agar tidak membingungkan pelanggan. Evaluasi efektivitas promo berdasarkan peningkatan transaksi.</p>
                        </div>
                    </div>
                </div>

                <!-- Bagian 4: Transaksi & Keuangan -->
                <div>
                    <h3 class="text-accent10 font-bold text-sm uppercase tracking-wider mb-3 flex items-center gap-2">
                        <i class="fas fa-chart-line"></i> Monitoring Transaksi & Keuangan
                    </h3>
                    <div class="space-y-3 ml-1">
                        <div class="sop-item bg-bg60/30 rounded-xl p-4 border border-bg20/50">
                            <p class="font-semibold text-sm">Rekap Harian</p>
                            <p class="text-textMuted text-xs mt-1">Periksa tab <strong class="text-accent10">Transaksi</strong> setiap akhir hari. Pastikan tidak ada transaksi berstatus <em>Proses</em> yang tertunda lebih dari 24 jam. Tindak lanjuti transaksi bermasalah.</p>
                        </div>
                        <div class="sop-item bg-bg60/30 rounded-xl p-4 border border-bg20/50">
                            <p class="font-semibold text-sm">Laporan Bulanan</p>
                            <p class="text-textMuted text-xs mt-1">Buat rekap pendapatan bulanan per cafe. Bandingkan performa antar cafe. Identifikasi cafe dengan performa rendah dan koordinasikan perbaikan dengan pemilik cafe.</p>
                        </div>
                    </div>
                </div>

                <!-- Bagian 5: Keamanan -->
                <div>
                    <h3 class="text-accent10 font-bold text-sm uppercase tracking-wider mb-3 flex items-center gap-2">
                        <i class="fas fa-shield-alt"></i> Keamanan & Kebijakan
                    </h3>
                    <div class="space-y-3 ml-1">
                        <div class="sop-item bg-bg60/30 rounded-xl p-4 border border-error/20">
                            <p class="font-semibold text-sm text-error">Kerahasiaan Akun Admin</p>
                            <p class="text-textMuted text-xs mt-1">Akun admin memiliki akses <strong>penuh</strong> ke seluruh data platform. Dilarang keras membagikan kredensial admin. Ganti password secara berkala (minimal setiap 3 bulan). Aktifkan autentikasi ganda jika memungkinkan.</p>
                        </div>
                        <div class="sop-item bg-bg60/30 rounded-xl p-4 border border-error/20">
                            <p class="font-semibold text-sm text-error">Penghapusan Data</p>
                            <p class="text-textMuted text-xs mt-1">Sebelum menghapus data (cafe, menu, user, transaksi), pastikan data telah di-backup atau memang tidak diperlukan lagi. Penghapusan bersifat <strong>permanen</strong> dan tidak bisa dikembalikan. Selalu konfirmasi ulang sebelum menghapus.</p>
                        </div>
                        <div class="sop-item bg-bg60/30 rounded-xl p-4 border border-warning/20">
                            <p class="font-semibold text-sm text-warning">Privasi Data Pelanggan</p>
                            <p class="text-textMuted text-xs mt-1">Data pelanggan (email, alamat, riwayat transaksi) bersifat <strong>konfidensial</strong>. Dilarang mengekspos, menjual, atau membagikan data pelanggan ke pihak ketiga tanpa persetujuan. Patuhi regulasi perlindungan data yang berlaku.</p>
                        </div>
                        <div class="sop-item bg-bg60/30 rounded-xl p-4 border border-warning/20">
                            <p class="font-semibold text-sm text-warning">Eskalasi Masalah</p>
                            <p class="text-textMuted text-xs mt-1">Jika terjadi insiden keamanan (percobaan peretasan, kebocoran data, penyalahgunaan akun kasir), segera: (1) nonaktifkan akun terkait, (2) catat kronologi kejadian, (3) laporkan ke pemilik platform, (4) lakukan perbaikan dan pencegahan.</p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-6 p-4 bg-accent10/5 border border-accent10/20 rounded-xl">
                <p class="text-accent10 text-xs flex items-start gap-2">
                    <i class="fas fa-crown mt-0.5"></i>
                    <span>Sebagai administrator, Anda bertanggung jawab penuh atas integritas data dan kelancaran operasional platform <strong>Salatiga Coffee Hub</strong>. SOP ini wajib dipatuhi untuk menjaga kualitas layanan dan kepercayaan mitra cafe serta pelanggan.</span>
                </p>
            </div>
        </div>

    </main>

    <script>
    function togglePw(id, el) {
        const input = document.getElementById(id);
        const icon = el.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            if(sidebarToggle && sidebar && sidebarOverlay) {
                sidebarToggle.addEventListener('click', () => {
                    sidebar.classList.toggle('-translate-x-full');
                    sidebarOverlay.classList.toggle('hidden');
                });
                sidebarOverlay.addEventListener('click', () => {
                    sidebar.classList.add('-translate-x-full');
                    sidebarOverlay.classList.add('hidden');
                });
            }
        });
    </script>
</body>
</html>
