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
            <a href="#" class="flex items-center">
                <i class="fa-solid fa-file-arrow-down mr-2"></i>Panduan Pengguna
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

    <!-- Statistik Chart -->
    <div id="chart" class="bg-white rounded shadow p-4 mb-4">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Statistik Laporan</h2>
            <select id="tahunChartSelect" class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring focus:border-blue-300">
            <!-- Tahun diisi via JS -->
            </select>
        </div>
        <div id="chartContainer"></div>
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

  <script>
    // Pagination variables
    let laporanData = [];
    let currentPage = 1;
    let itemsPerPage = 5;
    let filteredData = [];

    // Update current time
    function updateTime() {
      const now = new Date();
      const timeString = now.toLocaleString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      });
      document.getElementById('currentTime').textContent = timeString;
    }

    // Update statistics
    function updateStatistics() {
      const progressCount = laporanData.filter(item => item.status === 'Progress').length;
      const successCount = laporanData.filter(item => item.status === 'Selesai').length;
      const pengajuanCount = laporanData.filter(item => item.status === 'Pengajuan').length;

      // Animate counters
      animateCounter('progressCount', progressCount);
      animateCounter('successCount', successCount);
      animateCounter('pengajuanCount', pengajuanCount);
    }

    // Counter animation
    function animateCounter(elementId, targetValue) {
      const element = document.getElementById(elementId);
      let current = 0;
      const increment = targetValue / 30;
      const timer = setInterval(() => {
        current += increment;
        if (current >= targetValue) {
          element.textContent = targetValue;
          clearInterval(timer);
        } else {
          element.textContent = Math.floor(current);
        }
      }, 50);
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
                    <td class="px-2 lg:px-4 py-2 text-sm lg:text-base">${item.tanggal}</td>
                    <td class="px-2 lg:px-4 py-2 text-sm lg:text-base"><span class="px-2 py-1 ${statusClass} rounded text-xs">${item.status}</span></td>
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

      document.getElementById('paginationInfo').textContent =
        `Menampilkan ${startIndex} hingga ${endIndex} dari ${totalEntries} entri`;
    }

    // Render pagination
    function renderPagination() {
      const paginationContainer = document.getElementById('paginationButtons');
      const totalPages = Math.ceil(filteredData.length / itemsPerPage);

      if (totalPages <= 1) {
        paginationContainer.innerHTML = '';
        return;
      }

      let paginationHTML = '';

      // Previous button
      paginationHTML += `
        <button onclick="changePage(${currentPage - 1})"
                class="px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 transition-colors duration-200 ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}"
                ${currentPage === 1 ? 'disabled' : ''}>
          <i class="fas fa-chevron-left"></i>
        </button>
      `;

      // Page numbers
      for (let i = 1; i <= totalPages; i++) {
        if (i === currentPage) {
          paginationHTML += `
            <button class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600">
              ${i}
            </button>
          `;
        } else {
          paginationHTML += `
            <button onclick="changePage(${i})"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors duration-200">
              ${i}
            </button>
          `;
        }
      }

      // Next button
      paginationHTML += `
        <button onclick="changePage(${currentPage + 1})"
                class="px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 transition-colors duration-200 ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''}"
                ${currentPage === totalPages ? 'disabled' : ''}>
          <i class="fas fa-chevron-right"></i>
        </button>
      `;

      paginationContainer.innerHTML = paginationHTML;
    }

    // Change page
    function changePage(page) {
      const totalPages = Math.ceil(filteredData.length / itemsPerPage);
      if (page >= 1 && page <= totalPages) {
        currentPage = page;
        renderTable();
      }
    }

    // Filter table
    function filterTable() {
      const keyword = document.getElementById('searchInput').value.toLowerCase().trim();
      const status = document.getElementById('filterStatus').value;

        filteredData = laporanData.filter(item => {
            const idMatch = item.resi.toString().includes(keyword);
            const masalahMatch = item.masalah.toLowerCase().includes(keyword);
            const statusMatch = status === "" || item.status === status;

            return (idMatch || masalahMatch) && statusMatch;
        });

      currentPage = 1;
      renderTable();
    }

    // Handle entries change
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

    // Initialize
    document.addEventListener('DOMContentLoaded', function () {
        updateTime();
        setInterval(updateTime, 60000);

        fetch('/laporan/all')
        .then(response => response.json())
        .then(data => {
            laporanData = data.map(item => ({
                resi: item.resi,
                masalah: item.judul_masalah,
                status: item.status,
                tanggal: item.tanggal_pengajuan,
            }));
            filteredData = [...laporanData];
            updateStatistics();
            renderTable();

            const tahunSet = new Set();
            laporanData.forEach(item => {
                if (item.resi) {
                    const tahun = '20' + item.resi.substring(4, 6);
                    tahunSet.add(tahun);
                }
            });

            const tahunSelect = document.getElementById('tahunChartSelect');
            tahunSelect.innerHTML = '';
            [...tahunSet].sort().forEach(t => {
                const opt = document.createElement('option');
                opt.value = t;
                opt.textContent = t;
                tahunSelect.appendChild(opt);
            });

            // Set default tahun = tahun terbaru
            const currentYear = new Date().getFullYear().toString();
            const tahunArray = [...tahunSet].sort();
            const defaultTahun = tahunSet.has(currentYear) ? currentYear : tahunArray[tahunArray.length - 1];

            tahunSelect.value = defaultTahun;
            generateChart(defaultTahun);

            // Event ganti tahun
            tahunSelect.addEventListener('change', () => {
                generateChart(tahunSelect.value);
            });
        })
        .catch(error => {
            console.error('Gagal memuat data laporan:', error);
        });

        document.getElementById('searchInput').addEventListener('input', filterTable);
        document.getElementById('filterStatus').addEventListener('change', filterTable);
        document.getElementById('showEntries').addEventListener('change', handleEntriesChange);
    });

    let chart;
    function generateChart(tahun) {
        const monthly = {
            Pengajuan: Array(12).fill(0),
            Selesai: Array(12).fill(0)
        };

        laporanData.forEach(item => {
            if (!item.resi || !item.status) return;

            const resi = item.resi;
            const bulan = parseInt(resi.substring(2, 4), 10) - 1; // ambil MM dari ddmmyy
            const tahunResi = '20' + resi.substring(4, 6); // ambil YY dari ddmmyy dan ubah ke 20YY

            if (tahunResi === tahun) {
            if (item.status === 'Pengajuan' || item.status === 'Selesai') {
                monthly[item.status][bulan]++;
            }
            }
        });

        const options = {
            chart: {
                type: 'bar',
                height: 350,
            },
            series: [
                {
                    name: 'Pengajuan',
                    data: monthly['Pengajuan']
                },
                {
                    name: 'Selesai',
                    data: monthly['Selesai']
                }
            ],
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            },
            colors: ['#FF4560', '#00E396'],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '45%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                position: 'top'
            },
        };

        if (chart) {
            chart.updateOptions(options);
        } else {
            chart = new ApexCharts(document.querySelector("#chartContainer"), options);
            chart.render();
        }
    }
  </script>

</body>
</html>
