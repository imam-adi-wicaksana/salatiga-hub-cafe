<?php
include 'koneksi.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 1;

// 1. Ambil data informasi cafenya
$query_cafe = mysqli_query($conn, "SELECT nama, gambar, deskripsi FROM cafes WHERE id = $id");
$cafe_info = mysqli_fetch_assoc($query_cafe);

if (!$cafe_info) {
    die("Cafe tidak ditemukan.");
}

// 2. Ambil data menu milik cafe tersebut
$query_menu = mysqli_query($conn, "SELECT * FROM menu WHERE cafe_id = $id");
$menus = [];
$kategori_list = ["Semua"]; // Default tombol pertama selalu 'Semua'

while($row = mysqli_fetch_assoc($query_menu)) {
    $menus[] = $row;
    
    // Jika ada kategori baru dari database, masukkan ke daftar tombol filter
    if (!in_array($row['kategori'], $kategori_list)) {
        $kategori_list[] = $row['kategori'];
    }
}

// 3. Rangkai menjadi satu array utuh agar langsung dibaca oleh HTML
$cafe = [
    "nama_cafe" => $cafe_info['nama'],
    "banner" => $cafe_info['gambar'],
    "deskripsi_singkat" => $cafe_info['deskripsi'],
    "kategori_list" => $kategori_list,
    "menus" => $menus
];
?>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu | <?= $cafe['nama_cafe'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: { colors: { bg60: '#140F0A', bg30: '#2C1E16', accent10: '#D4A373', textLight: '#FDF8F5', textMuted: '#A89B91' } } } }
    </script>
