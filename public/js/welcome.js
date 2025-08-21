// Pagination variables
let laporanData = [];
let chartData = [];
let currentPage = 1;
let itemsPerPage = 5;
let filteredData = [];

// Update current time
function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleString("id-ID", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
    });
    document.getElementById("currentTime").textContent = timeString;
}

// Update statistics
function updateStatistics() {
    const progressCount = chartData.filter(
        (item) => item.status === "Progress"
    ).length;
    const successCount = chartData.filter(
        (item) => item.status === "Selesai"
    ).length;
    const pengajuanCount = chartData.filter(
        (item) => item.status === "Pengajuan"
    ).length;

    // Animate counters
    animateCounter("progressCount", progressCount);
    animateCounter("successCount", successCount);
    animateCounter("pengajuanCount", pengajuanCount);
}

// Counter animation
function animateCounter(elementId, targetValue) {
    const element = document.getElementById(elementId);
    let current = 0;
    const increment = targetValue / 30;
    const timer = setInterval(() => {
        current += increment;
        if (current >= targetValue) {
            element.textContent = targetValue;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current);
        }
    }, 50);
}

document.addEventListener("DOMContentLoaded", async function () {
    updateTime();
    setInterval(updateTime, 60000);

    try {
        const data = await fetchDataLaporan();
        inisialisasiData(data);
        inisialisasiDropdownTahun(data.chart);
        inisialisasiDropdownHarian(data.chart);
        pasangEventListenerDropdown();
    } catch (error) {
        console.error("Gagal memuat data laporan:", error);
    }
});

async function fetchDataLaporan() {
    const response = await fetch("/public/data");
    const data = await response.json();

    // Transform the latest_laporan data to match the expected format
    const transformedLatestLaporan = data.latest_laporan.map((item) => ({
        resi: item.resi,
        masalah: item.masalah,
        lampiran_url: item.lampiran_url,
        status: item.status,
        tanggal: item.tanggal_pengajuan,
    }));

    // Chart data stays as is
    const transformedChartData = data.chart.map((item) => ({
        status: item.status,
        tanggal: item.tanggal_pengajuan,
    }));

    return {
        latest_laporan: transformedLatestLaporan,
        chart: transformedChartData
    };
}

function renderLaporanCards(data) {
  const container = document.getElementById("laporanCarousel");
  container.innerHTML = "";

  // Use the latest_laporan data directly (it's already limited to 6 items from backend)
  data.forEach((item) => {
    const card = document.createElement("div");
    card.className = "swiper-slide h-auto";

    // Tentukan warna status
    let statusColorClass = "bg-gray-200 text-gray-800";
    if (item.status.toLowerCase() === "progress" || item.status.toLowerCase() === "proses") {
      statusColorClass = "bg-yellow-100 text-yellow-800";
    } else if (item.status.toLowerCase() === "selesai") {
      statusColorClass = "bg-green-100 text-green-800";
    } else if (item.status.toLowerCase() === "pengajuan") {
      statusColorClass = "bg-red-100 text-red-800";
    }

    // Format tanggal
    const formattedDate = new Date(item.tanggal).toLocaleDateString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric"
    });

    card.innerHTML = `
    <div class="w-full max-w-sm bg-white border border-gray-200 rounded-lg shadow hover:shadow-lg transition-shadow duration-300 flex flex-col h-full">
        <!-- Lampiran Gambar -->
        <div class="relative bg-gray-100 w-full h-48 flex items-center justify-center rounded-t-lg">
        <img src="${item.lampiran_url || 'https://placehold.co/800x400?text=Tidak+Ada+Lampiran'}"
            loading="lazy" class="h-full object-contain ${item.lampiran_url ? '' : 'opacity-60'}">
        </div>

        <!-- Konten -->
        <div class="flex flex-col flex-1 px-5 pb-5 justify-between">
        <div>
            <!-- Judul -->
            <a href="/detail/${item.resi}">
            <h5 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2 min-h-[3rem]">${item.masalah || 'Laporan Layanan Publik'}</h5>
            </a>

            <!-- Status -->
            <div class="flex items-center mb-4">
            <span class="${statusColorClass} text-xs font-semibold px-2 py-1 rounded">
                ${item.status}
            </span>
            </div>

            <!-- Informasi Resi dan Tanggal -->
            <div class="mb-4">
            <div class="text-sm text-gray-500">Tanggal:</div>
            <div class="text-lg font-bold text-gray-900">${formattedDate}</div>
            </div>
        </div>

        <!-- Tombol Lihat Detail dengan tanda panah -->
        <a href="/detail/${item.resi}"
            class="w-full flex items-center justify-center gap-2 text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2.5 transition-colors duration-200">
            Lihat Detail
            <i class="fas fa-arrow-right"></i>
        </a>
        </div>
    </div>
    `;

    container.appendChild(card);
  });

  // Inisialisasi ulang swiper
  if (window.laporanSwiper) {
    window.laporanSwiper.destroy(true, true);
  }

  window.laporanSwiper = new Swiper(".laporan-swiper", {
    slidesPerView: 1.1,
    spaceBetween: 16,
    loop: true,
    autoplay: {
      delay: 3000,
      disableOnInteraction: false,
      pauseOnMouseEnter: false
    },
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
      dynamicBullets: true
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    breakpoints: {
      480: { slidesPerView: 1.3 },
      640: { slidesPerView: 2 },
      768: { slidesPerView: 2.2 },
      1024: { slidesPerView: 3 },
      1280: { slidesPerView: 3.2 }
    },
    speed: 600,
    lazy: { loadPrevNext: true },
    a11y: {
      enabled: true,
      prevSlideMessage: 'Slide sebelumnya',
      nextSlideMessage: 'Slide selanjutnya',
    }
  });
}

