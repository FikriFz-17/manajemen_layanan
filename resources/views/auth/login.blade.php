<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN - LAPOR KOMINFO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@fortawesome/fontawesome-free@6.4.0/js/all.min.js" crossorigin="anonymous"></script>
</head>
<body class="h-screen w-screen bg-white overflow-hidden">

<div class="flex h-full">
    <!-- Sisi Kiri -->
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

    <!-- Sisi Kanan - Form Register -->
    <div class="w-full md:w-1/2 flex flex-col justify-center px-6 md:px-12 lg:px-16 bg-white relative">

        <!-- Mobile Header -->
        <div class="md:hidden text-center mb-8 md:mt-5">
            <img src="{{ asset('images/logo-kebumen.png') }}" alt="Logo Kebumen" class="w-20 mx-auto mb-2">
            <h1 class="text-lg font-bold text-[#262394] mb-2">DISKOMINFO KEBUMEN</h1>
        </div>


        <!-- Form Container -->
        <div class="w-full max-w-md mx-auto">
            <h2 class="text-2xl lg:text-2xl font-bold mb-2 text-black max-sm:hidden">LAPOR KOMINFO</h2>

            <!-- Error Message -->
            @if ($errors->has('login'))
                <div class="bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 p-4 mb-6 rounded-lg shadow-sm">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-red-500 text-lg"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-semibold text-red-800 mb-1">Login Gagal!</h3>
                            <p class="text-sm text-red-700">{{ $errors->first('login') }}</p>
                        </div>
                        <button type="button" onclick="this.parentElement.parentElement.style.display='none'"
                                class="flex-shrink-0 ml-3 text-red-400 hover:text-red-600 transition-colors duration-200">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}" class="space-y-6">
                @csrf

                <!-- Email -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <div class="flex items-center border-2 border-gray-300 rounded-lg px-4 py-3 focus-within:border-[#262394] transition">
                        <i class="fas fa-user text-gray-400 mr-3"></i>
                        <input type="text" name="email" id="name" placeholder="Email" value="{{old('email')}}"
                               class="w-full outline-none bg-transparent text-gray-900 placeholder-gray-500" required>
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <div class="flex items-center border-2 border-gray-300 rounded-lg px-4 py-3 focus-within:border-[#262394] transition">
                        <i class="fas fa-lock text-gray-400 mr-3"></i>
                        <input type="password" name="password" id="password" placeholder="Password"
                               class="w-full outline-none bg-transparent text-gray-900 placeholder-gray-500" required>
                        <button type="button" onclick="togglePassword('password', 'iconPass')" class="ml-2 text-gray-400 hover:text-gray-600 focus:outline-none">
                            <i id="iconPass" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-end text-sm">
                        <a href="#" class="text-[#262394] hover:text-[#1e1b75] font-medium">Lupa password?</a>
                    </div>

                <!-- Tombol Login -->
                <button type="submit"
                        class="bg-[#262394] text-white w-full py-3 rounded-lg font-semibold hover:bg-[#1e1b75] transition">
                    <i class="fas fa-sign-in-alt mr-2"></i>Log In
                </button>

                <!-- Register Link -->
                <div class="text-center text-sm pt-4">
                    <span class="text-gray-600">Belum punya akun? </span>
                    <a href="{{ route('register') }}" class="text-[#262394] hover:text-[#1e1b75] font-semibold">Register</a>
                </div>
            </form>
            <!-- Icon Home & Customer Service -->
            <div class="flex justify-center gap-6 mt-6 text-[#262394] text-sm">
                <a href="{{route('welcome')}}" class="flex flex-col items-center hover:text-[#1e1b75] transition">
                    <i class="fas fa-home text-xl mb-1"></i>
                </a>
            </div>

        </div>
    </div>
</div>

<script>
    // Toggle login password visibility
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
</script>

</body>
</html>
