<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard Publik - Kominfo Kebumen</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/@fortawesome/fontawesome-free@6.4.0/js/all.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          animation: {
            'bounce-slow': 'bounce 3s infinite',
            'pulse-slow': 'pulse 3s infinite',
            'spin-slow': 'spin 8s linear infinite',
          }
        }
      }
    }
  </script>
</head>
<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen">

  <!-- Header -->
  <header class="bg-white shadow-lg border-b-4 border-gradient-to-r from-blue-500 to-purple-600">
    <div class="container mx-auto px-4 py-6">
      <div class="flex flex-col md:flex-row items-center justify-between">
        <div class="flex items-center space-x-4 mb-4 md:mb-0">
          <img src="{{ asset('images/logo-kebumen.png') }}" alt="Logo Kebumen" class="w-20 h-20 object-contain">
          <div>
            <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
              Dashboard Publik
            </h1>
            <p class="text-gray-600 text-sm md:text-base">Kominfo Kebumen - Pantau Status Laporan Anda</p>
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
    <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 rounded-2xl p-8 mb-8 text-white relative overflow-hidden">
      <div class="absolute top-0 right-0 w-32 h-32 bg-white bg-opacity-10 rounded-full -mr-16 -mt-16"></div>
      <div class="absolute bottom-0 left-0 w-24 h-24 bg-white bg-opacity-10 rounded-full -ml-12 -mb-12"></div>
      <div class="relative z-10">
        <h2 class="text-2xl md:text-4xl font-bold mb-4 animate-pulse-slow">
          Selamat Datang di Portal Layanan Digital
        </h2>
        <p class="text-lg md:text-xl mb-6 opacity-90">
          Pantau perkembangan laporan dan layanan Anda
        </p>
        <div class="flex flex-col sm:flex-row gap-4">
          <button class="border-2 border-white text-white px-4 md:px-3 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-all duration-300">
            <a href="{{route('login')}}" class="flex items-center">
                <i class="fas fa-plus-circle mr-2"></i>Buat Laporan Baru
            </a>
          </button>
          <button class="border-2 border-white text-white px-4 sm:px- md:px-3 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-all duration-300">
            <a href="{{ asset('storage/user_manual/Panduan Registrasi Pengguna.pdf') }}" download class="flex items-center">
                <i class="fa-solid fa-file-arrow-down mr-2"></i>Panduan Registrasi
            </a>
          </button>
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
    <div class="flex flex-col lg:flex-row gap-4">
        <!-- Statistik Harian -->
        <div id="dailyChart" class="bg-white rounded shadow p-4 mb-4 w-full lg:w-1/2">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 mb-4">
            <h2 class="text-lg font-semibold">Statistik Laporan Harian</h2>
            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                <select id="tahunHarianSelect"
                class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring w-full sm:w-auto">
                <!-- Tahun akan diisi lewat JS -->
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
        <div id="chart" class="bg-white rounded shadow p-4 mb-4 w-full lg:w-1/2">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 mb-4">
            <h2 class="text-lg font-semibold">Statistik Laporan Bulanan</h2>
            <select id="tahunChartSelect"
                class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring focus:border-blue-300 w-full sm:w-auto">
                <!-- Tahun diisi via JS -->
            </select>
            </div>
            <div id="chartContainer" class="overflow-x-auto"></div>
        </div>
    </div>

    <!-- Quick Info -->
    <div class="bg-white rounded-xl p-6 mb-8 shadow-lg">
      <div class="flex items-center mb-4">
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-2 rounded-lg mr-3">
          <i class="fas fa-info-circle text-white"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800">Informasi Layanan</h3>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg">
          <i class="fas fa-clock text-blue-500 text-2xl mb-2"></i>
          <p class="text-sm text-gray-600">Jam Operasional</p>
          <p class="font-semibold text-gray-800">07:30 - 16:00 WIB</p>
        </div>
        <div class="text-center p-4 bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg">
            <a href="#">
                <i class="fas fa-headset text-green-500 text-2xl mb-2"></i>
                <p class="text-sm text-gray-600">Customer Services</p>
                <p class="font-semibold text-gray-800">(0287) 123-456</p>
            </a>
        </div>
        <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg">
            <a href="#">
                <i class="fas fa-envelope text-purple-500 text-2xl mb-2"></i>
                <p class="text-sm text-gray-600">Email</p>
                <p class="font-semibold text-gray-800">kominfo@kebumenkab.go.id</p>
            </a>
        </div>
      </div>
    </div>

    <!-- Search & Filter -->
    <div class="flex flex-col sm:flex-row gap-4 sm:justify-between sm:items-center mb-4">
        <div class="relative w-full sm:w-1/3 lg:w-1/4">
            <input type="text" id="searchInput" placeholder="Cari Laporan..."
                    class="border px-4 py-2 rounded w-full pr-10">
            <i class="fa-solid fa-magnifying-glass absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500"></i>
        </div>

        <div class="flex flex-col sm:flex-row gap-2">
            <select id="filterStatus" class="border px-3 py-2 rounded w-full sm:w-auto">
                <option value="">Semua Status</option>
                <option value="Pengajuan">Pengajuan</option>
                <option value="Progress">Progress</option>
                <option value="Selesai">Selesai</option>
            </select>

            <select id="showEntries" class="border px-3 py-2 rounded w-full sm:w-auto">
                <option value="5">Show 5 entries</option>
                <option value="10">Show 10 entries</option>
                <option value="25">Show 25 entries</option>
                <option value="50">Show 50 entries</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-auto bg-white shadow rounded">
        <table class="min-w-full text-center border">
            <thead class="bg-black text-white">
                <tr>
                    <th class="px-2 lg:px-4 py-2 text-sm lg:text-base">No Resi</th>
                    <th class="px-2 lg:px-4 py-2 text-sm lg:text-base">Masalah</th>
                    <th class="px-2 lg:px-4 py-2 text-sm lg:text-base">Tanggal</th>
                    <th class="px-2 lg:px-4 py-2 text-sm lg:text-base">Status</th>
                </tr>
            </thead>
            <tbody id="laporanTable">
                <!-- Data will be populated by JavaScript -->
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4 bg-white rounded-xl p-4 shadow-lg">
      <div class="text-sm text-gray-600">
        <span id="paginationInfo">Menampilkan 1 hingga 10 dari 15 entri</span>
      </div>
      <div class="flex space-x-1" id="paginationButtons">
        <!-- Pagination buttons akan diisi oleh JavaScript -->
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
            <p><i class="fas fa-phone mr-2"></i>(0287) 123-456</p>
            <p><i class="fas fa-envelope mr-2"></i>info@kebumen.go.id</p>
            <p><i class="fas fa-headset mr-2"></i>Customer Service</p>
          </div>
        </div>
        <div>
          <h4 class="text-lg font-semibold mb-4">Media Sosial</h4>
          <div class="flex space-x-4">
            <a href="#" class="text-gray-300 hover:text-blue-400 transition-colors duration-300">
              <i class="fab fa-facebook text-2xl"></i>
            </a>
            <a href="#" class="text-gray-300 hover:text-blue-400 transition-colors duration-300">
              <i class="fab fa-twitter text-2xl"></i>
            </a>
            <a href="#" class="text-gray-300 hover:text-red-400 transition-colors duration-300">
              <i class="fab fa-youtube text-2xl"></i>
            </a>
            <a href="#" class="text-gray-300 hover:text-pink-400 transition-colors duration-300">
              <i class="fab fa-instagram text-2xl"></i>
            </a>
          </div>
        </div>
      </div>
      <div class="border-t border-gray-700 mt-8 pt-4 text-center text-sm text-gray-400">
        <p>&copy; 2024 Kominfo Kebumen. Semua hak dilindungi undang-undang.</p>
      </div>
    </div>
  </footer>

  <!-- Script -->
  <script src="{{ asset('js/welcome.js') }}"></script>
</body>
</html>
