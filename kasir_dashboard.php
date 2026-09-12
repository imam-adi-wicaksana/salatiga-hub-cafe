<?php
include 'koneksi.php';
session_start();

// Cek apakah user sudah login dan role kasir
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'kasir') {
    header("Location: login.php");
    exit;
}

$cafe_id = intval($_SESSION['user_cafe_id']);
$cafe_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM cafes WHERE id = $cafe_id"));
if (!$cafe_data) { header("Location: login.php"); exit; }

$pesan = '';
$tipe_pesan = '';

// === HAPUS DATA (hanya milik cafe sendiri) ===
if (isset($_GET['hapus']) && isset($_GET['tabel'])) {
    $id = intval($_GET['hapus']);
    $tabel = $_GET['tabel'];
    if ($tabel === 'menu') {
        mysqli_query($conn, "DELETE FROM menu WHERE id = $id AND cafe_id = $cafe_id");
        $pesan = "Menu berhasil dihapus!"; $tipe_pesan = "sukses";
    } elseif ($tabel === 'reservasi') {
        mysqli_query($conn, "DELETE FROM reservasi WHERE id = $id AND cafe_id = $cafe_id");
        $pesan = "Reservasi berhasil dihapus!"; $tipe_pesan = "sukses";
    }
}

// === TAMBAH / EDIT MENU ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi_menu'])) {
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $harga = intval($_POST['harga']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    $img = isset($_POST['img_old']) ? mysqli_real_escape_string($conn, $_POST['img_old']) : '';
    if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
        $file_name = time() . '_' . basename($_FILES['img']['name']);
        $target_dir = 'uploads/menu/';
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        $target_file = $target_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (in_array($file_type, ['jpg', 'jpeg', 'png'])) {
            if (move_uploaded_file($_FILES['img']['tmp_name'], $target_file)) {
                $img = $target_file;
            }
        }
    } elseif (isset($_POST['img']) && !empty($_POST['img'])) {
        $img = mysqli_real_escape_string($conn, $_POST['img']);
    }

    if ($_POST['aksi_menu'] === 'tambah') {
        mysqli_query($conn, "INSERT INTO menu (cafe_id, kategori, nama, harga, deskripsi, img) VALUES ($cafe_id,'$kategori','$nama',$harga,'$deskripsi','$img')");
        $pesan = "Menu berhasil ditambahkan!";
    } elseif ($_POST['aksi_menu'] === 'edit') {
        $id = intval($_POST['id']);
        mysqli_query($conn, "UPDATE menu SET kategori='$kategori', nama='$nama', harga=$harga, deskripsi='$deskripsi', img='$img' WHERE id=$id AND cafe_id=$cafe_id");
        $pesan = "Menu berhasil diupdate!";
    }
    $tipe_pesan = "sukses";
}

// === UPDATE FASILITAS CAFE ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi_cafe'])) {
    if ($_POST['aksi_cafe'] === 'edit_fitur') {
        $fitur_array = isset($_POST['fitur']) ? $_POST['fitur'] : [];
        $fitur_string = mysqli_real_escape_string($conn, implode(',', $fitur_array));
        mysqli_query($conn, "UPDATE cafes SET fitur='$fitur_string' WHERE id=$cafe_id");
        $pesan = "Fasilitas cafe berhasil diupdate!";
        $tipe_pesan = "sukses";
        $cafe_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM cafes WHERE id = $cafe_id"));
    }
}

