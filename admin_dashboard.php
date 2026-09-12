<?php
include 'koneksi.php';
session_start();

// Cek apakah user sudah login dan role admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Handle aksi CRUD
$pesan = '';
$tipe_pesan = '';

// === HAPUS DATA ===
if (isset($_GET['hapus']) && isset($_GET['tabel'])) {
    $id = intval($_GET['hapus']);
    $tabel = $_GET['tabel'];
    $allowed = ['cafes', 'diskon', 'transaksi', 'users', 'ulasan'];
    if (in_array($tabel, $allowed)) {
        if ($tabel === 'users' && $id == $_SESSION['user_id']) {
            $pesan = "Tidak bisa menghapus akun sendiri!";
            $tipe_pesan = "error";
        } else {
            mysqli_query($conn, "DELETE FROM $tabel WHERE id = $id");
            $pesan = "Data berhasil dihapus dari tabel $tabel!";
            $tipe_pesan = "sukses";
        }
    }
}

// === TAMBAH / EDIT CAFE ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi_cafe'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $lokasi_filter = strtolower($lokasi);
    $rating = floatval($_POST['rating']);
    $total_ulasan = intval($_POST['total_ulasan']);
    $harga_min = intval($_POST['harga_min']);
    $harga_estimasi = mysqli_real_escape_string($conn, $_POST['harga_estimasi']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    $gambar = isset($_POST['gambar_old']) ? mysqli_real_escape_string($conn, $_POST['gambar_old']) : '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $file_name = time() . '_' . basename($_FILES['gambar']['name']);
        $target_dir = 'uploads/cafes/';
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        $target_file = $target_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (in_array($file_type, ['jpg', 'jpeg', 'png'])) {
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                $gambar = $target_file;
            }
        }
    } elseif (isset($_POST['gambar']) && !empty($_POST['gambar'])) {
        $gambar = mysqli_real_escape_string($conn, $_POST['gambar']);
    }

    if ($_POST['aksi_cafe'] === 'tambah') {
        mysqli_query($conn, "INSERT INTO cafes (nama, lokasi, lokasi_filter, rating, total_ulasan, harga_min, harga_estimasi, gambar, deskripsi) VALUES ('$nama','$lokasi','$lokasi_filter',$rating,$total_ulasan,$harga_min,'$harga_estimasi','$gambar','$deskripsi')");
        $new_cafe_id = mysqli_insert_id($conn);
        
        $email_kasir = mysqli_real_escape_string($conn, $_POST['email_kasir']);
        $password_kasir = password_hash($_POST['password_kasir'], PASSWORD_DEFAULT);
        mysqli_query($conn, "INSERT INTO users (nama, email, password, role, cafe_id) VALUES ('Kasir $nama', '$email_kasir', '$password_kasir', 'kasir', $new_cafe_id)");
        
        $pesan = "Cafe dan akun kasir berhasil ditambahkan!";
    } elseif ($_POST['aksi_cafe'] === 'edit') {
        $id = intval($_POST['id']);
        mysqli_query($conn, "UPDATE cafes SET nama='$nama', lokasi='$lokasi', lokasi_filter='$lokasi_filter', rating=$rating, total_ulasan=$total_ulasan, harga_min=$harga_min, harga_estimasi='$harga_estimasi', gambar='$gambar', deskripsi='$deskripsi' WHERE id=$id");
        $pesan = "Cafe berhasil diupdate!";
    }
    $tipe_pesan = "sukses";
}

// === TAMBAH / EDIT ULASAN ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi_ulasan'])) {
    $cafe_id = intval($_POST['cafe_id']);
    $nama_user = mysqli_real_escape_string($conn, $_POST['nama_user']);
    $rating = floatval($_POST['rating']);
    $komentar = mysqli_real_escape_string($conn, $_POST['komentar']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);

    if ($_POST['aksi_ulasan'] === 'tambah') {
        mysqli_query($conn, "INSERT INTO ulasan (cafe_id, nama_user, rating, komentar, tanggal) VALUES ($cafe_id, '$nama_user', $rating, '$komentar', '$tanggal')");
        mysqli_query($conn, "UPDATE cafes SET total_ulasan = (SELECT COUNT(*) FROM ulasan WHERE cafe_id=$cafe_id) WHERE id=$cafe_id");
        $pesan = "Ulasan berhasil ditambahkan!";
    } elseif ($_POST['aksi_ulasan'] === 'edit') {
        $id = intval($_POST['id']);
        mysqli_query($conn, "UPDATE ulasan SET cafe_id=$cafe_id, nama_user='$nama_user', rating=$rating, komentar='$komentar', tanggal='$tanggal' WHERE id=$id");
        mysqli_query($conn, "UPDATE cafes SET total_ulasan = (SELECT COUNT(*) FROM ulasan WHERE cafe_id=$cafe_id) WHERE id=$cafe_id");
        $pesan = "Ulasan berhasil diupdate!";
    }
    $tipe_pesan = "sukses";
}

// === TAMBAH / EDIT DISKON ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi_diskon'])) {
    $kode = mysqli_real_escape_string($conn, $_POST['kode']);
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $potongan = mysqli_real_escape_string($conn, $_POST['potongan']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $min_beli = mysqli_real_escape_string($conn, $_POST['min_beli']);
    $valid_hingga = mysqli_real_escape_string($conn, $_POST['valid_hingga']);

    if ($_POST['aksi_diskon'] === 'tambah') {
        mysqli_query($conn, "INSERT INTO diskon (kode, judul, potongan, deskripsi, min_beli, valid_hingga) VALUES ('$kode','$judul','$potongan','$deskripsi','$min_beli','$valid_hingga')");
        $pesan = "Diskon berhasil ditambahkan!";
    } elseif ($_POST['aksi_diskon'] === 'edit') {
        $id = intval($_POST['id']);
        mysqli_query($conn, "UPDATE diskon SET kode='$kode', judul='$judul', potongan='$potongan', deskripsi='$deskripsi', min_beli='$min_beli', valid_hingga='$valid_hingga' WHERE id=$id");
        $pesan = "Diskon berhasil diupdate!";
    }
    $tipe_pesan = "sukses";
}

