<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard - Kominfo Kebumen</title>
  @vite('resources/css/app.css')
  <script src="https://unpkg.com/@fortawesome/fontawesome-free@6.4.0/js/all.min.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 h-screen flex">
  <!-- Mobile Overlay -->
  <div id="mobileOverlay" class="fixed inset-0 bg-black/50 bg-opacity-50 z-40 hidden lg:hidden"></div>

  <!-- Sidebar -->
  <div id="sidebar" class="w-64 bg-[#262394] text-white flex flex-col p-6 fixed lg:relative h-full z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
    <img src="{{ asset('images/logo-kebumen.png') }}" alt="Logo" class="w-20 mx-auto mb-4">
    <h1 class="text-lg font-bold text-center mb-8">Diskominfo Kebumen</h1>
    <nav class="flex flex-col gap-4">
      <a href="{{ route('dashboard') }}" class="flex items-center gap-2 hover:text-gray-300 p-2 rounded transition-colors bg-white/20 bg-opacity-20"><i class="fas fa-home"></i> Dashboard</a>
      <a href="{{ route('ajukanLaporan') }}" class="flex items-center gap-2 hover:text-gray-300 p-2 rounded transition-colors"><i class="fas fa-plus-circle"></i> Ajukan Laporan</a>
      <a href="{{ asset('storage/user_manual/Panduan Pengajuan Pengguna.pdf') }}" download="" class="flex items-center gap-2 hover:text-gray-300 p-2 rounded transition-colors"><i class="fa-solid fa-file-arrow-down"></i> User Manual</a>

      <!-- Customer Service Section -->
        <div class="mt-auto pt-4 border-t border-white/20 border-opacity-20">
            <div class="flex items-center gap-2 p-2 rounded transition-colors hover:bg-white/20 hover:bg-opacity-10 cursor-pointer">
                <div class="relative">
                    <i class="fas fa-headset text-xl"></i>
                    <span class="absolute top-0 right-0 w-2 h-2 bg-green-500 rounded-full"></span>
                </div>
                <span>Customer Service</span>
            </div>
        </div>
    </nav>
  </div>

  <!-- Main Content -->
  <div class="flex-1 lg:ml-0 p-4 lg:p-6 overflow-auto">

    <!-- Mobile Header with Hamburger -->
    <div class="lg:hidden flex justify-between items-center mb-6 bg-white p-4 rounded shadow">
      <button id="hamburgerBtn" class="text-2xl text-[#262394] focus:outline-none">
        <i class="fas fa-bars"></i>
      </button>
      <h1 class="text-xl font-bold text-[#262394]">Dashboard</h1>
      <div class="relative inline-block text-left">
        <button onclick="toggleDropdown()" class="flex items-center gap-2 focus:outline-none">
          <i class="fas fa-user-circle text-2xl text-[#262394]"></i>
          <i class="fas fa-chevron-down text-sm text-[#262394]"></i>
        </button>
        <!-- Mobile Dropdown Menu -->
        <div id="userDropdown" class="absolute right-0 mt-2 w-40 bg-white rounded shadow hidden z-20 border">
            <!-- set Profile -->
            <a href="{{ route('setProfile', ['return_to' => url()->current()]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fa-solid fa-user mr-2"></i>Set Profile</a>
            <!-- logout -->
            <form action="{{ route('logout.submit') }}" method="post">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-100"><i class="fas fa-sign-out-alt mr-2"></i>Logout</button>
            </form>
        </div>
      </div>
    </div>

    <!-- Desktop Topbar with Username Dropdown -->
    <div class="hidden lg:flex justify-end mb-6 relative">
      <div class="relative inline-block text-left">
        <button onclick="toggleDropdown()" class="flex items-center gap-2 focus:outline-none">
          <i class="fas fa-user-circle text-2xl"></i>
          <span class="font-medium">{{ Auth::user()->nama }}</span>
          <i class="fas fa-chevron-down text-sm"></i>
        </button>

        <!-- Desktop Dropdown Menu -->
        <div id="userDropdownDesktop" class="absolute right-0 mt-2 w-40 bg-white rounded shadow hidden z-20">
          <!-- set Profile -->
          <a href="{{ route('setProfile', ['return_to' => url()->current()]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fa-solid fa-user mr-2"></i>Set Profile</a>
          <!-- logout -->
          <form action="{{ route('logout.submit') }}" method="post">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-100"><i class="fas fa-sign-out-alt mr-2"></i>Logout</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6 lg:mb-8">
        <!-- selesai -->
      <div class="bg-green-500 text-white p-4 lg:p-6 rounded shadow">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl lg:text-3xl font-bold" id="successCount">0</h2>
                    <p class="font-medium">Selesai</p>
                </div>
                <i class="fa-solid fa-circle-check text-3xl lg:text-4xl"></i>
            </div>
        </div>
        <!-- progress -->
      <div class="bg-yellow-500 text-white p-4 lg:p-6 rounded shadow">
        <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl lg:text-3xl font-bold" id="progressCount">0</h2>
                    <p class="font-medium">Progress</p>
                </div>
                <i class="fa-solid fa-bars-progress text-3xl lg:text-4xl"></i>
        </div>
      </div>
        <!-- Pengajuan -->
      <div class="bg-red-500 text-white p-4 lg:p-6 rounded shadow md:col-span-2 lg:col-span-1">
        <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl lg:text-3xl font-bold" id="pengajuanCount">0</h2>
                    <p class="font-medium">Pengajuan</p>
                </div>
                <i class="fa-solid fa-bullhorn text-3xl lg:text-4xl"></i>
        </div>
      </div>
    </div>

    <!-- Search & Filter -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4 flex-wrap">
        <!-- Search input -->
        <div class="relative w-full lg:w-1/3 mt-4">
            <input type="text" id="searchInput" placeholder="Cari laporan berdasarkan No. Resi atau Masalah..."
                class="border border-gray-300 px-4 py-2.5 rounded-lg w-full pr-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
            <i class="fa-solid fa-magnifying-glass absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>

        <!-- Filter group (Periode + Status + Show Entries) -->
        <div class="flex flex-col sm:flex-row gap-3 flex-wrap w-full lg:w-auto">
            <!-- Periode Tanggal -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Periode Tanggal</label>
                <div class="relative w-full sm:w-[320px] md:w-[300px]">
                    <input
                        type="text"
                        id="dateRange"
                        placeholder="Pilih periode..."
                        readonly
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer bg-white"
                        onclick="toggleDatePicker()"
                    >
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                </div>
                <!-- Date Picker Dropdown -->
                <div id="datePicker"
                    class="hidden z-20 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg p-4 w-[320px] sm:w-[360px] lg:absolute lg:min-w-64">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Dari</label>
                            <input type="date" id="startDate"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Sampai</label>
                            <input type="date" id="endDate"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2 mb-4">
                        <button onclick="setQuickRange('today')"
                            class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200">Hari Ini</button>
                        <button onclick="setQuickRange('week')"
                            class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200">7 Hari</button>
                        <button onclick="setQuickRange('month')"
                            class="px-2 py-1 text-xs bg-purple-100 text-purple-700 rounded hover:bg-purple-200">30 Hari</button>
                    </div>

                    <div class="flex justify-end">
                        <button onclick="clearDateRange()"
                            class="px-4 py-1 bg-gray-200 text-gray-700 text-sm rounded hover:bg-gray-300">Reset</button>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="filterStatus"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="Pengajuan">Pengajuan</option>
                    <option value="Progress">Progress</option>
                    <option value="Selesai">Selesai</option>
                </select>
            </div>

            <!-- Show Entries -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tampilkan</label>
                <select id="showEntries"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="5">Show 5 entries</option>
                    <option value="10">Show 10 entries</option>
                    <option value="25">Show 25 entries</option>
                    <option value="50">Show 50 entries</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-auto bg-white shadow rounded">
        <table class="min-w-full text-center border border-gray-200">
            <thead class="bg-black text-white">
                <tr>
                    <th class="px-2 lg:px-4 py-2 text-sm lg:text-base">No Resi</th>
                    <th class="px-2 lg:px-4 py-2 text-sm lg:text-base">Masalah</th>
                    <th class="px-2 lg:px-4 py-2 text-sm lg:text-base">Status</th>
                    <th class="px-2 lg:px-4 py-2 text-sm lg:text-base">Estimasi</th>
                    <th class="px-2 lg:px-4 py-2 text-sm lg:text-base">View</th>
                </tr>
            </thead>
            <tbody id="laporanTable">
                <!-- Data will be populated by JavaScript -->
            </tbody>
        </table>
    </div>

    <!-- Modal Pop-up -->
    <div id="detailModal" class="fixed inset-0 bg-black/50 bg-opacity-50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-lg w-full max-w-2xl shadow-lg overflow-auto max-h-[90vh]">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
            <h3 class="text-lg font-semibold">Detail Laporan</h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
            </div>
            <div class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <p><strong>Judul/Topik Permasalahan:</strong></p><p id="modalNama">-</p>
                <p><strong>Tanggal Pengajuan:</strong></p><p id="modalTanggal">-</p>
                <p><strong>Estimasi Selesai:</strong></p><p id="modalEstimasi">-</p>
                <p><strong>Status:</strong></p><p id="modalStatus">-</p>
                <p><strong>Deskripsi:</strong></p><p id="modalDeskripsi">-</p>
                <p><strong>Lampiran:</strong></p><p id="modalLampiran">-</p>
            </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="text-sm text-gray-600">
            <span id="paginationInfo">Showing 1 to 10 of 15 entries</span>
        </div>
        <div class="flex" id="paginationButtons">
            <!-- table di generate menggunakan javascript -->
        </div>
    </div>
  </div>

  <!-- Script -->
  <script src="{{ asset('js/userDashboard.js') }}"></script>
</body>
</html>
