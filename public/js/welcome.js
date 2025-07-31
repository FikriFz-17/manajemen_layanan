// Pagination variables
let laporanData = [];
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
    const progressCount = laporanData.filter(
        (item) => item.status === "Progress"
    ).length;
    const successCount = laporanData.filter(
        (item) => item.status === "Selesai"
    ).length;
    const pengajuanCount = laporanData.filter(
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

// Render table
function renderTable() {
    const tableBody = document.getElementById("laporanTable");
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex =
        itemsPerPage === 50 ? filteredData.length : startIndex + itemsPerPage;
    const currentData = filteredData.slice(startIndex, endIndex);

    tableBody.innerHTML = "";

    currentData.forEach((item, index) => {
        let statusClass = "";
        if (item.status === "Progress") {
            statusClass = "bg-yellow-100 text-blue-800";
        } else if (item.status === "Selesai") {
            statusClass = "bg-green-100 text-green-800";
        } else if (item.status === "Pengajuan") {
            statusClass = "bg-red-100 text-yellow-800";
        }

        const globalIndex = laporanData.findIndex((d) => d.id === item.id);
        const row = `
            <tr class="border-b hover:bg-gray-50" data-index="${globalIndex}">
                <td class="px-2 lg:px-4 py-2 text-sm lg:text-base">${item.resi}</td>
                <td class="px-2 lg:px-4 py-2 text-sm lg:text-base">${item.masalah}</td>
                <td class="px-2 lg:px-4 py-2 text-sm lg:text-base">${item.tanggal}</td>
                <td class="px-2 lg:px-4 py-2 text-sm lg:text-base"><span class="px-2 py-1 ${statusClass} rounded text-xs">${item.status}</span></td>
            </tr>
        `;
        tableBody.innerHTML += row;
    });

    updatePaginationInfo();
    renderPagination();
}

// Update pagination info
function updatePaginationInfo() {
    const startIndex = (currentPage - 1) * itemsPerPage + 1;
    const endIndex = Math.min(currentPage * itemsPerPage, filteredData.length);
    const totalEntries = filteredData.length;

    document.getElementById(
        "paginationInfo"
    ).textContent = `Menampilkan ${startIndex} hingga ${endIndex} dari ${totalEntries} entri`;
}

// Render pagination
function renderPagination() {
    const paginationContainer = document.getElementById("paginationButtons");
    const totalPages = Math.ceil(filteredData.length / itemsPerPage);

    if (totalPages <= 1) {
        paginationContainer.innerHTML = "";
        return;
    }

    let paginationHTML = "";

    // Tombol Previous
    paginationHTML += `
        <button onclick="changePage(${currentPage - 1})"
                class="px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 transition-colors duration-200 ${
                    currentPage === 1 ? "opacity-50 cursor-not-allowed" : ""
                }"
                ${currentPage === 1 ? "disabled" : ""}>
        <i class="fas fa-chevron-left"></i>
        </button>
    `;

    // Logic page numbers
    if (totalPages <= 5) {
        // Tampilkan semua jika <= 5
        for (let i = 1; i <= totalPages; i++) {
            paginationHTML += renderPageButton(i);
        }
    } else {
        // Halaman 1
        paginationHTML += renderPageButton(1);

        if (currentPage <= 2) {
            paginationHTML += renderPageButton(2);
            paginationHTML += renderEllipsis();
        } else if (currentPage >= totalPages - 1) {
            paginationHTML += renderEllipsis();
            paginationHTML += renderPageButton(totalPages - 1);
        } else {
            paginationHTML += renderEllipsis();
            paginationHTML += renderPageButton(currentPage);
            paginationHTML += renderEllipsis();
        }

        // Halaman terakhir
        paginationHTML += renderPageButton(totalPages);
    }

    // Tombol Next
    paginationHTML += `
        <button onclick="changePage(${currentPage + 1})"
                class="px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 transition-colors duration-200 ${
                    currentPage === totalPages
                        ? "opacity-50 cursor-not-allowed"
                        : ""
                }"
                ${currentPage === totalPages ? "disabled" : ""}>
        <i class="fas fa-chevron-right"></i>
        </button>
    `;

    paginationContainer.innerHTML = paginationHTML;

    // Helper
    function renderPageButton(page) {
        if (page === currentPage) {
            return `<button class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600">${page}</button>`;
        } else {
            return `<button onclick="changePage(${page})"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors duration-200">
                    ${page}
                </button>`;
        }
    }

    function renderEllipsis() {
        return `<span class="px-2 py-2 text-sm text-gray-500">...</span>`;
    }
}

// Change page
function changePage(page) {
    const totalPages = Math.ceil(filteredData.length / itemsPerPage);
    if (page >= 1 && page <= totalPages) {
        currentPage = page;
        renderTable();
    }
}

// Filter table
function filterTable() {
    const keyword = document
        .getElementById("searchInput")
        .value.toLowerCase()
        .trim();
    const status = document.getElementById("filterStatus").value;

    filteredData = laporanData.filter((item) => {
        const idMatch = item.resi.toString().includes(keyword);
        const masalahMatch = item.masalah.toLowerCase().includes(keyword);
        const statusMatch = status === "" || item.status === status;

        return (idMatch || masalahMatch) && statusMatch;
    });

    currentPage = 1;
    renderTable();
}

// Handle entries change
function handleEntriesChange() {
    const showEntries = parseInt(document.getElementById("showEntries").value);
    itemsPerPage = showEntries;
    currentPage = 1;
    renderTable();
}

document.addEventListener("DOMContentLoaded", async function () {
    updateTime();
    setInterval(updateTime, 60000);

    try {
        const data = await fetchDataLaporan();
        inisialisasiData(data);
        inisialisasiDropdownTahun(data);
        inisialisasiDropdownHarian(data);
        pasangEventListenerDropdown();
    } catch (error) {
        console.error("Gagal memuat data laporan:", error);
    }

    pasangEventListenerLainnya();
});

async function fetchDataLaporan() {
    const response = await fetch("/public/data");
    const data = await response.json();

    return data.map((item) => ({
        resi: item.resi,
        masalah: item.masalah,
        status: item.status,
        tanggal: item.tanggal_pengajuan,
    }));
}

function inisialisasiData(data) {
    laporanData = data;
    filteredData = [...laporanData];
    updateStatistics();
    renderTable();
}

function inisialisasiDropdownTahun(data) {
    const tahunSet = new Set();
    data.forEach((item) => {
        if (item.resi) {
            const tahun = "20" + item.resi.substring(4, 6);
            tahunSet.add(tahun);
        }
    });

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
    data.forEach((item) => {
        if (item.resi) {
            const tahun = "20" + item.resi.substring(4, 6);
            tahunSet.add(tahun);
        }
    });

    const tahunHarianSelect = document.getElementById("tahunHarianSelect");
    const bulanHarianSelect = document.getElementById("bulanHarianSelect");

    [...tahunSet].sort().forEach((t) => {
        const opt = document.createElement("option");
        opt.value = t;
        opt.textContent = t;
        tahunHarianSelect.appendChild(opt);
    });

    const now = new Date();
    tahunHarianSelect.value = now.getFullYear().toString();
    bulanHarianSelect.value = now.getMonth().toString(); // 0 - 11

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

function pasangEventListenerLainnya() {
    document
        .getElementById("searchInput")
        .addEventListener("input", filterTable);
    document
        .getElementById("filterStatus")
        .addEventListener("change", filterTable);
    document
        .getElementById("showEntries")
        .addEventListener("change", handleEntriesChange);
}

// monthly chart
let chart;
function generateChart(tahun) {
    const monthly = {
        Pengajuan: Array(12).fill(0),
        Selesai: Array(12).fill(0),
    };

    laporanData.forEach((item) => {
        if (!item.resi || !item.status) return;

        const resi = item.resi;
        const bulan = parseInt(resi.substring(2, 4), 10) - 1; // ambil MM dari ddmmyy
        const tahunResi = "20" + resi.substring(4, 6); // ambil YY dari ddmmyy dan ubah ke 20YY

        if (tahunResi === tahun) {
            if (item.status === "Pengajuan" || item.status === "Selesai") {
                monthly[item.status][bulan]++;
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
                rotate: -45, // rotasi label agar tidak tumpang tindih
                style: {
                    fontSize: "12px",
                },
            },
        },
        colors: ["#FF4560", "#00E396"],
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
                breakpoint: 640, // misal ukuran layar < 640px (mobile)
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

// daily chart
let dailyChart;
function generateDailyChart(tahun, bulan) {
    const daysInMonth = new Date(tahun, parseInt(bulan) + 1, 0).getDate();

    const daily = {
        Pengajuan: Array(daysInMonth).fill(0),
        Selesai: Array(daysInMonth).fill(0),
    };

    laporanData.forEach((item) => {
        const tgl = new Date(item.tanggal);
        const tahunItem = tgl.getFullYear();
        const bulanItem = tgl.getMonth();

        if (tahunItem == tahun && bulanItem == bulan) {
            const tanggal = tgl.getDate() - 1;
            if (item.status === "Pengajuan" || item.status === "Selesai") {
                daily[item.status][tanggal]++;
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
            { name: "Selesai", data: daily["Selesai"] },
        ],
        xaxis: {
            categories: Array.from({ length: daysInMonth }, (_, i) => i + 1),
            title: { text: "Tanggal" },
            labels: {
                rotate: -45,
                style: {
                    fontSize: window.innerWidth < 640 ? "10px" : "12px", // Lebih kecil di mobile
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
        colors: ["#FF4560", "#00E396"],
        legend: {
            position: "top",
        },
        dataLabels: {
            enabled: false,
        },
        responsive: [
            {
                breakpoint: 640, // max-width: 640px
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
