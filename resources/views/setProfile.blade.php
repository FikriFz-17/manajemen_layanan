<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Ajukan Laporan - Kominfo Kebumen</title>
  <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>
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
    <!-- main content -->
    <main class="flex-1 p-4 lg:p-8">
        <div class="max-w-4xl mx-auto bg-white p-6 sm:p-8 rounded-lg shadow-md">

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
                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Instansi</label>
                        <!-- Trigger input -->
                        <input
                            type="text"
                            id="InstanceToggle"
                            placeholder="Pilih instansi..."
                            readonly
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm cursor-pointer"
                            onclick="openInstanceModal()"
                            value="{{ Auth::user()->instansi }}"
                            data-default="{{ Auth::user()->instansi }}"
                            name="instansi"
                        >
                        <!-- Modal -->
                        <div id="InstanceModal" class="fixed inset-0 z-50 items-start justify-center bg-black bg-opacity-40 py-12 hidden">
                            <div class="bg-white rounded-lg shadow-lg w-full max-w-xl p-6 relative">
                                <!-- Tombol close -->
                                <button type="button" onclick="closeInstanceModal()" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-xl font-bold">
                                    &times;
                                </button>

                                <h2 class="text-xl font-semibold text-gray-800 mb-6">Pilih Instansi</h2>

                                <!-- Pilihan Tipe Instansi -->
                                <div class="grid grid-cols-2 gap-3 mb-6">
                                    <button type="button" onclick="setDesa()" class="w-full px-3 py-2 text-sm font-medium bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition">
                                        Desa
                                    </button>
                                    <button type="button" onclick="setPemda()" class="w-full px-3 py-2 text-sm font-medium bg-green-100 text-green-700 rounded-md hover:bg-green-200 transition">
                                        Pemerintah Daerah
                                    </button>
                                </div>

                                <!-- Informasi Instansi Desa -->
                                <div id="desa-options" class="hidden mb-6">
                                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Informasi Instansi Desa</h3>

                                    <div class="flex flex-col md:flex-row gap-4">
                                        <!-- Kecamatan -->
                                        <div class="flex-1">
                                            <label for="kecamatan" class="block text-sm mb-1 text-gray-700">Kecamatan</label>
                                            <select id="kecamatan" onchange="loadDesa()">
                                                <option value="">-- Pilih Kecamatan --</option>
                                            </select>
                                        </div>

                                        <!-- Desa -->
                                        <div class="flex-1">
                                            <label for="desa" class="block text-sm mb-1 text-gray-700">Desa</label>
                                            <select id="desa">
                                                <option value="">-- Pilih Desa --</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Informasi Instansi Pemda -->
                                <div id="pemda-options" class="hidden mb-6">
                                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Informasi Instansi Umum</h3>
                                    <label for="pemda" class="block text-sm mb-1">Nama Instansi</label>
                                    <select id="pemda">
                                        <option value="">-- Pilih Instansi --</option>
                                    </select>
                                </div>

                                <div class="flex justify-between">
                                    <button type="button" onclick="resetInstanceSelection()" class="px-4 py-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 text-sm font-medium">
                                        Reset Pilihan
                                    </button>
                                    <button type="button" onclick="saveInstance()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium">
                                        Simpan
                                    </button>
                                    <input type="hidden" id="jenisInstansi" value="" name="jenis_instansi">
                                </div>
                            </div>
                        </div>
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

    function openInstanceModal() {
        const modal = document.getElementById('InstanceModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeInstanceModal() {
        const modal = document.getElementById('InstanceModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    function saveInstance(){
        const kecamatanSelect = document.getElementById('kecamatan');
        const desaSelect = document.getElementById('desa');
        const pemdaSelect = document.getElementById('pemda');
        const instanceToggle = document.getElementById('InstanceToggle');

        // Cek apakah desa atau pemda yang sedang ditampilkan
        const isDesa = !document.getElementById('desa-options').classList.contains('hidden');
        const isPemda = !document.getElementById('pemda-options').classList.contains('hidden');

        if (isDesa) {
            const kecamatanText = kecamatanSelect.options[kecamatanSelect.selectedIndex].text;
            const desaText = desaSelect.options[desaSelect.selectedIndex].text;

            if (!kecamatanSelect.value || !desaSelect.value) {
                closeInstanceModal()
                return
            }

            instanceToggle.value = `Kecamatan ${kecamatanText}, Desa ${desaText}`;
        }

        if (isPemda) {
            const pemdaText = pemdaSelect.options[pemdaSelect.selectedIndex].text;

            if (!pemdaSelect.value) {
                closeInstanceModal()
                return
            }

            instanceToggle.value = pemdaText;
        }

        closeInstanceModal();
    }

    function resetInstanceSelection(){
        // Reset TomSelect UI
        kecamatanInstance.clear();
        desaSelectInstance.clear();
        pemdaInstance.clear();

        // Set kembali value input ke default
        const input = document.getElementById('InstanceToggle');
        const defaultValue = input.getAttribute('data-default');
        input.value = defaultValue;
    }

    function setDesa() {
        document.getElementById('desa-options')?.classList.remove('hidden');
        document.getElementById('pemda-options')?.classList.add('hidden');
        document.getElementById('jenisInstansi').value = 'desa';
    }

    function setPemda() {
        document.getElementById('desa-options')?.classList.add('hidden');
        document.getElementById('pemda-options')?.classList.remove('hidden');
        document.getElementById('jenisInstansi').value = 'pemda';
    }


    // === Load Dropdown ===
    let kecamatanData = []
    document.addEventListener('DOMContentLoaded', () => {
        fetch('/api/kecamatan')
            .then(response => response.json())
            .then(data => {
                kecamatanData = data.data.map(item => ({
                    id: item.kecamatan_id,
                    nama_kecamatan: item.kecamatan_nama
                }));
                loadKecamatan()
            })
            .catch(err => console.error('Gagal mengambil data kecamatan:', err));
    })

    function loadKecamatan() {
        const kecamatanSelect = document.getElementById('kecamatan');
        kecamatanSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';

        kecamatanData.forEach(k => {
            const option = document.createElement('option');
            option.value = k.id;
            option.textContent = k.nama_kecamatan;
            kecamatanSelect.appendChild(option);
        });

        // Inisialisasi TomSelect setelah opsi dimasukkan
        kecamatanInstance = new TomSelect("#kecamatan", {
            maxOptions: kecamatanData.length
        });
    }

    let desaSelectInstance;
    document.addEventListener('DOMContentLoaded', () => {
        desaSelectInstance = new TomSelect("#desa", {
            placeholder: "-- Pilih Desa --"
        });

    });

    function loadDesa() {
        const desaSelect = document.getElementById('desa');
        const kecamatanId = document.getElementById('kecamatan').value;

        if (!kecamatanId) {
            desaSelectInstance.clearOptions();
            desaSelectInstance.addOption({ value: '', text: '-- Pilih Desa --' });
            desaSelectInstance.setValue('');
            return;
        }

        fetch(`/api/desa?kecamatan_id=${kecamatanId}`)
            .then(res => res.json())
            .then(data => {
                const dataDesa = data.data.map(item => ({
                    value: item.kode_desa,
                    text: item.nama_desa
                }));

                // Reset dan isi ulang opsi TomSelect
                desaSelectInstance.clearOptions();
                desaSelectInstance.addOptions(dataDesa);
                desaSelectInstance.setValue('');
            })
            .catch(err => console.error('Gagal mengambil data desa:', err));
    }

    let pemdaData = [];
    document.addEventListener('DOMContentLoaded', () => {
        fetch('/api/pemda')
            .then(res => res.json())
            .then(data => {
                pemdaData = data.data.map(item => ({
                    id: item.pemda_id,
                    nama: item.pemda_nama
                }));
                loadPemda();
            })
            .catch(err => console.error('Gagal mengambil data pemda:', err));
    });

    function loadPemda() {
        const pemdaSelect = document.getElementById('pemda');
        pemdaSelect.innerHTML = '<option value="">-- Pilih Instansi --</option>';

        pemdaData.forEach(p => {
            const option = document.createElement('option');
            option.value = p.id;
            option.textContent = p.nama;
            pemdaSelect.appendChild(option);
        });

        // Inisialisasi TomSelect setelah opsi dimasukkan
        pemdaInstance = new TomSelect("#pemda", {
            maxOptions: pemdaData.length
        })
    }
  </script>

</body>
</html>
