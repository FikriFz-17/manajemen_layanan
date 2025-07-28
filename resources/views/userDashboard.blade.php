<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard - Kominfo Kebumen</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/@fortawesome/fontawesome-free@6.4.0/js/all.min.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 h-screen flex">
  <!-- Mobile Overlay -->
  <div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

  <!-- Sidebar -->
  <div id="sidebar" class="w-64 bg-[#262394] text-white flex flex-col p-6 fixed lg:relative h-full z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
    <img src="{{ asset('images/logo-kebumen.png') }}" alt="Logo" class="w-20 mx-auto mb-4">
    <h1 class="text-lg font-bold text-center mb-8">Diskominfo Kebumen</h1>
    <nav class="flex flex-col gap-4">
      <a href="{{ route('dashboard') }}" class="flex items-center gap-2 hover:text-gray-300 p-2 rounded transition-colors bg-white bg-opacity-20"><i class="fas fa-home"></i> Dashboard</a>
      <a href="{{ route('ajukanLaporan') }}" class="flex items-center gap-2 hover:text-gray-300 p-2 rounded transition-colors"><i class="fas fa-plus-circle"></i> Ajukan Laporan</a>
      <a href="{{ asset('storage/user_manual/Panduan Pengajuan Pengguna.pdf') }}" download="" class="flex items-center gap-2 hover:text-gray-300 p-2 rounded transition-colors"><i class="fa-solid fa-file-arrow-down"></i> User Manual</a>

      <!-- Customer Service Section -->
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
        <div class="relative w-full lg:w-1/3">
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
        <table class="min-w-full text-center border">
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
    <div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-lg w-full max-w-2xl shadow-lg overflow-auto max-h-[90vh]">
            <div class="flex justify-between items-center border-b px-6 py-4">
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
  <script>
    // Pagination variables
    let currentPage = 1;
    let itemsPerPage = 5;
    let laporanData = [];
    let filteredData = [];

    // Mobile hamburger menu
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const sidebar = document.getElementById('sidebar');
    const mobileOverlay = document.getElementById('mobileOverlay');

    hamburgerBtn.addEventListener('click', function() {
      sidebar.classList.toggle('-translate-x-full');
      mobileOverlay.classList.toggle('hidden');
    });

    // Close sidebar when clicking overlay
    mobileOverlay.addEventListener('click', function() {
      sidebar.classList.add('-translate-x-full');
      mobileOverlay.classList.add('hidden');
    });

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
      if (window.innerWidth < 1024) {
        if (!sidebar.contains(e.target) && !hamburgerBtn.contains(e.target)) {
          sidebar.classList.add('-translate-x-full');
          mobileOverlay.classList.add('hidden');
        }
      }
    });

    // Handle window resize
    window.addEventListener('resize', function() {
      if (window.innerWidth >= 1024) {
        sidebar.classList.remove('-translate-x-full');
        mobileOverlay.classList.add('hidden');
      } else {
        sidebar.classList.add('-translate-x-full');
      }
    });

    // Toggle user dropdown
    function toggleDropdown() {
      if (window.innerWidth < 1024) {
        const dropdown = document.getElementById('userDropdown');
        dropdown.classList.toggle('hidden');
      } else {
        const dropdown = document.getElementById('userDropdownDesktop');
        dropdown.classList.toggle('hidden');
      }
    }

    // Hide dropdown when clicking outside
    window.addEventListener('click', function(e) {
      const dropdownMobile = document.getElementById('userDropdown');
      const dropdownDesktop = document.getElementById('userDropdownDesktop');

      if (window.innerWidth < 1024) {
        if (!e.target.closest('button[onclick="toggleDropdown()"]') && !dropdownMobile.contains(e.target)) {
          dropdownMobile.classList.add('hidden');
        }
      } else {
        if (!e.target.closest('button[onclick="toggleDropdown()"]') && !dropdownDesktop.contains(e.target)) {
          dropdownDesktop.classList.add('hidden');
        }
      }
    });

    // Update statistics cards - Fixed
    function updateStatistics() {
        const progressCount = laporanData.filter(item => item.status === 'Progress').length;
        const successCount = laporanData.filter(item => item.status === 'Selesai').length;
        const pengajuanCount = laporanData.filter(item => item.status === 'Pengajuan').length;

        document.getElementById('progressCount').textContent = progressCount;
        document.getElementById('successCount').textContent = successCount;
        document.getElementById('pengajuanCount').textContent = pengajuanCount;
    }

    // Render table
    function renderTable() {
        const tableBody = document.getElementById('laporanTable');
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = itemsPerPage === 50 ? filteredData.length : startIndex + itemsPerPage;
        const currentData = filteredData.slice(startIndex, endIndex);

        tableBody.innerHTML = '';

        currentData.forEach((item, index) => {
            let statusClass = '';
            if (item.status === 'Progress') {
                statusClass = 'bg-yellow-100 text-blue-800';
            } else if (item.status === 'Selesai') {
                statusClass = 'bg-green-100 text-green-800';
            } else if (item.status === 'Pengajuan') {
                statusClass = 'bg-red-100 text-yellow-800';
            }

            const globalIndex = laporanData.findIndex(d => d.id === item.id);
            const row = `
                <tr class="border-b hover:bg-gray-50" data-index="${globalIndex}">
                    <td class="px-2 lg:px-4 py-2 text-sm lg:text-base">${item.resi}</td>
                    <td class="px-2 lg:px-4 py-2 text-sm lg:text-base">${item.masalah}</td>
                    <td class="px-2 lg:px-4 py-2 text-sm lg:text-base"><span class="px-2 py-1 ${statusClass} rounded text-xs">${item.status}</span></td>
                    <td class="px-2 lg:px-4 py-2 text-sm lg:text-base">
                        ${item.status === 'Pengajuan' ? '0 Hari' : item.status === 'Progress' ? (item.estimasi ? item.estimasi + ' Hari' : '-') : (item.tanggal_selesai ?? '-')}
                    </td>
                    <td class="px-2 lg:px-4 py-2"><i class="fas fa-eye text-blue-500 hover:text-blue-700 cursor-pointer view-btn"></i></td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });

        updatePaginationInfo();
        renderPagination();
    }

    // Update pagination info
    function updatePaginationInfo() {
        const startIndex = (currentPage - 1) * itemsPerPage + 1;
        const endIndex = Math.min(currentPage * itemsPerPage, filteredData.length);
        const totalEntries = filteredData.length;

        document.getElementById('paginationInfo').textContent = `Menampilkan ${startIndex} sampai ${endIndex} dari ${totalEntries} data`;
    }

    // Render pagination buttons
    function renderPagination() {
        const paginationContainer = document.getElementById('paginationButtons');
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);

        if (totalPages <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }

        let paginationHTML = '';

        // Tombol Previous
        paginationHTML += `
            <button onclick="changePage(${currentPage - 1})"
                    class="px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 transition-colors duration-200 ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}"
                    ${currentPage === 1 ? 'disabled' : ''}>
            <i class="fas fa-chevron-left"></i>
            </button>
        `;

        // Logic page numbers
        if (totalPages <= 5) {
            // Tampilkan semua jika <= 5
            for (let i = 1; i <= totalPages; i++) {
            paginationHTML += renderPageButton(i);
            }
        } else {
            // Halaman 1
            paginationHTML += renderPageButton(1);

            if (currentPage <= 2) {
            paginationHTML += renderPageButton(2);
            paginationHTML += renderEllipsis();
            } else if (currentPage >= totalPages - 1) {
            paginationHTML += renderEllipsis();
            paginationHTML += renderPageButton(totalPages - 1);
            } else {
            paginationHTML += renderEllipsis();
            paginationHTML += renderPageButton(currentPage);
            paginationHTML += renderEllipsis();
            }

            // Halaman terakhir
            paginationHTML += renderPageButton(totalPages);
        }

        // Tombol Next
        paginationHTML += `
            <button onclick="changePage(${currentPage + 1})"
                    class="px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 transition-colors duration-200 ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''}"
                    ${currentPage === totalPages ? 'disabled' : ''}>
            <i class="fas fa-chevron-right"></i>
            </button>
        `;

        paginationContainer.innerHTML = paginationHTML;

        // Helper
        function renderPageButton(page) {
            if (page === currentPage) {
            return `<button class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600">${page}</button>`;
            } else {
            return `<button onclick="changePage(${page})"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors duration-200">
                        ${page}
                    </button>`;
            }
        }

        function renderEllipsis() {
            return `<span class="px-2 py-2 text-sm text-gray-500">...</span>`;
        }
    }

    // Change page
    function changePage(page) {
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);
        if (page >= 1 && page <= totalPages) {
            currentPage = page;
            renderTable();
        }
    }

    // Filter & Search - Fixed
    function toggleDatePicker() {
        const datePicker = document.getElementById('datePicker');
        datePicker.classList.toggle('hidden');
    }

    document.addEventListener('click', function (e) {
        const datePicker = document.getElementById('datePicker');
        const dateRangeInput = document.getElementById('dateRange');

        if (
            !datePicker.contains(e.target) &&
            !dateRangeInput.contains(e.target)
        ) {
            datePicker.classList.add('hidden');
        }
    });

    function filterTable() {
        const formatDate = (date) => {
            if (!date) return '';
            const yyyy = date.getFullYear();
            const mm = String(date.getMonth() + 1).padStart(2, '0');
            const dd = String(date.getDate()).padStart(2, '0');
            return `${yyyy}/${mm}/${dd}`;
        };

        const keyword = document.getElementById('searchInput').value.toLowerCase().trim();
        const status = document.getElementById('filterStatus').value;

        const startDateInput = document.getElementById('startDate').value;
        const endDateInput = document.getElementById('endDate').value;

        const startDate = startDateInput ? new Date(startDateInput) : null;
        const endDate = endDateInput ? new Date(endDateInput) : null;

        filteredData = laporanData.filter(item => {
            const idMatch = item.resi.toString().includes(keyword);
            const masalahMatch = item.masalah.toLowerCase().includes(keyword);
            const statusMatch = status === "" || item.status === status;

            let tanggalMatch = true;
            if (startDate || endDate) {
                // Update text input dateRange
                document.getElementById('dateRange').value = `${formatDate(startDate)} - ${formatDate(endDate)}`;

                const resiDateStr = item.resi.substring(0, 6); // "ddmmyy"
                const day = resiDateStr.substring(0, 2);
                const month = resiDateStr.substring(2, 4);
                const year = '20' + resiDateStr.substring(4, 6);

                const resiDate = new Date(`${year}-${month}-${day}`);

                if (startDate && resiDate < startDate) tanggalMatch = false;
                if (endDate && resiDate > endDate) tanggalMatch = false;
            }

            return (idMatch || masalahMatch) && statusMatch  && tanggalMatch;
        });

        currentPage = 1;
        renderTable();
    }

    function setQuickRange(range){
        const today = new Date()

        let start = new Date()

        if (range === "today"){
            start = new Date(today)
        } else if (range === "week"){
            start.setDate(today.getDate() - 6)
        } else if (range === "month"){
            start.setDate(today.getDate() - 29)
        }

        const formatDate = (date) => {
            const yyyy = date.getFullYear();
            const mm = String(date.getMonth() + 1).padStart(2, '0');
            const dd = String(date.getDate()).padStart(2, '0');
            return `${yyyy}-${mm}-${dd}`;
        };

        document.getElementById('startDate').value = formatDate(start);
        document.getElementById('endDate').value = formatDate(today);

        // Update tampilan teks range
        document.getElementById('dateRange').value = `${formatDate(start)} - ${formatDate(today)}`;

        // Apply filter
        filterTable();
    }

    function clearDateRange(){
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        document.getElementById('dateRange').value = '';

        // Sembunyikan date picker jika sedang terbuka
        document.getElementById('datePicker').classList.add('hidden');

        // Render ulang tabel tanpa filter tanggal
        filterTable();
    }

    // Handle entries per page change
    function handleEntriesChange() {
        const showEntries = parseInt(document.getElementById('showEntries').value);
        itemsPerPage = showEntries;
        currentPage = 1;
        renderTable();
    }

    // Event listeners
    document.getElementById('searchInput').addEventListener('input', filterTable);
    document.getElementById('filterStatus').addEventListener('change', filterTable);
    document.getElementById('showEntries').addEventListener('change', handleEntriesChange);
    document.getElementById('datePicker').addEventListener('change', filterTable);

    function openModal(dataIndex) {
        currentEditingIndex = dataIndex;
        const data = laporanData[currentEditingIndex];

        document.getElementById('modalNama').innerText = data.masalah;
        document.getElementById('modalTanggal').innerText = data.tanggal;
        document.getElementById('modalStatus').innerText = data.status;
        document.getElementById('modalDeskripsi').innerText = data.deskripsi;
        document.getElementById('modalLampiran').innerHTML = data.lampiran ? `<a href="/storage/${data.lampiran}" target="_blank" class="text-blue-600 underline hover:text-blue-800">
         Lihat Lampiran
       </a>` : '-';
        document.getElementById('modalEstimasi').innerText =
            data.status === 'Pengajuan' ? '0 Hari' :
            data.status === 'Progress' ? (data.estimasi || 0) + ' Hari' :
            data.status === 'Selesai' ? (data.tanggal_selesai + " ( selesai )" || '-') : '-';
        document.getElementById('detailModal').classList.remove('hidden');
        document.getElementById('detailModal').classList.add('flex');
    }

    function closeModal() {
        document.getElementById('detailModal').classList.add('hidden');
        document.getElementById('detailModal').classList.remove('flex');
    }

    // Close modal when clicking outside
    document.getElementById('detailModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Event listener setelah DOM loaded
    document.addEventListener('DOMContentLoaded', function() {
        fetch('/laporan/user')
        .then(response => response.json())
        .then(data => {
            laporanData = data.map(item => ({
                id: item.id,
                resi: item.resi,
                masalah: item.judul_masalah,
                status: item.status,
                kategori: item.kategori,
                tanggal: item.tanggal_pengajuan,
                tanggal_selesai: item.tanggal_selesai,
                estimasi: item.estimasi,
                deskripsi: item.deskripsi,
                lampiran: item.lampiran,
            }));
            filteredData = [...laporanData];
            updateStatistics();
            renderTable();
        })
        .catch(error => {
            console.error('Gagal memuat data laporan:', error);
        });
        document.getElementById('laporanTable').addEventListener('click', function(e) {
            const editBtn = e.target.closest('.view-btn');
            if (editBtn) {
                const row = editBtn.closest('tr');
                const dataIndex = parseInt(row.getAttribute('data-index'));
                openModal(dataIndex);
            }
        });
    });
  </script>

</body>
</html>