// === EDIT TRANSAKSI ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi_transaksi'])) {
    if ($_POST['aksi_transaksi'] === 'edit') {
        $id = intval($_POST['id']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        $metode = mysqli_real_escape_string($conn, $_POST['metode_pembayaran']);
        mysqli_query($conn, "UPDATE transaksi SET status='$status', metode_pembayaran='$metode' WHERE id=$id");
        $pesan = "Transaksi berhasil diupdate!";
        $tipe_pesan = "sukses";
    }
}

// === EDIT USER ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi_user'])) {
    if ($_POST['aksi_user'] === 'edit') {
        $id = intval($_POST['id']);
        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $role = mysqli_real_escape_string($conn, $_POST['role']);
        $poin = intval($_POST['poin']);
        mysqli_query($conn, "UPDATE users SET nama='$nama', email='$email', role='$role', poin=$poin WHERE id=$id");
        $pesan = "User berhasil diupdate!";
        $tipe_pesan = "sukses";
    }
}

// Ambil data untuk statistik
$total_cafes = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM cafes"))['total'];
$total_menu = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM menu"))['total'];
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
$total_transaksi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi"))['total'];
$total_pendapatan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total),0) as total FROM transaksi"))['total'];
$total_diskon = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM diskon"))['total'];

// Ambil data tabel
$cafes = mysqli_query($conn, "SELECT * FROM cafes ORDER BY id DESC");
$menus = mysqli_query($conn, "SELECT menu.*, cafes.nama as nama_cafe FROM menu LEFT JOIN cafes ON menu.cafe_id = cafes.id ORDER BY menu.id DESC");
$diskons = mysqli_query($conn, "SELECT * FROM diskon ORDER BY id DESC");
$transaksis = mysqli_query($conn, "SELECT transaksi.*, users.nama as nama_user FROM transaksi LEFT JOIN users ON transaksi.user_id = users.id ORDER BY transaksi.id DESC");
$userlist = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
$cafes_list = mysqli_query($conn, "SELECT id, nama FROM cafes ORDER BY nama");

