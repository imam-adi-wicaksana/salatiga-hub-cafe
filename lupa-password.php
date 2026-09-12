<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | Salatiga Coffee Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: { colors: { bg60: '#140F0A', bg30: '#2C1E16', accent10: '#D4A373', textLight: '#FDF8F5', textMuted: '#A89B91' } } } }
    </script>
</head>
<body class="bg-bg60 text-textLight min-h-screen flex items-center justify-center relative bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1497935586351-b67a49e012bf?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');">
    
    <div class="absolute inset-0 bg-bg60/80 backdrop-blur-sm"></div>

    <div class="relative z-10 bg-bg30/90 p-8 sm:p-10 rounded-2xl shadow-2xl w-full max-w-md border border-accent10/20 backdrop-blur-md m-4 text-center">
        
        <div class="w-16 h-16 bg-bg60 rounded-full flex items-center justify-center text-accent10 text-3xl mx-auto mb-6 border border-accent10/30 shadow-lg">
            <i class="fas fa-key"></i>
        </div>

        <h1 class="text-2xl font-bold text-textLight mb-2">Lupa Password?</h1>
        <p class="text-textMuted text-sm mb-8 px-2">Jangan khawatir! Masukkan alamat email yang terdaftar, dan kami akan mengirimkan link untuk mereset password Anda.</p>

        <form action="login.php" method="GET" class="space-y-5 text-left">
            <div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-textMuted">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <input type="email" required placeholder="Masukkan email Anda" 
                           class="w-full bg-bg60/50 border border-textMuted/30 text-textLight rounded-lg pl-10 pr-4 py-3.5 focus:outline-none focus:border-accent10 transition-colors">
                </div>
            </div>

            <button type="button" onclick="alert('Link reset password telah dikirim ke email Anda!')" class="w-full py-3.5 bg-accent10 text-bg60 font-bold rounded-lg hover:bg-[#c09161] transition-all shadow-lg">
                Kirim Link Reset
            </button>
        </form>

        <div class="mt-8">
            <a href="login.php" class="text-textMuted hover:text-accent10 text-sm font-semibold transition-colors flex items-center justify-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali ke Halaman Login
            </a>
        </div>
    </div>

</body>
</html>