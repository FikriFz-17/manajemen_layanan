<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard Publik - Kominfo Kebumen</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  @vite('resources/css/app.css')
  <script src="https://unpkg.com/@fortawesome/fontawesome-free@6.4.0/js/all.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <style>
    /* Custom Swiper Styles */
    .laporan-swiper .swiper-button-prev:hover,
    .laporan-swiper .swiper-button-next:hover {
        transform: scale(1.05) !important;
        box-shadow: 0 8px 25px rgba(37, 99, 235, 0.25) !important;
    }

    /* Mengecilkan ukuran panah */
    .laporan-swiper .swiper-button-prev::after,
    .laporan-swiper .swiper-button-next::after {
        font-size: 12px !important;
        font-weight: 600 !important;
    }
  </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen">

    <!-- Header -->
    <header class="bg-white shadow-lg">
    <div class="container mx-auto px-4 py-6">
        <div class="flex flex-col md:flex-row items-center justify-between">
        <div class="flex items-center space-x-4 mb-4 md:mb-0">
            <img src="{{ asset('images/logo-kebumen.png') }}" alt="Logo Kebumen" class="w-20 h-20 object-contain">
            <div>
            <h1 class="text-2xl md:text-3xl font-bold text-blue-600">
                Sistem Informasi Manajemen Layanan TIK
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Diskominfo Kabupaten Kebumen</p>
            </div>
        </div>
        <div class="flex items-center space-x-2 text-sm text-gray-500">
            <i class="fas fa-clock animate-spin-slow"></i>
            <span id="currentTime" class="font-medium"></span>
        </div>
        </div>
    </div>
    </header>


  <div class="container mx-auto px-4 py-8">

    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-8 mb-8 text-white relative overflow-hidden">
      <div class="absolute top-0 right-0 w-32 h-32 bg-white/25 bg-opacity-10 rounded-full -mr-16 -mt-16"></div>
      <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/25 bg-opacity-10 rounded-full -ml-12 -mb-12"></div>
      <div class="relative z-10">
        <h2 class="text-2xl md:text-4xl font-bold mb-4 animate-pulse-slow">
          Pantau Perkembangan Laporan Anda
        </h2>
        <div class="flex flex-col sm:flex-row gap-4">
            <a href="{{route('login')}}" class="inline-flex items-center border-2 border-white text-white px-6 py-3 rounded-xl font-medium hover:bg-white hover:text-blue-600 transition-all duration-300 text-sm">
                    <i class="fas fa-plus-circle mr-2"></i>Buat Aduan Baru
            </a>
            <a href="{{ asset('storage/user_manual/Panduan Registrasi Pengguna.pdf') }}" download class="inline-flex items-center border-2 border-white text-white px-6 py-3 rounded-xl font-medium hover:bg-white hover:text-blue-600 transition-all duration-300 text-sm">
                <i class="fas fa-download mr-2"></i>Panduan Registrasi
            </a>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border-l-4 border-green-500">
        <div class="flex justify-between items-center">
          <div>
            <h3 class="text-3xl font-bold text-green-600 animate-bounce-slow" id="successCount">0</h3>
            <p class="text-gray-600 font-medium">Selesai</p>
          </div>
          <div class="bg-green-100 p-4 rounded-full">
            <i class="fas fa-check-circle text-green-500 text-2xl animate-pulse"></i>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border-l-4 border-yellow-500">
        <div class="flex justify-between items-center">
          <div>
            <h3 class="text-3xl font-bold text-yellow-600 animate-bounce-slow" id="progressCount">0</h3>
            <p class="text-gray-600 font-medium">Progress</p>
          </div>
          <div class="bg-yellow-100 p-4 rounded-full">
            <i class="fa-solid fa-bars-progress text-yellow-500 text-2xl animate-pulse"></i>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border-l-4 border-red-500">
        <div class="flex justify-between items-center">
          <div>
            <h3 class="text-3xl font-bold text-red-600 animate-bounce-slow" id="pengajuanCount">0</h3>
            <p class="text-gray-600 font-medium">Pengajuan</p>
          </div>
          <div class="bg-red-100 p-4 rounded-full">
            <i class="fa-solid fa-bullhorn text-red-500 text-2xl animate-pulse"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- statistik charts -->
    <div class="lg:flex lg:gap-4 mb-4">
    <!-- Wrapper untuk mobile swipe -->
    <div class="flex gap-4 overflow-x-auto scroll-smooth lg:overflow-visible lg:flex-1 w-full">

        <!-- Statistik Harian -->
        <div id="dailyChart" class="bg-white rounded shadow p-4 mb-4 w-[90vw] lg:w-1/2 shrink-0 lg:shrink lg:mb-0">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 mb-4">
            <h2 class="text-lg font-semibold">Statistik Aduan Harian</h2>
            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <select id="tahunHarianSelect"
                class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring w-full sm:w-auto">
            </select>
            <select id="bulanHarianSelect"
                class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring w-full sm:w-auto">
                <option value="0">Jan</option>
                <option value="1">Feb</option>
                <option value="2">Mar</option>
                <option value="3">Apr</option>
                <option value="4">Mei</option>
                <option value="5">Jun</option>
                <option value="6">Jul</option>
                <option value="7">Agu</option>
                <option value="8">Sep</option>
                <option value="9">Okt</option>
                <option value="10">Nov</option>
                <option value="11">Des</option>
            </select>
            </div>
        </div>
        <div id="dailyChartContainer" class="overflow-x-hidden overflow-y-hidden"></div>
        </div>

        <!-- Statistik Bulanan -->
        <div id="chart" class="bg-white rounded shadow p-4 mb-4 w-[90vw] lg:w-1/2 shrink-0 lg:shrink lg:mb-0">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 mb-4">
            <h2 class="text-lg font-semibold">Statistik Aduan Bulanan</h2>
            <select id="tahunChartSelect"
            class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring focus:border-blue-300 w-full sm:w-auto">
            </select>
        </div>
        <div id="chartContainer" class="overflow-x-auto"></div>
        </div>

    </div>
    </div>

    <!-- laporan terbaru -->
    <section class="relative bg-gray-50 py-10">
        <div class="container mx-auto px-4">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 mb-6 mx-3">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-800">
                Aduan Terbaru
            </h2>
            <a href="/semua/laporan" class="text-blue-600 hover:underline font-medium text-base sm:text-sm">
                Jelajah Aduan
            </a>
            </div>

            <div class="laporan-swiper swiper relative px-4">
            <!-- Wrapper untuk slide -->
            <div id="laporanCarousel" class="swiper-wrapper">
                <!-- Isi slide akan di-render otomatis oleh JS -->
            </div>

            <!-- Panah navigasi dengan styling yang lebih baik -->
            <div class="swiper-button-prev !w-10 !h-10 !bg-white !text-gray-800 !rounded-full !shadow-md hover:!bg-blue-600 hover:!text-white"></div>
            <div class="swiper-button-next !w-10 !h-10 !bg-white !text-gray-800 !rounded-full !shadow-md hover:!bg-blue-600 hover:!text-white"></div>

            <!-- Bullet pagination dengan styling custom -->
            <div class="swiper-pagination !relative !mt-8"></div>
            </div>
        </div>
    </section>

    <!-- informasi layanan-->
    <div class="bg-white rounded-xl p-6 mb-8 shadow-lg mt-4">
        <div class="flex items-center mb-4">
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-2 rounded-lg mr-3">
            <i class="fas fa-info-circle text-white"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800">Informasi Layanan</h3>
        </div>

        <!-- Wrapper scroll untuk mobile, grid untuk md+ -->
        <div class="overflow-x-auto scroll-smooth md:overflow-visible">
            <div class="flex md:grid md:grid-cols-2 lg:grid-cols-3 gap-4 w-max md:w-full">
                <!-- Jam Operasional -->
                <div class="p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg shrink-0 w-[80vw] md:w-auto">
                    <div class="flex flex-col items-center mb-3">
                        <i class="fas fa-clock text-blue-500 text-2xl"></i>
                        <p class="text-sm text-gray-600 mt-1">Jam Operasional</p>
                    </div>
                    <!-- Carousel scroll -->
                    <div class="flex gap-4 overflow-x-auto scroll-smooth">
                        <!-- Senin - Kamis -->
                        <div class="flex-shrink-0 w-48 bg-white rounded-lg p-3 shadow">
                        <p class="font-semibold text-gray-800 text-center">Senin - Kamis</p>
                        <p class="text-center text-gray-700">07:30 - 16:00 WIB</p>
                        </div>
                        <!-- Jumat -->
                        <div class="flex-shrink-0 w-48 bg-white rounded-lg p-3 shadow">
                        <p class="font-semibold text-gray-800 text-center">Jumat</p>
                        <p class="text-center text-gray-700">07:30 - 11:00 WIB</p>
                        </div>
                    </div>
                </div>

                <!-- Customer Service -->
                <div class="flex flex-col items-center text-center p-4 bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg shrink-0 w-[80vw] md:w-auto">
                    <i class="fas fa-headset text-green-500 text-2xl mb-2"></i>
                    <p class="text-sm text-gray-600">Customer Services</p>
                    <p class="font-semibold text-gray-800 mb-2">(0813) 2556-8441</p>
                    <a href="https://wa.me/6281325568441" target="_blank" class="inline-flex items-center gap-1 px-3 py-1 bg-green-500 text-white text-sm rounded hover:bg-green-600 transition">
                        <i class="fab fa-whatsapp"></i> Chat via WhatsApp
                    </a>
                </div>

                <!-- Email -->
                <div class="flex flex-col items-center text-center p-4 bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg shrink-0 w-[80vw] md:w-auto">
                    <i class="fas fa-envelope text-purple-500 text-2xl mb-2"></i>
                    <p class="text-sm text-gray-600">Email</p>
                    <p class="font-semibold text-gray-800 mb-2">kominfo@kebumenkab.go.id</p>
                    <a href="mailto:kominfo@kebumenkab.go.id" target="_blank" class="inline-flex items-center gap-1 px-3 py-1 bg-purple-500 text-white text-sm rounded hover:bg-purple-600 transition">
                        <i class="fas fa-paper-plane"></i> Kirim Email
                    </a>
                </div>
            </div>
        </div>
    </div>

  </div>

  <!-- Footer -->
  <footer class="bg-gray-800 text-white py-8 mt-12">
    <div class="container mx-auto px-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
          <h4 class="text-lg font-semibold mb-4">Kominfo Kebumen</h4>
          <p class="text-gray-300 text-sm">Melayani masyarakat dengan teknologi informasi terdepan untuk kemajuan Kabupaten Kebumen.</p>
        </div>
        <div>
          <h4 class="text-lg font-semibold mb-4">Kontak</h4>
          <div class="space-y-2 text-sm text-gray-300">
            <p><i class="fas fa-map-marker-alt mr-2"></i>Jalan K.H. Hasyim Asy’ari No. 6 Kebumen, Kodepos 54312</p>
            <p><i class="fas fa-phone mr-2"></i>(0813) 2556-8441</p>
            <p><i class="fas fa-envelope mr-2"></i>kominfo@kebumenkab.go.id</p>
          </div>
        </div>
        <div>
          <h4 class="text-lg font-semibold mb-4">Media Sosial</h4>
          <div class="flex space-x-4">
            <a href="https://www.facebook.com/profile.php?id=100015621966895&locale=id_ID" target="_blank" class="text-gray-300 hover:text-blue-400 transition-colors duration-300">
              <i class="fab fa-facebook text-2xl"></i>
            </a>
            <a href="https://x.com/diskominfokbm" target="_blank" class="text-gray-300 hover:text-blue-400 transition-colors duration-300">
              <i class="fab fa-twitter text-2xl"></i>
            </a>
            <a href="https://www.youtube.com/c/RatihTVNews" target="_blank" class="text-gray-300 hover:text-red-400 transition-colors duration-300">
              <i class="fab fa-youtube text-2xl"></i>
            </a>
            <a href="https://www.instagram.com/kominfo_kebumen/" target="_blank" class="text-gray-300 hover:text-pink-400 transition-colors duration-300">
              <i class="fab fa-instagram text-2xl"></i>
            </a>
          </div>
        </div>
      </div>
      <div class="border-t border-gray-700 mt-8 pt-4 text-center text-sm text-gray-400">
        <p>&copy; 2025 Dinas Komunikasi dan Informatika Kabupaten Kebumen</p>
      </div>
    </div>
  </footer>

  <!-- Script -->
  <script src="{{ asset('js/welcome.js') }}"></script>
</body>
</html>