// Tab aktif
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'overview';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Salatiga Coffee Hub</title>
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
                        warning: '#FBBF24'
                    }
                }
            }
        }
    </script>
    <style>
        * { scrollbar-width: thin; scrollbar-color: #D4A373 #2C1E16; }
        .tab-active { background: linear-gradient(135deg, #D4A373, #c09161); color: #140F0A; font-weight: 700; }
        .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 30px rgba(212,163,115,0.15); }
        .table-row:hover { background: rgba(212,163,115,0.05); }
        .modal-overlay { backdrop-filter: blur(4px); }
        .modal-bg { position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px); z-index: 100; display: flex; align-items: center; justify-content: center; }
        .modal-box { background: #2C1E16; border: 1px solid #3D2B1F; border-radius: 1rem; padding: 2rem; width: 95%; max-width: 600px; max-height: 90vh; overflow-y: auto; }
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
            <a href="?tab=overview" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all <?= $tab === 'overview' ? 'tab-active' : 'text-textMuted hover:text-textLight hover:bg-bg20' ?>">
                <i class="fas fa-chart-pie w-5"></i> Overview
            </a>
            <a href="?tab=cafes" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all <?= $tab === 'cafes' ? 'tab-active' : 'text-textMuted hover:text-textLight hover:bg-bg20' ?>">
                <i class="fas fa-store w-5"></i> Kelola Cafe
            </a>

            <a href="?tab=diskon" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all <?= $tab === 'diskon' ? 'tab-active' : 'text-textMuted hover:text-textLight hover:bg-bg20' ?>">
                <i class="fas fa-tags w-5"></i> Kelola Diskon
            </a>
            <a href="?tab=transaksi" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all <?= $tab === 'transaksi' ? 'tab-active' : 'text-textMuted hover:text-textLight hover:bg-bg20' ?>">
                <i class="fas fa-receipt w-5"></i> Transaksi
            </a>
            <a href="?tab=users" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all <?= $tab === 'users' ? 'tab-active' : 'text-textMuted hover:text-textLight hover:bg-bg20' ?>">
                <i class="fas fa-users w-5"></i> Kelola User
            </a>
            <a href="?tab=ulasan" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all <?= $tab === 'ulasan' ? 'tab-active' : 'text-textMuted hover:text-textLight hover:bg-bg20' ?>">
                <i class="fas fa-comments w-5"></i> Kelola Ulasan
            </a>
            <a href="pengaturan_admin.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all text-textMuted hover:text-textLight hover:bg-bg20">
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

        <!-- Notifikasi -->
        <?php if ($pesan): ?>
        <div class="mb-6 px-5 py-4 rounded-xl border <?= $tipe_pesan === 'sukses' ? 'bg-sukses/10 border-sukses/30 text-sukses' : 'bg-error/10 border-error/30 text-error' ?> flex items-center gap-3">
            <i class="fas <?= $tipe_pesan === 'sukses' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
            <?= $pesan ?>
        </div>
        <?php endif; ?>

        <!-- ==================== OVERVIEW ==================== -->
        <?php if ($tab === 'overview'): ?>
        <div class="mb-8">
            <h1 class="text-3xl font-bold">Dashboard <span class="text-accent10">Overview</span></h1>
            <p class="text-textMuted mt-1">Selamat datang, <?= htmlspecialchars($_SESSION['user_nama']) ?>!</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="stat-card bg-bg30 rounded-2xl p-6 border border-bg20">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-accent10/15 flex items-center justify-center"><i class="fas fa-store text-accent10 text-xl"></i></div>
                    <span class="text-xs text-sukses bg-sukses/10 px-2 py-1 rounded-full"><i class="fas fa-arrow-up mr-1"></i>Aktif</span>
                </div>
                <p class="text-3xl font-bold"><?= $total_cafes ?></p>
                <p class="text-textMuted text-sm mt-1">Total Cafe</p>
            </div>
            <div class="stat-card bg-bg30 rounded-2xl p-6 border border-bg20">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-accent10/15 flex items-center justify-center"><i class="fas fa-utensils text-accent10 text-xl"></i></div>
                </div>
                <p class="text-3xl font-bold"><?= $total_menu ?></p>
                <p class="text-textMuted text-sm mt-1">Total Menu</p>
            </div>
            <div class="stat-card bg-bg30 rounded-2xl p-6 border border-bg20">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-accent10/15 flex items-center justify-center"><i class="fas fa-users text-accent10 text-xl"></i></div>
                </div>
                <p class="text-3xl font-bold"><?= $total_users ?></p>
                <p class="text-textMuted text-sm mt-1">Total Users</p>
            </div>
            <div class="stat-card bg-bg30 rounded-2xl p-6 border border-bg20">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-accent10/15 flex items-center justify-center"><i class="fas fa-receipt text-accent10 text-xl"></i></div>
                </div>
                <p class="text-3xl font-bold"><?= $total_transaksi ?></p>
                <p class="text-textMuted text-sm mt-1">Total Transaksi</p>
            </div>
            <div class="stat-card bg-bg30 rounded-2xl p-6 border border-bg20">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-sukses/15 flex items-center justify-center"><i class="fas fa-money-bill-wave text-sukses text-xl"></i></div>
                </div>
                <p class="text-3xl font-bold">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></p>
                <p class="text-textMuted text-sm mt-1">Total Pendapatan</p>
            </div>
            <div class="stat-card bg-bg30 rounded-2xl p-6 border border-bg20">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-warning/15 flex items-center justify-center"><i class="fas fa-tags text-warning text-xl"></i></div>
                </div>
                <p class="text-3xl font-bold"><?= $total_diskon ?></p>
                <p class="text-textMuted text-sm mt-1">Promo Aktif</p>
            </div>
        </div>

        <!-- Transaksi Terbaru -->
        <div class="bg-bg30 rounded-2xl border border-bg20 p-6">
            <h2 class="text-lg font-bold mb-4"><i class="fas fa-clock text-accent10 mr-2"></i>Transaksi Terbaru</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="text-textMuted border-b border-bg20">
                        <th class="text-left py-3 px-4">Order ID</th>
                        <th class="text-left py-3 px-4">User</th>
                        <th class="text-left py-3 px-4">Total</th>
                        <th class="text-left py-3 px-4">Metode</th>
                        <th class="text-left py-3 px-4">Status</th>
                        <th class="text-left py-3 px-4">Tanggal</th>
                    </tr></thead>
                    <tbody>
                    <?php 
                    $recent = mysqli_query($conn, "SELECT transaksi.*, users.nama as nama_user FROM transaksi LEFT JOIN users ON transaksi.user_id = users.id ORDER BY transaksi.id DESC LIMIT 5");
                    while ($t = mysqli_fetch_assoc($recent)): ?>
                        <tr class="table-row border-b border-bg20/50">
                            <td class="py-3 px-4 font-mono text-accent10"><?= htmlspecialchars($t['order_id']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($t['nama_user'] ?? '-') ?></td>
                            <td class="py-3 px-4 font-semibold">Rp <?= number_format($t['total'], 0, ',', '.') ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($t['metode_pembayaran']) ?></td>
                            <td class="py-3 px-4"><span class="px-2 py-1 rounded-full text-xs bg-sukses/15 text-sukses"><?= htmlspecialchars($t['status']) ?></span></td>
                            <td class="py-3 px-4 text-textMuted"><?= $t['tanggal'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- ==================== KELOLA CAFE ==================== -->
        <?php if ($tab === 'cafes'): ?>
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold">Kelola <span class="text-accent10">Cafe</span></h1>
                <p class="text-textMuted mt-1"><?= $total_cafes ?> cafe terdaftar</p>
            </div>
            <button onclick="document.getElementById('formCafe').classList.toggle('hidden')" class="px-5 py-2.5 bg-accent10 text-bg60 font-bold rounded-xl hover:bg-[#c09161] transition-all text-sm">
                <i class="fas fa-plus mr-2"></i>Tambah Cafe
            </button>
        </div>

        <!-- Form Tambah Cafe -->
        <div id="formCafe" class="hidden bg-bg30 rounded-2xl border border-bg20 p-6 mb-6">
            <h3 class="font-bold text-lg mb-4"><i class="fas fa-store text-accent10 mr-2"></i>Tambah Cafe Baru</h3>
            <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" name="aksi_cafe" value="tambah">
                <div><label class="text-xs text-textMuted block mb-1">Nama Cafe</label><input type="text" name="nama" required class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div><label class="text-xs text-textMuted block mb-1">Lokasi</label>
                    <select name="lokasi" required class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none">
                        <option value="Sidorejo">Sidorejo</option>
                        <option value="Tingkir">Tingkir</option>
                        <option value="Argomulyo">Argomulyo</option>
                        <option value="Sidomukti">Sidomukti</option>
                    </select>
                </div>
                <div><label class="text-xs text-textMuted block mb-1">Rating</label><input type="number" step="0.1" name="rating" max="5" class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div><label class="text-xs text-textMuted block mb-1">Total Ulasan</label><input type="number" name="total_ulasan" class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div><label class="text-xs text-textMuted block mb-1">Harga Min</label><input type="number" name="harga_min" class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div><label class="text-xs text-textMuted block mb-1">Estimasi Harga</label><input type="text" name="harga_estimasi" class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div><label class="text-xs text-textMuted block mb-1">Upload Gambar (.jpg/.png)</label><input type="file" name="gambar" accept=".jpg,.jpeg,.png" class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div class="md:col-span-2"><label class="text-xs text-textMuted block mb-1">Deskripsi</label><textarea name="deskripsi" rows="3" class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none resize-none"></textarea></div>
                
                <div class="md:col-span-2 mt-2 pt-4 border-t border-bg20">
                    <h4 class="text-sm font-bold text-accent10 mb-3"><i class="fas fa-user-lock mr-2"></i>Akun Kasir</h4>
                </div>
                <div><label class="text-xs text-textMuted block mb-1">Email Kasir</label><input type="email" name="email_kasir" required class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div><label class="text-xs text-textMuted block mb-1">Password Kasir</label><input type="password" name="password_kasir" required class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>

                <div class="md:col-span-2 mt-2"><button type="submit" class="px-6 py-2.5 bg-accent10 text-bg60 font-bold rounded-xl hover:bg-[#c09161] text-sm"><i class="fas fa-save mr-2"></i>Simpan</button></div>
            </form>
        </div>

        <!-- Tabel Cafe -->
        <div class="bg-bg30 rounded-2xl border border-bg20 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="text-textMuted border-b border-bg20 bg-bg20/30">
                        <th class="text-left py-3 px-4">ID</th>
                        <th class="text-left py-3 px-4">Nama</th>
                        <th class="text-left py-3 px-4">Lokasi</th>
                        <th class="text-left py-3 px-4">Rating</th>
                        <th class="text-left py-3 px-4">Harga</th>
                        <th class="text-left py-3 px-4">Aksi</th>
                    </tr></thead>
                    <tbody>
                    <?php while ($c = mysqli_fetch_assoc($cafes)): ?>
                        <tr class="table-row border-b border-bg20/50">
                            <td class="py-3 px-4 text-textMuted"><?= $c['id'] ?></td>
                            <td class="py-3 px-4 font-semibold"><?= htmlspecialchars($c['nama']) ?></td>
                            <td class="py-3 px-4 text-textMuted"><?= htmlspecialchars($c['lokasi']) ?></td>
                            <td class="py-3 px-4"><span class="text-warning"><i class="fas fa-star mr-1"></i><?= $c['rating'] ?></span></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($c['harga_estimasi']) ?></td>
                            <td class="py-3 px-4 flex gap-2">
                                <button onclick="editCafe(this)" data-id="<?= $c['id'] ?>" data-nama="<?= htmlspecialchars($c['nama']) ?>" data-lokasi="<?= htmlspecialchars($c['lokasi']) ?>" data-lokasi_filter="<?= htmlspecialchars($c['lokasi_filter']) ?>" data-rating="<?= $c['rating'] ?>" data-total_ulasan="<?= $c['total_ulasan'] ?>" data-harga_min="<?= $c['harga_min'] ?>" data-harga_estimasi="<?= htmlspecialchars($c['harga_estimasi']) ?>" data-gambar="<?= htmlspecialchars($c['gambar']) ?>" data-deskripsi="<?= htmlspecialchars($c['deskripsi']) ?>" class="text-accent10 hover:text-yellow-300 text-xs"><i class="fas fa-edit mr-1"></i>Edit</button>
                                <a href="?tab=cafes&hapus=<?= $c['id'] ?>&tabel=cafes" onclick="return confirm('Hapus cafe ini?')" class="text-error hover:text-red-300 text-xs"><i class="fas fa-trash mr-1"></i>Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>


        <!-- ==================== KELOLA DISKON ==================== -->
        <?php if ($tab === 'diskon'): ?>
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold">Kelola <span class="text-accent10">Diskon</span></h1>
                <p class="text-textMuted mt-1"><?= $total_diskon ?> promo aktif</p>
            </div>
            <button onclick="document.getElementById('formDiskon').classList.toggle('hidden')" class="px-5 py-2.5 bg-accent10 text-bg60 font-bold rounded-xl hover:bg-[#c09161] transition-all text-sm">
                <i class="fas fa-plus mr-2"></i>Tambah Diskon
            </button>
        </div>

        <div id="formDiskon" class="hidden bg-bg30 rounded-2xl border border-bg20 p-6 mb-6">
            <h3 class="font-bold text-lg mb-4"><i class="fas fa-tags text-accent10 mr-2"></i>Tambah Diskon Baru</h3>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" name="aksi_diskon" value="tambah">
                <div><label class="text-xs text-textMuted block mb-1">Kode</label><input type="text" name="kode" required class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div><label class="text-xs text-textMuted block mb-1">Judul</label><input type="text" name="judul" required class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div><label class="text-xs text-textMuted block mb-1">Potongan</label><input type="text" name="potongan" required placeholder="Rp 5.000 atau 10%" class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div><label class="text-xs text-textMuted block mb-1">Min. Pembelian</label><input type="text" name="min_beli" required class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div><label class="text-xs text-textMuted block mb-1">Valid Hingga</label><input type="text" name="valid_hingga" required class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div><label class="text-xs text-textMuted block mb-1">Deskripsi</label><input type="text" name="deskripsi" required class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div class="md:col-span-2"><button type="submit" class="px-6 py-2.5 bg-accent10 text-bg60 font-bold rounded-xl hover:bg-[#c09161] text-sm"><i class="fas fa-save mr-2"></i>Simpan</button></div>
            </form>
        </div>

        <div class="bg-bg30 rounded-2xl border border-bg20 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="text-textMuted border-b border-bg20 bg-bg20/30">
                        <th class="text-left py-3 px-4">Kode</th>
                        <th class="text-left py-3 px-4">Judul</th>
                        <th class="text-left py-3 px-4">Potongan</th>
                        <th class="text-left py-3 px-4">Min. Beli</th>
                        <th class="text-left py-3 px-4">Valid</th>
                        <th class="text-left py-3 px-4">Aksi</th>
                    </tr></thead>
                    <tbody>
                    <?php while ($d = mysqli_fetch_assoc($diskons)): ?>
                        <tr class="table-row border-b border-bg20/50">
                            <td class="py-3 px-4 font-mono text-accent10"><?= htmlspecialchars($d['kode']) ?></td>
                            <td class="py-3 px-4 font-semibold"><?= htmlspecialchars($d['judul']) ?></td>
                            <td class="py-3 px-4 text-sukses font-semibold"><?= htmlspecialchars($d['potongan']) ?></td>
                            <td class="py-3 px-4 text-textMuted"><?= htmlspecialchars($d['min_beli']) ?></td>
                            <td class="py-3 px-4 text-textMuted"><?= htmlspecialchars($d['valid_hingga']) ?></td>
                            <td class="py-3 px-4 flex gap-2">
                                <button onclick="editDiskon(this)" data-id="<?= $d['id'] ?>" data-kode="<?= htmlspecialchars($d['kode']) ?>" data-judul="<?= htmlspecialchars($d['judul']) ?>" data-potongan="<?= htmlspecialchars($d['potongan']) ?>" data-deskripsi="<?= htmlspecialchars($d['deskripsi']) ?>" data-min_beli="<?= htmlspecialchars($d['min_beli']) ?>" data-valid_hingga="<?= htmlspecialchars($d['valid_hingga']) ?>" class="text-accent10 hover:text-yellow-300 text-xs"><i class="fas fa-edit mr-1"></i>Edit</button>
                                <a href="?tab=diskon&hapus=<?= $d['id'] ?>&tabel=diskon" onclick="return confirm('Hapus diskon ini?')" class="text-error hover:text-red-300 text-xs"><i class="fas fa-trash mr-1"></i>Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- ==================== TRANSAKSI ==================== -->
        <?php if ($tab === 'transaksi'): ?>
        <div class="mb-8">
            <h1 class="text-3xl font-bold">Riwayat <span class="text-accent10">Transaksi</span></h1>
            <p class="text-textMuted mt-1"><?= $total_transaksi ?> transaksi | Total: Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></p>
        </div>

        <div class="bg-bg30 rounded-2xl border border-bg20 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="text-textMuted border-b border-bg20 bg-bg20/30">
                        <th class="text-left py-3 px-4">Order ID</th>
                        <th class="text-left py-3 px-4">User</th>
                        <th class="text-left py-3 px-4">Total</th>
                        <th class="text-left py-3 px-4">Metode</th>
                        <th class="text-left py-3 px-4">Status</th>
                        <th class="text-left py-3 px-4">Tanggal</th>
                        <th class="text-left py-3 px-4">Aksi</th>
                    </tr></thead>
                    <tbody>
                    <?php while ($t = mysqli_fetch_assoc($transaksis)): ?>
                        <tr class="table-row border-b border-bg20/50">
                            <td class="py-3 px-4 font-mono text-accent10"><?= htmlspecialchars($t['order_id']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($t['nama_user'] ?? '-') ?></td>
                            <td class="py-3 px-4 font-semibold">Rp <?= number_format($t['total'], 0, ',', '.') ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($t['metode_pembayaran']) ?></td>
                            <td class="py-3 px-4"><span class="px-2 py-1 rounded-full text-xs bg-sukses/15 text-sukses"><?= htmlspecialchars($t['status']) ?></span></td>
                            <td class="py-3 px-4 text-textMuted"><?= $t['tanggal'] ?></td>
                            <td class="py-3 px-4 flex gap-2">
                                <button onclick="editTransaksi(this)" data-id="<?= $t['id'] ?>" data-status="<?= htmlspecialchars($t['status']) ?>" data-metode="<?= htmlspecialchars($t['metode_pembayaran']) ?>" class="text-accent10 hover:text-yellow-300 text-xs"><i class="fas fa-edit mr-1"></i>Edit</button>
                                <a href="?tab=transaksi&hapus=<?= $t['id'] ?>&tabel=transaksi" onclick="return confirm('Hapus transaksi ini?')" class="text-error hover:text-red-300 text-xs"><i class="fas fa-trash mr-1"></i>Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- ==================== USERS ==================== -->
        <?php if ($tab === 'users'): ?>
        <div class="mb-8">
            <h1 class="text-3xl font-bold">Kelola <span class="text-accent10">Users</span></h1>
            <p class="text-textMuted mt-1"><?= $total_users ?> pengguna terdaftar</p>
        </div>

        <div class="bg-bg30 rounded-2xl border border-bg20 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="text-textMuted border-b border-bg20 bg-bg20/30">
                        <th class="text-left py-3 px-4">ID</th>
                        <th class="text-left py-3 px-4">Nama</th>
                        <th class="text-left py-3 px-4">Email</th>
                        <th class="text-left py-3 px-4">Role</th>
                        <th class="text-left py-3 px-4">Poin</th>
                        <th class="text-left py-3 px-4">Pesanan</th>
                        <th class="text-left py-3 px-4">Member Sejak</th>
                        <th class="text-left py-3 px-4">Aksi</th>
                    </tr></thead>
                    <tbody>
                    <?php while ($u = mysqli_fetch_assoc($userlist)): ?>
                        <tr class="table-row border-b border-bg20/50">
                            <td class="py-3 px-4 text-textMuted"><?= $u['id'] ?></td>
                            <td class="py-3 px-4 font-semibold"><?= htmlspecialchars($u['nama']) ?></td>
                            <td class="py-3 px-4 text-textMuted"><?= htmlspecialchars($u['email']) ?></td>
                            <td class="py-3 px-4">
                                <?php if ($u['role'] === 'admin'): ?>
                                <span class="px-2 py-1 rounded-full text-xs bg-accent10/15 text-accent10">👑 Admin</span>
                                <?php elseif ($u['role'] === 'kasir'): ?>
                                <span class="px-2 py-1 rounded-full text-xs bg-blue-500/15 text-blue-400">💼 Kasir</span>
                                <?php else: ?>
                                <span class="px-2 py-1 rounded-full text-xs bg-bg20 text-textMuted">👤 User</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4"><?= $u['poin'] ?></td>
                            <td class="py-3 px-4"><?= $u['pesanan_selesai'] ?></td>
                            <td class="py-3 px-4 text-textMuted"><?= htmlspecialchars($u['member_sejak']) ?></td>
                            <td class="py-3 px-4 flex gap-2">
                                <button onclick="editUser(this)" data-id="<?= $u['id'] ?>" data-nama="<?= htmlspecialchars($u['nama']) ?>" data-email="<?= htmlspecialchars($u['email']) ?>" data-role="<?= $u['role'] ?>" data-poin="<?= $u['poin'] ?>" class="text-accent10 hover:text-yellow-300 text-xs"><i class="fas fa-edit mr-1"></i>Edit</button>
                                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                <a href="?tab=users&hapus=<?= $u['id'] ?>&tabel=users" onclick="return confirm('Hapus user ini?')" class="text-error hover:text-red-300 text-xs"><i class="fas fa-trash mr-1"></i>Hapus</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- ==================== KELOLA ULASAN ==================== -->
        <?php if ($tab === 'ulasan'): 
            $ulasans = mysqli_query($conn, "SELECT u.*, c.nama as nama_cafe FROM ulasan u JOIN cafes c ON u.cafe_id = c.id ORDER BY u.tanggal DESC");
        ?>
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold">Kelola <span class="text-accent10">Ulasan</span></h1>
                <p class="text-textMuted mt-1">Kelola data ulasan pelanggan</p>
            </div>
            <button onclick="document.getElementById('formUlasan').classList.toggle('hidden')" class="px-5 py-2.5 bg-accent10 text-bg60 font-bold rounded-xl hover:bg-[#c09161] transition-all text-sm">
                <i class="fas fa-plus mr-2"></i>Tambah Ulasan
            </button>
        </div>

        <div id="formUlasan" class="hidden bg-bg30 rounded-2xl border border-bg20 p-6 mb-6">
            <h3 class="font-bold text-lg mb-4"><i class="fas fa-comments text-accent10 mr-2"></i>Tambah Ulasan</h3>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" name="aksi_ulasan" value="tambah">
                <div><label class="text-xs text-textMuted block mb-1">Cafe</label>
                    <select name="cafe_id" required class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none">
                        <?php $cl3 = mysqli_query($conn, "SELECT id, nama FROM cafes ORDER BY nama"); while ($cc3 = mysqli_fetch_assoc($cl3)): ?>
                        <option value="<?= $cc3['id'] ?>"><?= htmlspecialchars($cc3['nama']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div><label class="text-xs text-textMuted block mb-1">Nama User</label><input type="text" name="nama_user" required class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div><label class="text-xs text-textMuted block mb-1">Rating</label><input type="number" step="0.1" max="5" name="rating" required class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div><label class="text-xs text-textMuted block mb-1">Tanggal</label><input type="date" name="tanggal" required class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div class="md:col-span-2"><label class="text-xs text-textMuted block mb-1">Komentar</label><textarea name="komentar" rows="3" required class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none resize-none"></textarea></div>
                <div class="md:col-span-2"><button type="submit" class="px-6 py-2.5 bg-accent10 text-bg60 font-bold rounded-xl hover:bg-[#c09161] text-sm"><i class="fas fa-save mr-2"></i>Simpan</button></div>
            </form>
        </div>

        <div class="bg-bg30 rounded-2xl border border-bg20 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="text-textMuted border-b border-bg20 bg-bg20/30">
                        <th class="text-left py-3 px-4">Cafe</th>
                        <th class="text-left py-3 px-4">User</th>
                        <th class="text-left py-3 px-4">Rating</th>
                        <th class="text-left py-3 px-4">Tanggal</th>
                        <th class="text-left py-3 px-4">Aksi</th>
                    </tr></thead>
                    <tbody>
                    <?php while ($ul = mysqli_fetch_assoc($ulasans)): ?>
                        <tr class="table-row border-b border-bg20/50">
                            <td class="py-3 px-4 font-semibold"><?= htmlspecialchars($ul['nama_cafe']) ?></td>
                            <td class="py-3 px-4 text-textLight"><?= htmlspecialchars($ul['nama_user']) ?></td>
                            <td class="py-3 px-4 text-accent10"><i class="fas fa-star mr-1"></i><?= $ul['rating'] ?></td>
                            <td class="py-3 px-4 text-textMuted"><?= $ul['tanggal'] ?></td>
                            <td class="py-3 px-4 flex gap-2">
                                <button onclick="editUlasan(this)" data-id="<?= $ul['id'] ?>" data-cafe_id="<?= $ul['cafe_id'] ?>" data-nama_user="<?= htmlspecialchars($ul['nama_user']) ?>" data-rating="<?= $ul['rating'] ?>" data-tanggal="<?= $ul['tanggal'] ?>" data-komentar="<?= htmlspecialchars($ul['komentar']) ?>" class="text-accent10 hover:text-yellow-300 text-xs"><i class="fas fa-edit mr-1"></i>Edit</button>
                                <a href="?tab=ulasan&hapus=<?= $ul['id'] ?>&tabel=ulasan" onclick="return confirm('Hapus ulasan ini?')" class="text-error hover:text-red-300 text-xs"><i class="fas fa-trash mr-1"></i>Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <!-- ==================== MODAL EDIT CAFE ==================== -->
    <div id="modalCafe" class="modal-bg hidden">
        <div class="modal-box">
            <div class="flex justify-between items-center mb-5">
                <h3 class="font-bold text-lg"><i class="fas fa-edit text-accent10 mr-2"></i>Edit Cafe</h3>
                <button onclick="document.getElementById('modalCafe').classList.add('hidden')" class="text-textMuted hover:text-textLight text-xl">&times;</button>
            </div>
            <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" name="aksi_cafe" value="edit">
                <input type="hidden" name="id" id="editCafe_id">
                <div><label class="text-xs text-textMuted block mb-1">Nama Cafe</label><input type="text" name="nama" id="editCafe_nama" required class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div><label class="text-xs text-textMuted block mb-1">Lokasi</label>
                    <select name="lokasi" id="editCafe_lokasi" required class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none">
                        <option value="Sidorejo">Sidorejo</option>
                        <option value="Tingkir">Tingkir</option>
                        <option value="Argomulyo">Argomulyo</option>
                        <option value="Sidomukti">Sidomukti</option>
                    </select>
                </div>
                <div><label class="text-xs text-textMuted block mb-1">Rating</label><input type="number" step="0.1" name="rating" id="editCafe_rating" max="5" class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div><label class="text-xs text-textMuted block mb-1">Total Ulasan</label><input type="number" name="total_ulasan" id="editCafe_total_ulasan" class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div><label class="text-xs text-textMuted block mb-1">Harga Min</label><input type="number" name="harga_min" id="editCafe_harga_min" class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div><label class="text-xs text-textMuted block mb-1">Estimasi Harga</label><input type="text" name="harga_estimasi" id="editCafe_harga_estimasi" class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div><label class="text-xs text-textMuted block mb-1">Ganti Gambar (.jpg/.png)</label><input type="file" name="gambar" accept=".jpg,.jpeg,.png" class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"><input type="hidden" name="gambar_old" id="editCafe_gambar_old"></div>
                <div class="md:col-span-2"><label class="text-xs text-textMuted block mb-1">Deskripsi</label><textarea name="deskripsi" id="editCafe_deskripsi" rows="3" class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none resize-none"></textarea></div>
                <div class="md:col-span-2 flex gap-3"><button type="submit" class="px-6 py-2.5 bg-accent10 text-bg60 font-bold rounded-xl hover:bg-[#c09161] text-sm"><i class="fas fa-save mr-2"></i>Simpan Perubahan</button><button type="button" onclick="document.getElementById('modalCafe').classList.add('hidden')" class="px-6 py-2.5 bg-bg20 text-textMuted rounded-xl hover:bg-bg60 text-sm">Batal</button></div>
            </form>
        </div>
    </div>


    <!-- ==================== MODAL EDIT DISKON ==================== -->
    <div id="modalDiskon" class="modal-bg hidden">
        <div class="modal-box">
            <div class="flex justify-between items-center mb-5">
                <h3 class="font-bold text-lg"><i class="fas fa-edit text-accent10 mr-2"></i>Edit Diskon</h3>
                <button onclick="document.getElementById('modalDiskon').classList.add('hidden')" class="text-textMuted hover:text-textLight text-xl">&times;</button>
            </div>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" name="aksi_diskon" value="edit">
                <input type="hidden" name="id" id="editDiskon_id">
                <div><label class="text-xs text-textMuted block mb-1">Kode</label><input type="text" name="kode" id="editDiskon_kode" required class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div><label class="text-xs text-textMuted block mb-1">Judul</label><input type="text" name="judul" id="editDiskon_judul" required class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div><label class="text-xs text-textMuted block mb-1">Potongan</label><input type="text" name="potongan" id="editDiskon_potongan" required class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div><label class="text-xs text-textMuted block mb-1">Min. Pembelian</label><input type="text" name="min_beli" id="editDiskon_min_beli" required class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div><label class="text-xs text-textMuted block mb-1">Valid Hingga</label><input type="text" name="valid_hingga" id="editDiskon_valid_hingga" required class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div><label class="text-xs text-textMuted block mb-1">Deskripsi</label><input type="text" name="deskripsi" id="editDiskon_deskripsi" required class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div class="md:col-span-2 flex gap-3"><button type="submit" class="px-6 py-2.5 bg-accent10 text-bg60 font-bold rounded-xl hover:bg-[#c09161] text-sm"><i class="fas fa-save mr-2"></i>Simpan Perubahan</button><button type="button" onclick="document.getElementById('modalDiskon').classList.add('hidden')" class="px-6 py-2.5 bg-bg20 text-textMuted rounded-xl hover:bg-bg60 text-sm">Batal</button></div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL EDIT TRANSAKSI ==================== -->
    <div id="modalTransaksi" class="modal-bg hidden">
        <div class="modal-box">
            <div class="flex justify-between items-center mb-5">
                <h3 class="font-bold text-lg"><i class="fas fa-edit text-accent10 mr-2"></i>Edit Status Transaksi</h3>
                <button onclick="document.getElementById('modalTransaksi').classList.add('hidden')" class="text-textMuted hover:text-textLight text-xl">&times;</button>
            </div>
            <form method="POST" class="grid grid-cols-1 gap-4">
                <input type="hidden" name="aksi_transaksi" value="edit">
                <input type="hidden" name="id" id="editTransaksi_id">
                <div><label class="text-xs text-textMuted block mb-1">Status</label>
                    <select name="status" id="editTransaksi_status" class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none">
                        <option value="Selesai">Selesai</option>
                        <option value="Proses">Proses</option>
                        <option value="Batal">Batal</option>
                    </select>
                </div>
                <div><label class="text-xs text-textMuted block mb-1">Metode Pembayaran</label><input type="text" name="metode_pembayaran" id="editTransaksi_metode" class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div class="flex gap-3"><button type="submit" class="px-6 py-2.5 bg-accent10 text-bg60 font-bold rounded-xl hover:bg-[#c09161] text-sm"><i class="fas fa-save mr-2"></i>Simpan Perubahan</button><button type="button" onclick="document.getElementById('modalTransaksi').classList.add('hidden')" class="px-6 py-2.5 bg-bg20 text-textMuted rounded-xl hover:bg-bg60 text-sm">Batal</button></div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL EDIT USER ==================== -->
    <div id="modalUser" class="modal-bg hidden">
        <div class="modal-box">
            <div class="flex justify-between items-center mb-5">
                <h3 class="font-bold text-lg"><i class="fas fa-edit text-accent10 mr-2"></i>Edit User</h3>
                <button onclick="document.getElementById('modalUser').classList.add('hidden')" class="text-textMuted hover:text-textLight text-xl">&times;</button>
            </div>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" name="aksi_user" value="edit">
                <input type="hidden" name="id" id="editUser_id">
                <div><label class="text-xs text-textMuted block mb-1">Nama</label><input type="text" name="nama" id="editUser_nama" required class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div><label class="text-xs text-textMuted block mb-1">Email</label><input type="email" name="email" id="editUser_email" required class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div><label class="text-xs text-textMuted block mb-1">Role</label>
                    <select name="role" id="editUser_role" class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div><label class="text-xs text-textMuted block mb-1">Poin</label><input type="number" name="poin" id="editUser_poin" class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div class="md:col-span-2 flex gap-3"><button type="submit" class="px-6 py-2.5 bg-accent10 text-bg60 font-bold rounded-xl hover:bg-[#c09161] text-sm"><i class="fas fa-save mr-2"></i>Simpan Perubahan</button><button type="button" onclick="document.getElementById('modalUser').classList.add('hidden')" class="px-6 py-2.5 bg-bg20 text-textMuted rounded-xl hover:bg-bg60 text-sm">Batal</button></div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL EDIT ULASAN ==================== -->
    <div id="modalUlasan" class="modal-bg hidden">
        <div class="modal-box">
            <div class="flex justify-between items-center mb-5">
                <h3 class="font-bold text-lg"><i class="fas fa-edit text-accent10 mr-2"></i>Edit Ulasan</h3>
                <button onclick="document.getElementById('modalUlasan').classList.add('hidden')" class="text-textMuted hover:text-textLight text-xl">&times;</button>
            </div>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" name="aksi_ulasan" value="edit">
                <input type="hidden" name="id" id="editUlasan_id">
                <div><label class="text-xs text-textMuted block mb-1">Cafe</label>
                    <select name="cafe_id" id="editUlasan_cafe_id" required class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none">
                        <?php $cl4 = mysqli_query($conn, "SELECT id, nama FROM cafes ORDER BY nama"); while ($cc4 = mysqli_fetch_assoc($cl4)): ?>
                        <option value="<?= $cc4['id'] ?>"><?= htmlspecialchars($cc4['nama']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div><label class="text-xs text-textMuted block mb-1">Nama User</label><input type="text" name="nama_user" id="editUlasan_nama_user" required class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div><label class="text-xs text-textMuted block mb-1">Rating</label><input type="number" step="0.1" max="5" name="rating" id="editUlasan_rating" required class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div><label class="text-xs text-textMuted block mb-1">Tanggal</label><input type="date" name="tanggal" id="editUlasan_tanggal" required class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none"></div>
                <div class="md:col-span-2"><label class="text-xs text-textMuted block mb-1">Komentar</label><textarea name="komentar" id="editUlasan_komentar" rows="3" required class="w-full bg-bg60 border border-bg20 rounded-lg px-4 py-2.5 text-sm focus:border-accent10 outline-none resize-none"></textarea></div>
                <div class="md:col-span-2 flex gap-3"><button type="submit" class="px-6 py-2.5 bg-accent10 text-bg60 font-bold rounded-xl hover:bg-[#c09161] text-sm"><i class="fas fa-save mr-2"></i>Simpan Perubahan</button><button type="button" onclick="document.getElementById('modalUlasan').classList.add('hidden')" class="px-6 py-2.5 bg-bg20 text-textMuted rounded-xl hover:bg-bg60 text-sm">Batal</button></div>
            </form>
        </div>
    </div>

    <script>
    function editCafe(btn) {
        const fields = ['id','nama','lokasi','rating','total_ulasan','harga_min','harga_estimasi','deskripsi'];
        fields.forEach(f => document.getElementById('editCafe_' + f).value = btn.dataset[f] || '');
        if(document.getElementById('editCafe_gambar_old')) document.getElementById('editCafe_gambar_old').value = btn.dataset.gambar || '';
        document.getElementById('modalCafe').classList.remove('hidden');
    }

    function editUlasan(btn) {
        const fields = ['id','cafe_id','nama_user','rating','tanggal','komentar'];
        fields.forEach(f => document.getElementById('editUlasan_' + f).value = btn.dataset[f] || '');
        document.getElementById('modalUlasan').classList.remove('hidden');
    }

    function editDiskon(btn) {
        const fields = ['id','kode','judul','potongan','deskripsi','min_beli','valid_hingga'];
        fields.forEach(f => document.getElementById('editDiskon_' + f).value = btn.dataset[f] || '');
        document.getElementById('modalDiskon').classList.remove('hidden');
    }
    function editTransaksi(btn) {
        document.getElementById('editTransaksi_id').value = btn.dataset.id;
        document.getElementById('editTransaksi_status').value = btn.dataset.status;
        document.getElementById('editTransaksi_metode').value = btn.dataset.metode;
        document.getElementById('modalTransaksi').classList.remove('hidden');
    }
    function editUser(btn) {
        const fields = ['id','nama','email','role','poin'];
        fields.forEach(f => document.getElementById('editUser_' + f).value = btn.dataset[f] || '');
        document.getElementById('modalUser').classList.remove('hidden');
    }
    // Close modal on backdrop click
    document.querySelectorAll('.modal-bg').forEach(m => {
        m.addEventListener('click', e => { if (e.target === m) m.classList.add('hidden'); });
    });
    </script>
    <script>
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
