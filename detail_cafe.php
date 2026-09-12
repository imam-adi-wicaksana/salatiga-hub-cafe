<?php
include 'koneksi.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 1;

$query = mysqli_query($conn, "SELECT * FROM cafes WHERE id = $id");
$cafe = mysqli_fetch_assoc($query);

if (!$cafe) {
    die("Cafe tidak ditemukan.");
}

// Format data dari database agar sesuai dengan variabel di HTML Anda
$cafe['fitur'] = explode(',', $cafe['fitur']);
$cafe['gambar_utama'] = $cafe['gambar'];
$cafe['kontak'] = [
    "ig" => $cafe['kontak_ig'],
    "wa" => $cafe['kontak_wa']
];

// Data sisipan sementara (karena belum ada tabel khusus di SQL)
$cafe['jam_buka'] = [
    "Senin - Jumat" => "08:00 - 22:00 WIB",
    "Sabtu - Minggu" => "07:00 - 23:00 WIB"
];
$cafe['galeri'] = [
    "https://images.unsplash.com/photo-1497935586351-b67a49e012bf?auto=format&fit=crop&w=400&q=80",
    "https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=400&q=80",
    "https://images.unsplash.com/photo-1525610553991-2bede1a236e2?auto=format&fit=crop&w=400&q=80"
];
$cafe['ulasan'] = [
    ["nama" => "Bima A.", "tanggal" => "12 Mei 2026", "rating" => 5, "teks" => "Tempatnya cozy banget buat nugas skripsi. Wi-Finya kenceng dan colokan ada di setiap meja."],
    ["nama" => "Siti N.", "tanggal" => "04 Mei 2026", "rating" => 4, "teks" => "Kopinya enak, pelayanannya ramah. Sayang kalau sore akhir pekan agak susah cari parkir."]
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile <?= $cafe['nama'] ?> | Salatiga Coffee Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: { colors: { bg60: '#140F0A', bg30: '#2C1E16', accent10: '#D4A373', textLight: '#FDF8F5', textMuted: '#A89B91' } } } }
    </script>
