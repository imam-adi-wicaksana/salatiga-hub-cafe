<?php
include 'koneksi.php';
session_start();

// BLOK AJAX HANDLER: Menangkap data JSON yang dikirim via Fetch API dari JS di bawah
$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['simpan_transaksi'])) {
    if (!isset($_SESSION['user_id'])) {
        echo "Error: Belum Login"; exit;
    }
    
    $user_id = $_SESSION['user_id'];
    $order_id = mysqli_real_escape_string($conn, $data['order_id']);
    $total = (int)$data['total'];
    $metode = mysqli_real_escape_string($conn, $data['metode']);
    $items = mysqli_real_escape_string($conn, $data['items']);
    $tanggal = date('Y-m-d H:i:s');

    // 1. Simpan ke tabel transaksi
    $query = "INSERT INTO transaksi (user_id, order_id, tanggal, total, metode_pembayaran, status, detail_items) 
              VALUES ('$user_id', '$order_id', '$tanggal', '$total', '$metode', 'Selesai', '$items')";
    mysqli_query($conn, $query);

    // 2. Update statistik user (Tambah 1 Pesanan Selesai dan Tambah 50 Coffee Poin)
    mysqli_query($conn, "UPDATE users SET pesanan_selesai = pesanan_selesai + 1, poin = poin + 50 WHERE id = '$user_id'");
    
    echo "Sukses";
    exit; // Stop render HTML karena ini hanya request API latar belakang
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang | Salatiga Coffee Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: { colors: { bg60: '#140F0A', bg30: '#2C1E16', accent10: '#D4A373', textLight: '#FDF8F5', textMuted: '#A89B91' } } } }
    </script>
    <style>
        @media print {
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            body > header, body > div:not(#receiptModal) { display: none !important; }
            body { background: white !important; color: black !important; }
            #receiptModal { position: static !important; display: block !important; background: white !important; padding: 20px !important; margin: 0 !important; }
            #receiptModal > div { position: static !important; max-width: 400px !important; margin: 0 auto !important; box-shadow: none !important; transform: none !important; }
            #printArea { border: 1px solid #ccc !important; border-radius: 8px !important; box-shadow: none !important; }
            #actionArea, #splitBillArea, .no-print { display: none !important; }
        }
    </style>
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
            <a href="keranjang.php" class="relative text-accent10 group flex items-center">
                <i class="fas fa-shopping-cart"></i>
                <span id="cartCount" class="absolute -top-2 -right-2.5 bg-accent10 text-bg60 text-xs font-bold px-1.5 py-0.5 rounded-full hidden">0</span>
            </a>
            <a href="profil.php" class="cursor-pointer hover:text-accent10 transition-colors flex items-center"><i class="fas fa-user-circle"></i></a>
        </div>
    </header>

    <div class="max-w-6xl mx-auto px-5 py-10 w-full flex-1 grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <h2 class="text-3xl font-bold mb-6">Keranjang Anda</h2>
            <div id="cartItemsContainer" class="space-y-4"></div>
            <div id="addMoreMenuBtn" class="mt-6 flex justify-end hidden">
                <button onclick="window.history.back()" class="py-2.5 px-6 bg-transparent border-2 border-accent10 text-accent10 font-bold rounded-md hover:bg-accent10 hover:text-bg60 transition-all shadow-lg flex items-center gap-2">
                    <i class="fas fa-plus"></i> Tambah Menu Lain
                </button>
            </div>
        </div>

        <div class="bg-bg30 p-6 rounded-xl h-fit sticky top-[100px]">
            <div class="mb-6 border-b border-bg60 pb-6">
                <h4 class="font-bold mb-3 text-sm uppercase tracking-wider text-textMuted flex items-center gap-2"><i class="fas fa-ticket-alt text-accent10"></i> Punya Kode Promo?</h4>
                <div class="flex gap-2">
                    <input type="text" id="promoInput" placeholder="Masukkan kode..." class="flex-1 bg-bg60 border border-textMuted/30 text-textLight rounded-md px-3 py-2 uppercase tracking-wider focus:outline-none focus:border-accent10">
                    <button onclick="applyPromo()" class="bg-accent10 text-bg60 font-bold px-4 py-2 rounded-md hover:bg-[#c09161] transition-all text-sm">Klaim</button>
                </div>
                <p id="promoMessage" class="text-xs mt-2 hidden"></p>
            </div>

            <h3 class="text-xl font-bold mb-4 border-b border-bg60 pb-3">Ringkasan Pesanan</h3>
            <div class="flex justify-between text-textMuted mb-2"><span>Subtotal</span><span id="subtotalDisplay">Rp 0</span></div>
            <div id="discountRow" class="flex justify-between text-green-400 mb-2 hidden"><span>Diskon (<span id="discountLabel"></span>)</span><span id="discountDisplay">-Rp 0</span></div>
            <div class="flex justify-between text-textMuted mb-4"><span>Biaya Layanan (10%)</span><span id="taxDisplay">Rp 0</span></div>
            <div class="flex justify-between text-xl font-bold text-accent10 mb-6 border-t border-bg60 pt-3"><span>Total Akhir</span><span id="totalDisplay">Rp 0</span></div>

            <h4 class="font-bold mb-3">Metode Pembayaran</h4>
            <div class="grid grid-cols-2 gap-3 mb-8">
                <label class="border border-textMuted/30 rounded-lg p-3 flex items-center gap-2 cursor-pointer hover:border-accent10 transition-colors"><input type="radio" name="payment" value="QRIS" checked class="accent-accent10"><i class="fas fa-qrcode text-accent10"></i> QRIS</label>
                <label class="border border-textMuted/30 rounded-lg p-3 flex items-center gap-2 cursor-pointer hover:border-accent10 transition-colors"><input type="radio" name="payment" value="GoPay" class="accent-accent10"><i class="fas fa-wallet text-blue-400"></i> GoPay</label>
                <label class="border border-textMuted/30 rounded-lg p-3 flex items-center gap-2 cursor-pointer hover:border-accent10 transition-colors"><input type="radio" name="payment" value="DANA" class="accent-accent10"><i class="fas fa-wallet text-blue-500"></i> DANA</label>
                <label class="border border-textMuted/30 rounded-lg p-3 flex items-center gap-2 cursor-pointer hover:border-accent10 transition-colors"><input type="radio" name="payment" value="M-Banking" class="accent-accent10"><i class="fas fa-university text-green-500"></i> M-Banking</label>
            </div>

            <button onclick="processPayment()" id="btnBayar" class="w-full py-3 bg-accent10 text-bg60 font-bold rounded-md hover:bg-[#c09161] transition-all text-lg shadow-lg">
                Bayar Sekarang
            </button>
        </div>
    </div>

    <div id="receiptModal" class="fixed inset-0 bg-black/80 z-[100] hidden flex items-center justify-center p-4 overflow-y-auto">
        <div class="w-full max-w-sm my-8 relative">
            <div id="printArea" class="bg-white text-black p-6 rounded-t-lg shadow-2xl font-mono text-sm border-b-4 border-gray-200 relative">
                <div class="text-center mb-4 no-print"><i class="fas fa-check-circle text-5xl text-green-500 bg-white rounded-full"></i><h3 class="font-bold text-lg mt-2 text-green-600">Pembayaran Berhasil!</h3></div>
                <div class="text-center border-b-2 border-dashed border-gray-400 pb-4 mb-4"><h2 class="text-xl font-bold uppercase tracking-wider">Salatiga Coffee Hub</h2><p class="text-xs text-gray-600 mt-1">Platform Direktori Cafe & Pemesanan</p><p class="text-xs text-gray-600">Lima Serangkai Foundation</p></div>
                <div class="mb-4 text-xs space-y-1"><div class="flex justify-between"><span>No. Order:</span> <span id="receiptOrderNo" class="font-bold"></span></div><div class="flex justify-between"><span>Tanggal:</span> <span id="receiptDate"></span></div><div class="flex justify-between"><span>Metode:</span> <span id="receiptMethod" class="font-bold uppercase"></span></div><div class="flex justify-between"><span>Status:</span> <span class="font-bold text-green-600">LUNAS</span></div></div>
                <div class="border-t-2 border-b-2 border-dashed border-gray-400 py-3 mb-4"><div class="font-bold mb-2 flex justify-between"><span>ITEM</span> <span>TOTAL</span></div><div id="receiptItems" class="space-y-2"></div></div>
                <div class="space-y-1 mb-4 text-xs"><div class="flex justify-between"><span>Subtotal</span> <span id="receiptSubtotal"></span></div><div id="receiptDiscountRow" class="flex justify-between hidden text-gray-600"><span>Diskon Promo</span> <span id="receiptDiscount"></span></div><div class="flex justify-between"><span>Biaya Layanan (10%)</span> <span id="receiptTax"></span></div></div>
                <div class="border-t-2 border-dashed border-gray-400 pt-3 flex justify-between font-bold text-lg"><span>TOTAL</span><span id="receiptTotal"></span></div>
                <div class="text-center mt-6 pt-4 border-t-2 border-dashed border-gray-400 text-xs text-gray-500"><p class="mb-1">Terima kasih telah mendukung UMKM lokal!</p><p>*** SALATIGA COFFEE HUB ***</p></div>
            </div>
            <div id="actionArea" class="bg-gray-100 p-5 rounded-b-lg no-print border-x border-b border-gray-300">
                <button onclick="window.print()" class="w-full py-2.5 mb-5 bg-blue-600 text-white font-bold rounded-md shadow hover:bg-blue-700 transition-colors flex justify-center items-center gap-2"><i class="fas fa-file-pdf"></i> Cetak Struk / Simpan PDF</button>
                <div class="text-center border-t border-gray-300 pt-4"><p class="text-sm font-bold text-gray-700 mb-3">Ingin Split Bill (Patungan)?</p>
                    <div class="flex gap-3 justify-center"><button onclick="showSplitBill()" class="flex-1 py-2 bg-accent10 text-bg60 font-bold rounded-md hover:bg-[#c09161]">Ya, Split Bill</button><button onclick="closeModal()" class="flex-1 py-2 bg-gray-300 text-gray-700 font-bold rounded-md hover:bg-gray-400">Tidak, Tutup</button></div>
                </div>
            </div>
            <div id="splitBillArea" class="bg-bg30 text-textLight p-6 rounded-b-lg hidden no-print">
                <h3 class="font-bold text-accent10 mb-2"><i class="fas fa-users"></i> Mode Patungan (Split Bill)</h3>
                <p class="text-sm text-textMuted mb-4 leading-relaxed">Silakan cetak struk (PDF) di atas, lalu upload di sini sebagai bukti.</p>
                <input type="file" accept=".pdf, image/*" id="uploadStruk" class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-bold file:bg-accent10 file:text-bg60 hover:file:bg-[#c09161] mb-4 cursor-pointer">
                <div class="flex gap-2 items-center mb-4"><label class="text-sm flex-1">Dibagi untuk berapa orang?</label><input type="number" id="splitCount" value="2" min="2" class="w-16 p-2 rounded bg-bg60 text-textLight text-center border border-accent10 focus:outline-none"></div>
                <button onclick="calculateSplit()" class="w-full py-2.5 bg-transparent border-2 border-accent10 text-accent10 font-bold rounded-md hover:bg-accent10 hover:text-bg60 transition-colors mt-2">Hitung Tagihan</button>
                <div id="splitResult" class="mt-5 text-center text-lg font-bold text-green-400 hidden bg-bg60 p-4 rounded-lg border border-green-400/30">Masing-masing bayar: <br><span class="text-3xl" id="splitAmount"></span></div>
                <button onclick="closeModal()" class="w-full mt-6 py-2 text-sm font-bold bg-gray-300 text-black rounded hover:bg-white transition-colors">Selesai & Tutup</button>
            </div>
        </div>
    </div>

    <footer class="bg-bg30 text-textLight px-[5%] py-6 mt-12 text-center text-sm text-textMuted border-t border-accent10/20">
        &copy; 2026 Lima Serangkai Foundation, Salatiga.
    </footer>

    <script>
        let cartData = JSON.parse(localStorage.getItem('cart')) || [];
        let subTotalRaw = 0, taxRaw = 0, grandTotal = 0;
        let activeDiscountName = "", activeDiscountAmount = 0;

        const promoDatabase = {
            "MABACOFFEE": { type: "percent", value: 0.25 },
            "SENJAKOPI": { type: "percent", value: 0.15 },
            "COFFEEHUB20": { type: "percent", value: 0.20 },
            "QRISHEMAT": { type: "flat", value: 10000 }
        };

        function renderCart() {
            const container = document.getElementById('cartItemsContainer');
            const addMoreBtn = document.getElementById('addMoreMenuBtn');
            container.innerHTML = '';
            
            if(cartData.length === 0) {
                container.innerHTML = '<div class="text-textMuted p-10 text-center bg-bg30 rounded-xl">Keranjang Anda masih kosong.</div>';
                addMoreBtn.classList.add('hidden');
                updateSummary();
                return;
            }

            addMoreBtn.classList.remove('hidden');
            cartData.forEach((item, index) => {
                container.innerHTML += `
                    <div class="bg-bg30 p-4 rounded-xl flex items-center gap-4 border border-transparent hover:border-accent10/30 transition-colors">
                        <img src="${item.img}" class="w-20 h-20 object-cover rounded-lg">
                        <div class="flex-1">
                            <h4 class="text-lg font-bold text-textLight line-clamp-1">${item.nama}</h4>
                            <p class="text-accent10 font-bold">Rp ${item.harga.toLocaleString('id-ID')}</p>
                        </div>
                        <div class="flex items-center gap-3 bg-bg60 px-3 py-1.5 rounded-lg border border-textMuted/20">
                            <button onclick="changeQty(${index}, -1)" class="text-textMuted hover:text-accent10"><i class="fas fa-minus"></i></button>
                            <span class="font-bold w-6 text-center">${item.qty}</span>
                            <button onclick="changeQty(${index}, 1)" class="text-textMuted hover:text-accent10"><i class="fas fa-plus"></i></button>
                        </div>
                        <button onclick="removeItem(${index})" class="text-red-500 hover:text-red-400 p-2 ml-2"><i class="fas fa-trash"></i></button>
                    </div>
                `;
            });
            updateSummary();
        }

        function changeQty(index, delta) { cartData[index].qty += delta; if(cartData[index].qty <= 0) cartData.splice(index, 1); saveAndRender(); }
        function removeItem(index) { cartData.splice(index, 1); saveAndRender(); }
        function saveAndRender() { localStorage.setItem('cart', JSON.stringify(cartData)); renderCart(); }

        function applyPromo() {
            const inputVal = document.getElementById('promoInput').value.trim().toUpperCase();
            const msgObj = document.getElementById('promoMessage');
            if (inputVal === "") { msgObj.innerText = "Masukkan kode promo."; msgObj.className = "text-xs mt-2 block text-red-400 font-bold"; return; }
            if (promoDatabase.hasOwnProperty(inputVal)) {
                msgObj.innerText = `✓ Kode "${inputVal}" berhasil diterapkan!`; msgObj.className = "text-xs mt-2 block text-green-400 font-bold"; activeDiscountName = inputVal; updateSummary();
            } else {
                msgObj.innerText = "✕ Kode promo tidak valid."; msgObj.className = "text-xs mt-2 block text-red-400 font-bold"; activeDiscountName = ""; updateSummary();
            }
        }

        function updateSummary() {
            subTotalRaw = cartData.reduce((sum, item) => sum + (item.harga * item.qty), 0);
            activeDiscountAmount = 0;
            if (activeDiscountName !== "") {
                const promo = promoDatabase[activeDiscountName];
                if (promo.type === "percent") activeDiscountAmount = subTotalRaw * promo.value;
                else if (promo.type === "flat") activeDiscountAmount = promo.value;
                if(activeDiscountAmount > subTotalRaw) activeDiscountAmount = subTotalRaw;
            }

            let hargaSetelahDiskon = subTotalRaw - activeDiscountAmount;
            taxRaw = hargaSetelahDiskon * 0.10;
            grandTotal = hargaSetelahDiskon + taxRaw;

            document.getElementById('subtotalDisplay').innerText = 'Rp ' + subTotalRaw.toLocaleString('id-ID');
            const discountRow = document.getElementById('discountRow');
            if (activeDiscountAmount > 0) {
                document.getElementById('discountLabel').innerText = activeDiscountName;
                document.getElementById('discountDisplay').innerText = '-Rp ' + activeDiscountAmount.toLocaleString('id-ID');
                discountRow.classList.remove('hidden');
            } else { discountRow.classList.add('hidden'); }
            
            document.getElementById('taxDisplay').innerText = 'Rp ' + taxRaw.toLocaleString('id-ID');
            document.getElementById('totalDisplay').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
        }

        // FUNGSI UPDATE: Kirim Data ke MySQL pakai Fetch API
        function processPayment() {
            if(cartData.length === 0) return alert("Keranjang kosong!");
            
            const btnBayar = document.getElementById('btnBayar');
            btnBayar.innerText = "Memproses...";
            btnBayar.disabled = true;

            const method = document.querySelector('input[name="payment"]:checked').value;
            const orderId = 'SCH-' + Math.floor(Math.random() * 90000) + 10000;

            const payload = {
                simpan_transaksi: true,
                order_id: orderId,
                total: grandTotal,
                metode: method,
                items: JSON.stringify(cartData) // Konversi array keranjang jadi string untuk disimpan
            };

            // Kirim request di belakang layar ke PHP di atas
            fetch('keranjang.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.text())
            .then(data => {
                // Jika MySQL berhasil menyimpan, render struk PDF
                document.getElementById('receiptOrderNo').innerText = orderId;
                document.getElementById('receiptDate').innerText = new Date().toLocaleString('id-ID');
                document.getElementById('receiptMethod').innerText = method;

                const receiptItems = document.getElementById('receiptItems');
                receiptItems.innerHTML = '';
                cartData.forEach(item => {
                    let sub = item.harga * item.qty;
                    receiptItems.innerHTML += `<div class="mb-2"><div class="font-semibold">${item.nama}</div><div class="flex justify-between text-gray-600 text-xs"><span>${item.qty} x Rp ${item.harga.toLocaleString('id-ID')}</span><span>Rp ${sub.toLocaleString('id-ID')}</span></div></div>`;
                });

                document.getElementById('receiptSubtotal').innerText = 'Rp ' + subTotalRaw.toLocaleString('id-ID');
                const receiptDiscountRow = document.getElementById('receiptDiscountRow');
                if (activeDiscountAmount > 0) {
                    document.getElementById('receiptDiscount').innerText = '-Rp ' + activeDiscountAmount.toLocaleString('id-ID');
                    receiptDiscountRow.classList.remove('hidden');
                } else { receiptDiscountRow.classList.add('hidden'); }

                document.getElementById('receiptTax').innerText = 'Rp ' + taxRaw.toLocaleString('id-ID');
                document.getElementById('receiptTotal').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');

                document.getElementById('actionArea').classList.remove('hidden');
                document.getElementById('splitBillArea').classList.add('hidden');
                document.getElementById('splitResult').classList.add('hidden');
                document.getElementById('uploadStruk').value = "";
                
                document.getElementById('receiptModal').classList.remove('hidden');
                
                // Kembalikan status tombol
                btnBayar.innerText = "Bayar Sekarang";
                btnBayar.disabled = false;
            })
            .catch(err => {
                alert("Terjadi kesalahan sistem: " + err);
                btnBayar.innerText = "Bayar Sekarang";
                btnBayar.disabled = false;
            });
        }

        function showSplitBill() { document.getElementById('actionArea').classList.add('hidden'); document.getElementById('splitBillArea').classList.remove('hidden'); }
        function calculateSplit() {
            const fileInput = document.getElementById('uploadStruk');
            if(!fileInput.files.length) return alert("Upload file struk terlebih dahulu!");
            const persons = document.getElementById('splitCount').value;
            if(persons < 2) return alert("Minimal dibagi 2 orang.");
            document.getElementById('splitAmount').innerText = 'Rp ' + Math.ceil(grandTotal / persons).toLocaleString('id-ID');
            document.getElementById('splitResult').classList.remove('hidden');
        }
        function closeModal() {
            cartData = [];
            localStorage.removeItem('cart');
            window.location.href = 'riwayat_pesanan.php'; // Otomatis arahkan ke Riwayat Pesanan
        }

        renderCart();
        // Tambahkan fungsi ini di bawah skrip yang sudah ada
        window.addEventListener('load', function() {
            // Cek apakah ada promo yang dibawa dari page diskon.php
            const pendingPromo = localStorage.getItem('pendingPromo');
            if (pendingPromo) {
                document.getElementById('promoInput').value = pendingPromo;
                applyPromo(); // Otomatis jalankan fungsi cek promo
                localStorage.removeItem('pendingPromo'); // Hapus agar tidak terus-terusan terpakai
            }
        });

        // Pastikan fungsi gunakanPromo di diskon.php sudah seperti ini:
        function gunakanPromo(kode) {
            localStorage.setItem('pendingPromo', kode);
            window.location.href = 'keranjang.php';
        }
    </script>
</body>
</html>