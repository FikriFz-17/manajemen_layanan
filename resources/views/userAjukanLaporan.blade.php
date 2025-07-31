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
      <a href="{{ route('ajukanLaporan') }}" class="flex items-center gap-2 hover:text-gray-300 p-2 rounded transition-colors bg-white bg-opacity-20"><i class="fas fa-plus-circle"></i> Ajukan Laporan</a>
      <a href="{{ asset('storage/user_manual/Panduan Pengajuan Pengguna.pdf') }}" download class="flex items-center gap-2 hover:text-gray-300 p-2 rounded transition-colors"><i class="fa-solid fa-file-arrow-down"></i> User Manual</a>

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
          <span class="font-medium">{{Auth::user()->nama}}</span>
          <i class="fas fa-chevron-down text-sm"></i>
        </button>

        <!-- Desktop Dropdown Menu -->
        <div id="userDropdownDesktop" class="absolute right-0 mt-2 w-40 bg-white rounded shadow hidden z-20">
          <!-- set Profile -->
          <a href="{{ route('setProfile', ['return_to' => url()->current()]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" id="reset-btn"><i class="fa-solid fa-user mr-2"></i>Set Profile</a>
          <!-- logout -->
          <form action="{{ route('logout.submit') }}" method="post">
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

    <!-- Validation Errors
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
    @endif -->

    <!-- FORM PENGAJUAN - Responsive -->
    <div class="bg-white p-4 md:p-6 lg:p-8 shadow-md rounded-lg w-full max-w-none">
      <h2 class="text-xl md:text-2xl font-semibold mb-4 md:mb-6 text-[#262394] text-center lg:text-left">Ajukan Laporan Permasalahan</h2>
      <form action="{{ route('laporan.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="form-ajuan">
        @csrf
        <!-- Row: Nama, Instansi & Tanggal -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 lg:gap-6">
          <div class="flex flex-col">
            <label for="nama" class="block text-sm font-medium mb-2 text-gray-700">
                <i class="fas fa-user mr-2 text-[#262394]"></i>Nama
            </label>
            <input type="text" id="nama" name="nama"
                class="w-full border-b-2 border-gray-300 focus:outline-none focus:border-[#262394] py-3 px-2 text-base transition-colors duration-200"
                placeholder="Nama lengkap Anda"
                value="{{ Auth::user()->nama }}"
                readonly>
          </div>
          <div class="flex flex-col">
            <label for="instansi" class="block text-sm font-medium mb-2 text-gray-700">
                <i class="fas fa-network-wired mr-2 text-[#262394]"></i>Instansi
            </label>
            <input type="text" id="instansi" name="instansi"
                class="w-full border-b-2 border-gray-300 focus:outline-none focus:border-[#262394] py-3 px-2 text-base transition-colors duration-200"
                placeholder="Instansi anda"
                value="{{ Auth::user()->instansi }}"
                readonly>
          </div>
          <div class="flex flex-col md:col-span-2 xl:col-span-1">
            <label for="tanggal" class="block text-sm font-medium mb-2 text-gray-700">
              <i class="fas fa-calendar mr-2 text-[#262394]"></i>Tanggal Pengajuan
            </label>
            <input type="date" id="tanggal" name="tanggal"
                   class="w-full border-b-2 border-gray-300 focus:outline-none focus:border-[#262394] py-3 px-2 text-base transition-colors duration-200">
                @error('tanggal')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
          </div>
        </div>

        <!-- Row: Permasalahan -->
        <div class="flex flex-col">
          <label for="masalah" class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-exclamation-triangle mr-2 text-[#262394]"></i>Permasalahan
          </label>
          <input type="text" id="masalah" name="masalah"
                 class="w-full border-b-2 border-gray-300 focus:outline-none focus:border-[#262394] py-3 px-2 text-base transition-colors duration-200"
                 placeholder="Judul atau topik permasalahan">
                @error('masalah')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
        </div>

        <!-- Deskripsi -->
        <div class="flex flex-col">
          <label for="deskripsi" class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-align-left mr-2 text-[#262394]"></i>Deskripsi
          </label>
          <textarea id="deskripsi" name="deskripsi" rows="5"
                    class="w-full border-2 border-gray-300 focus:outline-none focus:border-[#262394] py-3 px-3 text-base rounded-md transition-colors duration-200 resize-none"
                    placeholder="Jelaskan permasalahan secara detail, termasuk kapan terjadi, dampak yang dirasakan, dan informasi lain yang relevan..."></textarea>
                @error('deskripsi')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
        </div>

        <!-- File Upload (Optional) -->
        <div class="flex flex-col">
            <!-- label -->
          <label for="lampiran" class="block text-sm font-medium mb-2 text-gray-700">
            <i class="fas fa-paperclip mr-2 text-[#262394]"></i>Lampiran (Opsional)
          </label>
          <!-- Area Upload -->
          <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 hover:border-[#262394] transition-colors duration-200">
            <input type="file" id="lampiran" name="lampiran" class="hidden" accept="image/*,.pdf" onchange="previewLampiran(event)">
            <label for="lampiran" class="cursor-pointer flex flex-col items-center justify-center text-gray-500 hover:text-[#262394]">
              <i class="fas fa-cloud-upload-alt text-3xl mb-2"></i>
              <span class="text-sm text-center">Klik untuk upload file</span>
              <span class="text-xs text-gray-400 mt-1">Format: JPG, PNG, PDF (Max: 5MB)</span>
            </label>
          </div>
        </div>

        <!-- Tombol -->
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 pt-4">
          <button type="submit" id="submitButton" class="w-full sm:w-auto bg-[#262394] text-white px-8 py-3 rounded-lg hover:bg-[#1e1b75] font-semibold transition-colors duration-200 flex items-center justify-center disabled:bg-gray-400 disabled:cursor-not-allowed" disabled>
            <i class="fas fa-paper-plane mr-2"></i>
            Ajukan Laporan
          </button>
          <button type="reset" id="reset-btn" class="w-full sm:w-auto bg-gray-500 text-white px-8 py-3 rounded-lg hover:bg-gray-600 font-semibold transition-colors duration-200 flex items-center justify-center">
            <i class="fas fa-undo mr-2"></i>
            Reset Form
          </button>
        </div>
      </form>
    </div>

    <!-- Info Card -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4 w-full max-w-none">
      <div class="flex items-start">
        <i class="fas fa-info-circle text-blue-500 text-xl mr-3 mt-1 flex-shrink-0"></i>
        <div class="flex-1">
          <h3 class="text-sm font-semibold text-blue-800 mb-2">Informasi Penting:</h3>
          <ul class="text-sm text-blue-700 space-y-1">
            <li>• Anda harus melengkapi profile anda terlebih dahulu pada menu akun > set profile</li>
            <li>• Terkait pengajuan laporan mohon isi berdasarkan informasi yang diminta</li>
            <li>• Sebelum mengajukan laporan anda dapat membaca user manual yang sudah disediakan</li>
            <li>• Untuk masalah urgent, hubungi customer service di menu sidebar</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- Script -->
  <script src="{{ asset('js/userAjukanLaporan.js') }}"></script>

</body>
</html>
