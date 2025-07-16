<?php
// Sesi harus dimulai di baris paling atas, sebelum tag HTML apapun.
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Virtual Gift Box - Kirim Kado Digital Spesial</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex-grow: 1;
        }
        /* Animasi sederhana untuk efek mengambang */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</head>
<body class="text-gray-800">

    <nav class="bg-white shadow-sm py-4 sticky top-0 z-50">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <a href="index.php" class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                </svg>
                <span class="ml-2 text-xl font-bold text-gray-800">VirtualGift</span>
            </a>
            <div>
                <?php if (isset($_SESSION['loggedin'])): ?>
                    <a href="dashboard.php" class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-full font-medium transition duration-300 text-sm sm:text-base">Dashboard</a>
                    <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-full font-medium transition duration-300 text-sm sm:text-base">Logout</a>
                <?php else: ?>
                    <a href="login-form.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-full font-medium transition duration-300 text-sm sm:text-base">Masuk</a>
                    <a href="register-form.php" class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-full font-medium transition duration-300 text-sm sm:text-base">Daftar Gratis</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <section class="py-16 md:py-24 bg-gradient-to-br from-pink-50 via-rose-50 to-fuchsia-100">
            <div class="container mx-auto px-4 flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 mb-10 md:mb-0 text-center md:text-left">
                    <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold text-gray-800 leading-tight mb-6">
                        Kirim <span class="text-pink-500">Kado Digital</span> Penuh Makna, Kapan Saja, Di Mana Saja!
                    </h1>
                    <p class="text-lg text-gray-600 mb-8">
                        Buat kado virtual berisi pesan, foto, video, atau kejutan spesial lainnya. Bagikan kode uniknya dan biarkan orang terkasih membuka kejutannya secara online!
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                        <a href="<?php echo isset($_SESSION['loggedin']) ? 'dashboard.php' : 'login-form.php'; ?>" class="bg-pink-500 hover:bg-pink-600 text-white px-8 py-3 rounded-full font-semibold text-lg transition duration-300 shadow-lg transform hover:scale-105">
                            🎁 Buat Kado Sekarang
                        </a>
                        <button id="punyaKodeBtn" class="border-2 border-pink-500 text-pink-500 hover:bg-pink-50 hover:text-pink-600 px-8 py-3 rounded-full font-semibold text-lg transition duration-300">
                            🔑 Punya Kode Kado?
                        </button>
                    </div>
                </div>
                <div class="md:w-1/2 flex justify-center items-center mt-10 md:mt-0">
                    <div class="relative gift-box animate-float">
                        <img src="https://placehold.co/400x400/ec4899/ffffff?text=Kado+Istimewa" alt="Ilustrasi Kotak Kado Virtual" class="rounded-xl shadow-2xl w-64 h-64 sm:w-80 sm:h-80 md:w-96 md:h-96 object-cover" onerror="this.onerror=null;this.src='https://placehold.co/400x400/cccccc/ffffff?text=Gagal+Muat';"/>
                        <div class="absolute -bottom-4 -right-4 sm:-bottom-6 sm:-right-6 bg-yellow-400 text-gray-800 px-3 py-1 sm:px-4 sm:py-2 rounded-full font-bold shadow-md text-sm sm:text-base">
                            Spesial!
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div id="kodeKadoModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 overflow-y-auto h-full w-full flex items-center justify-center hidden z-50 p-4 transition-opacity duration-300 ease-in-out">
            <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md transform transition-all duration-300 ease-in-out scale-95 opacity-0" id="modalContent">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-semibold text-gray-800">Buka Kado Virtual Anda</h3>
                    <button id="closeModalBtn" class="text-gray-500 hover:text-gray-800 text-3xl leading-none">&times;</button>
                </div>
                <form id="bukaKadoForm">
                    <div>
                        <label for="kodeUnik" class="block text-sm font-medium text-gray-700 mb-1">Masukkan Kode Unik Kado:</label>
                        <input type="text" id="kodeUnik" name="kodeUnik" required class="appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 sm:text-sm" placeholder="Contoh: KADO123XYZ" />
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-pink-500 hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition duration-300">
                            Buka Kado
                        </button>
                    </div>
                </form>
                <div id="modalMessage" class="mt-4 text-sm"></div>
            </div>
        </div>

        <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
          <h2 class="text-3xl font-bold text-center text-gray-800 mb-16">
            Mengapa Pilih Kado Virtual?
          </h2>
          <div class="grid md:grid-cols-3 gap-8">
            <div
              class="bg-gray-50 p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 transform hover:-translate-y-1"
            >
              <div
                class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mb-6 mx-auto"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-8 w-8 text-pink-500"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13 10V3L4 14h7v7l9-11h-7z"
                  />
                </svg>
              </div>
              <h3 class="text-xl font-semibold mb-2 text-gray-800 text-center">
                Cepat & Praktis
              </h3>
              <p class="text-gray-600 text-center">
                Buat dalam hitungan menit, kirim langsung via WhatsApp, email,
                atau media sosial.
              </p>
            </div>
            <div
              class="bg-gray-50 p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 transform hover:-translate-y-1"
            >
              <div
                class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mb-6 mx-auto"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-8 w-8 text-purple-500"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>
              <h3 class="text-xl font-semibold mb-2 text-gray-800 text-center">
                Interaktif & Personal
              </h3>
              <p class="text-gray-600 text-center">
                Sertakan pesan suara, video, musik, atau galeri foto untuk
                sentuhan personal.
              </p>
            </div>
            <div
              class="bg-gray-50 p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 transform hover:-translate-y-1"
            >
              <div
                class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-6 mx-auto"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-8 w-8 text-blue-500"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>
              <h3 class="text-xl font-semibold mb-2 text-gray-800 text-center">
                Gratis & Ramah Lingkungan
              </h3>
              <p class="text-gray-600 text-center">
                100% gratis untuk fitur dasar. Mengurangi limbah fisik dan lebih
                peduli lingkungan.
              </p>
            </div>
          </div>
        </div>
      </section>

      <section class="py-16 bg-gray-100">
        <div class="container mx-auto px-4">
          <h2 class="text-3xl font-bold text-center text-gray-800 mb-16">
            Cara Kerjanya Mudah!
          </h2>
          <div class="grid md:grid-cols-3 gap-8 text-center">
            <div class="flex flex-col items-center">
              <div
                class="w-24 h-24 bg-pink-500 rounded-full flex items-center justify-center mb-4 text-white text-3xl font-bold shadow-md"
              >
                1
              </div>
              <h3 class="text-xl font-semibold mb-2 text-gray-800">
                Buat Kado
              </h3>
              <p class="text-gray-600 px-4">
                Pilih jenis kado, isi pesan, upload foto/video, atau tambahkan
                link spesial.
              </p>
            </div>
            <div class="flex flex-col items-center">
              <div
                class="w-24 h-24 bg-purple-500 rounded-full flex items-center justify-center mb-4 text-white text-3xl font-bold shadow-md"
              >
                2
              </div>
              <h3 class="text-xl font-semibold mb-2 text-gray-800">
                Bagikan Kode
              </h3>
              <p class="text-gray-600 px-4">
                Dapatkan kode unik kado Anda dan kirimkan ke teman atau
                keluarga.
              </p>
            </div>
            <div class="flex flex-col items-center">
              <div
                class="w-24 h-24 bg-blue-500 rounded-full flex items-center justify-center mb-4 text-white text-3xl font-bold shadow-md"
              >
                3
              </div>
              <h3 class="text-xl font-semibold mb-2 text-gray-800">
                Buka Kado
              </h3>
              <p class="text-gray-600 px-4">
                Penerima memasukkan kode unik dan langsung menikmati kejutan
                virtualnya!
              </p>
            </div>
          </div>
        </div>
      </section>

      <section class="py-16 bg-pink-500 text-white">
        <div class="container mx-auto px-4 text-center">
          <h2 class="text-3xl font-bold mb-6">
            Siap Membuat Kejutan Spesial Hari Ini?
          </h2>
          <p class="text-xl mb-8 max-w-2xl mx-auto">
            Bergabung dengan ribuan orang yang sudah mengirimkan kebahagiaan dan
            momen tak terlupakan melalui VirtualGift.
          </p>
          <a
            href="form_kado.php"
            class="bg-white text-pink-600 hover:bg-gray-100 px-10 py-4 rounded-full font-bold text-lg shadow-xl transition duration-300 transform hover:scale-105 inline-block"
          >
            Mulai Sekarang - Gratis!
          </a>
        </div>
      </section>
    </div>

    <footer class="bg-gray-800 text-white py-8 text-center">
      <div class="container mx-auto px-4">
        <p>
          &copy; <span id="currentYear"></span> VirtualGift. Semua Hak Cipta
          Dilindungi.
        </p>
        <p class="text-sm mt-2">Dibuat dengan ❤️ untuk momen spesial Anda.</p>
      </div>
    </footer>

    <script>
      document.addEventListener("DOMContentLoaded", () => {
        document.getElementById("currentYear").textContent =
          new Date().getFullYear();

        const punyaKodeBtn = document.getElementById("punyaKodeBtn");
        const kodeKadoModal = document.getElementById("kodeKadoModal");
        const closeModalBtn = document.getElementById("closeModalBtn");
        const bukaKadoForm = document.getElementById("bukaKadoForm");
        const modalMessage = document.getElementById("modalMessage");
        const modalContent = document.getElementById("modalContent");

        if (punyaKodeBtn) {
          punyaKodeBtn.addEventListener("click", () => {
            kodeKadoModal.classList.remove("hidden");
            setTimeout(() => {
              modalContent.classList.remove("opacity-0", "scale-95");
              modalContent.classList.add("opacity-100", "scale-100");
            }, 10);
            modalMessage.textContent = "";
            modalMessage.className = "mt-4 text-sm";
            document.getElementById("kodeUnik").value = ""; // Bersihkan input kode
          });
        }

        function closeModal() {
          modalContent.classList.add("opacity-0", "scale-95");
          setTimeout(() => {
            kodeKadoModal.classList.add("hidden");
          }, 300);
        }

        if (closeModalBtn) {
          closeModalBtn.addEventListener("click", closeModal);
        }

        if (kodeKadoModal) {
          kodeKadoModal.addEventListener("click", (event) => {
            if (event.target === kodeKadoModal) {
              closeModal();
            }
          });
        }

        if (bukaKadoForm) {
            bukaKadoForm.addEventListener("submit", (event) => {
                event.preventDefault(); // Tetap cegah submit form default
                const kode = document.getElementById("kodeUnik").value;
                modalMessage.textContent = ""; // Bersihkan pesan sebelumnya
                modalMessage.className = "mt-4 text-sm"; // Reset class pesan

                if (!kode.trim()) {
                    modalMessage.textContent = "Kode tidak boleh kosong.";
                    modalMessage.classList.add("text-red-600");
                    return;
                }

                // Alihkan ke halaman view_gift_page.php dengan kode sebagai parameter GET
                // Ini akan membuat URL seperti: view_gift_page.php?kode=KADO123XYZ
                window.location.href = `view_gift_page.php?code=${encodeURIComponent(kode.trim())}`;
            });
        }
        });
    </script>
  </body>
</html>