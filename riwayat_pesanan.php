<?php
include 'koneksi.php';
session_start();

// Pengecekan Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Mengambil Data Dinamis dari Database MySQL
$id = $_SESSION['user_id'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id'");
$user = mysqli_fetch_assoc($query);

// Nilai Bawaan (Menghindari Error jika kolom belum dibuat di tabel)
$user['poin'] = $user['poin'] ?? 0; 
$user['pesanan_selesai'] = $user['pesanan_selesai'] ?? 0;
$user['member_sejak'] = $user['member_sejak'] ?? 'Juni 2026';

// Mengambil data Riwayat Transaksi langsung dari database untuk user ini
$query_transaksi = mysqli_query($conn, "SELECT * FROM transaksi WHERE user_id = '$id' ORDER BY tanggal DESC");
$riwayat = [];
while($row = mysqli_fetch_assoc($query_transaksi)) {
    $riwayat[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Pesanan | Salatiga Coffee Hub</title>
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
                    <span class="bg-bg60 px-4 py-1.5 rounded-lg border border-accent10/30 text-sm">Member Sejak: <?= htmlspecialchars($user['member_sejak']) ?></span>
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
                <a href="profil.php" class="w-full text-left p-4 hover:bg-bg30 rounded-lg transition-colors block">
                    <i class="fas fa-user mr-2"></i> Profil
                </a>
                <a href="riwayat_pesanan.php" class="w-full text-left p-4 bg-accent10 text-bg60 font-bold rounded-lg block">
                    <i class="fas fa-receipt mr-2"></i> Riwayat Pesanan
                </a>
                <a href="voucher_saya.php" class="w-full text-left p-4 hover:bg-bg30 rounded-lg transition-colors block">
                    <i class="fas fa-ticket-alt mr-2"></i> Voucher Saya
                </a>
                <a href="logout.php" class="w-full text-left p-4 hover:bg-bg30 rounded-lg transition-colors text-red-400 block">
                    <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                </a>
            </div>

            <div class="md:col-span-3 space-y-4">
                <h2 class="text-2xl font-bold mb-6">Riwayat Pesanan</h2>
                
                <?php if(count($riwayat) == 0): ?>
                    <div class="text-textMuted p-10 text-center bg-bg30 rounded-xl border border-accent10/10">
                        Belum ada riwayat pesanan. Yuk eksplor cafe sekarang!
                    </div>
                <?php endif; ?>

                <?php foreach($riwayat as $order): 
                    // Mengambil 1 nama item dari struktur JSON untuk dijadikan judul
                    $items_arr = json_decode($order['detail_items'], true);
                    $judul_pesanan = (count($items_arr) > 0) ? $items_arr[0]['nama'] : "Pesanan Cafe";
                    if(count($items_arr) > 1) {
                        $judul_pesanan .= " (+ " . (count($items_arr)-1) . " menu lain)";
                    }
                ?>
                <div class="bg-bg30 p-6 rounded-xl border border-accent10/10 flex flex-col md:flex-row justify-between items-center gap-4 hover:border-accent10/30 transition-all">
                    <div class="flex items-center gap-4 w-full md:w-auto">
                        <div class="w-12 h-12 bg-bg60 rounded-lg flex items-center justify-center text-accent10 border border-accent10/20">
                            <i class="fas fa-coffee"></i>
                        </div>
                        <div>
                            <div class="font-bold text-textLight line-clamp-1"><?= htmlspecialchars($judul_pesanan) ?></div>
                            <div class="text-xs text-textMuted mt-1">
                                <?= $order['order_id'] ?> &bull; <?= date('d M Y, H:i', strtotime($order['tanggal'])) ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center w-full md:w-auto md:text-right">
                        <div class="font-bold text-accent10 mb-1">Rp <?= number_format($order['total'], 0, ',', '.') ?></div>
                        <div class="text-xs text-textMuted font-mono">Via <?= $order['metode_pembayaran'] ?></div>
                    </div>
                    
                    <div>
                        <?php if($order['status'] == 'Selesai'): ?>
                            <span class="px-3 py-1 bg-green-900/20 text-green-400 text-xs font-bold rounded-full border border-green-900/50">Selesai</span>
                        <?php else: ?>
                            <span class="px-3 py-1 bg-red-900/20 text-red-400 text-xs font-bold rounded-full border border-red-900/50">Dibatalkan</span>
                        <?php endif; ?>
                    </div>
                    
                    <button type="button" 
                        class="btn-detail px-4 py-2 bg-bg60 border border-accent10/20 text-xs rounded hover:bg-accent10 hover:text-bg60 transition-all font-bold"
                        data-id="<?= htmlspecialchars($order['order_id']) ?>"
                        data-date="<?= htmlspecialchars(date('d M Y, H:i', strtotime($order['tanggal']))) ?>"
                        data-method="<?= htmlspecialchars($order['metode_pembayaran']) ?>"
                        data-items='<?= htmlspecialchars($order['detail_items']) ?>'
                        data-total="<?= number_format($order['total'], 0, ',', '.') ?>">
                        Detail
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <footer class="bg-bg30 text-textLight px-[5%] py-6 mt-12 text-center text-sm text-textMuted border-t border-accent10/20">
        &copy; 2026 Lima Serangkai Foundation, Salatiga.
    </footer>

    <div id="receiptModal" class="fixed inset-0 bg-black/80 z-[100] hidden flex items-center justify-center p-4">
        <div class="w-full max-w-sm bg-white text-black p-6 rounded-lg shadow-2xl font-mono text-sm border-b-4 border-gray-200">
            <div class="text-center border-b-2 border-dashed border-gray-400 pb-4 mb-4">
                <h2 class="text-xl font-bold uppercase">Salatiga Coffee Hub</h2>
            </div>
            <div class="mb-4 text-xs space-y-1">
                <div class="flex justify-between"><span>No. Order:</span> <span id="modalOrderId" class="font-bold"></span></div>
                <div class="flex justify-between"><span>Tanggal:</span> <span id="modalDate"></span></div>
                <div class="flex justify-between"><span>Metode:</span> <span id="modalMethod" class="font-bold uppercase"></span></div>
            </div>
            <div class="border-t-2 border-b-2 border-dashed border-gray-400 py-3 mb-4">
                <div class="font-bold mb-2 flex justify-between"><span>ITEM</span> <span>TOTAL</span></div>
                <div id="modalItems" class="space-y-2"></div>
            </div>
            <div class="flex justify-between font-bold text-lg">
                <span>TOTAL</span>
                <span id="modalTotal"></span>
            </div>
            <button onclick="document.getElementById('receiptModal').classList.add('hidden')" class="w-full mt-6 py-2 bg-gray-200 hover:bg-gray-300 font-bold rounded">Tutup</button>
        </div>
    </div>

    <script>
    // 1. Ambil semua tombol detail
    const detailButtons = document.querySelectorAll('.btn-detail');

    // 2. Tambahkan event listener ke setiap tombol
    detailButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Ambil data dari atribut HTML
            const id = this.getAttribute('data-id');
            const date = this.getAttribute('data-date');
            const method = this.getAttribute('data-method');
            const total = this.getAttribute('data-total');
            const items = JSON.parse(this.getAttribute('data-items'));

            // Panggil fungsi showDetail
            openModalDetail(id, date, method, items, total);
        });
    });

    // 3. Modifikasi fungsi showDetail agar menerima array items (bukan JSON string)
    function openModalDetail(id, date, method, items, total) {
        document.getElementById('modalOrderId').innerText = id;
        document.getElementById('modalDate').innerText = date;
        document.getElementById('modalMethod').innerText = method;
        document.getElementById('modalTotal').innerText = 'Rp ' + total;

        const itemContainer = document.getElementById('modalItems');
        itemContainer.innerHTML = '';

        // items sudah berupa array/object, jadi tidak perlu JSON.parse lagi
        items.forEach(item => {
            let sub = item.harga * item.qty;
            itemContainer.innerHTML += `
                <div class="flex justify-between text-xs">
                    <span>${item.qty}x ${item.nama}</span>
                    <span>Rp ${sub.toLocaleString('id-ID')}</span>
                </div>
            `;
        });

        document.getElementById('receiptModal').classList.remove('hidden');
    }
    </script>
</body>
</html>