</head>
<body class="bg-bg60 text-textLight leading-relaxed antialiased flex flex-col min-h-screen">
    
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

    <main class="max-w-6xl mx-auto px-5 py-10 w-full flex-1">
        
        <div class="relative w-full h-[300px] md:h-[450px] rounded-2xl mb-8 shadow-xl overflow-hidden group">
            <img src="<?= $cafe['gambar_utama'] ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
            <div class="absolute inset-0 bg-gradient-to-t from-bg60 via-bg60/40 to-transparent"></div>
            
            <div class="absolute top-5 right-5 bg-bg60/90 backdrop-blur border border-accent10/50 px-4 py-2 rounded-lg flex items-center gap-2 shadow-lg">
                <i class="fas fa-star text-accent10"></i>
                <span class="font-bold text-lg"><?= $cafe['rating'] ?></span>
                <span class="text-textMuted text-sm">/ 5.0</span>
            </div>

            <div class="absolute bottom-6 left-6 md:bottom-10 md:left-10 right-6 flex flex-col md:flex-row justify-between items-end gap-6">
                <div>
                    <h1 class="text-4xl md:text-6xl font-bold mb-3 drop-shadow-lg"><?= $cafe['nama'] ?></h1>
                    <p class="text-textLight/90 text-lg flex items-center gap-2 drop-shadow-md">
                        <i class="fas fa-map-marker-alt text-accent10"></i> <?= $cafe['lokasi'] ?>
                        <span class="mx-2 text-textMuted">|</span>
                        <span class="text-sm bg-accent10 text-bg60 font-bold px-2 py-0.5 rounded">Buka</span>
                    </p>
                </div>
                <button onclick="window.location.href='detail_menu.php?id=<?= $id ?>'" class="w-full md:w-auto px-8 py-3.5 bg-accent10 text-bg60 font-bold rounded-md hover:bg-[#c09161] transition-all text-lg shadow-[0_0_20px_rgba(212,163,115,0.4)] whitespace-nowrap flex items-center justify-center gap-2">
                    <i class="fas fa-book-open"></i> Lihat Menu & Pesan
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            
            <div class="lg:col-span-2 space-y-12">
                
                <section>
                    <h3 class="text-2xl font-bold mb-4 flex items-center gap-2"><i class="fas fa-info-circle text-accent10"></i> Tentang Cafe</h3>
                    <p class="text-textMuted leading-relaxed text-lg text-justify"><?= $cafe['deskripsi'] ?></p>
                </section>

                <hr class="border-bg30">

                <section>
                    <h3 class="text-2xl font-bold mb-5 flex items-center gap-2"><i class="fas fa-camera-retro text-accent10"></i> Suasana Cafe</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <?php foreach($cafe['galeri'] as $img): ?>
                        <div class="h-[150px] md:h-[200px] rounded-xl overflow-hidden cursor-pointer group">
                            <img src="<?= $img ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <hr class="border-bg30">

                <section>
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="text-2xl font-bold flex items-center gap-2"><i class="fas fa-comments text-accent10"></i> Ulasan Pelanggan</h3>
                        <span class="text-textMuted text-sm"><?= $cafe['total_ulasan'] ?> ulasan</span>
                    </div>
                    
                    <div class="space-y-4">
                        <?php foreach($cafe['ulasan'] as $ulasan): ?>
                        <div class="bg-bg30 p-5 rounded-xl border border-transparent hover:border-accent10/20 transition-colors">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-bg60 flex items-center justify-center text-accent10 font-bold border border-accent10/30">
                                        <?= substr($ulasan['nama'], 0, 1) ?>
                                    </div>
                                    <div>
                                        <div class="font-bold text-textLight"><?= $ulasan['nama'] ?></div>
                                        <div class="text-xs text-textMuted"><?= $ulasan['tanggal'] ?></div>
                                    </div>
                                </div>
                                <div class="flex text-accent10 text-xs">
                                    <?php for($i=0; $i<$ulasan['rating']; $i++) echo '<i class="fas fa-star"></i>'; ?>
                                </div>
                            </div>
                            <p class="text-textMuted text-sm leading-relaxed">"<?= $ulasan['teks'] ?>"</p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="w-full mt-4 py-3 border border-textMuted/30 text-textLight font-bold rounded-md hover:border-accent10 hover:text-accent10 transition-colors text-sm">
                        Lihat Semua Ulasan
                    </button>
                </section>
            </div>
            
            <div class="space-y-6 sticky top-[100px] h-fit">
                
                <div class="bg-bg30 p-6 rounded-xl shadow-lg border border-bg30">
                    <h4 class="text-accent10 font-bold mb-4 uppercase tracking-wider text-sm flex items-center gap-2"><i class="far fa-clock"></i> Jam Operasional</h4>
                    <ul class="space-y-3 text-sm">
                        <?php foreach($cafe['jam_buka'] as $hari => $jam): ?>
                        <li class="flex justify-between border-b border-bg60 pb-2">
                            <span class="text-textMuted"><?= $hari ?></span>
                            <span class="font-bold text-textLight"><?= $jam ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="bg-bg30 p-6 rounded-xl shadow-lg border border-bg30">
                    <h4 class="text-accent10 font-bold mb-4 uppercase tracking-wider text-sm flex items-center gap-2"><i class="fas fa-concierge-bell"></i> Fasilitas</h4>
                    <ul class="space-y-3 text-sm">
                        <?php foreach($cafe['fitur'] as $f): ?>
                            <li class="flex items-center gap-3 text-textLight">
                                <i class="fas fa-check text-green-500 w-4 text-center"></i> <?= $f ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="bg-bg30 p-6 rounded-xl shadow-lg border border-bg30">
                    <h4 class="text-accent10 font-bold mb-4 uppercase tracking-wider text-sm flex items-center gap-2"><i class="fas fa-map-marked-alt"></i> Lokasi & Kontak</h4>
                    
                    <div class="w-full h-32 bg-bg60 rounded-lg mb-4 overflow-hidden relative border border-textMuted/20 group cursor-pointer">
                        <img src="https://developers.google.com/static/maps/images/landing/hero_maps_static_api.png" class="w-full h-full object-cover opacity-70 group-hover:opacity-100 transition-opacity">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="bg-bg60/90 text-textLight text-xs font-bold px-3 py-1.5 rounded-full shadow-lg group-hover:bg-accent10 group-hover:text-bg60 transition-colors">
                                Lihat Rute
                            </div>
                        </div>
                    </div>

                    <p class="text-textMuted text-xs mb-4 leading-relaxed"><i class="fas fa-map-pin text-accent10 mr-1"></i> <?= $cafe['alamat_lengkap'] ?></p>
                    
                    <div class="space-y-3 text-sm">
                        <a href="#" class="flex items-center gap-3 text-textLight hover:text-accent10 transition-colors"><i class="fab fa-instagram w-4 text-center"></i> <?= $cafe['kontak']['ig'] ?></a>
                        <a href="#" class="flex items-center gap-3 text-textLight hover:text-accent10 transition-colors"><i class="fab fa-whatsapp w-4 text-center"></i> <?= $cafe['kontak']['wa'] ?></a>
                    </div>
                </div>

            </div>
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
        function syncCartBadge() {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            let totalQty = cart.reduce((sum, item) => sum + item.qty, 0);
            const badge = document.getElementById('cartCount');
            if(totalQty > 0) {
                badge.innerText = totalQty;
                badge.classList.remove('hidden');
            }
        }
        syncCartBadge();
    </script>
</body>
</html>