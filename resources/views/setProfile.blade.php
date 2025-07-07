<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Ajukan Laporan - Kominfo Kebumen</title>
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
      <a href="{{ route('dashboard') }}" class="flex items-center gap-2 hover:text-gray-300 p-2 rounded transition-colors"><i class="fas fa-home"></i> Dashboard</a>
      <a href="{{ route('ajukanLaporan') }}" class="flex items-center gap-2 hover:text-gray-300 p-2 rounded transition-colors"><i class="fas fa-plus-circle"></i> Ajukan Laporan</a>
      <a href="#" class="flex items-center gap-2 hover:text-gray-300 p-2 rounded transition-colors"><i class="fa-solid fa-file-arrow-down"></i> User Manual</a>

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
      <h1 class="text-xl font-bold text-[#262394]">Ajukan Laporan</h1>
      <div class="relative inline-block text-left">
        <button onclick="toggleDropdown()" class="flex items-center gap-2 focus:outline-none">
          <i class="fas fa-user-circle text-2xl text-[#262394]"></i>
          <i class="fas fa-chevron-down text-sm text-[#262394]"></i>
        </button>
        <!-- Mobile Dropdown Menu -->
        <div id="userDropdown" class="absolute right-0 mt-2 w-40 bg-white rounded shadow hidden z-20 border">
          <!-- Ganti password -->
          <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fas fa-key mr-2"></i>Ganti Password</a>
          <!-- Logout -->
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
          <!-- ganti password -->
          <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fas fa-key mr-2"></i>Ganti Password</a>
          <!-- logout -->
          <form action="{{ route('logout.submit') }}" method="post">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-100"><i class="fas fa-sign-out-alt mr-2"></i>Logout</button>
          </form>
          <!-- set Profile -->
          <a href="{{ route('setProfile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fa-solid fa-user mr-2"></i>Set Profile</a>
        </div>
      </div>
    </div>

    <!-- main content -->
    <main class="flex-1 p-4 lg:p-8">
        <div class="max-w-4xl mx-auto bg-white p-6 sm:p-8 rounded-lg shadow-md">
            <!-- Success Message -->
            @if (session('success'))
                <div id="successToast" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-50 scale-0 opacity-0 transition-all duration-300 ease-out">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-100 border border-green-200 p-5 rounded-xl shadow-2xl w-96 max-w-sm mx-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-white text-lg"></i>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-semibold text-green-800 mb-1">Berhasil!</h3>
                                <p class="text-sm text-green-700">{{ session('success') }}</p>
                            </div>
                            <button type="button" onclick="closeToast('successToast')"
                                    class="flex-shrink-0 ml-3 w-8 h-8 bg-green-100 hover:bg-green-200 rounded-full flex items-center justify-center transition-all duration-200 hover:scale-110">
                                <i class="fas fa-times text-green-600 text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Validation Errors -->
            @if ($errors->any())
                <div id="errorToast" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-50 scale-0 opacity-0 transition-all duration-300 ease-out">
                    <div class="bg-gradient-to-r from-red-50 to-pink-100 border border-red-200 p-5 rounded-xl shadow-2xl w-96 max-w-sm mx-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-semibold text-red-800 mb-2">Terjadi Kesalahan!</h3>
                                <div class="max-h-32 overflow-y-auto pr-2">
                                    <ul class="space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li class="text-sm text-red-700 flex items-start">
                                                <i class="fas fa-dot-circle text-red-500 text-xs mt-1.5 mr-2 flex-shrink-0"></i>
                                                <span>{{ $error }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <button type="button" onclick="closeToast('errorToast')"
                                    class="flex-shrink-0 ml-3 w-8 h-8 bg-red-100 hover:bg-red-200 rounded-full flex items-center justify-center transition-all duration-200 hover:scale-110">
                                <i class="fas fa-times text-red-600 text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <h2 class="text-2xl font-bold text-gray-800 mb-8">Akun Pengguna</h2>
            <form action="{{ route('update.submit') }}" method="POST">
                @csrf
                <div class="flex flex-col sm:flex-row sm:items-center gap-6 mb-8 pb-8 border-b">
                    <img src="{{Auth::user()->profile_url === 'default.jpg' ? 'images/user.png' : 'storage/' .Auth::user()->profile_url}}" alt="Foto Profil" class="w-24 h-24 rounded-full object-cover border-2 border-gray-200">
                    <div>
                        <label for="photo-upload" class="cursor-pointer bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors text-sm font-medium">
                            Ubah
                        </label>
                        <input type="file" id="photo-upload" name="photo" class="hidden">
                        <p class="text-xs text-gray-500 mt-2">JPG, GIF atau PNG. Ukuran maks 800K.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <div>
                        <label for="nama-lengkap" class="block text-sm font-medium text-gray-700">Nama</label>
                        <input type="text" id="nama-lengkap" name="nama" value="{{ Auth::user()->nama }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" value="{{ Auth::user()->email }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="no-telpon" class="block text-sm font-medium text-gray-700">No. Telepon</label>
                        <input type="tel" id="no-telpon" name="phone" value="{{ Auth::user()->phone }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="instansi" class="block text-sm font-medium text-gray-700">Instansi</label>
                        <input type="text" id="instansi" name="instansi" value="{{ Auth::user()->instansi }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>

                <div class="flex justify-end items-center gap-4 mt-10 pt-6 border-t">
                    <a href="{{ url()->previous() }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-300 transition-colors font-medium">
                        Batal
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors font-medium">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </main>
  </div>

  <!-- Script -->
  <script>
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

    function closeToast(toastId) {
        const toast = document.getElementById(toastId);
        const backdrop = document.getElementById(toastId + '_backdrop');

        if (toast) {
            toast.classList.remove('scale-100', 'opacity-100');
            toast.classList.add('scale-0', 'opacity-0');

            if (backdrop) {
                backdrop.classList.remove('opacity-100');
                backdrop.classList.add('opacity-0');
            }

            setTimeout(() => {
                toast.remove();
                if (backdrop) {
                    backdrop.remove();
                }
            }, 300);
        }
    }

    // Success Toast
    document.addEventListener('DOMContentLoaded', function () {
        const toast = document.getElementById('successToast');
        if (toast) {
            // Add backdrop blur
            const backdrop = document.createElement('div');
            backdrop.id = 'successToast_backdrop';
            backdrop.className = 'fixed inset-0 bg-black bg-opacity-20 backdrop-blur-sm z-40 opacity-0 transition-opacity duration-300';
            document.body.appendChild(backdrop);

            // Delay agar animasi berjalan
            setTimeout(() => {
                toast.classList.remove('scale-0', 'opacity-0');
                toast.classList.add('scale-100', 'opacity-100');
                backdrop.classList.add('opacity-100');
            }, 100);

            // Auto close setelah 5s
            setTimeout(() => {
                closeToast('successToast');
            }, 5000);
        }
    });

    // Error Toast
    document.addEventListener('DOMContentLoaded', function () {
        const toast = document.getElementById('errorToast');
        if (toast) {
            // Add backdrop blur
            const backdrop = document.createElement('div');
            backdrop.id = 'errorToast_backdrop';
            backdrop.className = 'fixed inset-0 bg-black bg-opacity-20 backdrop-blur-sm z-40 opacity-0 transition-opacity duration-300';
            document.body.appendChild(backdrop);

            // Delay agar animasi berjalan
            setTimeout(() => {
                toast.classList.remove('scale-0', 'opacity-0');
                toast.classList.add('scale-100', 'opacity-100');
                backdrop.classList.add('opacity-100');
            }, 100);

            // Auto close setelah 5s
            setTimeout(() => {
                closeToast('errorToast');
            }, 5000);
        }
    });

    // Close toast when clicking backdrop
    document.addEventListener('click', function(e) {
        if (e.target.id.endsWith('_backdrop')) {
            const toastId = e.target.id.replace('_backdrop', '');
            closeToast(toastId);
        }
    });
  </script>

</body>
</html>
