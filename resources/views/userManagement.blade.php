<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>User Management - Kominfo Kebumen</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/@fortawesome/fontawesome-free@6.4.0/js/all.min.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 h-screen flex">

  <div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

  <div id="sidebar" class="w-64 bg-[#262394] text-white flex flex-col p-6 fixed lg:relative h-full z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
    <img src="{{ asset('images/logo-kebumen.png') }}" alt="Logo" class="w-20 mx-auto mb-4">
    <h1 class="text-lg font-bold text-center mb-8">Diskominfo Kebumen</h1>
    <nav class="flex flex-col gap-4">
      <a href="{{ route('adminDashboard') }}" class="flex items-center gap-2 hover:text-gray-300 p-2 rounded transition-colors"><i class="fas fa-home"></i> Dashboard</a>
      <a href="#" class="flex items-center gap-2 hover:text-gray-300 p-2 rounded transition-colors bg-white bg-opacity-20"><i class="fa-solid fa-user"></i>User Management</a>

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
      <h1 class="text-xl font-bold text-[#262394]">User Management</h1>
      <div class="relative inline-block text-left">
        <button onclick="toggleDropdown()" class="flex items-center gap-2 focus:outline-none">
          <i class="fas fa-user-circle text-2xl text-[#262394]"></i>
          <i class="fas fa-chevron-down text-sm text-[#262394]"></i>
        </button>
        <div id="userDropdown" class="absolute right-0 mt-2 w-40 bg-white rounded shadow hidden z-20 border">
          <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fas fa-key mr-2"></i>Ganti Password</a>
          <form action="{{ route('login') }}" method="post">
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
          <form action="{{ route('login') }}" method="post">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-100"><i class="fas fa-sign-out-alt mr-2"></i>Logout</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div id="successToast" class="fixed top-0 left-1/2 z-50 transform -translate-x-1/2 -translate-y-full opacity-0 transition duration-500 ease-out">
            <div class="bg-gradient-to-r from-green-50 to-emerald-100 border-l-4 border-green-500 p-4 rounded-lg shadow-lg w-80">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500 text-lg"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                    <button type="button" onclick="document.getElementById('successToast').remove()"
                            class="flex-shrink-0 ml-3 text-green-400 hover:text-green-600 transition-colors duration-200">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Validation Errors -->
    @if ($errors->any())
        <div id="errorToast" class="fixed top-0 left-1/2 z-50 transform -translate-x-1/2 -translate-y-full opacity-0 transition duration-500 ease-out">
            <div class="bg-gradient-to-r from-green-50 to-emerald-100 border-l-4 border-red-500 p-4 rounded-lg shadow-lg w-80">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-red-500 text-lg"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-semibold text-red-800 mb-1">Terjadi beberapa kesalahan:</p>
                        <ul class="list-disc pl-4 text-sm text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" onclick="document.getElementById('errorToast').remove()"
                            class="flex-shrink-0 ml-3 text-red-400 hover:text-red-600 transition-colors duration-200">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Card Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-4 mb-6 lg:mb-8">
      <div class="bg-blue-500 text-white p-4 lg:p-6 rounded shadow">
          <div class="flex justify-between items-center">
              <div>
                  <h2 class="text-2xl lg:text-3xl font-bold" id="totalUsers">0</h2>
                  <p class="font-medium">Total Users</p>
              </div>
              <i class="fa-solid fa-users text-3xl lg:text-4xl"></i>
          </div>
      </div>
      <div class="bg-green-500 text-white p-4 lg:p-6 rounded shadow">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl lg:text-3xl font-bold" id="activeUsers">0</h2>
                <p class="font-medium">Active Users</p>
            </div>
            <i class="fa-solid fa-user-check text-3xl lg:text-4xl"></i>
        </div>
      </div>
      <div class="bg-yellow-500 text-white p-4 lg:p-6 rounded shadow">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl lg:text-3xl font-bold" id="pendingUsers">0</h2>
                <p class="font-medium">Pending Users</p>
            </div>
            <i class="fa-solid fa-user-clock text-3xl lg:text-4xl"></i>
        </div>
      </div>
    </div>

    <!-- Search & Filter -->
    <div class="flex flex-col sm:flex-row gap-4 sm:justify-between sm:items-center mb-4">
        <div class="relative w-full lg:w-1/3">
            <input type="text" id="searchInput" placeholder="Cari Email User..."
                    class="border border-gray-300 px-4 py-2.5 rounded-lg w-full pr-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
            <i class="fa-solid fa-magnifying-glass absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>

        <div class="flex flex-col sm:flex-row gap-2">
            <select id="filterStatus" class="border px-3 py-2 rounded w-full sm:w-auto">
                <option value="">Semua Status</option>
                <option value="Active">Active</option>
                <option value="Pending">Pending</option>
            </select>

            <select id="showEntries" class="border px-3 py-2 rounded w-full sm:w-auto">
                <option value="5">Show 5 entries</option>
                <option value="10">Show 10 entries</option>
                <option value="25">Show 25 entries</option>
                <option value="50">Show 50 entries</option>
            </select>
        </div>
    </div>

    <!-- Users Table -->
    <div class="overflow-auto bg-white shadow rounded">
        <table class="min-w-full text-center border">
            <thead class="bg-black text-white">
                <tr>
                    <th class="px-2 lg:px-4 py-2 text-sm lg:text-base">Nama</th>
                    <th class="px-2 lg:px-4 py-2 text-sm lg:text-base">Email</th>
                    <th class="px-2 lg:px-4 py-2 text-sm lg:text-base">Instansi</th>
                    <th class="px-2 lg:px-4 py-2 text-sm lg:text-base">Status</th>
                    <th class="px-2 lg:px-4 py-2 text-sm lg:text-base">Tanggal Daftar</th>
                    <th class="px-2 lg:px-4 py-2 text-sm lg:text-base">Actions</th>
                </tr>
            </thead>
            <tbody id="usersTable"></tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="text-sm text-gray-600">
            <span id="paginationInfo">Showing 1 to 10 of 15 entries</span>
        </div>
        <div class="flex" id="paginationButtons">
            <!-- Pagination buttons will be generated by JavaScript -->
        </div>
    </div>
  </div>

  <script>
    // Mobile hamburger menu
    let currentPage = 1;
    let itemsPerPage = 5;
    let filteredData = [];
    let laporanData = [];

    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const sidebar = document.getElementById('sidebar');
    const mobileOverlay = document.getElementById('mobileOverlay');

    hamburgerBtn.addEventListener('click', function() {
        sidebar.classList.toggle('-translate-x-full');
        mobileOverlay.classList.toggle('hidden');
    });

    mobileOverlay.addEventListener('click', function() {
        sidebar.classList.add('-translate-x-full');
        mobileOverlay.classList.add('hidden');
    });

    document.addEventListener('click', function(e) {
        if (window.innerWidth < 1024) {
            if (!sidebar.contains(e.target) && !hamburgerBtn.contains(e.target)) {
            sidebar.classList.add('-translate-x-full');
            mobileOverlay.classList.add('hidden');
            }
        }
    });

    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            sidebar.classList.remove('-translate-x-full');
            mobileOverlay.classList.add('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
        }
    });

    function toggleDropdown() {
        if (window.innerWidth < 1024) {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('hidden');
        } else {
            const dropdown = document.getElementById('userDropdownDesktop');
            dropdown.classList.toggle('hidden');
        }
    }

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

    function updateStatistics() {
        const totalUsers = laporanData.length;
        const activeUsers = laporanData.filter(user => user.active !== null).length;
        const pendingUsers = laporanData.filter(user => user.active === null).length;

        document.getElementById('totalUsers').textContent = totalUsers;
        document.getElementById('activeUsers').textContent = activeUsers;
        document.getElementById('pendingUsers').textContent = pendingUsers;
    }

    function renderTable() {
        const tableBody = document.getElementById('usersTable');
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = itemsPerPage === 50 ? filteredData.length : startIndex + itemsPerPage;
        const currentData = filteredData.slice(startIndex, endIndex);

        tableBody.innerHTML = '';

        currentData.forEach((user, index) => {
            const statusText = user.active ? 'Active' : 'Pending';
            const statusClass = user.active ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';

            const emailBtnClass = user.active ? 'text-gray-400 cursor-not-allowed' : 'text-green-500 hover:text-green-700 cursor-pointer';
            const emailBtnTitle = user.active ? 'User already verified' : 'Send Verification Email';

            const row = `
                <tr class="border-b hover:bg-gray-50" data-user-id="${user.id}">
                    <td class="px-2 lg:px-4 py-2 text-sm lg:text-base">${user.nama}</td>
                    <td class="px-2 lg:px-4 py-2 text-sm lg:text-base">${user.email}</td>
                    <td class="px-2 lg:px-4 py-2 text-sm lg:text-base">${user.instansi}</td>
                    <td class="px-2 lg:px-4 py-2 text-sm lg:text-base">
                        <span class="px-2 py-1 ${statusClass} rounded text-xs">${statusText}</span>
                    </td>
                    <td class="px-2 lg:px-4 py-2 text-sm lg:text-base">${user.created}</td>
                    <td class="px-2 lg:px-4 py-2">
                        <div class="flex flex-col gap-2 justify-center">
                            <i class="fas fa-envelope ${emailBtnClass} email-btn" title="${emailBtnTitle}" ${user.active ? 'data-disabled="true"' : ''}></i>
                        </div>
                    </td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });

        updatePaginationInfo();
        renderPagination();
    }

    function updatePaginationInfo() {
        const startIndex = (currentPage - 1) * itemsPerPage + 1;
        const endIndex = Math.min(currentPage * itemsPerPage, filteredData.length);
        const totalEntries = filteredData.length;

        document.getElementById('paginationInfo').textContent = `Showing ${startIndex} to ${endIndex} of ${totalEntries} entries`;
    }

    function renderPagination() {
        const paginationContainer = document.getElementById('paginationButtons');
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);

        if (totalPages <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }

        let paginationHTML = '';

        paginationHTML += `
            <button onclick="changePage(${currentPage - 1})"
                    class="px-3 lg:px-4 py-2 border rounded-l bg-gray-200 hover:bg-gray-300 text-sm lg:text-base ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}"
                    ${currentPage === 1 ? 'disabled' : ''}>
                Prev
            </button>
        `;

        for (let i = 1; i <= totalPages; i++) {
            if (i === currentPage) {
                paginationHTML += `<button class="px-3 lg:px-4 py-2 border bg-blue-500 text-white text-sm lg:text-base">${i}</button>`;
            } else {
                paginationHTML += `<button onclick="changePage(${i})" class="px-3 lg:px-4 py-2 border bg-gray-100 hover:bg-gray-200 text-sm lg:text-base">${i}</button>`;
            }
        }

        paginationHTML += `
            <button onclick="changePage(${currentPage + 1})"
                    class="px-3 lg:px-4 py-2 border rounded-r bg-gray-200 hover:bg-gray-300 text-sm lg:text-base ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''}"
                    ${currentPage === totalPages ? 'disabled' : ''}>
                Next
            </button>
        `;

        paginationContainer.innerHTML = paginationHTML;
    }

    function changePage(page) {
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);
        if (page >= 1 && page <= totalPages) {
            currentPage = page;
            renderTable();
        }
    }

    function filterTable() {
        const keyword = document.getElementById('searchInput').value.toLowerCase().trim();
        const status = document.getElementById('filterStatus').value;

        filteredData = laporanData.filter(item => {
            const nameMatch = item.nama.toLowerCase().includes(keyword);
            const emailMatch = item.email.toLowerCase().includes(keyword);

            // Filter by status
            let statusMatch = true;
            if (status === 'Active') {
                statusMatch = item.active !== null;
            } else if (status === 'Pending') {
                statusMatch = item.active === null;
            }

            return (nameMatch || emailMatch) && statusMatch;
        });

        currentPage = 1;
        renderTable();
    }

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

    document.addEventListener('DOMContentLoaded', function() {
        fetch('/user/all')
        .then(response => response.json())
        .then(data => {
            laporanData = data.map(user => ({
                id: user.id,
                nama: user.nama,
                instansi: user.instansi,
                email: user.email,
                active: user.email_verified_at,
                created: user.created_at
            }));
            filteredData = [...laporanData];
            updateStatistics();
            renderTable();
        })
        .catch(error => {
            console.error('Gagal memuat data laporan:', error);
        });

        // Event listener for email buttons
        document.getElementById('usersTable').addEventListener('click', function(e) {
            const emailBtn = e.target.closest('.email-btn');
            if (emailBtn) {
                const row = emailBtn.closest('tr');
                const userId = row.getAttribute('data-user-id');
                const user = laporanData.find(u => u.id == userId);
                if (user) {
                    if (user.active !== null) {
                        console.log("user ini active");
                    } else {
                        console.log("user ini tidak active - sending verification email");
                        // Buat form dinamis untuk submit
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '/email/verification-notification';
                        form.style.display = 'none';
                        // Tambahkan CSRF token
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        if (csrfToken) {
                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            csrfInput.value = csrfToken;
                            form.appendChild(csrfInput);
                        }
                        // Tambahkan email input
                        const emailInput = document.createElement('input');
                        emailInput.type = 'hidden';
                        emailInput.name = 'email';
                        emailInput.value = user.email;
                        form.appendChild(emailInput);
                        // Append form ke body dan submit
                        document.body.appendChild(form);
                        form.submit();
                    }
                    console.log('Email button clicked for user ID:', userId);
                    console.log('User data:', user);
                }
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        ['successToast', 'errorToast'].forEach(function (id) {
            const toast = document.getElementById(id);
            if (toast) {
                // Munculkan dengan animasi slide-down
                setTimeout(() => {
                    toast.classList.remove('-translate-y-full', 'opacity-0');
                    toast.classList.add('translate-y-10', 'opacity-100');
                }, 100);

                // Hilangkan setelah 5 detik
                setTimeout(() => {
                    toast.classList.remove('translate-y-10', 'opacity-100');
                    toast.classList.add('-translate-y-full', 'opacity-0');
                }, 5000);
            }
        });
    });
  </script>
</body>
</html>
