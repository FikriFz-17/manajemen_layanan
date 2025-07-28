<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REGISTER - LAPOR KOMINFO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@fortawesome/fontawesome-free@6.4.0/js/all.min.js" crossorigin="anonymous"></script>
</head>
<body class="h-screen w-screen bg-white overflow-hidden">

    <div class="flex h-full">
        <!-- Sisi Kiri - Desktop & Tablet -->
        <div class="hidden md:flex w-full md:w-1/2 bg-[#262394] text-white flex-col items-center justify-center p-6 lg:p-10 relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-10 left-10 w-20 h-20 border-2 border-white rounded-full"></div>
                <div class="absolute top-32 right-16 w-16 h-16 border-2 border-white rounded-lg rotate-45"></div>
                <div class="absolute bottom-20 left-20 w-12 h-12 bg-white rounded-full"></div>
                <div class="absolute bottom-32 right-10 w-24 h-24 border-2 border-white rounded-full"></div>
            </div>

            <!-- Content -->
            <div class="relative z-10 text-center">
                <img src="{{ asset('images/logo-kebumen.png') }}" alt="Logo Kebumen" class="w-32 md:w-40 lg:w-48 mb-6 mx-auto">
                <h1 class="text-xl md:text-2xl font-bold leading-relaxed mb-4">
                    Dinas Komunikasi dan Informatika<br>
                    Kabupaten Kebumen
                </h1>
                <p class="text-blue-100 text-sm md:text-base max-w-md mx-auto">
                    Platform pelaporan digital untuk melayani masyarakat dengan lebih baik
                </p>
            </div>
        </div>

        <!-- Sisi Kanan - Form Login -->
        <div class="w-full md:w-1/2 flex flex-col justify-center px-6 md:px-12 lg:px-16 bg-white relative">
            <!-- Mobile Header -->
            <div class="md:hidden text-center mb-8 md:mt-5">
                <img src="{{ asset('images/logo-kebumen.png') }}" alt="Logo Kebumen" class="w-20 mx-auto mb-2">
                <h1 class="text-lg font-bold text-[#262394] mb-2">
                    DISKOMINFO KEBUMEN
                </h1>
            </div>

            <!-- Form Container -->
            <div class="w-full max-w-md mx-auto">
                <h2 class="text-2xl lg:text-2xl font-bold mb-2 text-black max-sm:hidden">LAPOR KOMINFO</h2>

                <!-- Success Message -->
                @if (session('success'))
                    <div class="bg-gradient-to-r from-green-50 to-emerald-100 border-l-4 border-green-500 p-4 mb-6 rounded-lg shadow-sm">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-500 text-lg"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                            </div>
                            <button type="button" onclick="this.parentElement.parentElement.style.display='none'"
                                    class="flex-shrink-0 ml-3 text-green-400 hover:text-green-600 transition-colors duration-200">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </div>
                @endif
                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 p-4 mb-6 rounded-lg shadow-sm">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-500 text-lg"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-red-800">Terdapat Kesalahan:</p>
                                <ul class="mt-2 space-y-1 text-sm text-red-700">
                                    @foreach ($errors->all() as $error)
                                        <li class="flex gap-2 text-justify">
                                            <span>•</span>
                                            <span class="flex-1">{{ $error }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <button type="button" onclick="this.parentElement.parentElement.style.display='none'"
                                    class="flex-shrink-0 ml-3 text-red-400 hover:text-red-600 transition-colors duration-200">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('register.submit') }}" class="space-y-6">
                    @csrf

                    <!-- Nama Lengkap -->
                    <div class="flex items-center border border-gray-300 rounded px-3 py-2">
                        <i class="fas fa-user text-gray-400 mr-3"></i>
                        <input type="text" value="{{old('nama')}}" name="nama" placeholder="Nama Lengkap" class="w-full outline-none bg-transparent" required>
                    </div>

                    <!-- Email -->
                    <div class="flex items-center border border-gray-300 rounded px-3 py-2">
                        <i class="fa-solid fa-envelope text-gray-400 mr-3"></i>
                        <input type="email" value="{{old ('email')}}" name="email" placeholder="Email" class="w-full outline-none bg-transparent" required>
                    </div>

                    <!-- Nomor Telepon -->
                    <div class="flex items-center border border-gray-300 rounded px-3 py-2">
                        <i class="fa-brands fa-whatsapp text-gray-400 mr-3"></i>
                        <input type="text" value="{{old ('phone')}}" name="phone" placeholder="Nomor Telepon/WhatsApp" class="w-full outline-none bg-transparent" required>
                    </div>

                    <!-- Password -->
                    <div class="flex items-center border border-gray-300 rounded px-3 py-2">
                        <i class="fas fa-lock text-gray-400 mr-3"></i>
                        <input type="password" id="password" name="password" placeholder="Password"
                            class="w-full outline-none bg-transparent" required>
                        <button type="button" onclick="togglePassword()" class="ml-2 text-gray-500 hover:text-gray-700">
                            <i id="toggleIcon" class="fas fa-eye"></i>
                        </button>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="flex items-center border border-gray-300 rounded px-3 py-2">
                        <i class="fas fa-lock text-gray-400 mr-3"></i>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi Password"
                            class="w-full outline-none bg-transparent" required>
                        <button type="button" onclick="togglePasswordConfirm()" class="ml-2 text-gray-500 hover:text-gray-700">
                            <i id="toggleConfirmIcon" class="fas fa-eye"></i>
                        </button>
                    </div>

                    <!-- Tombol Register -->
                    <button type="submit"
                            class="bg-[#262394] text-white w-full py-3 rounded-lg font-semibold hover:bg-[#1e1b75] transition">
                        <i class="fas fa-user-plus mr-2"></i> Daftar
                    </button>

                    <!-- Login Link -->
                    <div class="text-center text-sm pt-4">
                        <span class="text-gray-600">Sudah punya akun? </span>
                        <a href="{{ route('login') }}" class="text-[#262394] hover:text-[#1e1b75] font-semibold">Masuk</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- Script -->
  <script src="{{ asset('js/register.js') }}"></script>

</body>
</html>
