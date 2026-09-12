<?php
include 'koneksi.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: profil.php"); // Sukses, masuk ke profil
        exit;
    } else {
        echo "<script>alert('Email atau Password salah!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | Salatiga Coffee Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: { colors: { bg60: '#140F0A', bg30: '#2C1E16', accent10: '#D4A373', textLight: '#FDF8F5', textMuted: '#A89B91' } } } }
    </script>
</head>
<body class="bg-bg60 text-textLight min-h-screen flex items-center justify-center relative bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1497935586351-b67a49e012bf?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');">
    
    <div class="absolute inset-0 bg-bg60/80 backdrop-blur-sm"></div>

    <div class="relative z-10 bg-bg30/90 p-8 sm:p-10 rounded-2xl shadow-2xl w-full max-w-md border border-accent10/20 backdrop-blur-md m-4">
        
        <div class="text-center mb-8">
            <a href="home.php" class="text-3xl font-bold text-textLight flex items-center justify-center gap-2 mb-2 hover:scale-105 transition-transform">
                <i class="fas fa-mug-hot text-accent10"></i> 
                Salatiga <span class="text-accent10">Coffee</span>
            </a>
            <p class="text-textMuted text-sm">Selamat datang kembali! Silakan masuk ke akun Anda.</p>
        </div>

        <form action="login.php" method="POST" class="space-y-5">
            <div>
                <label class="block text-sm font-semibold mb-2 text-textLight">Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-textMuted">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <input type="email" name="email" required placeholder="nama@email.com" 
                           class="w-full bg-bg60/50 border border-textMuted/30 text-textLight rounded-lg pl-10 pr-4 py-3 focus:outline-none focus:border-accent10 transition-colors">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2 text-textLight">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-textMuted">
                        <i class="fas fa-lock"></i>
                    </div>
                    <input type="password" name="password" id="pwd" required placeholder="••••••••" 
                           class="w-full bg-bg60/50 border border-textMuted/30 text-textLight rounded-lg pl-10 pr-10 py-3 focus:outline-none focus:border-accent10 transition-colors">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-textMuted hover:text-accent10 transition-colors" onclick="togglePwd('pwd', this)">
                        <i class="fas fa-eye"></i>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" class="accent-accent10 w-4 h-4 rounded border-textMuted/30">
                    <span class="text-textMuted group-hover:text-textLight transition-colors">Ingat Saya</span>
                </label>
                <a href="lupa-password.php" class="text-accent10 hover:underline font-semibold">Lupa Password?</a>
            </div>

            <button type="submit" class="w-full py-3.5 mt-2 bg-accent10 text-bg60 font-bold rounded-lg hover:bg-[#c09161] transition-all shadow-[0_4px_15px_rgba(212,163,115,0.2)]">
                Masuk
            </button>
        </form>

        <div class="mt-8 text-center text-sm text-textMuted border-t border-bg60 pt-6">
            Belum punya akun? <a href="register.php" class="text-accent10 font-bold hover:underline">Daftar Sekarang</a>
        </div>
    </div>

    <script>
        function togglePwd(id, iconContainer) {
            const input = document.getElementById(id);
            const icon = iconContainer.querySelector('i');
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>