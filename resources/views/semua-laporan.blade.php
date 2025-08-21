<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Detail Aduan</title>
  @vite('resources/css/app.css')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-pQ0ZJ+...etc" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
        <h2 class="text-2xl font-bold text-gray-900">Semua Aduan</h2>
        <div class="text-sm text-gray-500 flex flex-wrap items-center">
          <a href="/" class="text-blue-500 hover:underline whitespace-nowrap">Dashboard</a>
          <span class="mx-1 sm:mx-2">></span>
          <a href="#" class="text-gray-700 whitespace-nowrap">Jelajah Aduan</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Filter dan Search -->
  <div class="max-w-7xl mx-auto px-4 mt-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <input type="text" id="searchInput" placeholder="Cari berdasarkan Resi atau Masalah..."
        class="w-full sm:w-1/2 border border-gray-300 rounded-lg px-4 py-2 text-sm bg-white" />

      <select id="filterStatus"
        class="w-full sm:w-48 border border-gray-300 rounded-lg px-4 py-2 text-sm bg-white">
        <option value="">Semua Status</option>
        <option value="Progress">Progress</option>
        <option value="Selesai">Selesai</option>
      </select>
    </div>
  </div>

  <!-- Container Kartu -->
  <div class="max-w-7xl mx-auto px-4 mt-6 space-y-4" id="laporanCards"></div>

    <!-- Pagination -->
    <div class="max-w-7xl mx-auto px-4 mt-6">
        <div class="bg-white border border-gray-200 shadow-md rounded-lg p-4 flex flex-col items-center gap-3">
            <div id="paginationInfo" class="text-sm text-gray-600"></div>
            <div id="paginationButtons" class="flex flex-wrap justify-center gap-2"></div>
        </div>
    </div>

  <script src="{{ asset('js/semua-laporan.js') }}"></script>
</body>
</html>
