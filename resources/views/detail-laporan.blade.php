<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Detail Aduan</title>
  @vite('resources/css/app.css')
  <script src="https://unpkg.com/@fortawesome/fontawesome-free@6.4.0/js/all.min.js" crossorigin="anonymous"></script>
  <style>
    .logo-bg {
      background: linear-gradient(135deg, #3b4cb8, #4f46e5);
    }
  </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Breadcrumb -->
    <div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-0">
        <!-- Title -->
        <h2 class="text-2xl font-bold text-gray-900">Detail Aduan</h2>

        <!-- Breadcrumb Links -->
        <div class="text-sm text-gray-500 flex flex-wrap items-center">
            <a href="/" class="text-blue-500 hover:underline whitespace-nowrap">Dashboard</a>
            <span class="mx-1 sm:mx-2">></span>
            <a href="/semua/laporan" class="text-blue-500 hover:underline whitespace-nowrap">Jelajah Aduan</a>
            <span class="mx-1 sm:mx-2">></span>
            <span class="text-gray-700 whitespace-nowrap">Detail Aduan</span>
        </div>
        </div>
    </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-2 lg:gap-4">
            <!-- Main Content -->
            <div class="lg:col-span-3 w-full">
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <!-- Judul dan Status -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                        <h3 class="text-lg text-gray-600">
                            Rincian Aduan : <span class="font-bold text-gray-900">{{ $laporan->resi }}</span>
                        </h3>
                        <div class="flex space-x-2">
                            @switch($laporan->status)
                                @case('Pengajuan')
                                    <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-sm font-medium">
                                        {{ $laporan->status }}
                                    </span>
                                    @break

                                @case('Progress')
                                    <span class="bg-yellow-100 text-yellow-600 px-3 py-1 rounded-full text-sm font-medium">
                                        {{ $laporan->status }}
                                    </span>
                                    @break

                                @case('Selesai')
                                    <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm font-medium">
                                        {{ $laporan->status }}
                                    </span>
                                    @break

                                @default
                                    <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-sm font-medium">
                                        {{ $laporan->status }}
                                    </span>
                            @endswitch
                        </div>
                    </div>

                    <!-- Divider atas gambar -->
                    <hr class="mb-4 border-gray-200">

                    <!-- Gambar (Jika ada) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        @if ($laporan->lampiran)
                            <div class="aspect-video bg-gray-200 rounded-lg overflow-hidden">
                                <a href="{{ asset('storage/' . $laporan->lampiran) }}">
                                    <img src="{{ asset('storage/' . $laporan->lampiran) }}"
                                        alt="Dokumentasi 1"
                                        class="w-full h-full object-cover">
                                </a>
                            </div>
                        @else
                            <div class="aspect-video bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center">
                                <img src="https://placehold.co/600x400?text=Tidak+Ada+Lampiran" alt="Placeholder"
                                    class="w-full h-full object-contain opacity-60">
                            </div>
                        @endif
                    </div>

                    <!-- Tanggal -->
                    <div class="mb-2">
                        <p class="text-gray-600 font-medium">
                            {{ \Carbon\Carbon::parse($laporan->tanggal_pengajuan)->translatedFormat('j F Y') }}
                        </p>
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-6">
                        <p class="text-gray-700 leading-relaxed">
                            {{ $laporan->deskripsi }}
                        </p>
                    </div>

                    <!-- Divider atas tombol -->
                    <hr class="mb-4 border-gray-200">

                    <!-- Tombol Aksi -->
                    <div class="flex flex-col items-start space-y-2 relative">
                        <button onclick="toggleShareMenu()"
                            class="flex items-center space-x-2 bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z">
                                </path>
                            </svg>
                            <span>Bagikan </span>
                        </button>

                        <!-- Menu Share -->
                        <div id="shareMenu" class="hidden absolute top-full left-0 mt-12 bg-white rounded-lg shadow-lg border p-4 z-10 min-w-[200px] opacity-0 scale-90 transition-all duration-300 ease-out">
                            <div class="flex items-center gap-3">
                                <!-- Facebook -->
                                <button onclick="shareToFacebook()"
                                    class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white hover:bg-blue-700 hover:-translate-y-1 transition-all duration-200">
                                    <i class="fab fa-facebook-f text-lg"></i>
                                </button>

                                <!-- WhatsApp -->
                                <button onclick="shareToWhatsApp()"
                                    class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center text-white hover:bg-green-600 hover:-translate-y-1 transition-all duration-200">
                                    <i class="fab fa-whatsapp text-lg"></i>
                                </button>

                                <!-- X (Twitter) -->
                                <button onclick="shareToX()"
                                    class="w-12 h-12 bg-black rounded-full flex items-center justify-center text-white hover:bg-gray-800 hover:-translate-y-1 transition-all duration-200">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                    </svg>
                                </button>

                                <!-- Copy Link -->
                                <button onclick="copyLink()"
                                    class="w-12 h-12 bg-black rounded-full flex items-center justify-center text-white hover:bg-gray-700 hover:-translate-y-1 transition-all duration-200">
                                    <i class="fas fa-link text-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h4 class="text-lg font-bold mb-4 text-gray-800">Progres Aduan</h4>

                    <ol class="relative border-s border-gray-200 dark:border-gray-700 ms-2">
                        @if ($statusLaporan->pengajuan_tanggal != null)
                        <!-- Tahapan 1: Pengajuan -->
                        <li class="mb-10 ms-6">
                            <span class="absolute flex items-center justify-center w-6 h-6 bg-red-100 rounded-full -start-3 ring-8 ring-white">
                                <i class="fa-solid fa-bullhorn text-red-600 text-xs"></i>
                            </span>
                            <h3 class="text-base font-semibold text-gray-900">Pengajuan</h3>
                            <time class="block mb-1 text-sm text-gray-500">
                                {{ $statusLaporan->pengajuan_tanggal ? \Carbon\Carbon::parse($statusLaporan->pengajuan_tanggal)->translatedFormat('j F Y') : 'Belum diajukan' }}
                            </time>
                            <p class="text-sm text-gray-600">Laporan berhasil diajukan oleh pelapor.</p>
                        </li>
                        @endif

                        @if ($statusLaporan->progress_tanggal != null)
                        <!-- Tahapan 2: Diproses -->
                        <li class="mb-10 ms-6">
                            <span class="absolute flex items-center justify-center w-6 h-6 bg-yellow-100 rounded-full -start-3 ring-8 ring-white">
                                <i class="fa-solid fa-bars-progress text-yellow-600 text-xs"></i>
                            </span>
                            <h3 class="text-base font-semibold text-gray-900">Progress</h3>
                            <time class="block mb-1 text-sm text-gray-500">
                                {{ $statusLaporan->progress_tanggal ? \Carbon\Carbon::parse($statusLaporan->progress_tanggal)->translatedFormat('d M Y') : 'Belum diproses' }}
                            </time>
                            <p class="text-sm text-gray-600">Laporan sedang dalam proses tindak lanjut.</p>
                        </li>
                        @endif

                        @if ($statusLaporan->selesai_tanggal != null)
                        <!-- Tahapan 3: Selesai -->
                        <li class="ms-6">
                            <span class="absolute flex items-center justify-center w-6 h-6 bg-green-100 rounded-full -start-3 ring-8 ring-white">
                                <i class="fas fa-check-circle text-green-700 text-xs"></i>
                            </span>
                            <h3 class="text-base font-semibold text-gray-900">Selesai</h3>
                            <time class="block mb-1 text-sm text-gray-500">
                                {{ $statusLaporan->selesai_tanggal ? \Carbon\Carbon::parse($statusLaporan->selesai_tanggal)->translatedFormat('d M Y') : 'Belum selesai' }}
                            </time>
                            <p class="text-sm text-gray-600">Laporan telah diselesaikan oleh pihak terkait.</p>
                        </li>
                        @endif
                    </ol>

                </div>
            </div>

        </div>

    </div>

  <!-- Script -->
  <script src="{{ asset('js/detail-laporan.js') }}"></script>
</body>
</html>
