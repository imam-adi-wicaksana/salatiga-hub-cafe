<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'kasir') {
    header("Location: login.php");
    exit;
}

$cafe_id = intval($_SESSION['user_cafe_id']);
$cafe_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM cafes WHERE id = $cafe_id"));
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
        // Refresh user data
        $user_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = " . intval($_SESSION['user_id'])));
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Kasir | <?= htmlspecialchars($cafe_data['nama']) ?></title>
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
            <a href="kasir_dashboard.php" class="text-xl font-bold flex items-center gap-2">
                <i class="fas fa-cash-register text-accent10"></i>
                <span class="text-accent10">Kasir</span> Panel
            </a>
            <p class="text-xs text-textMuted mt-1"><?= htmlspecialchars($cafe_data['nama']) ?></p>
        </div>

        <nav class="flex-1 p-4 space-y-1">
            <a href="kasir_dashboard.php?tab=overview" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all text-textMuted hover:text-textLight hover:bg-bg20">
                <i class="fas fa-chart-pie w-5"></i> Overview
            </a>
            <a href="kasir_dashboard.php?tab=menu" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all text-textMuted hover:text-textLight hover:bg-bg20">
                <i class="fas fa-utensils w-5"></i> Kelola Menu
            </a>
            <a href="kasir_dashboard.php?tab=reservasi" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all text-textMuted hover:text-textLight hover:bg-bg20">
                <i class="fas fa-calendar-check w-5"></i> Reservasi
            </a>
            <a href="kasir_dashboard.php?tab=transaksi" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all text-textMuted hover:text-textLight hover:bg-bg20">
                <i class="fas fa-receipt w-5"></i> Transaksi
            </a>
            <a href="kasir_dashboard.php?tab=cafe" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all text-textMuted hover:text-textLight hover:bg-bg20">
                <i class="fas fa-store w-5"></i> Profil Cafe
            </a>
            <a href="pengaturan_kasir.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all tab-active">
                <i class="fas fa-cog w-5"></i> Pengaturan
            </a>
        </nav>

        <div class="p-4 border-t border-bg20">
            <div class="flex items-center gap-3 mb-3 px-2">
                <div class="w-9 h-9 rounded-full bg-info flex items-center justify-center text-bg60 font-bold text-sm">K</div>
                <div>
                    <p class="text-sm font-semibold"><?= htmlspecialchars($_SESSION['user_nama']) ?></p>
                    <p class="text-xs text-info">Kasir</p>
                </div>
            </div>
            <a href="logout.php" class="flex items-center gap-2 px-4 py-2 text-sm text-error hover:bg-error/10 rounded-lg transition-all">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </aside>

    <!-- Mobile Header -->
    <div class="md:hidden fixed top-0 left-0 w-full bg-bg30 border-b border-bg20 z-40 px-5 py-4 flex items-center justify-between shadow-md">
        <a href="kasir_dashboard.php" class="text-lg font-bold flex items-center gap-2">
            <i class="fas fa-cash-register text-accent10"></i>
            <span class="text-accent10">Kasir</span> Panel
        </a>
        <button id="sidebarToggle" class="text-textLight hover:text-accent10 focus:outline-none">
            <i class="fas fa-bars text-2xl"></i>
        </button>
    </div>
    
    <!-- Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden backdrop-blur-sm"></div>

    <!-- Main Content -->
    <main class="md:ml-64 p-4 md:p-8 pt-24 md:pt-8">

        <!-- Notifikasi -->
        <?php if ($pesan): ?>
        <div class="mb-6 px-5 py-4 rounded-xl border <?= $tipe_pesan === 'sukses' ? 'bg-sukses/10 border-sukses/30 text-sukses' : 'bg-error/10 border-error/30 text-error' ?> flex items-center gap-3">
            <i class="fas <?= $tipe_pesan === 'sukses' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
            <?= $pesan ?>
        </div>
        <?php endif; ?>

        <div class="mb-8">
            <h1 class="text-3xl font-bold"><i class="fas fa-cog text-accent10 mr-3"></i>Pengaturan <span class="text-accent10">Akun</span></h1>
            <p class="text-textMuted mt-1">Kelola profil dan keamanan akun kasir</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

            <!-- Ubah Nama -->
            <div class="bg-bg30 rounded-2xl border border-bg20 p-6">
                <h2 class="font-bold text-lg mb-1 flex items-center gap-2"><i class="fas fa-user-edit text-accent10"></i> Ubah Nama</h2>
                <p class="text-textMuted text-xs mb-5">Nama yang ditampilkan di dashboard kasir</p>
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
                    <p class="text-sm font-semibold"><span class="text-info"><i class="fas fa-cash-register mr-1"></i>Kasir</span></p>
                </div>
                <div class="bg-bg60/50 rounded-xl p-4 border border-bg20">
                    <p class="text-xs text-textMuted mb-1">Cafe</p>
                    <p class="text-sm font-semibold text-accent10"><?= htmlspecialchars($cafe_data['nama']) ?></p>
                </div>
                <div class="bg-bg60/50 rounded-xl p-4 border border-bg20">
                    <p class="text-xs text-textMuted mb-1">Member Sejak</p>
                    <p class="text-sm font-semibold"><?= htmlspecialchars($user_data['member_sejak']) ?></p>
                </div>
                <div class="bg-bg60/50 rounded-xl p-4 border border-bg20">
                    <p class="text-xs text-textMuted mb-1">User ID</p>
                    <p class="text-sm font-semibold font-mono">#<?= $user_data['id'] ?></p>
                </div>
                <div class="bg-bg60/50 rounded-xl p-4 border border-bg20">
                    <p class="text-xs text-textMuted mb-1">Lokasi Cafe</p>
                    <p class="text-sm font-semibold"><?= htmlspecialchars($cafe_data['lokasi']) ?></p>
                </div>
            </div>
        </div>

        <!-- SOP Kasir -->
        <div class="bg-bg30 rounded-2xl border border-bg20 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 rounded-xl bg-accent10/15 flex items-center justify-center"><i class="fas fa-book text-accent10 text-xl"></i></div>
                <div>
                    <h2 class="font-bold text-lg">SOP Kasir — Salatiga Coffee Hub</h2>
                    <p class="text-textMuted text-xs">Standard Operating Procedure untuk seluruh kasir</p>
                </div>
            </div>

            <div class="space-y-6" style="counter-reset: sop;">

                <!-- Bagian 1: Persiapan -->
                <div>
                    <h3 class="text-accent10 font-bold text-sm uppercase tracking-wider mb-3 flex items-center gap-2">
                        <i class="fas fa-clipboard-check"></i> Persiapan Sebelum Shift
                    </h3>
                    <div class="space-y-3 ml-1">
                        <div class="sop-item bg-bg60/30 rounded-xl p-4 border border-bg20/50">
                            <p class="font-semibold text-sm">Login ke Sistem</p>
                            <p class="text-textMuted text-xs mt-1">Login menggunakan akun kasir yang telah diberikan. Segera ubah password default setelah login pertama kali demi keamanan akun.</p>
                        </div>
                        <div class="sop-item bg-bg60/30 rounded-xl p-4 border border-bg20/50">
                            <p class="font-semibold text-sm">Periksa Stok Menu</p>
                            <p class="text-textMuted text-xs mt-1">Cek ketersediaan semua item menu di tab <strong class="text-accent10">Kelola Menu</strong>. Pastikan harga dan deskripsi sudah benar. Nonaktifkan menu yang stoknya habis.</p>
                        </div>
                        <div class="sop-item bg-bg60/30 rounded-xl p-4 border border-bg20/50">
                            <p class="font-semibold text-sm">Cek Reservasi Hari Ini</p>
                            <p class="text-textMuted text-xs mt-1">Periksa tab <strong class="text-accent10">Reservasi</strong> untuk melihat jadwal pelanggan hari ini. Siapkan meja sesuai jumlah orang dan jam kedatangan.</p>
                        </div>
                    </div>
                </div>

                <!-- Bagian 2: Selama Bertugas -->
                <div>
                    <h3 class="text-accent10 font-bold text-sm uppercase tracking-wider mb-3 flex items-center gap-2">
                        <i class="fas fa-user-clock"></i> Selama Bertugas
                    </h3>
                    <div class="space-y-3 ml-1">
                        <div class="sop-item bg-bg60/30 rounded-xl p-4 border border-bg20/50">
                            <p class="font-semibold text-sm">Layani Pelanggan dengan Ramah</p>
                            <p class="text-textMuted text-xs mt-1">Sapa pelanggan dengan senyum. Gunakan sapaan: <em class="text-accent10">"Selamat datang di [Nama Cafe], ada yang bisa dibantu?"</em>. Berikan rekomendasi menu unggulan jika diminta.</p>
                        </div>
                        <div class="sop-item bg-bg60/30 rounded-xl p-4 border border-bg20/50">
                            <p class="font-semibold text-sm">Input Pesanan dengan Teliti</p>
                            <p class="text-textMuted text-xs mt-1">Pastikan pesanan yang diinput sesuai permintaan pelanggan. Ulangi pesanan sebelum diproses. Perhatikan catatan khusus (alergi, tingkat kemanisan, dsb).</p>
                        </div>
                        <div class="sop-item bg-bg60/30 rounded-xl p-4 border border-bg20/50">
                            <p class="font-semibold text-sm">Kelola Reservasi Masuk</p>
                            <p class="text-textMuted text-xs mt-1">Saat ada reservasi baru via telepon/walk-in, segera input ke sistem melalui tab <strong class="text-accent10">Reservasi</strong>. Konfirmasi ketersediaan meja terlebih dahulu. Update status menjadi <strong class="text-sukses">Dikonfirmasi</strong> jika sudah pasti.</p>
                        </div>
                        <div class="sop-item bg-bg60/30 rounded-xl p-4 border border-bg20/50">
                            <p class="font-semibold text-sm">Proses Pembayaran</p>
                            <p class="text-textMuted text-xs mt-1">Terima pembayaran tunai/non-tunai sesuai pilihan pelanggan. Pastikan nominal sesuai. Update status transaksi di tab <strong class="text-accent10">Transaksi</strong> setelah pembayaran berhasil.</p>
                        </div>
                    </div>
                </div>

                <!-- Bagian 3: Aturan Penting -->
                <div>
                    <h3 class="text-accent10 font-bold text-sm uppercase tracking-wider mb-3 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle"></i> Aturan Penting
                    </h3>
                    <div class="space-y-3 ml-1">
                        <div class="sop-item bg-bg60/30 rounded-xl p-4 border border-error/20">
                            <p class="font-semibold text-sm text-error">Jangan Bagikan Akun</p>
                            <p class="text-textMuted text-xs mt-1">Akun kasir bersifat <strong>pribadi</strong>. Dilarang membagikan email dan password kepada siapapun. Segala aktivitas yang tercatat pada akun menjadi tanggung jawab pemilik akun.</p>
                        </div>
                        <div class="sop-item bg-bg60/30 rounded-xl p-4 border border-error/20">
                            <p class="font-semibold text-sm text-error">Batasan Akses</p>
                            <p class="text-textMuted text-xs mt-1">Kasir <strong>hanya</strong> dapat mengelola data cafe sendiri. Tidak diperkenankan mengakses atau memodifikasi data cafe lain. Pelanggaran akan berakibat pencabutan akses.</p>
                        </div>
                        <div class="sop-item bg-bg60/30 rounded-xl p-4 border border-warning/20">
                            <p class="font-semibold text-sm text-warning">Perubahan Menu</p>
                            <p class="text-textMuted text-xs mt-1">Perubahan harga menu harus mendapat persetujuan manajer/pemilik cafe. Penambahan menu baru harus disertai foto dan deskripsi yang akurat.</p>
                        </div>
                        <div class="sop-item bg-bg60/30 rounded-xl p-4 border border-warning/20">
                            <p class="font-semibold text-sm text-warning">Penanganan Komplain</p>
                            <p class="text-textMuted text-xs mt-1">Jika ada komplain pelanggan, dengarkan dengan sabar. Tawarkan solusi (penggantian menu/diskon). Jika tidak bisa ditangani, eskalasi ke manajer. Catat semua insiden di catatan reservasi.</p>
                        </div>
                    </div>
                </div>

                <!-- Bagian 4: Akhir Shift -->
                <div>
                    <h3 class="text-accent10 font-bold text-sm uppercase tracking-wider mb-3 flex items-center gap-2">
                        <i class="fas fa-door-open"></i> Akhir Shift
                    </h3>
                    <div class="space-y-3 ml-1">
                        <div class="sop-item bg-bg60/30 rounded-xl p-4 border border-bg20/50">
                            <p class="font-semibold text-sm">Rekap Transaksi</p>
                            <p class="text-textMuted text-xs mt-1">Periksa semua transaksi hari ini di tab <strong class="text-accent10">Transaksi</strong>. Pastikan semua transaksi berstatus <strong class="text-sukses">Selesai</strong> atau <strong class="text-error">Batal</strong>. Tidak boleh ada yang berstatus <em>Proses</em> saat shift berakhir.</p>
                        </div>
                        <div class="sop-item bg-bg60/30 rounded-xl p-4 border border-bg20/50">
                            <p class="font-semibold text-sm">Cek Reservasi Besok</p>
                            <p class="text-textMuted text-xs mt-1">Periksa apakah ada reservasi untuk hari berikutnya. Informasikan ke kasir shift selanjutnya jika ada reservasi pagi.</p>
                        </div>
                        <div class="sop-item bg-bg60/30 rounded-xl p-4 border border-bg20/50">
                            <p class="font-semibold text-sm">Logout dari Sistem</p>
                            <p class="text-textMuted text-xs mt-1">Selalu <strong>logout</strong> setelah selesai shift. Jangan biarkan sesi terbuka di komputer kasir tanpa pengawasan.</p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-6 p-4 bg-info/5 border border-info/20 rounded-xl">
                <p class="text-info text-xs flex items-start gap-2">
                    <i class="fas fa-info-circle mt-0.5"></i>
                    <span>SOP ini berlaku untuk seluruh kasir di bawah jaringan <strong>Salatiga Coffee Hub</strong>. Perubahan SOP akan diinformasikan melalui manajer masing-masing cafe. Jika ada pertanyaan, hubungi admin melalui email <strong>admin@salatiga.coffee</strong>.</span>
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