function inisialisasiData(data) {
    laporanData = data.latest_laporan;
    chartData = data.chart;
    filteredData = [...laporanData];
    updateStatistics();
    renderLaporanCards(filteredData);
}

function inisialisasiDropdownTahun(data) {
    const tahunSet = new Set();
    const now = new Date();

    data.forEach((item) => {
        const date = new Date(item.tanggal);
        const tahun = date.getFullYear();
        tahunSet.add(tahun.toString());
    });

    tahunSet.add(now.getFullYear().toString());

    const tahunSelect = document.getElementById("tahunChartSelect");
    tahunSelect.innerHTML = "";

    [...tahunSet].sort().forEach((t) => {
        const opt = document.createElement("option");
        opt.value = t;
        opt.textContent = t;
        tahunSelect.appendChild(opt);
    });

    const currentYear = new Date().getFullYear().toString();
    tahunSelect.value = currentYear;
    generateChart(currentYear);
}

function inisialisasiDropdownHarian(data) {
    const tahunSet = new Set();
    const now = new Date();

    data.forEach((item) => {
        const date = new Date(item.tanggal);
        const tahun = date.getFullYear();
        tahunSet.add(tahun.toString());
    });

    tahunSet.add(now.getFullYear().toString());

    const tahunHarianSelect = document.getElementById("tahunHarianSelect");
    const bulanHarianSelect = document.getElementById("bulanHarianSelect");

    [...tahunSet].sort().forEach((t) => {
        const opt = document.createElement("option");
        opt.value = t;
        opt.textContent = t;
        tahunHarianSelect.appendChild(opt);
    });

    const current = new Date();
    tahunHarianSelect.value = current.getFullYear().toString();
    bulanHarianSelect.value = current.getMonth().toString(); // 0 - 11

    generateDailyChart(tahunHarianSelect.value, bulanHarianSelect.value);
}

function pasangEventListenerDropdown() {
    const tahunSelect = document.getElementById("tahunChartSelect");
    const tahunHarianSelect = document.getElementById("tahunHarianSelect");
    const bulanHarianSelect = document.getElementById("bulanHarianSelect");

    tahunSelect.addEventListener("change", () => {
        generateChart(tahunSelect.value);
    });

    tahunHarianSelect.addEventListener("change", () => {
        generateDailyChart(tahunHarianSelect.value, bulanHarianSelect.value);
    });

    bulanHarianSelect.addEventListener("change", () => {
        generateDailyChart(tahunHarianSelect.value, bulanHarianSelect.value);
    });
}

