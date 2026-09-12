<?php
include 'koneksi.php';

// Ambil data menu dan join dengan tabel cafes untuk mendapatkan nama cafe
$query = mysqli_query($conn, "SELECT menu.*, cafes.nama as nama_cafe FROM menu JOIN cafes ON menu.cafe_id = cafes.id LIMIT 3");
$featured_products = mysqli_fetch_all($query, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salatiga Coffee Hub | Temukan Kopi Terbaikmu</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bg60: '#140F0A',     // Dark Espresso
                        bg30: '#2C1E16',     // Mocha
                        accent10: '#D4A373', // Caramel/Krem
                        textLight: '#FDF8F5',// Putih Tulang
                        textMuted: '#A89B91' // Teks sekunder
                    },
                    fontFamily: {
                        sans: ['Segoe UI', 'Tahoma', 'Geneva', 'Verdana', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-bg60 text-textLight leading-relaxed antialiased">

    <header class="sticky top-0 z-50 flex justify-between items-center px-[5%] py-5 bg-bg60/95 backdrop-blur-sm border-b border-bg30">
        <div class="text-2xl font-bold text-textLight flex items-center gap-2.5">
            <i class="fas fa-mug-hot text-accent10"></i> 
            Salatiga <span class="text-accent10">Coffee Hub</span>
        </div>
        
        <nav class="hidden md:block">
            <ul class="flex list-none gap-8">
                <li><a href="home.php" class="text-accent10 font-semibold border-b-2 border-accent10 pb-1">Beranda</a></li>
                <li><a href="direktori_cafe.php" class="text-textLight hover:text-accent10 transition-colors duration-300">Direktori Cafe</a></li>
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

    <section class="flex items-center px-[5%] py-20 min-h-[80vh] bg-cover bg-center" id="home" 
             style="background-image: linear-gradient(to right, #140F0A 50%, transparent), url('https://images.unsplash.com/photo-1497935586351-b67a49e012bf?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80');">
        <div class="max-w-2xl z-10">
            <h1 class="text-5xl md:text-6xl mb-5 leading-tight font-bold">Kopi Terbaik, <br><span class="text-accent10">Hari Lebih Baik.</span></h1>
            <p class="text-lg md:text-xl mb-8 text-textMuted">Platform digital untuk menemukan, memesan, dan membayar kopi favorit dari berbagai cafe di Salatiga dalam satu tempat. Praktis dengan QRIS.</p>
            <a href="#menu" class="inline-block px-8 py-3 bg-accent10 text-bg60 font-bold rounded-md transition-all duration-300 hover:bg-[#c09161] hover:-translate-y-0.5 shadow-lg">Eksplor Menu</a>
        </div>
    </section>

    <section class="flex flex-col md:flex-row items-center px-[5%] py-20 gap-12 bg-textLight text-bg60" id="tentang">
        <div class="flex-1">
            <h4 class="text-bg30 uppercase tracking-[0.2em] mb-2.5 font-semibold text-sm">Tentang Kami</h4>
            <h2 class="text-4xl md:text-5xl font-bold mb-5 leading-tight">Dibuat Sepenuh Hati, Disajikan dengan Cinta</h2>
            <p class="mb-8 text-gray-700">Di Salatiga Coffee Hub, kami percaya kopi yang luar biasa dapat menyatukan banyak orang. Kami mengumpulkan informasi cafe, menu, harga, dan promo secara lengkap agar Anda tidak membuang waktu mencari di berbagai platform berbeda. Dukung UMKM F&B lokal Salatiga bersama kami.</p>
            <button class="px-8 py-3 bg-bg30 text-textLight font-bold rounded-md transition-all duration-300 hover:bg-bg60 hover:-translate-y-0.5 shadow-md">Cari Tahu Lebih Lanjut</button>
        </div>
        <div class="flex-1 rounded-[50%_50%_0_50%] overflow-hidden shadow-2xl">
            <img src="https://i.pinimg.com/1200x/7c/99/93/7c999356bb6a91273d34fe0b4548997f.jpg" alt="Barista menuangkan latte art" class="w-full h-auto block hover:scale-105 transition-transform duration-700">
        </div>
    </section>

    <section class="px-[5%] py-20 bg-bg60" id="menu">
        <div class="mb-10">
            <h4 class="text-accent10 uppercase tracking-[0.2em] font-semibold text-sm">Menu Spesial</h4>
            <h2 class="text-4xl md:text-5xl font-bold mt-1.5">Nikmati Setiap Tetesnya</h2>
            <hr class="w-16 border-t-2 border-accent10 mt-4 mb-5">
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach($featured_products as $product): ?>
            <a href="detail_menu.php?id=<?= $product['cafe_id'] ?>" class="block group">
                <div class="bg-bg30 rounded-xl overflow-hidden p-5 transition-transform duration-300 hover:-translate-y-1.5 hover:shadow-xl hover:shadow-black/50 h-full flex flex-col">
                    <div class="overflow-hidden rounded-lg mb-4 h-[250px]">
                        <img src="<?= $product['img'] ?>" alt="<?= $product['nama'] ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>
                    <div class="flex-1">
                        <div class="text-xs text-accent10 mb-1.5 font-medium">
                            <i class="fas fa-map-marker-alt mr-1"></i> <?= $product['nama_cafe'] ?>
                        </div>
                        <h3 class="text-2xl font-bold mb-2 text-textLight"><?= $product['nama'] ?></h3>
                        <p class="text-textMuted text-sm mb-4 min-h-[40px]"><?= $product['deskripsi'] ?></p>
                    </div>
                    <div class="flex justify-between items-center mt-2 border-t border-bg60 pt-4">
                        <span class="text-xl font-bold text-accent10">Rp <?= number_format($product['harga'], 0, ',', '.') ?></span>
                        <button class="px-4 py-2 bg-accent10 text-bg60 font-bold text-sm rounded-md hover:bg-[#c09161] transition-all">
                            Lihat Cafe
                        </button>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-12">
            <a href="direktori_cafe.php" class="inline-block px-8 py-3 bg-transparent border-2 border-accent10 text-accent10 font-bold rounded-md hover:bg-accent10 hover:text-bg60 transition-all duration-300">
                Lihat Semua Menu
            </a>
        </div>
    </section>

    <section class="px-[5%] py-24 text-center bg-cover bg-center bg-fixed" 
             style="background-image: linear-gradient(rgba(44, 30, 22, 0.9), rgba(44, 30, 22, 0.9)), url('https://images.unsplash.com/photo-1600093463592-8e36ae95ef56?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80');">
        <h4 class="text-accent10 mb-4 tracking-[0.2em] font-semibold text-sm">PENAWARAN SPESIAL</h4>
        <h2 class="text-4xl md:text-5xl font-bold mb-5 leading-tight">Diskon 20% <br>Untuk Pesanan Pertamamu</h2>
        <p class="text-textMuted mb-8 max-w-xl mx-auto">Karena hari yang baik dimulai dengan kopi yang baik dari cafe lokal pilihanmu.</p>
        <button class="px-8 py-3 bg-accent10 text-bg60 font-bold rounded-md hover:bg-[#c09161] hover:-translate-y-0.5 transition-all shadow-lg">Pesan Sekarang</button>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 text-center px-[5%] py-16 gap-8 bg-textLight text-bg60 border-t border-[#e0d5cb]">
        <div class="p-4">
            <i class="fas fa-store text-4xl text-bg30 mb-4"></i>
            <h4 class="font-bold text-lg mb-2">Direktori Terpusat</h4>
            <p class="text-sm text-gray-600">Temukan ratusan cafe di Salatiga dalam satu layar.</p>
        </div>
        <div class="p-4">
            <i class="fas fa-qrcode text-4xl text-bg30 mb-4"></i>
            <h4 class="font-bold text-lg mb-2">Bayar via QRIS</h4>
            <p class="text-sm text-gray-600">Pemesanan digital terintegrasi yang mudah dan cepat.</p>
        </div>
        <div class="p-4">
            <i class="fas fa-tags text-4xl text-bg30 mb-4"></i>
            <h4 class="font-bold text-lg mb-2">Banyak Promo</h4>
            <p class="text-sm text-gray-600">Dapatkan update diskon dan menu baru setiap harinya.</p>
        </div>
        <div class="p-4">
            <i class="fas fa-hands-helping text-4xl text-bg30 mb-4"></i>
            <h4 class="font-bold text-lg mb-2">Dukung UMKM</h4>
            <p class="text-sm text-gray-600">Membantu cafe lokal menjangkau lebih banyak pelanggan.</p>
        </div>
    </section>

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
        let cartTotal = 0;

        function addToCart() {
            cartTotal++;
            const badge = document.getElementById('cartCount');
            badge.innerText = cartTotal;
            
            // Tampilkan badge (hapus class hidden, tambah block)
            badge.classList.remove('hidden');
            badge.classList.add('block');
            
            // Animasi pop pada badge
            badge.classList.add('scale-150');
            setTimeout(() => {
                badge.classList.remove('scale-150');
            }, 200);
        }
    </script>
</body>
</html>