// === TAMBAH / EDIT RESERVASI ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi_reservasi'])) {
    $nama_pemesan = mysqli_real_escape_string($conn, $_POST['nama_pemesan']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jam = mysqli_real_escape_string($conn, $_POST['jam']);
    $jumlah_orang = intval($_POST['jumlah_orang']);
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);
    $status = mysqli_real_escape_string($conn, $_POST['status'] ?? 'Menunggu');

    if ($_POST['aksi_reservasi'] === 'tambah') {
        mysqli_query($conn, "INSERT INTO reservasi (cafe_id, nama_pemesan, no_hp, tanggal, jam, jumlah_orang, catatan, status) VALUES ($cafe_id,'$nama_pemesan','$no_hp','$tanggal','$jam',$jumlah_orang,'$catatan','$status')");
        $pesan = "Reservasi berhasil ditambahkan!";
    } elseif ($_POST['aksi_reservasi'] === 'edit') {
        $id = intval($_POST['id']);
        mysqli_query($conn, "UPDATE reservasi SET nama_pemesan='$nama_pemesan', no_hp='$no_hp', tanggal='$tanggal', jam='$jam', jumlah_orang=$jumlah_orang, catatan='$catatan', status='$status' WHERE id=$id AND cafe_id=$cafe_id");
        $pesan = "Reservasi berhasil diupdate!";
    }
    $tipe_pesan = "sukses";
}

// [KODE BARU] Statistik & Pemanggilan Transaksi menggunakan relasi cafe_id
$total_transaksi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE cafe_id = $cafe_id"))['total'];
$total_pendapatan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total),0) as total FROM transaksi WHERE cafe_id = $cafe_id AND status = 'Selesai'"))['total'];

// Data tabel
$menus = mysqli_query($conn, "SELECT * FROM menu WHERE cafe_id = $cafe_id ORDER BY id DESC");
$reservasis = mysqli_query($conn, "SELECT * FROM reservasi WHERE cafe_id = $cafe_id ORDER BY tanggal DESC, jam DESC");
$transaksis = mysqli_query($conn, "SELECT transaksi.*, users.nama as nama_user FROM transaksi LEFT JOIN users ON transaksi.user_id = users.id WHERE transaksi.cafe_id = $cafe_id ORDER BY transaksi.tanggal DESC");

// Statistik cafe sendiri
$total_menu = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM menu WHERE cafe_id = $cafe_id"))['total'];
$total_reservasi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM reservasi WHERE cafe_id = $cafe_id"))['total'];
$reservasi_hari_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM reservasi WHERE cafe_id = $cafe_id AND tanggal = CURDATE()"))['total'];
$total_transaksi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi t INNER JOIN (SELECT DISTINCT user_id FROM transaksi WHERE detail_items LIKE '%{$cafe_data['nama']}%') sub ON t.user_id = sub.user_id WHERE t.detail_items LIKE '%{$cafe_data['nama']}%'"))['total'];
$total_pendapatan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total),0) as total FROM transaksi WHERE detail_items LIKE '%{$cafe_data['nama']}%'"))['total'];