// Monthly chart
let chart;
function generateChart(tahun) {
    const monthly = {
        Pengajuan: Array(12).fill(0),
        Selesai: Array(12).fill(0),
        Progress: Array(12).fill(0),
    };

    chartData.forEach((item) => {
        const date = new Date(item.tanggal);
        const tahunItem = date.getFullYear();
        const bulan = date.getMonth(); // 0-11

        if (tahunItem.toString() === tahun) {
            const status = item.status;
            if (monthly[status] !== undefined) {
                monthly[status][bulan]++;
            }
        }
    });

    const options = {
        chart: {
            type: "bar",
            height: 350,
            toolbar: {
                show: true,
            },
        },
        series: [
            {
                name: "Pengajuan",
                data: monthly["Pengajuan"],
            },
            {
                name: "Progress",
                data: monthly["Progress"],
            },
            {
                name: "Selesai",
                data: monthly["Selesai"],
            },
        ],
        xaxis: {
            categories: [
                "Jan",
                "Feb",
                "Mar",
                "Apr",
                "Mei",
                "Jun",
                "Jul",
                "Agu",
                "Sep",
                "Okt",
                "Nov",
                "Des",
            ],
            labels: {
                rotate: -45,
                style: {
                    fontSize: "12px",
                },
            },
        },
        colors: ["#FF4560", "#FFA500", "#00E396"],
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: "45%",
                endingShape: "rounded",
            },
        },
        dataLabels: {
            enabled: false,
        },
        legend: {
            position: "top",
        },
        responsive: [
            {
                breakpoint: 640,
                options: {
                    chart: {
                        height: 320,
                    },
                    plotOptions: {
                        bar: {
                            columnWidth: "60%",
                        },
                    },
                    xaxis: {
                        labels: {
                            rotate: -45,
                            style: {
                                fontSize: "10px",
                            },
                        },
                    },
                    legend: {
                        position: "bottom",
                    },
                },
            },
        ],
    };

    if (chart) {
        chart.updateOptions(options);
    } else {
        chart = new ApexCharts(
            document.querySelector("#chartContainer"),
            options
        );
        chart.render();
    }
}

// Daily chart
let dailyChart;
function generateDailyChart(tahun, bulan) {
    const daysInMonth = new Date(tahun, parseInt(bulan) + 1, 0).getDate();

    const daily = {
        Pengajuan: Array(daysInMonth).fill(0),
        Selesai: Array(daysInMonth).fill(0),
        Progress: Array(daysInMonth).fill(0),
    };

    chartData.forEach((item) => {
        const date = new Date(item.tanggal);
        const tahunItem = date.getFullYear();
        const bulanItem = date.getMonth();

        if (tahunItem.toString() === tahun && bulanItem.toString() === bulan) {
            const tanggal = date.getDate() - 1; // 0-based index
            const status = item.status;
            if (daily[status] !== undefined && tanggal >= 0 && tanggal < daysInMonth) {
                daily[status][tanggal]++;
            }
        }
    });

    const options = {
        chart: {
            type: "line",
            height: 350,
            zoom: {
                enabled: false,
            },
            toolbar: {
                show: true,
                tools: {
                    download: true,
                },
            },
        },
        series: [
            { name: "Pengajuan", data: daily["Pengajuan"] },
            { name: "Progress", data: daily["Progress"] },
            { name: "Selesai", data: daily["Selesai"] },
        ],
        xaxis: {
            categories: Array.from({ length: daysInMonth }, (_, i) => i + 1),
            title: { text: "Tanggal" },
            labels: {
                rotate: -45,
                style: {
                    fontSize: window.innerWidth < 640 ? "10px" : "12px",
                },
                hideOverlappingLabels: true,
                showDuplicates: false,
                trim: true,
            },
            tickPlacement: "on",
        },
        stroke: {
            curve: "smooth",
            width: 2,
        },
        colors: ["#FF4560", "#FFA500", "#00E396"],
        legend: {
            position: "top",
        },
        dataLabels: {
            enabled: false,
        },
        responsive: [
            {
                breakpoint: 640,
                options: {
                    chart: {
                        height: 300,
                    },
                    xaxis: {
                        labels: {
                            rotate: -60,
                            style: {
                                fontSize: "9px",
                            },
                        },
                    },
                    legend: {
                        position: "bottom",
                    },
                },
            },
        ],
    };

    if (dailyChart) {
        dailyChart.updateOptions(options);
    } else {
        dailyChart = new ApexCharts(
            document.querySelector("#dailyChartContainer"),
            options
        );
        dailyChart.render();
    }
}
