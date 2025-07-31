<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Dashboard Admin - Kominfo Kebumen</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/@fortawesome/fontawesome-free@6.4.0/js/all.min.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 h-screen flex">

  <div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

  <div id="sidebar" class="w-64 bg-[#262394] text-white flex flex-col p-6 fixed lg:relative h-full z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
    <img src="{{ asset('images/logo-kebumen.png') }}" alt="Logo" class="w-20 mx-auto mb-4">
    <h1 class="text-lg font-bold text-center mb-8">Diskominfo Kebumen</h1>
    <nav class="flex flex-col gap-4">
      <a href="{{ route('adminDashboard') }}" class="flex items-center gap-2 hover:text-gray-300 p-2 rounded transition-colors bg-white bg-opacity-20"><i class="fas fa-home"></i> Dashboard</a>
      <a href="{{route('userManagement')}}" class="flex items-center gap-2 hover:text-gray-300 p-2 rounded transition-colors"><i class="fa-solid fa-user"></i></i>User Management</a>
      <a href="{{ asset('storage/user_manual/Panduan Admin.pdf') }}" download class="flex items-center gap-2 hover:text-gray-300 p-2 rounded transition-colors"><i class="fa-solid fa-file-arrow-down"></i> User Manual</a>

      <div class="mt-auto pt-4 border-t border-white border-opacity-20">
        <div class="flex items-center gap-2 p-2 rounded transition-colors hover:bg-white hover:bg-opacity-10 cursor-pointer">
          <div class="relative">
            <i class="fas fa-headset text-xl"></i>
            <span class="absolute top-0 right-0 w-2 h-2 bg-green-500 rounded-full"></span>
          </div>
          <span>Customer Service</span>
        </div>
      </div>
    </nav>
  </div>

  <div class="flex-1 lg:ml-0 p-4 lg:p-6 overflow-auto">

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
        <div id="userDropdown" class="absolute right-0 mt-2 w-40 bg-white rounded shadow hidden z-20 border">
          <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fas fa-key mr-2"></i>Ganti Password</a>
          <form action="{{ route('logout.submit') }}" method="post">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-100"><i class="fas fa-sign-out-alt mr-2"></i>Logout</button>
          </form>
        </div>
      </div>
    </div>

    <div class="hidden lg:flex justify-end mb-6 relative">
      <div class="relative inline-block text-left">
        <button onclick="toggleDropdown()" class="flex items-center gap-2 focus:outline-none">
          <i class="fas fa-user-circle text-2xl"></i>
          <span class="font-medium">Admin</span>
          <i class="fas fa-chevron-down text-sm"></i>
        </button>

        <div id="userDropdownDesktop" class="absolute right-0 mt-2 w-40 bg-white rounded shadow hidden z-20">
          <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fas fa-key mr-2"></i>Ganti Password</a>
          <form action="{{ route('logout.submit') }}" method="post">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-100"><i class="fas fa-sign-out-alt mr-2"></i>Logout</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Card -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6 lg:mb-8">
      <div class="bg-green-500 text-white p-4 lg:p-6 rounded shadow">
          <div class="flex justify-between items-center">
              <div>
                  <h2 class="text-2xl lg:text-3xl font-bold" id="successCount">0</h2>
                  <p class="font-medium">Selesai</p>
              </div>
              <i class="fa-solid fa-circle-check text-3xl lg:text-4xl"></i>
          </div>
      </div>
      <div class="bg-yellow-500 text-white p-4 lg:p-6 rounded shadow">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl lg:text-3xl font-bold" id="progressCount">0</h2>
                <p class="font-medium">Progress</p>
            </div>
            <i class="fa-solid fa-bars-progress text-3xl lg:text-4xl"></i>
        </div>
      </div>
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

    <!-- Search dan Action Buttons -->
    <div class="flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center mb-6">
        <!-- Search Bar -->
        <div class="relative w-full lg:w-1/3">
            <input type="text" id="searchInput" placeholder="Cari laporan berdasarkan No. Resi atau Masalah..."
                    class="border border-gray-300 px-4 py-2.5 rounded-lg w-full pr-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
            <i class="fa-solid fa-magnifying-glass absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>

        <!-- Export Buttons -->
        <div class="flex flex-wrap gap-2">
            <button id="filterBtn" class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 px-4 py-2.5 rounded-lg border transition-colors">
                <i class="fas fa-filter text-gray-600"></i>
                <span class="text-gray-700">Filter</span>
            </button>
            <button id="exportBtn" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg transition-colors">
                <i class="fas fa-download"></i>
                <span>Export</span>
            </button>
            <button onclick="openImportModal()" id="importBtn" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg transition-colors">
                <i class="fa-solid fa-upload"></i>
                <span>Import</span>
            </button>
        </div>
    </div>

    <!-- Collapsible Filter Panel -->
    <div id="filterPanel" class="hidden bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Compact Date Range Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Periode Tanggal</label>
                <div class="relative">
                    <input
                        type="text"
                        id="dateRange"
                        placeholder="Pilih periode..."
                        readonly
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer bg-white"
                        onclick="toggleDatePicker()"
                    >
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
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

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Instansi</label>
                <select id="filterJenisInstansi" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Instansi</option>
                    <option value="desa">Desa</option>
                    <option value="pemda">Pemerintah Daerah</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select id="filterKategori" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Kategori</option>
                    <option value="Aplikasi">Aplikasi</option>
                    <option value="Infrastruktur">Infrastruktur</option>
                    <option value="Jaringan">Jaringan</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="filterStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="Pengajuan">Pengajuan</option>
                    <option value="Progress">Progress</option>
                    <option value="Selesai">Selesai</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tampilkan</label>
                <select id="showEntries" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="5">5 entries</option>
                    <option value="10">10 entries</option>
                    <option value="25">25 entries</option>
                    <option value="50">50 entries</option>
                </select>
            </div>
        </div>
    </div>

    <!-- table -->
    <div class="overflow-auto bg-white shadow rounded">
        <table class="min-w-full text-center border">
            <thead class="bg-black text-white">
                <tr>
                    <th class="px-2 lg:px-4 py-2 text-sm lg:text-base">No. Resi</th>
                    <th class="px-2 lg:px-4 py-2 text-sm lg:text-base">Masalah</th>
                    <th class="px-2 lg:px-4 py-2 text-sm lg:text-base">Status</th>
                    <th class="px-2 lg:px-4 py-2 text-sm lg:text-base">Tanggal Pengajuan</th>
                    <th class="px-2 lg:px-4 py-2 text-sm lg:text-base">Tangani</th>
                </tr>
            </thead>
            <tbody id="laporanTable"></tbody>
        </table>
    </div>

    <!-- modal/popup -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
      <div class="bg-white rounded-lg w-full max-w-2xl shadow-lg overflow-auto max-h-[90vh]">
        <div class="flex justify-between items-center border-b px-6 py-4">
          <h3 class="text-lg font-semibold">Edit Laporan</h3>
          <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="p-6 space-y-4">
          <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm mb-6">
            <p><strong>Nama:</strong></p><p id="modalUserNama">-</p>
            <p><strong>Instansi:</strong></p><p id="modalUserInstansi">-</p>
            <p><strong>Jenis Instansi:</strong></p><p id="modalUserJenisInstansi">-</p>
            <p><strong>Email:</strong></p><p id="modalUserEmail">-</p>
            <p><strong>No. Resi:</strong></p><p id="modalResi">-</p>
            <p><strong>Judul/Topik Permasalahan:</strong></p><p id="modalNama">-</p>
            <p><strong>Tanggal Pengajuan:</strong></p><p id="modalTanggal">-</p>
            <p><strong>Deskripsi Awal:</strong></p><p id="modalDeskripsi">-</p>
            <p><strong>Lampiran:</strong></p><p id="modalLampiran">-</p>
          </div>

            <div class="space-y-4 border-t pt-4">
                <form method="post" id="editForm">
                    @csrf
                    @method('PUT')
                    <div>
                    <label for="modalSetStatus" class="block text-sm font-medium text-gray-700 mb-2 mt-2">Set Status</label>
                    <select id="modalSetStatus" name="status" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="" selected>--- Set Status ---</option>
                        <option value="Pengajuan">Pengajuan</option>
                        <option value="Progress">Progress</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                    </div>
                    <div>
                    <label for="modalSetKategori" class="block text-sm font-medium text-gray-700 mb-2 mt-2">Kategori</label>
                    <select id="modalSetKategori" name="kategori" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="" selected>--- Set Kategori ---</option>
                        <option value="Aplikasi">Aplikasi</option>
                        <option value="Infrastruktur">Infrastruktur</option>
                        <option value="Jaringan">Jaringan</option>
                    </select>
                    </div>
                    <div>
                        <label for="modalSetTanggalSelesai" class="block text-sm font-medium text-gray-700 mb-2 mt-2">Tanggal Selesai/Perkiraan Selesai</label>
                        <input type="date" id="modalSetTanggalSelesai" name="tanggal_selesai" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="modalDeskripsiPenanganan" class="block text-sm font-medium text-gray-700 mb-2 mt-2">Deskripsi Penanganan</label>
                        <textarea id="modalDeskripsiPenanganan" name="deskripsi_penanganan" rows="4" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Jelaskan bagaimana masalah ditangani..."></textarea>
                    </div>
                    <div class="flex justify-end items-center border-t px-6 py-4 gap-2">
                        <!-- Submit edit btn -->
                        <button type="submit" id="adminEditBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Simpan Perubahan</button>
                        <!-- Loading button -->
                        <button disabled type="button" id="loadingBtn" class="hidden items-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            <svg aria-hidden="true" role="status" class="inline w-4 h-4 me-3 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"/>
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"/>
                            </svg>
                            Loading...
                        </button>
                    </div>
                </form>
            </div>
        </div>
      </div>
    </div>

    <!-- modal import -->
    <div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white w-full max-w-lg mx-4 sm:mx-auto rounded-lg shadow-lg p-6 relative">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Upload Data Excel</h2>

            <!-- Upload Form -->
            <form id="uploadForm" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Pilih Jenis -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Jenis Data</label>
                    <select id="jenisData" name="jenis" onchange="onJenisChange()" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Pilih Jenis --</option>
                        <option value="desa">Desa</option>
                        <option value="kecamatan">Kecamatan</option>
                        <option value="pemda">Pemerintah Daerah</option>
                    </select>
                </div>

                <!-- Upload File -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">File Excel</label>
                    <input type="file" name="file" id="fileInput" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 mt-6 border-t pt-4">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Upload
                    </button>
                </div>
            </form>

            <!-- Tombol Close (pojok kanan atas) -->
            <button onclick="closeImportModal()" class="absolute top-3 right-3 text-gray-500 hover:text-red-500">
                ✕
            </button>
        </div>
    </div>

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
  <script src="{{ asset('js/adminDashboard.js') }}"></script>
</body>
</html>
