<?php
include 'koneksi.php';

$query = mysqli_query($conn, "SELECT * FROM cafes");
$direktori_cafe = [];

while($row = mysqli_fetch_assoc($query)) {
    // Ubah string "Bisa QRIS,Wi-Fi Cepat" dari database menjadi array
    $row['fitur'] = explode(',', $row['fitur']);
    $direktori_cafe[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Direktori Cafe | Salatiga Coffee Hub</title>
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

    <section class="px-[5%] pt-12 pb-8 bg-bg60 border-b border-bg30">
        <h4 class="text-accent10 uppercase tracking-[0.2em] font-semibold text-sm mb-2">Eksplorasi</h4>
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Temukan Cafe Favoritmu</h1>
        <p class="text-textMuted max-w-2xl text-lg">Bandingkan suasana, harga, dan promo dari ratusan cafe lokal di Salatiga. Tidak perlu lagi bingung mencari tempat nongkrong atau nugas.</p>
    </section>

    <section class="px-[5%] py-6 bg-bg30 sticky top-[76px] z-40 shadow-lg">
            <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                <div class="relative w-full md:w-1/3">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-textMuted"></i>
                    <input type="text" id="searchInput" oninput="applyFilters()" placeholder="Cari nama cafe, menu, atau area..." class="w-full bg-bg60 border border-textMuted/30 text-textLight rounded-md py-2.5 pl-12 pr-4 focus:outline-none focus:border-accent10 transition-colors">
                </div>
                <div class="flex w-full md:w-auto gap-3 overflow-x-auto pb-2 md:pb-0 hide-scrollbar">
                    <select id="locFilter" onchange="applyFilters()" class="bg-bg60 border border-textMuted/30 text-textLight rounded-md py-2.5 px-4 focus:outline-none focus:border-accent10 cursor-pointer appearance-none pr-8">
                        <option value="">Semua Area</option>
                        <option value="sidorejo">Sidorejo</option>
                        <option value="tingkir">Tingkir</option>
                        <option value="argomulyo">Argomulyo</option>
                        <option value="sidomukti">Sidomukti</option>
                    </select>
                    <button id="filterBtn" onclick="toggleModal(true)" class="bg-bg60 border border-textMuted/30 text-textLight rounded-md py-2.5 px-4 hover:border-accent10 hover:text-accent10 transition-colors flex items-center gap-2 whitespace-nowrap">
                        <i class="fas fa-sliders-h"></i> Filter Lain
                    </button>
                </div>
            </div>
    </section>

    <section class="px-[5%] py-12 bg-bg60 min-h-[50vh]">
            <div id="cafeGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach($direktori_cafe as $cafe): ?>
                <div class="cafe-card bg-bg30 rounded-xl overflow-hidden border border-bg30 hover:border-accent10/50 transition-all duration-300 hover:-translate-y-1.5 flex flex-col"
                     data-nama="<?= strtolower($cafe['nama']) ?>"
                     data-lokasi="<?= strtolower($cafe['lokasi_filter']) ?>"
                     data-harga="<?= $cafe['harga_min'] ?>"
                     data-fitur="<?= implode(',', array_map('trim', $cafe['fitur'])) ?>">
                    
                    <div class="relative overflow-hidden h-[220px]">
                        <img src="<?= $cafe['gambar'] ?>" alt="<?= $cafe['nama'] ?>" class="w-full h-full object-cover">
                        <?php if($cafe['promo']): ?>
                        <div class="absolute top-3 left-3 bg-red-600 text-white text-xs font-bold px-3 py-1 rounded-md">Ada Promo</div>
                        <?php endif; ?>
                    </div>

                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="text-2xl font-bold text-textLight mb-2"><?= $cafe['nama'] ?></h3>
                        <p class="text-textMuted text-sm mb-4"><i class="fas fa-map-marker-alt text-accent10"></i> <?= $cafe['lokasi'] ?></p>
                        <div class="flex flex-wrap gap-2 mb-6">
                            <?php foreach($cafe['fitur'] as $fitur): ?>
                            <span class="text-[10px] uppercase font-bold px-2 py-1 rounded bg-bg60 text-textMuted border border-bg60"><?= $fitur ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-auto flex gap-3">
                            <button onclick="window.location.href='detail_menu.php?id=<?= $cafe['id'] ?>'" class="flex-1 py-2 bg-accent10 text-bg60 font-bold rounded">Lihat Menu</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div id="emptyState" class="hidden text-center py-20 text-textMuted">Cafe tidak ditemukan.</div>
            <div id="paginationContainer" class="mt-12 flex justify-center items-center gap-2"></div>
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

        <div id="filterModal" class="fixed inset-0 z-[100] hidden bg-black/80 flex items-center justify-center p-4">
            <div class="bg-bg60 border border-accent10/30 p-6 rounded-xl max-w-sm w-full">
                <h3 class="text-xl font-bold mb-4 text-accent10">Filter Fasilitas</h3>
                <div id="featureList" class="grid grid-cols-2 gap-3 mb-6">
                    <?php 
                    // Ambil semua fitur unik
                    $all_features = [];
                    foreach($direktori_cafe as $c) foreach($c['fitur'] as $f) if(!empty(trim($f))) $all_features[trim($f)] = true;
                    foreach(array_keys($all_features) as $fitur): ?>
                        <label class="flex items-center gap-2 text-sm text-textLight cursor-pointer">
                            <input type="checkbox" class="feature-check accent-accent10" value="<?= $fitur ?>" onchange="applyFilters()"> <?= $fitur ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <button onclick="toggleModal(false)" class="w-full py-2 bg-accent10 text-bg60 font-bold rounded hover:bg-[#c09161]">Terapkan & Tutup</button>
            </div>
        </div>

        <div class="text-center pt-6 border-t border-white/10 text-textMuted text-xs">
            <p>&copy; 2026 Lima Serangkai Foundation, Salatiga. All rights reserved.</p>
        </div>
    </footer>

    <style>
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    <script>
        const cards = Array.from(document.querySelectorAll('.cafe-card'));
        const itemsPerPage = 6; 
        let currentPage = 1;
        let filteredCards = [...cards];

        function toggleModal(show) {
            document.getElementById('filterModal').classList.toggle('hidden', !show);
        }

        function applyFilters() {
            const searchVal = document.getElementById('searchInput').value.toLowerCase();
            const locVal = document.getElementById('locFilter').value;
            const priceVal = document.getElementById('priceFilter').value;
            
            // Ambil semua checkbox yang dicentang
            const selectedFeatures = Array.from(document.querySelectorAll('.feature-check:checked')).map(cb => cb.value);

            filteredCards = cards.filter(card => {
                const nama = card.getAttribute('data-nama');
                const lokasi = card.getAttribute('data-lokasi');
                const harga = parseInt(card.getAttribute('data-harga'));
                const fitur = card.getAttribute('data-fitur').split(',');

                let matchSearch = nama.includes(searchVal);
                let matchLoc = (locVal === "" || lokasi === locVal);
                let matchPrice = true;
                
                // Cek apakah fitur yang dipilih user ada di fitur cafe
                let matchFeatures = selectedFeatures.every(f => fitur.includes(f));

                if(priceVal === 'low') matchPrice = harga < 20000;
                if(priceVal === 'mid') matchPrice = (harga >= 20000 && harga <= 40000);
                if(priceVal === 'high') matchPrice = harga > 40000;

                return matchSearch && matchLoc && matchPrice && matchFeatures;
            });

            currentPage = 1;
            renderGrid();
        }

        function renderGrid() {
            // Sembunyikan semua terlebih dahulu
            cards.forEach(card => card.style.display = 'none');

            // Hitung data untuk pagination
            const startIdx = (currentPage - 1) * itemsPerPage;
            const endIdx = startIdx + itemsPerPage;
            const cardsToShow = filteredCards.slice(startIdx, endIdx);

            // Tampilkan card yang sesuai halaman
            cardsToShow.forEach(card => card.style.display = 'flex');

            // Tampilkan state kosong jika tidak ada data
            document.getElementById('emptyState').style.display = filteredCards.length === 0 ? 'block' : 'none';

            renderPagination();
        }

        function renderPagination() {
            const totalPages = Math.ceil(filteredCards.length / itemsPerPage);
            const container = document.getElementById('paginationContainer');
            container.innerHTML = '';

            if (totalPages <= 1) return; // Sembunyikan pagination jika hanya 1 halaman

            // CLASS CSS PERSIS SEPERTI DESAIN ASLI ANDA
            const btnClass = "w-10 h-10 rounded-md transition-colors flex justify-center items-center ";
            const activeClass = "bg-accent10 text-bg60 font-bold";
            const inactiveClass = "bg-bg30 text-textLight hover:bg-accent10 hover:text-bg60";
            const disabledClass = "bg-bg30 text-textMuted cursor-not-allowed opacity-50";

            // Tombol Prev
            let prevBtn = document.createElement('button');
            prevBtn.className = btnClass + (currentPage === 1 ? disabledClass : inactiveClass);
            prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
            if(currentPage > 1) prevBtn.onclick = () => { currentPage--; renderGrid(); window.scrollTo(0, 0); };
            container.appendChild(prevBtn);

            // Tombol Angka
            for(let i = 1; i <= totalPages; i++) {
                let pageBtn = document.createElement('button');
                pageBtn.className = btnClass + (currentPage === i ? activeClass : inactiveClass);
                pageBtn.innerText = i;
                pageBtn.onclick = () => { currentPage = i; renderGrid(); window.scrollTo(0, 0); };
                container.appendChild(pageBtn);
            }

            // Tombol Next
            let nextBtn = document.createElement('button');
            nextBtn.className = btnClass + (currentPage === totalPages ? disabledClass : inactiveClass);
            nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
            if(currentPage < totalPages) nextBtn.onclick = () => { currentPage++; renderGrid(); window.scrollTo(0, 0); };
            container.appendChild(nextBtn);
        }

        // Inisialisasi tampilan pertama kali
        renderGrid();
    </script>
</body>
</html>