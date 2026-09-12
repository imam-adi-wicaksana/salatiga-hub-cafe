<?php
include 'koneksi.php';
$query = mysqli_query($conn, "SELECT * FROM diskon");
$voucher_list = mysqli_fetch_all($query, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promo & Diskon | Salatiga Coffee Hub</title>
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
<body class="bg-bg60 text-textLight leading-relaxed antialiased min-h-screen flex flex-col">

    <header class="sticky top-0 z-50 flex justify-between items-center px-[5%] py-5 bg-bg60/95 backdrop-blur-sm border-b border-bg30 shadow-md">
        <div class="text-2xl font-bold text-textLight flex items-center gap-2.5">
            <i class="fas fa-mug-hot text-accent10"></i> 
            Salatiga <span class="text-accent10">Coffee Hub</span>
        </div>
        
        <nav class="hidden md:block">
            <ul class="flex list-none gap-8">
                <li><a href="home.php" class="text-textLight hover:text-accent10 transition-colors duration-300">Beranda</a></li>
                <li><a href="direktori_cafe.php" class="text-textLight hover:text-accent10 transition-colors duration-300">Direktori Cafe</a></li>
                <li><a href="diskon.php" class="text-accent10 font-semibold border-b-2 border-accent10 pb-1">Diskon</a></li>
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

    <section class="relative w-full py-20 px-5 flex flex-col items-center justify-center text-center overflow-hidden" 
             style="background-image: linear-gradient(rgba(20, 15, 10, 0.85), rgba(20, 15, 10, 0.95)), url('https://images.unsplash.com/photo-1559925393-8be0ec4767c8?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80'); background-size: cover; background-position: center; background-attachment: fixed;">
        <h4 class="text-accent10 uppercase tracking-[0.2em] font-bold text-sm mb-3"><i class="fas fa-gift mr-2"></i>Penawaran Spesial</h4>
        <h1 class="text-4xl md:text-6xl font-bold mb-5 drop-shadow-lg">Klaim <span class="text-accent10">Voucher Diskonmu</span></h1>
        <p class="text-textLight/90 max-w-2xl text-lg md:text-xl drop-shadow-md">Makin hemat menikmati sajian kopi dari cafe-cafe terbaik di Salatiga. Masukkan kode promo atau klaim voucher aktif di bawah ini untuk potongan harga instan.</p>
    </section>

    <section class="px-[5%] py-10 bg-bg30/50 border-b border-bg30">
        <div class="max-w-6xl mx-auto w-full">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center divide-y md:divide-y-0 md:divide-x divide-bg60">
                <div class="pt-4 md:pt-0 px-4">
                    <div class="w-14 h-14 mx-auto bg-bg60 rounded-full flex items-center justify-center text-accent10 text-2xl mb-4 shadow-lg border border-accent10/30">1</div>
                    <h4 class="font-bold text-lg mb-2 text-textLight">Pilih Voucher</h4>
                    <p class="text-textMuted text-sm">Temukan voucher yang sesuai dengan pesanan dan metode pembayaranmu.</p>
                </div>
                <div class="pt-8 md:pt-0 px-4">
                    <div class="w-14 h-14 mx-auto bg-bg60 rounded-full flex items-center justify-center text-accent10 text-2xl mb-4 shadow-lg border border-accent10/30">2</div>
                    <h4 class="font-bold text-lg mb-2 text-textLight">Klaim / Salin Kode</h4>
                    <p class="text-textMuted text-sm">Klik 'Gunakan' untuk langsung diarahkan ke keranjang, atau salin manual.</p>
                </div>
                <div class="pt-8 md:pt-0 px-4">
                    <div class="w-14 h-14 mx-auto bg-bg60 rounded-full flex items-center justify-center text-accent10 text-2xl mb-4 shadow-lg border border-accent10/30">3</div>
                    <h4 class="font-bold text-lg mb-2 text-textLight">Nikmati Diskonnya</h4>
                    <p class="text-textMuted text-sm">Harga di Ringkasan Pesanan akan otomatis terpotong. Selesaikan pembayaran!</p>
                </div>
            </div>
        </div>
    </section>

    <section class="px-[5%] py-12 max-w-6xl mx-auto w-full">
        <div class="bg-gradient-to-r from-bg30 to-[#3a281d] p-8 rounded-2xl border border-accent10/30 shadow-2xl max-w-3xl mx-auto transform hover:-translate-y-1 transition-transform duration-300">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-full bg-accent10 text-bg60 flex items-center justify-center text-2xl shadow-lg">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-textLight">Punya Kode Redeem Sendiri?</h3>
                    <p class="text-sm text-textMuted">Masukkan kode promo spesial yang kamu dapatkan dari event atau kampus.</p>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3 mt-6">
                <input type="text" id="redeemInput" placeholder="Ketik kode promo (Contoh: MABACOFFEE)" 
                       class="flex-1 bg-bg60 border-2 border-textMuted/20 text-textLight rounded-lg py-4 px-5 uppercase tracking-wider focus:outline-none focus:border-accent10 font-bold placeholder:font-normal placeholder:normal-case placeholder:tracking-normal transition-colors shadow-inner">
                <button onclick="checkRedeemCode()" class="py-4 px-8 bg-accent10 text-bg60 font-bold text-lg rounded-lg hover:bg-[#c09161] transition-all shadow-[0_0_15px_rgba(212,163,115,0.3)] whitespace-nowrap flex items-center gap-2">
                    Cek & Gunakan <i class="fas fa-arrow-right"></i>
                </button>
            </div>
            <p id="redeemMessage" class="text-sm mt-4 hidden"></p>
        </div>
    </section>

    <section class="px-[5%] pb-16 max-w-6xl mx-auto w-full flex-1">
        <div class="flex justify-between items-end mb-8">
            <h2 class="text-3xl font-bold border-l-4 border-accent10 pl-4">Voucher Tersedia</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach($voucher_list as $voucher): ?>
            <div class="bg-bg30 rounded-xl overflow-hidden border border-bg30 hover:border-accent10/50 transition-all shadow-lg grid grid-cols-[100px_1fr] relative group h-full">
                
                <div class="bg-gradient-to-b from-[#dcae81] to-accent10 text-bg60 p-4 flex flex-col justify-center items-center text-center border-r-2 border-dashed border-bg30/50">
                    <i class="<?= $voucher['ikon']; ?> text-2xl mb-2"></i>
                    <span class="font-mono font-black text-sm leading-tight text-center break-words w-full"><?= $voucher['potongan']; ?></span>
                </div>

                <div class="p-4 flex flex-col justify-between min-w-0">
                    <div class="mb-3">
                        <div class="flex justify-between items-start gap-2 mb-1">
                            <h3 class="font-bold text-textLight text-sm leading-tight line-clamp-2"><?= $voucher['judul']; ?></h3>
                        </div>
                        <p class="text-textMuted text-[11px] leading-relaxed line-clamp-2"><?= $voucher['deskripsi']; ?></p>
                    </div>

                    <div class="border-t border-bg60 pt-3 flex flex-wrap items-center justify-between gap-2 mt-auto">
                        <div class="text-[10px] text-textMuted space-y-0.5">
                            <div class="truncate">Bag: <?= $voucher['min_beli']; ?></div>
                            <div class="truncate">Exp: <?= $voucher['valid_hingga']; ?></div>
                        </div>

                        <div class="flex gap-1.5 ml-auto">
                            <button onclick="copyPromoCode('<?= $voucher['kode']; ?>', this)" 
                                    class="py-1 px-3 bg-bg60 border border-accent10/30 text-accent10 text-[10px] font-bold rounded hover:bg-accent10 hover:text-bg60 transition-all" 
                                    title="Salin">
                                <span>Salin</span>
                            </button>
                            
                            <button onclick="gunakanPromo('<?= $voucher['kode']; ?>')" 
                                    class="py-1 px-3 bg-accent10 text-bg60 text-[10px] font-bold rounded hover:bg-[#c09161] transition-all">
                                Gunakan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
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
        const validCodes = ["MABACOFFEE", "QRISHEMAT", "SENJAKOPI", "COFFEEHUB20"];

        function copyPromoCode(kode, buttonElement) {
            navigator.clipboard.writeText(kode).then(() => {
                const textSpan = buttonElement.querySelector('span'); // Sekarang span ini ada
                const originalText = textSpan.innerText;
                
                textSpan.innerText = "Tersalin!";
                buttonElement.classList.remove('text-accent10', 'bg-bg60', 'border-accent10/30');
                buttonElement.classList.add('text-bg60', 'bg-green-400', 'border-green-400');

                setTimeout(() => {
                    textSpan.innerText = originalText;
                    buttonElement.classList.remove('text-bg60', 'bg-green-400', 'border-green-400');
                    buttonElement.classList.add('text-accent10', 'bg-bg60', 'border-accent10/30');
                }, 1500);
            });
        }

        function checkRedeemCode() {
            const inputVal = document.getElementById('redeemInput').value.trim().toUpperCase();
            const messageElement = document.getElementById('redeemMessage');
            
            if(validCodes.includes(inputVal)) {
                showMsg(messageElement, `✓ Kode valid! Mengalihkan ke keranjang...`, "text-green-400");
                gunakanPromo(inputVal); 
            } else {
                showMsg(messageElement, "✕ Kode tidak valid atau sudah kedaluwarsa.", "text-red-400");
            }
        }

        function gunakanPromo(kode) {
            // Simpan ke localStorage agar ditangkap oleh keranjang.php
            localStorage.setItem('pendingPromo', kode);
            
            // Animasi loading/feedback kecil (opsional)
            alert("Voucher " + kode + " berhasil dipilih! Mengarahkan ke keranjang...");
            
            setTimeout(() => {
                window.location.href = 'keranjang.php';
            }, 300);
        }

        function showMsg(el, text, colorClass) {
            el.innerText = text;
            el.className = `text-sm mt-4 block ${colorClass} font-bold animate-pulse`;
        }

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