// Data tabel
$menus = mysqli_query($conn, "SELECT * FROM menu WHERE cafe_id = $cafe_id ORDER BY id DESC");
$reservasis = mysqli_query($conn, "SELECT * FROM reservasi WHERE cafe_id = $cafe_id ORDER BY tanggal DESC, jam DESC");
$transaksis = mysqli_query($conn, "SELECT transaksi.*, users.nama as nama_user FROM transaksi LEFT JOIN users ON transaksi.user_id = users.id WHERE transaksi.detail_items LIKE '%{$cafe_data['nama']}%' ORDER BY transaksi.id DESC");

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'overview';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir Dashboard | <?= htmlspecialchars($cafe_data['nama']) ?></title>
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
        .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 30px rgba(212,163,115,0.15); }
        .table-row:hover { background: rgba(212,163,115,0.05); }
        .modal-bg { position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px); z-index: 100; display: flex; align-items: center; justify-content: center; }
        .modal-box { background: #2C1E16; border: 1px solid #3D2B1F; border-radius: 1rem; padding: 2rem; width: 95%; max-width: 600px; max-height: 90vh; overflow-y: auto; }
        .input-field { width: 100%; background: #140F0A; border: 1px solid #3D2B1F; border-radius: 0.5rem; padding: 0.625rem 1rem; font-size: 0.875rem; outline: none; color: #FDF8F5; }
        .input-field:focus { border-color: #D4A373; }
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
            <a href="?tab=overview" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all <?= $tab === 'overview' ? 'tab-active' : 'text-textMuted hover:text-textLight hover:bg-bg20' ?>">
                <i class="fas fa-chart-pie w-5"></i> Overview
            </a>
            <a href="?tab=menu" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all <?= $tab === 'menu' ? 'tab-active' : 'text-textMuted hover:text-textLight hover:bg-bg20' ?>">
                <i class="fas fa-utensils w-5"></i> Kelola Menu
            </a>
            <a href="?tab=reservasi" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all <?= $tab === 'reservasi' ? 'tab-active' : 'text-textMuted hover:text-textLight hover:bg-bg20' ?>">
                <i class="fas fa-calendar-check w-5"></i> Reservasi
            </a>
            <a href="kasir_dashboard.php?tab=transaksi" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all <?= $tab === 'transaksi' ? 'tab-active' : 'text-textMuted hover:text-textLight hover:bg-bg20' ?>">
                <i class="fas fa-receipt w-5"></i> Transaksi
            </a>
            <a href="?tab=cafe" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all <?= $tab === 'cafe' ? 'tab-active' : 'text-textMuted hover:text-textLight hover:bg-bg20' ?>">
                <i class="fas fa-store w-5"></i> Profil Cafe
            </a>
            <a href="pengaturan_kasir.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all text-textMuted hover:text-textLight hover:bg-bg20">
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

        <!-- ==================== PROFIL & FASILITAS CAFE ==================== -->
        <?php if ($tab === 'cafe'): ?>
        <div class="mb-8">
            <h1 class="text-3xl font-bold">Profil & <span class="text-accent10">Fasilitas Cafe</span></h1>
            <p class="text-textMuted mt-1">Kelola data profil dan fasilitas yang tersedia di <?= htmlspecialchars($cafe_data['nama']) ?></p>
        </div>
        
        <div class="bg-bg30 rounded-2xl border border-bg20 p-6 mb-6">
            <h3 class="font-bold text-lg mb-4"><i class="fas fa-check-circle text-accent10 mr-2"></i>Fasilitas Pendukung (Filter Direktori)</h3>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" name="aksi_cafe" value="edit_fitur">
                <?php 
                $current_features = array_map('trim', explode(',', $cafe_data['fitur'] ?? ''));
                $available_features = [
                    'Wi-Fi Cepat', 'Colokan Banyak', 'Area Smoking', 'Ruang AC', 'Bisa QRIS', 
                    'Parkir Luas', 'Live Music', 'Mushola', 'Meeting Room', 'Board Games',
                    'Pet Friendly', 'Akses Kursi Roda', 'Toilet Bersih', 'Highchair Bayi'
                ];
                foreach($available_features as $f):
                    $checked = in_array($f, $current_features) ? 'checked' : '';
                ?>
                <label class="flex items-center gap-3 text-sm text-textLight cursor-pointer bg-bg60 p-3 rounded-xl border border-bg20 hover:border-accent10 transition-colors">
                    <input type="checkbox" name="fitur[]" value="<?= $f ?>" <?= $checked ?> class="accent-accent10 w-4 h-4">
                    <?= $f ?>
                </label>
                <?php endforeach; ?>
                <div class="md:col-span-2 mt-4 pt-4 border-t border-bg20">
                    <button type="submit" class="px-6 py-2.5 bg-accent10 text-bg60 font-bold rounded-xl hover:bg-[#c09161] text-sm"><i class="fas fa-save mr-2"></i>Simpan Fasilitas</button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <!-- ==================== OVERVIEW ==================== -->
        <?php if ($tab === 'overview'): ?>
        <div class="mb-8">
            <h1 class="text-3xl font-bold"><?= htmlspecialchars($cafe_data['nama']) ?></h1>
            <p class="text-textMuted mt-1">Selamat datang, <?= htmlspecialchars($_SESSION['user_nama']) ?>! <span class="text-info text-xs px-2 py-0.5 bg-info/10 rounded-full ml-1">Kasir</span></p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stat-card bg-bg30 rounded-2xl p-6 border border-bg20">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-accent10/15 flex items-center justify-center"><i class="fas fa-utensils text-accent10 text-xl"></i></div>
                </div>
                <p class="text-3xl font-bold"><?= $total_menu ?></p>
                <p class="text-textMuted text-sm mt-1">Total Menu</p>
            </div>
            <div class="stat-card bg-bg30 rounded-2xl p-6 border border-bg20">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-info/15 flex items-center justify-center"><i class="fas fa-calendar-check text-info text-xl"></i></div>
                </div>
                <p class="text-3xl font-bold"><?= $total_reservasi ?></p>
                <p class="text-textMuted text-sm mt-1">Total Reservasi</p>
            </div>
            <div class="stat-card bg-bg30 rounded-2xl p-6 border border-bg20">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-warning/15 flex items-center justify-center"><i class="fas fa-calendar-day text-warning text-xl"></i></div>
                </div>
                <p class="text-3xl font-bold"><?= $reservasi_hari_ini ?></p>
                <p class="text-textMuted text-sm mt-1">Reservasi Hari Ini</p>
            </div>
            <div class="stat-card bg-bg30 rounded-2xl p-6 border border-bg20">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-sukses/15 flex items-center justify-center"><i class="fas fa-money-bill-wave text-sukses text-xl"></i></div>
                </div>
                <p class="text-3xl font-bold">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></p>
                <p class="text-textMuted text-sm mt-1">Pendapatan</p>
            </div>
        </div>

        <!-- Reservasi Hari Ini -->
        <div class="bg-bg30 rounded-2xl border border-bg20 p-6 mb-6">
            <h2 class="text-lg font-bold mb-4"><i class="fas fa-calendar-day text-warning mr-2"></i>Reservasi Hari Ini</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="text-textMuted border-b border-bg20">
                        <th class="text-left py-3 px-4">Nama</th>
                        <th class="text-left py-3 px-4">No. HP</th>
                        <th class="text-left py-3 px-4">Jam</th>
                        <th class="text-left py-3 px-4">Orang</th>
                        <th class="text-left py-3 px-4">Status</th>
                    </tr></thead>
                    <tbody>
                    <?php
                    $today = mysqli_query($conn, "SELECT * FROM reservasi WHERE cafe_id = $cafe_id AND tanggal = CURDATE() ORDER BY jam");
                    while ($r = mysqli_fetch_assoc($today)): ?>
                        <tr class="table-row border-b border-bg20/50">
                            <td class="py-3 px-4 font-semibold"><?= htmlspecialchars($r['nama_pemesan']) ?></td>
                            <td class="py-3 px-4 text-textMuted"><?= htmlspecialchars($r['no_hp']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($r['jam']) ?></td>
                            <td class="py-3 px-4"><?= $r['jumlah_orang'] ?> org</td>
                            <td class="py-3 px-4">
                                <?php
                                $sc = $r['status'];
                                $cls = $sc === 'Dikonfirmasi' ? 'bg-sukses/15 text-sukses' : ($sc === 'Batal' ? 'bg-error/15 text-error' : 'bg-warning/15 text-warning');
                                ?>
                                <span class="px-2 py-1 rounded-full text-xs <?= $cls ?>"><?= $sc ?></span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if ($reservasi_hari_ini == 0): ?>
                        <tr><td colspan="5" class="py-8 text-center text-textMuted">Belum ada reservasi hari ini</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- ==================== KELOLA MENU ==================== -->
        <?php if ($tab === 'menu'): ?>
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold">Menu <span class="text-accent10"><?= htmlspecialchars($cafe_data['nama']) ?></span></h1>
                <p class="text-textMuted mt-1"><?= $total_menu ?> item menu</p>
            </div>
            <button onclick="document.getElementById('formMenu').classList.toggle('hidden')" class="px-5 py-2.5 bg-accent10 text-bg60 font-bold rounded-xl hover:bg-[#c09161] transition-all text-sm">
                <i class="fas fa-plus mr-2"></i>Tambah Menu
            </button>
        </div>

        <div id="formMenu" class="hidden bg-bg30 rounded-2xl border border-bg20 p-6 mb-6">
            <h3 class="font-bold text-lg mb-4"><i class="fas fa-utensils text-accent10 mr-2"></i>Tambah Menu Baru</h3>
            <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" name="aksi_menu" value="tambah">
                <div><label class="text-xs text-textMuted block mb-1">Kategori</label><input type="text" name="kategori" required placeholder="Kopi, Non-Kopi, Makanan..." class="input-field"></div>
                <div><label class="text-xs text-textMuted block mb-1">Nama Menu</label><input type="text" name="nama" required class="input-field"></div>
                <div><label class="text-xs text-textMuted block mb-1">Harga (Rp)</label><input type="number" name="harga" required class="input-field"></div>
                <div><label class="text-xs text-textMuted block mb-1">Upload Gambar (.jpg/.png)</label><input type="file" name="img" accept=".jpg,.jpeg,.png" class="input-field"></div>
                <div class="md:col-span-2"><label class="text-xs text-textMuted block mb-1">Deskripsi</label><textarea name="deskripsi" rows="2" class="input-field resize-none"></textarea></div>
                <div class="md:col-span-2"><button type="submit" class="px-6 py-2.5 bg-accent10 text-bg60 font-bold rounded-xl hover:bg-[#c09161] text-sm"><i class="fas fa-save mr-2"></i>Simpan</button></div>
            </form>
        </div>

        <div class="bg-bg30 rounded-2xl border border-bg20 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="text-textMuted border-b border-bg20 bg-bg20/30">
                        <th class="text-left py-3 px-4">ID</th>
                        <th class="text-left py-3 px-4">Nama</th>
                        <th class="text-left py-3 px-4">Kategori</th>
                        <th class="text-left py-3 px-4">Harga</th>
                        <th class="text-left py-3 px-4">Aksi</th>
                    </tr></thead>
                    <tbody>
                    <?php while ($m = mysqli_fetch_assoc($menus)): ?>
                        <tr class="table-row border-b border-bg20/50">
                            <td class="py-3 px-4 text-textMuted"><?= $m['id'] ?></td>
                            <td class="py-3 px-4 font-semibold"><?= htmlspecialchars($m['nama']) ?></td>
                            <td class="py-3 px-4"><span class="px-2 py-1 rounded-full text-xs bg-accent10/15 text-accent10"><?= htmlspecialchars($m['kategori']) ?></span></td>
                            <td class="py-3 px-4 font-semibold">Rp <?= number_format($m['harga'], 0, ',', '.') ?></td>
                            <td class="py-3 px-4 flex gap-2">
                                <button onclick="editMenu(this)" data-id="<?= $m['id'] ?>" data-kategori="<?= htmlspecialchars($m['kategori']) ?>" data-nama="<?= htmlspecialchars($m['nama']) ?>" data-harga="<?= $m['harga'] ?>" data-deskripsi="<?= htmlspecialchars($m['deskripsi']) ?>" data-img="<?= htmlspecialchars($m['img']) ?>" class="text-accent10 hover:text-yellow-300 text-xs"><i class="fas fa-edit mr-1"></i>Edit</button>
                                <a href="?tab=menu&hapus=<?= $m['id'] ?>&tabel=menu" onclick="return confirm('Hapus menu ini?')" class="text-error hover:text-red-300 text-xs"><i class="fas fa-trash mr-1"></i>Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- ==================== RESERVASI ==================== -->
        <?php if ($tab === 'reservasi'): ?>
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold">Reservasi <span class="text-accent10"><?= htmlspecialchars($cafe_data['nama']) ?></span></h1>
                <p class="text-textMuted mt-1"><?= $total_reservasi ?> reservasi total</p>
            </div>
            <button onclick="document.getElementById('formReservasi').classList.toggle('hidden')" class="px-5 py-2.5 bg-accent10 text-bg60 font-bold rounded-xl hover:bg-[#c09161] transition-all text-sm">
                <i class="fas fa-plus mr-2"></i>Tambah Reservasi
            </button>
        </div>

        <div id="formReservasi" class="hidden bg-bg30 rounded-2xl border border-bg20 p-6 mb-6">
            <h3 class="font-bold text-lg mb-4"><i class="fas fa-calendar-check text-accent10 mr-2"></i>Tambah Reservasi</h3>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" name="aksi_reservasi" value="tambah">
                <div><label class="text-xs text-textMuted block mb-1">Nama Pemesan</label><input type="text" name="nama_pemesan" required class="input-field"></div>
                <div><label class="text-xs text-textMuted block mb-1">No. HP</label><input type="text" name="no_hp" required class="input-field"></div>
                <div><label class="text-xs text-textMuted block mb-1">Tanggal</label><input type="date" name="tanggal" required class="input-field"></div>
                <div><label class="text-xs text-textMuted block mb-1">Jam</label><input type="time" name="jam" required class="input-field"></div>
                <div><label class="text-xs text-textMuted block mb-1">Jumlah Orang</label><input type="number" name="jumlah_orang" min="1" value="1" required class="input-field"></div>
                <div><label class="text-xs text-textMuted block mb-1">Status</label>
                    <select name="status" class="input-field">
                        <option value="Menunggu">Menunggu</option>
                        <option value="Dikonfirmasi">Dikonfirmasi</option>
                    </select>
                </div>
                <div class="md:col-span-2"><label class="text-xs text-textMuted block mb-1">Catatan</label><textarea name="catatan" rows="2" class="input-field resize-none" placeholder="Catatan khusus..."></textarea></div>
                <div class="md:col-span-2"><button type="submit" class="px-6 py-2.5 bg-accent10 text-bg60 font-bold rounded-xl hover:bg-[#c09161] text-sm"><i class="fas fa-save mr-2"></i>Simpan</button></div>
            </form>
        </div>

        <div class="bg-bg30 rounded-2xl border border-bg20 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="text-textMuted border-b border-bg20 bg-bg20/30">
                        <th class="text-left py-3 px-4">Nama</th>
                        <th class="text-left py-3 px-4">No. HP</th>
                        <th class="text-left py-3 px-4">Tanggal</th>
                        <th class="text-left py-3 px-4">Jam</th>
                        <th class="text-left py-3 px-4">Orang</th>
                        <th class="text-left py-3 px-4">Status</th>
                        <th class="text-left py-3 px-4">Catatan</th>
                        <th class="text-left py-3 px-4">Aksi</th>
                    </tr></thead>
                    <tbody>
                    <?php while ($r = mysqli_fetch_assoc($reservasis)): ?>
                        <tr class="table-row border-b border-bg20/50">
                            <td class="py-3 px-4 font-semibold"><?= htmlspecialchars($r['nama_pemesan']) ?></td>
                            <td class="py-3 px-4 text-textMuted"><?= htmlspecialchars($r['no_hp']) ?></td>
                            <td class="py-3 px-4"><?= $r['tanggal'] ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($r['jam']) ?></td>
                            <td class="py-3 px-4"><?= $r['jumlah_orang'] ?></td>
                            <td class="py-3 px-4">
                                <?php
                                $sc = $r['status'];
                                $cls = $sc === 'Dikonfirmasi' ? 'bg-sukses/15 text-sukses' : ($sc === 'Batal' ? 'bg-error/15 text-error' : 'bg-warning/15 text-warning');
                                ?>
                                <span class="px-2 py-1 rounded-full text-xs <?= $cls ?>"><?= $sc ?></span>
                            </td>
                            <td class="py-3 px-4 text-textMuted text-xs max-w-[150px] truncate"><?= htmlspecialchars($r['catatan'] ?? '-') ?></td>
                            <td class="py-3 px-4 flex gap-2">
                                <button onclick="editReservasi(this)" data-id="<?= $r['id'] ?>" data-nama_pemesan="<?= htmlspecialchars($r['nama_pemesan']) ?>" data-no_hp="<?= htmlspecialchars($r['no_hp']) ?>" data-tanggal="<?= $r['tanggal'] ?>" data-jam="<?= htmlspecialchars($r['jam']) ?>" data-jumlah_orang="<?= $r['jumlah_orang'] ?>" data-catatan="<?= htmlspecialchars($r['catatan']) ?>" data-status="<?= htmlspecialchars($r['status']) ?>" class="text-accent10 hover:text-yellow-300 text-xs"><i class="fas fa-edit mr-1"></i>Edit</button>
                                <a href="?tab=reservasi&hapus=<?= $r['id'] ?>&tabel=reservasi" onclick="return confirm('Hapus reservasi ini?')" class="text-error hover:text-red-300 text-xs"><i class="fas fa-trash mr-1"></i>Hapus</a>
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
            <h1 class="text-3xl font-bold">Transaksi <span class="text-accent10"><?= htmlspecialchars($cafe_data['nama']) ?></span></h1>
            <p class="text-textMuted mt-1">Pendapatan: Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></p>
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
                            <td class="py-3 px-4">
                                <?php
                                $sc = $t['status'];
                                $cls = $sc === 'Selesai' ? 'bg-sukses/15 text-sukses' : ($sc === 'Batal' ? 'bg-error/15 text-error' : 'bg-warning/15 text-warning');
                                ?>
                                <span class="px-2 py-1 rounded-full text-xs <?= $cls ?>"><?= $sc ?></span>
                            </td>
                            <td class="py-3 px-4 text-textMuted"><?= $t['tanggal'] ?></td>
                            <td class="py-3 px-4">
                                <button onclick="editTransaksi(this)" data-id="<?= $t['id'] ?>" data-status="<?= htmlspecialchars($t['status']) ?>" class="text-accent10 hover:text-yellow-300 text-xs"><i class="fas fa-edit mr-1"></i>Edit</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

    </main>

    <!-- ==================== MODAL EDIT MENU ==================== -->
    <div id="modalMenu" class="modal-bg hidden">
        <div class="modal-box">
            <div class="flex justify-between items-center mb-5">
                <h3 class="font-bold text-lg"><i class="fas fa-edit text-accent10 mr-2"></i>Edit Menu</h3>
                <button onclick="document.getElementById('modalMenu').classList.add('hidden')" class="text-textMuted hover:text-textLight text-xl">&times;</button>
            </div>
            <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" name="aksi_menu" value="edit">
                <input type="hidden" name="id" id="editMenu_id">
                <div><label class="text-xs text-textMuted block mb-1">Kategori</label><input type="text" name="kategori" id="editMenu_kategori" required class="input-field"></div>
                <div><label class="text-xs text-textMuted block mb-1">Nama Menu</label><input type="text" name="nama" id="editMenu_nama" required class="input-field"></div>
                <div><label class="text-xs text-textMuted block mb-1">Harga (Rp)</label><input type="number" name="harga" id="editMenu_harga" required class="input-field"></div>
                <div><label class="text-xs text-textMuted block mb-1">Ganti Gambar (.jpg/.png)</label><input type="file" name="img" accept=".jpg,.jpeg,.png" class="input-field"><input type="hidden" name="img_old" id="editMenu_img_old"></div>
                <div class="md:col-span-2"><label class="text-xs text-textMuted block mb-1">Deskripsi</label><textarea name="deskripsi" id="editMenu_deskripsi" rows="2" class="input-field resize-none"></textarea></div>
                <div class="md:col-span-2 flex gap-3">
                    <button type="submit" class="px-6 py-2.5 bg-accent10 text-bg60 font-bold rounded-xl hover:bg-[#c09161] text-sm"><i class="fas fa-save mr-2"></i>Simpan</button>
                    <button type="button" onclick="document.getElementById('modalMenu').classList.add('hidden')" class="px-6 py-2.5 bg-bg20 text-textMuted rounded-xl hover:bg-bg60 text-sm">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL EDIT RESERVASI ==================== -->
    <div id="modalReservasi" class="modal-bg hidden">
        <div class="modal-box">
            <div class="flex justify-between items-center mb-5">
                <h3 class="font-bold text-lg"><i class="fas fa-edit text-accent10 mr-2"></i>Edit Reservasi</h3>
                <button onclick="document.getElementById('modalReservasi').classList.add('hidden')" class="text-textMuted hover:text-textLight text-xl">&times;</button>
            </div>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" name="aksi_reservasi" value="edit">
                <input type="hidden" name="id" id="editReservasi_id">
                <div><label class="text-xs text-textMuted block mb-1">Nama Pemesan</label><input type="text" name="nama_pemesan" id="editReservasi_nama_pemesan" required class="input-field"></div>
                <div><label class="text-xs text-textMuted block mb-1">No. HP</label><input type="text" name="no_hp" id="editReservasi_no_hp" required class="input-field"></div>
                <div><label class="text-xs text-textMuted block mb-1">Tanggal</label><input type="date" name="tanggal" id="editReservasi_tanggal" required class="input-field"></div>
                <div><label class="text-xs text-textMuted block mb-1">Jam</label><input type="time" name="jam" id="editReservasi_jam" required class="input-field"></div>
                <div><label class="text-xs text-textMuted block mb-1">Jumlah Orang</label><input type="number" name="jumlah_orang" id="editReservasi_jumlah_orang" min="1" required class="input-field"></div>
                <div><label class="text-xs text-textMuted block mb-1">Status</label>
                    <select name="status" id="editReservasi_status" class="input-field">
                        <option value="Menunggu">Menunggu</option>
                        <option value="Dikonfirmasi">Dikonfirmasi</option>
                        <option value="Batal">Batal</option>
                    </select>
                </div>
                <div class="md:col-span-2"><label class="text-xs text-textMuted block mb-1">Catatan</label><textarea name="catatan" id="editReservasi_catatan" rows="2" class="input-field resize-none"></textarea></div>
                <div class="md:col-span-2 flex gap-3">
                    <button type="submit" class="px-6 py-2.5 bg-accent10 text-bg60 font-bold rounded-xl hover:bg-[#c09161] text-sm"><i class="fas fa-save mr-2"></i>Simpan</button>
                    <button type="button" onclick="document.getElementById('modalReservasi').classList.add('hidden')" class="px-6 py-2.5 bg-bg20 text-textMuted rounded-xl hover:bg-bg60 text-sm">Batal</button>
                </div>
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
                    <select name="status" id="editTransaksi_status" class="input-field">
                        <option value="Selesai">Selesai</option>
                        <option value="Proses">Proses</option>
                        <option value="Batal">Batal</option>
                    </select>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="px-6 py-2.5 bg-accent10 text-bg60 font-bold rounded-xl hover:bg-[#c09161] text-sm"><i class="fas fa-save mr-2"></i>Simpan</button>
                    <button type="button" onclick="document.getElementById('modalTransaksi').classList.add('hidden')" class="px-6 py-2.5 bg-bg20 text-textMuted rounded-xl hover:bg-bg60 text-sm">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function editMenu(btn) {
        ['id','kategori','nama','harga','deskripsi'].forEach(f => document.getElementById('editMenu_' + f).value = btn.dataset[f] || '');
        if(document.getElementById('editMenu_img_old')) document.getElementById('editMenu_img_old').value = btn.dataset.img || '';
        document.getElementById('modalMenu').classList.remove('hidden');
    }
    function editReservasi(btn) {
        ['id','nama_pemesan','no_hp','tanggal','jam','jumlah_orang','catatan','status'].forEach(f => document.getElementById('editReservasi_' + f).value = btn.dataset[f] || '');
        document.getElementById('modalReservasi').classList.remove('hidden');
    }
    function editTransaksi(btn) {
        document.getElementById('editTransaksi_id').value = btn.dataset.id;
        document.getElementById('editTransaksi_status').value = btn.dataset.status;
        document.getElementById('modalTransaksi').classList.remove('hidden');
    }
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
