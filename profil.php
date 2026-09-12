<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Kalau belum login, lempar ke halaman login
    exit;
}

$id = $_SESSION['user_id'];

// --- LOGIKA UPDATE PROFIL ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profil'])) {
    $nama_baru = mysqli_real_escape_string($conn, $_POST['nama']);
    $email_baru = mysqli_real_escape_string($conn, $_POST['email']);
    $alamat_baru = mysqli_real_escape_string($conn, $_POST['alamat']);

    $update_query = "UPDATE users SET nama='$nama_baru', email='$email_baru', alamat='$alamat_baru' WHERE id='$id'";
    
    if(mysqli_query($conn, $update_query)) {
        echo "<script>alert('Profil berhasil diperbarui!');</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat memperbarui profil!');</script>";
    }
}
// ----------------------------

// Ambil data user terbaru (akan langsung mengambil data baru jika barusan di-update)
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id'");
$user = mysqli_fetch_assoc($query);

// Memberikan nilai bawaan jika kolom belum ada/kosong di database XAMPP
$user['poin'] = $user['poin'] ?? 0; 
$user['pesanan_selesai'] = $user['pesanan_selesai'] ?? 0;
$user['member_sejak'] = $user['member_sejak'] ?? 'Juni 2026';
$user['alamat'] = $user['alamat'] ?? ''; // Bawaan alamat kosong jika belum diisi
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya | Salatiga Coffee Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: { colors: { bg60: '#140F0A', bg30: '#2C1E16', accent10: '#D4A373', textLight: '#FDF8F5', textMuted: '#A89B91' } } } }
    </script>
</head>
<body class="bg-bg60 text-textLight min-h-screen flex flex-col">

    <header class="sticky top-0 z-50 flex justify-between items-center px-[5%] py-5 bg-bg60/95 backdrop-blur-sm border-b border-bg30">
        <div class="text-2xl font-bold text-textLight flex items-center gap-2.5">
            <i class="fas fa-mug-hot text-accent10"></i> Salatiga <span class="text-accent10">Coffee Hub</span>
        </div>
        <nav class="hidden md:block">
            <ul class="flex list-none gap-8">
                <li><a href="home.php" class="text-textLight hover:text-accent10 transition-colors">Beranda</a></li>
                <li><a href="direktori_cafe.php" class="text-textLight hover:text-accent10 transition-colors">Direktori Cafe</a></li>
                <li><a href="diskon.php" class="text-textLight hover:text-accent10 transition-colors">Diskon</a></li>
                <li><a href="tentang_kami.php" class="text-textLight hover:text-accent10 transition-colors">Tentang Kami</a></li>
            </ul>
        </nav>
        <div class="flex items-center gap-5 text-xl">
            <a href="keranjang.php" class="relative group flex items-center"><i class="fas fa-shopping-cart"></i></a>
            <a href="profil.php" class="text-accent10 flex items-center"><i class="fas fa-user-circle"></i></a>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-5 py-12 w-full flex-1">
        <div class="bg-bg30 rounded-2xl p-8 mb-8 flex flex-col md:flex-row items-center gap-8 shadow-xl border border-accent10/10">
            <div class="w-32 h-32 bg-accent10 rounded-full flex items-center justify-center text-bg60 text-5xl font-bold shadow-2xl">
                <?= substr($user['nama'], 0, 1) ?>
            </div>
            <div class="text-center md:text-left flex-1">
                <h1 class="text-3xl font-bold"><?= htmlspecialchars($user['nama']) ?></h1>
                <p class="text-textMuted mb-4"><?= htmlspecialchars($user['email']) ?></p>
                <div class="flex gap-4 justify-center md:justify-start">
                    <span class="bg-bg60 px-4 py-1.5 rounded-lg border border-accent10/30 text-sm">Member Sejak: <?= $user['member_sejak'] ?></span>
                </div>
            </div>
            <div class="flex gap-6 text-center">
                <div>
                    <div class="text-2xl font-bold text-accent10"><?= $user['poin'] ?></div>
                    <div class="text-xs text-textMuted uppercase tracking-wider">Coffee Poin</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-accent10"><?= $user['pesanan_selesai'] ?></div>
                    <div class="text-xs text-textMuted uppercase tracking-wider">Pesanan</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="md:col-span-1 space-y-2">
            <a href="profil.php" class="w-full text-left p-4 bg-accent10 text-bg60 font-bold rounded-lg block">
                <i class="fas fa-user mr-2"></i> Profil
            </a>
            
            <a href="riwayat_pesanan.php" class="w-full text-left p-4 hover:bg-bg30 rounded-lg transition-colors block">
                <i class="fas fa-receipt mr-2"></i> Riwayat Pesanan
            </a>
            
            <a href="voucher_saya.php" class="w-full text-left p-4 hover:bg-bg30 rounded-lg transition-colors block">
                <i class="fas fa-ticket-alt mr-2"></i> Voucher Saya
            </a>
            
            <a href="logout.php" class="w-full text-left p-4 hover:bg-bg30 rounded-lg transition-colors text-red-400 block">
                <i class="fas fa-sign-out-alt mr-2"></i> Keluar
            </a>
        </div>

            <div class="md:col-span-3 bg-bg30 p-8 rounded-2xl border border-accent10/10">
                <h2 class="text-2xl font-bold mb-6">Edit Informasi Profil</h2>
                
                <form action="profil.php" method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm text-textMuted mb-2">Nama Lengkap</label>
                            <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required class="w-full bg-bg60 p-3 rounded-lg border border-textMuted/20 focus:border-accent10 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm text-textMuted mb-2">Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required class="w-full bg-bg60 p-3 rounded-lg border border-textMuted/20 focus:border-accent10 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm text-textMuted mb-2">Alamat Utama</label>
                        <textarea name="alamat" class="w-full bg-bg60 p-3 rounded-lg border border-textMuted/20 focus:border-accent10 outline-none" rows="3"><?= htmlspecialchars($user['alamat']) ?></textarea>
                    </div>
                    
                    <button type="submit" name="update_profil" class="px-8 py-3 bg-accent10 text-bg60 font-bold rounded-lg hover:bg-[#c09161] transition-all">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </main>

    <footer class="bg-bg30 text-textLight px-[5%] py-6 mt-12 text-center text-sm text-textMuted border-t border-accent10/20">
        &copy; 2026 Lima Serangkai Foundation, Salatiga.
    </footer>
</body>
</html>