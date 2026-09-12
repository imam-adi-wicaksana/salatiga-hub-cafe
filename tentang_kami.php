<?php
// Simulasi data statistik live dari database Salatiga Coffee Hub
$statistik = [
    "tahun_berdiri" => 2026,
    "jumlah_akses" => "14.250+", // Total klik/akses unik bulanan
    "mitra_cafe" => "48 Cafe",    // Jumlah UMKM Cafe Salatiga yang bergabung
    "transaksi_qris" => "3.800+"  // Total pemesanan sukses via QRIS
];
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami | Salatiga Coffee Hub</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bg60: '#140F0A',     // Dark Espresso (Dominan)
                        bg30: '#2C1E16',     // Mocha (Elemen Pendukung)
                        accent10: '#D4A373', // Caramel/Krem (Aksen)
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
<body class="bg-bg60 text-textLight leading-relaxed antialiased min-h-screen flex flex-col">

    <header class="sticky top-0 z-50 flex justify-between items-center px-[5%] py-5 bg-bg60/95 backdrop-blur-sm border-b border-bg30">
        <div class="text-2xl font-bold text-textLight flex items-center gap-2.5">
            <i class="fas fa-mug-hot text-accent10"></i> 
            Salatiga <span class="text-accent10">Coffee Hub</span>
        </div>
        
        <nav class="hidden md:block">
            <ul class="flex list-none gap-8">
                <li><a href="home.php" class="text-textLight hover:text-accent10 transition-colors duration-300">Beranda</a></li>
                <li><a href="direktori_cafe.php" class="text-textLight hover:text-accent10 transition-colors duration-300">Direktori Cafe</a></li>
                <li><a href="diskon.php" class="text-textLight hover:text-accent10 transition-colors duration-300">Diskon</a></li>
                <li><a href="tentang_kami.php" class="text-accent10 font-semibold border-b-2 border-accent10 pb-1">Tentang Kami</a></li>
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

    <section class="px-[5%] py-16 max-w-5xl mx-auto text-center">
        <h4 class="text-accent10 uppercase tracking-[0.2em] font-semibold text-sm mb-3">Mengenal Lebih Dekat</h4>
        <h1 class="text-4xl md:text-5xl font-bold mb-6">Satu Platform Untuk Seluruh <br><span class="text-accent10">Kopi di Salatiga</span></h1>
        
        <p class="text-textMuted text-lg max-w-3xl mx-auto leading-relaxed mb-10">
            Salatiga Coffee Hub adalah platform digital terintegrasi yang digagas sebagai solusi atas tersebarnya informasi kuliner kopi di Kota Salatiga. Kami hadir dalam format <b>Software-as-a-Service (SaaS)</b> untuk menjembatani penikmat kopi dengan para pelaku usaha lokal.
        </p>

        <div class="w-full h-[300px] md:h-[400px] rounded-2xl overflow-hidden shadow-2xl relative border border-bg30">
            <img src="https://i.pinimg.com/1200x/c2/fb/29/c2fb29bc4516c0ed4141f7aec7d2d8fb.jpg" alt="Suasana Cafe" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
            <div class="absolute inset-0 bg-bg60/20"></div>
        </div>
    </section>

    <section class="px-[5%] py-12 bg-bg30 border-y border-bg30 shadow-inner">
        <div class="max-w-5xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div class="p-4">
                <div class="text-3xl md:text-4xl font-bold text-accent10 mb-1"><?= $statistik['tahun_berdiri']; ?></div>
                <div class="text-xs uppercase tracking-wider text-textMuted">Tahun Berdiri</div>
            </div>
            <div class="p-4">
                <div class="text-3xl md:text-4xl font-bold text-accent10 mb-1"><?= $statistik['jumlah_akses']; ?></div>
                <div class="text-xs uppercase tracking-wider text-textMuted">Akses Bulanan</div>
            </div>
            <div class="p-4">
                <div class="text-3xl md:text-4xl font-bold text-accent10 mb-1"><?= $statistik['mitra_cafe']; ?></div>
                <div class="text-xs uppercase tracking-wider text-textMuted">Mitra Cafe UMKM</div>
            </div>
            <div class="p-4">
                <div class="text-3xl md:text-4xl font-bold text-accent10 mb-1"><?= $statistik['transaksi_qris']; ?></div>
                <div class="text-xs uppercase tracking-wider text-textMuted">Transaksi QRIS</div>
            </div>
        </div>
    </section>

    <section class="px-[5%] py-20 max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
        <div>
            <span class="text-xs font-bold text-accent10 tracking-widest uppercase">Latar Belakang</span>
            <h2 class="text-3xl font-bold mt-2 mb-4">Mengapa Kami Hadir?</h2>
            <p class="text-textMuted text-sm mb-4 leading-relaxed">
                Berdasarkan data riset industri, sekitar <b>81% pelanggan</b> terbiasa mencari informasi menu, harga, serta ulasan sebuah cafe secara online sebelum mereka memutuskan untuk berkunjung secara langsung.
            </p>
            <p class="text-textMuted text-sm leading-relaxed">
                Namun di Kota Salatiga, informasi tersebut masih sangat tersebar di berbagai platform media sosial dan aplikasi pesan instan yang tidak konsisten. Hal ini membuat pelanggan kehilangan banyak waktu, sementara pemilik cafe lokal kesulitan menjangkau pasar mahasiswa dan pekerja muda usia 18-35 tahun secara optimal.
            </p>
        </div>
        <div class="bg-bg30 p-8 rounded-2xl border border-accent10/10 shadow-xl">
            <i class="fas fa-lightbulb text-3xl text-accent10 mb-4"></i>
            <h3 class="text-xl font-bold mb-3 text-textLight">Solusi Terpusat</h3>
            <p class="text-textMuted text-sm leading-relaxed mb-4">
                Kami menyatukan seluruh direktori cafe, daftar menu, variasi harga, hingga pembaruan promo aktif di Salatiga ke dalam satu dashboard <i>self-service</i> yang efisien. 
            </p>
            <p class="text-textMuted text-sm leading-relaxed">
                Tidak hanya pencarian informasi, platform ini didukung oleh sistem pemesanan online dan integrasi pembayaran <b>QRIS</b> resmi guna mengurangi antrean fisik serta memberikan efisiensi operasional bagi merchant.
            </p>
        </div>
    </section>

    <section class="px-[5%] py-16 bg-textLight text-bg60">
        <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12">
            <div>
                <h3 class="text-2xl font-bold text-bg30 flex items-center gap-3 mb-4">
                    <i class="fas fa-eye text-accent10"></i> Visi Kami
                </h3>
                <p class="text-gray-700 text-base leading-relaxed">
                    Menjadi ekosistem digital kuliner kopi nomor satu di Kota Salatiga yang mampu mendongkrak pertumbuhan ekonomi UMKM lokal melalui implementasi teknologi tepat guna secara inklusif.
                </p>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-bg30 flex items-center gap-3 mb-4">
                    <i class="fas fa-bullseye text-accent10"></i> Misi Utama
                </h3>
                <ul class="space-y-3 text-gray-700 text-sm">
                    <li class="flex items-start gap-2.5">
                        <i class="fas fa-check text-accent10 mt-1"></i>
                        <span>Menyediakan data informasi cafe di Salatiga secara valid, lengkap, dan transparan dalam satu pintu.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <i class="fas fa-check text-accent10 mt-1"></i>
                        <span>Mendorong akselerasi digitalisasi sistem pembayaran non-tunai (QRIS) bagi pelaku F&B regional.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <i class="fas fa-check text-accent10 mt-1"></i>
                        <span>Membantu merchant mitra meningkatkan rata-rata volume pemesanan (Business Growth) hingga minimal +15%.</span>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <section class="px-[5%] py-20 max-w-5xl mx-auto text-center border-t border-bg30">
        <div class="bg-bg30 inline-block px-4 py-1.5 rounded-full text-xs font-semibold text-accent10 tracking-wider uppercase mb-4">
            Inisiator Proyek
        </div>
        <h2 class="text-3xl font-bold mb-8">Lima Serangkai Foundation</h2>
        
        <div class="flex justify-center mb-8">
            <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Tim Pengembang" class="w-full max-w-3xl h-[250px] md:h-[350px] object-cover rounded-xl shadow-lg border border-bg30 hover:shadow-accent10/10 transition-shadow">
        </div>

        <p class="text-textMuted text-base max-w-2xl mx-auto leading-relaxed">
            Aplikasi Salatiga Coffee Hub dirancang, dikembangkan, dan dikelola sepenuhnya di bawah naungan <b>Lima Serangkai Foundation Salatiga</b> sebagai wujud nyata pengabdian mahasiswa dalam mendukung transformasi digital komunitas lokal.
        </p>
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

</body>
</html>