</head>
<body class="bg-bg60 text-textLight min-h-screen flex flex-col">
    
    <header class="sticky top-0 z-50 flex justify-between items-center px-[5%] py-5 bg-bg60/95 backdrop-blur-sm border-b border-bg30">
        <div class="text-2xl font-bold text-textLight flex items-center gap-2.5">
            <i class="fas fa-mug-hot text-accent10"></i> 
            Salatiga <span class="text-accent10">Coffee Hub</span>
        </div>
        
        <nav class="hidden md:block">
            <ul class="flex list-none gap-8">
                <li><a href="home.php" class="text-textLight hover:text-accent10 transition-colors duration-300">Beranda</a></li>
                <li><a href="direktori_cafe.php" class="text-accent10 font-semibold border-b-2 border-accent10 pb-1">Direktori Cafe</a></li>
                <li><a href="diskon.php" class="text-textLight hover:text-accent10 transition-colors duration-300">Diskon</a></li>
                <li><a href="tentang_kami.php" class="text-textLight hover:text-accent10 transition-colors duration-300">Tentang Kami</a></li>
            </ul>
        </nav>

        <div class="flex items-center gap-5 text-xl">
            
            <a href="keranjang.php" class="relative cursor-pointer hover:text-accent10 transition-colors duration-300 group flex items-center">
                <i class="fas fa-shopping-cart"></i>
                <span id="cartCount" class="absolute -top-2 -right-2.5 bg-accent10 text-bg60 text-xs font-bold px-1.5 py-0.5 rounded-full hidden group-hover:scale-110 transition-transform">0</span>
            </a>
            
            <a href="profil.php" class="cursor-pointer hover:text-accent10 transition-colors duration-300 flex items-center">
                <i class="fas fa-user-circle"></i>
            </a>
            
        </div>
    </header>

    <section class="relative w-full h-[250px] md:h-[350px]">
        <img src="<?= $cafe['banner'] ?>" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-bg60/70"></div>
        <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-5">
            <h4 class="text-accent10 uppercase tracking-[0.2em] font-semibold text-sm mb-2">Pemesanan Online</h4>
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Menu <span class="text-accent10"><?= $cafe['nama_cafe'] ?></span></h1>
            <p class="text-textLight max-w-xl text-sm md:text-base opacity-90"><?= $cafe['deskripsi_singkat'] ?></p>
        </div>
    </section>

    <main class="max-w-7xl mx-auto px-[5%] py-12 flex-1 w-full">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10 gap-6">
            <div>
                <h2 class="text-3xl font-bold mb-2">Daftar Menu</h2>
                <p class="text-textMuted text-sm">Pilih dan tambahkan ke keranjang belanja Anda.</p>
            </div>
            
            <div class="flex gap-3 overflow-x-auto pb-2 w-full md:w-auto scrollbar-hide">
                <?php foreach($cafe['kategori_list'] as $index => $kat): ?>
                    <button onclick="filterMenu('<?= $kat ?>')" class="filter-btn whitespace-nowrap px-5 py-2 rounded-full border-2 border-accent10 text-sm font-bold transition-colors <?= $index === 0 ? 'bg-accent10 text-bg60' : 'text-accent10 hover:bg-accent10/20' ?>" data-kat="<?= $kat ?>">
                        <?= $kat ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" id="menuContainer">
            <?php foreach($cafe['menus'] as $m): ?>
            <div class="menu-card bg-bg30 rounded-xl overflow-hidden group border border-transparent hover:border-accent10/30 transition-all duration-300 shadow-lg flex flex-col h-full" data-category="<?= $m['kategori'] ?>">
                
                <div class="overflow-hidden h-[220px] relative">
                    <img src="<?= $m['img'] ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute top-3 left-3 bg-bg60/90 backdrop-blur-sm px-3 py-1 rounded shadow text-[10px] font-bold text-accent10 uppercase tracking-widest border border-accent10/20">
                        <?= $m['kategori'] ?>
                    </div>
                </div>
                
                <div class="p-5 flex-1 flex flex-col">
                    <h3 class="text-xl font-bold mb-2 text-textLight"><?= $m['nama'] ?></h3>
                    <p class="text-textMuted text-sm mb-4 leading-relaxed line-clamp-2 flex-1"><?= $m['deskripsi'] ?></p>
                    
                    <div class="flex justify-between items-center mt-auto border-t border-bg60 pt-4">
                        <span class="text-lg font-bold text-accent10">Rp <?= number_format($m['harga'], 0, ',', '.') ?></span>
                        
                        <button onclick="addToCart('<?= addslashes($m['nama']) ?>', <?= $m['harga'] ?>, '<?= $m['img'] ?>')" class="w-10 h-10 rounded-full bg-bg60 border border-accent10 text-accent10 hover:bg-accent10 hover:text-bg60 transition-all flex items-center justify-center shadow-lg group-hover:-translate-y-1">
                            <i class="fas fa-shopping-cart text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="bg-bg30 text-textLight px-[5%] pt-16 pb-6 border-t border-accent10/20">
        <div class="grid grid-cols-1 md:grid-cols-[2fr_1fr_1fr] gap-10 mb-12">
            <div>
                <div class="text-2xl font-bold mb-4 flex items-center gap-2.5">
                    <i class="fas fa-mug-hot text-accent10"></i> 
                    Salatiga <span class="text-accent10">Coffee Hub</span>
                </div>
                <p class="text-textMuted text-sm mb-6 max-w-md leading-relaxed">Platform digital terpadu untuk menemukan, memesan, dan membayar kopi dari berbagai cafe terbaik di Salatiga. Mendukung pertumbuhan UMKM F&B lokal melalui digitalisasi.</p>
                <div class="flex gap-4">
                    <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-bg60 text-textLight hover:bg-accent10 hover:text-bg60 hover:-translate-y-1 transition-all"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-bg60 text-textLight hover:bg-accent10 hover:text-bg60 hover:-translate-y-1 transition-all"><i class="fab fa-tiktok"></i></a>
                    <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-bg60 text-textLight hover:bg-accent10 hover:text-bg60 hover:-translate-y-1 transition-all"><i class="fab fa-facebook-f"></i></a>
                </div>
            </div>

            <div>
                <h4 class="text-accent10 text-lg mb-5 font-semibold tracking-wide">Tautan Cepat</h4>
                <ul class="space-y-3">
                    <li><a href="#home" class="text-textMuted hover:text-accent10 transition-colors text-sm">Beranda</a></li>
                    <li><a href="#direktori" class="text-textMuted hover:text-accent10 transition-colors text-sm">Direktori Cafe</a></li>
                    <li><a href="#menu" class="text-textMuted hover:text-accent10 transition-colors text-sm">Menu Spesial</a></li>
                    <li><a href="#tentang" class="text-textMuted hover:text-accent10 transition-colors text-sm">Tentang Kami</a></li>
                    <li><a href="#" class="text-textMuted hover:text-accent10 transition-colors text-sm">Daftarkan Cafe Anda</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-accent10 text-lg mb-5 font-semibold tracking-wide">Hubungi Kami</h4>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3 text-sm text-textMuted">
                        <i class="fas fa-map-marker-alt text-accent10 mt-1"></i>
                        <span>Salatiga, Jawa Tengah, Indonesia</span>
                    </li>
                    <li class="flex items-center gap-3 text-sm text-textMuted">
                        <i class="fas fa-envelope text-accent10"></i>
                        <span>halo@salatigacoffeehub.com</span>
                    </li>
                    <li class="flex items-center gap-3 text-sm text-textMuted">
                        <i class="fas fa-phone-alt text-accent10"></i>
                        <span>+62 812 3456 7890</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="text-center pt-6 border-t border-white/10 text-textMuted text-xs">
            <p>&copy; 2026 Lima Serangkai Foundation, Salatiga. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Fungsi Filter Kategori Menu
        function filterMenu(category) {
            // Update UI Tombol
            document.querySelectorAll('.filter-btn').forEach(btn => {
                if(btn.getAttribute('data-kat') === category) {
                    btn.classList.add('bg-accent10', 'text-bg60');
                    btn.classList.remove('text-accent10', 'hover:bg-accent10/20');
                } else {
                    btn.classList.remove('bg-accent10', 'text-bg60');
                    btn.classList.add('text-accent10', 'hover:bg-accent10/20');
                }
            });

            // Filter Card Menu
            document.querySelectorAll('.menu-card').forEach(card => {
                if(category === "Semua" || card.getAttribute('data-category') === category) {
                    card.style.display = "flex";
                } else {
                    card.style.display = "none";
                }
            });
        }

        // Sinkronisasi Ikon Keranjang Merah
        function updateCartBadge() {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            let totalQty = cart.reduce((sum, item) => sum + item.qty, 0);
            const badge = document.getElementById('cartCount');
            if(totalQty > 0) {
                badge.innerText = totalQty;
                badge.classList.remove('hidden');
                badge.classList.add('block');
            } else {
                badge.classList.add('hidden');
            }
        }

        // Fungsi Menambah ke Keranjang & Pindah Halaman
        function addToCart(nama, harga, img) {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            let existingItem = cart.find(item => item.nama === nama);
            
            if(existingItem) {
                existingItem.qty++;
            } else {
                cart.push({ nama: nama, harga: harga, img: img, qty: 1 });
            }
            
            localStorage.setItem('cart', JSON.stringify(cart));
            window.location.href = 'keranjang.php';
        }

        // Inisialisasi awal
        updateCartBadge();
    </script>
</body>
</html>