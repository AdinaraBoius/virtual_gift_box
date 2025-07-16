<?php
// Selalu mulai sesi di baris paling atas
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Virtual Gift Box</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen py-8 px-4">
    <div class="bg-white p-8 md:p-12 rounded-xl shadow-2xl w-full max-w-md">
        <div class="text-center mb-8">
            <a href="dashboard.php" class="flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                </svg>
                <span class="ml-2 text-2xl font-bold text-gray-800">VirtualGift</span>
            </a>
            <h2 class="text-2xl font-bold text-gray-800">Masuk ke Akun Anda</h2>
        </div>
        
        <div class="mb-4 text-center">
            <?php if (isset($_GET['error'])): ?>
                <div class="p-3 rounded-md text-sm bg-red-100 text-red-700">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['success'])): ?>
                <div class="p-3 rounded-md text-sm bg-green-100 text-green-700">
                    <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>
        </div>

        <form id="loginForm" action="login.php" method="POST" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Alamat Email</label>
                <div class="mt-1">
                    <input id="email" name="email" type="email" autocomplete="email" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" title="Masukkan email valid, misal: user@domain.com" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-pink-500 focus:border-pink-500 sm:text-sm" placeholder="anda@email.com" />
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <div class="mt-1">
                    <input id="password" name="password" type="password" autocomplete="current-password" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-pink-500 focus:border-pink-500 sm:text-sm" placeholder="Masukkan password" />
                </div>
            </div>

            <div>
                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-pink-500 hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition duration-300">
                    Masuk
                </button>
            </div>
        </form>

        <script>
            document.getElementById('email').addEventListener('input', function() {
                const regex = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i;
                if (!regex.test(this.value)) {
                    this.setCustomValidity("Email tidak valid");
                } else {
                    this.setCustomValidity("");
                }
            });
        </script>
        
        <p class="mt-8 text-center text-sm text-gray-600">
            Belum punya akun? <a href="register-form.php" class="font-medium text-pink-600 hover:text-pink-500">Daftar di sini</a>
        </p>
        <p class="mt-2 text-center text-sm text-gray-600">
            <a href="index.php" class="font-medium text-gray-500 hover:text-pink-500">Kembali ke Beranda</a>
        </p>
    </div>
</body>
</html>