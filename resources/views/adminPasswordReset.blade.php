<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Reset Password Admin - Kominfo Kebumen</title>
  <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.css" rel="stylesheet">
  <!-- CropperJS CDN -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>
  @vite('resources/css/app.css')
  <script src="https://unpkg.com/@fortawesome/fontawesome-free@6.4.0/js/all.min.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 h-screen flex">

  <!-- Mobile Overlay -->
  <div id="mobileOverlay" class="fixed inset-0 bg-black/30 bg-opacity-50 z-40 hidden lg:hidden"></div>

  <!-- Sidebar -->
  <div id="sidebar" class="w-64 bg-[#262394] text-white flex flex-col p-6 fixed lg:relative h-full z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
    <img src="{{ asset('images/logo-kebumen.png') }}" alt="Logo" class="w-20 mx-auto mb-4">
    <h1 class="text-lg font-bold text-center mb-8">Diskominfo Kebumen</h1>
    <nav class="flex flex-col gap-4">
      <a href="{{ route('adminDashboard') }}" class="flex items-center gap-2 hover:text-gray-300 p-2 rounded transition-colors"><i class="fas fa-home"></i>Dashboard</a>
      <a href="{{ route('userManagement') }}" class="flex items-center gap-2 hover:text-gray-300 p-2 rounded transition-colors"><i class="fa-solid fa-user"></i>User Management</a>
      <a href="#" class="flex items-center gap-2 hover:text-gray-300 p-2 rounded transition-colors"><i class="fa-solid fa-file-arrow-down"></i> User Manual</a>

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
    
  <!-- Main Content -->
  <div class="flex-1 lg:ml-0 p-4 lg:p-6 overflow-auto">

    <!-- Mobile Header with Hamburger -->
    <div class="lg:hidden flex justify-between items-center mb-6 bg-white p-4 rounded shadow">
      <button id="hamburgerBtn" class="text-2xl text-[#262394] focus:outline-none">
        <i class="fas fa-bars"></i>
      </button>
      <h1 class="text-xl font-bold text-[#262394]">Ganti Kata Sandi</h1>
      <div class="relative inline-block text-left">
        <button onclick="toggleDropdown()" class="flex items-center gap-2 focus:outline-none">
          <i class="fas fa-user-circle text-2xl text-[#262394]"></i>
          <i class="fas fa-chevron-down text-sm text-[#262394]"></i>
        </button>
        <!-- Mobile Dropdown Menu -->
        <div id="userDropdown" class="absolute right-0 mt-2 w-40 bg-white rounded shadow hidden z-20 border">
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
          <span class="font-medium">Admin</span>
          <i class="fas fa-chevron-down text-sm"></i>
        </button>
        <!-- Desktop Dropdown Menu -->
        <div id="userDropdownDesktop" class="absolute right-0 mt-2 w-40 bg-white rounded shadow hidden z-20">
          <!-- logout -->
          <form action="{{ route('logout.submit') }}" method="post">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-100"><i class="fas fa-sign-out-alt mr-2"></i>Logout</button>
          </form>
        </div>
      </div>
    </div>

    <!-- main content -->
         <div class="max-w-md mx-auto bg-white rounded-xl shadow-md p-8 text-center">

        <!-- Logo -->
        <div class="mb-6">
            <img src="{{ asset('images/logo-kebumen.png') }}" alt="Logo KOMINFO"
                class="w-20 h-20 mx-auto object-contain">
        </div>

        <!-- Judul -->
        <h2 class="text-gray-800 text-2xl font-bold mb-10">Atur Ulang Kata Sandi</h2>

        <!-- Form Reset Password -->
        <form method="POST" action="{{ route('updateAdminPass.submit') }}" class="mt-2 space-y-3 text-left">
            @csrf
            @method('PUT')
            <!-- Password Lama -->
            <div>
                <label for="password_lama" class="block text-sm font-medium text-gray-700 mb-2">Password Lama</label>
                <div class="flex items-center border-2 border-gray-300 rounded-lg px-4 py-3 focus-within:border-[#262394] transition">
                    <i class="fas fa-lock text-gray-400 mr-3"></i>
                    <input type="password" name="password_lama" id="password_lama" placeholder="Password Lama"
                            class="w-full outline-none bg-transparent text-gray-900 placeholder-gray-500" required>
                    <button type="button" onclick="togglePasswordLama()" class="ml-2 text-gray-400 hover:text-gray-600 focus:outline-none">
                        <i id="togglePassLama" class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password_lama')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password baru -->
            <div>
                <label for="password_baru" class="block text-sm font-medium text-gray-700 mb-2">Password baru</label>
                <div class="flex items-center border-2 border-gray-300 rounded-lg px-4 py-3 focus-within:border-[#262394] transition">
                    <i class="fas fa-lock text-gray-400 mr-3"></i>
                    <input type="password" name="password_baru" id="password_baru" placeholder="Password Baru"
                            class="w-full outline-none bg-transparent text-gray-900 placeholder-gray-500" required>
                    <button type="button" onclick="togglePasswordBaru()" class="ml-2 text-gray-400 hover:text-gray-600 focus:outline-none">
                        <i id="togglePassbaru" class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password_baru')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Konfirmasi Password baru -->
            <div>
                <label for="password_baru_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                <div class="flex items-center border-2 border-gray-300 rounded-lg px-4 py-3 focus-within:border-[#262394] transition">
                    <i class="fas fa-lock text-gray-400 mr-3"></i>
                    <input type="password" name="password_baru_confirmation" id="password_baru_conformation" placeholder="Konfirmasi Password Baru"
                            class="w-full outline-none bg-transparent text-gray-900 placeholder-gray-500" required>
                    <button type="button" onclick="toggleConfirmPasswordBaru()" class="ml-2 text-gray-400 hover:text-gray-600 focus:outline-none">
                        <i id="toggleConfirmIconPassBaru" class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password_baru_confirmation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tombol Submit -->
            <button type="submit"
                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded transition duration-200">
                <i class="fas fa-lock mr-2"></i> Atur Ulang Kata Sandi
            </button>
        </form>

        <!-- Footer -->
        <p class="text-xs text-gray-400 mt-8">
            © 2025 Dinas Komunikasi dan Informatika Kabupaten Kebumen
        </p>
    </div>
  </div>

  <!-- Script -->
  <script src="{{ asset('js/adminResetPassword.js') }}"></script>
</body>
</html>
