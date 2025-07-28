<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Email - LAPOR KOMINFO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 py-10 font-sans">

    <div class="max-w-md mx-auto bg-white rounded-xl shadow-md p-8 text-center">

        {{-- Logo --}}
        <div class="mb-6">
            <img src="{{ asset('images/logo-kebumen.png') }}" alt="Logo KOMINFO"
                 class="w-20 h-20 mx-auto object-contain">
        </div>

        {{-- Judul --}}
        <h2 class="text-gray-800 text-2xl font-bold mb-2">Verifikasi Email Anda</h2>

        {{-- Pesan --}}
        <p class="text-gray-600 text-base leading-relaxed mb-6">
            Kami telah mengirimkan email verifikasi ke alamat email Anda. Silakan cek email Anda untuk melanjutkan proses verifikasi.
        </p>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded relative">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
                <button type="button" onclick="this.parentElement.style.display='none'"
                        class="absolute right-3 top-2 text-green-600 hover:text-green-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        {{-- Error Message --}}
        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded relative">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
                <button type="button" onclick="this.parentElement.style.display='none'"
                        class="absolute right-3 top-2 text-red-600 hover:text-red-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        {{-- Form Resend --}}
        <form action="{{ route('verification.send') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
            <button type="submit"
                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded transition duration-200">
                <i class="fas fa-paper-plane mr-2"></i> Kirim Ulang Email Verifikasi
            </button>
        </form>

        {{-- Countdown Timer --}}
        <div class="mt-4 text-sm text-gray-600">
            Link verifikasi akan kadaluarsa dalam
            <span id="countdown" class="font-bold text-red-600">--:--</span>
        </div>


        {{-- Footer --}}
        <p class="text-xs text-gray-400 mt-8">
            © 2025 Dinas Komunikasi dan Informatika Kabupaten Kebumen
        </p>

    </div>

<!-- Script -->
  <script src="{{ asset('js/verify-email.js') }}"></script>

</body>
</html>
