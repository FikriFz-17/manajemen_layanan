let laporanData = [];
let filteredData = [];
let currentPage = 1;
const itemsPerPage = 5;

document.getElementById("filterStatus").addEventListener("change", applyFilterAndSearch);
document.getElementById("searchInput").addEventListener("input", applyFilterAndSearch);

async function fetchDataLaporan() {
    const response = await fetch("/public/data");
    const data = await response.json();

    return data.map((item) => ({
        resi: item.resi,
        masalah: item.masalah,
        lampiran_url: item.lampiran_url,
        status: item.status,
        tanggal: item.tanggal_pengajuan,
        kota: item.kota || "Kota tidak diketahui",
    }));
}

function getVisibleData() {
    const search = document.getElementById("searchInput").value.toLowerCase().trim();

    if (!search) {
        return filteredData.filter(item => item.status.toLowerCase() !== "pengajuan");
    }

    return filteredData;
}

function applyFilterAndSearch() {
    const status = document.getElementById("filterStatus").value.toLowerCase();
    const search = document.getElementById("searchInput").value.toLowerCase().trim();

    filteredData = laporanData.filter((item) => {
        const matchesStatus = !status || item.status.toLowerCase() === status;
        const matchesSearch = !search || item.resi.toLowerCase().includes(search);
        return matchesStatus && matchesSearch;
    });

    currentPage = 1;
    renderCards();
}

function renderCards() {
    const container = document.getElementById("laporanCards");
    const visibleData = getVisibleData();

    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = Math.min(startIndex + itemsPerPage, visibleData.length);
    const currentData = visibleData.slice(startIndex, endIndex);

    container.innerHTML = "";

    currentData.forEach((item) => {
        const statusClass = {
            Progress: "bg-yellow-100 text-blue-800",
            Selesai: "bg-green-100 text-green-800",
            Pengajuan: "bg-red-100 text-yellow-800",
        }[item.status] || "bg-gray-100 text-gray-800";

        container.innerHTML += `
            <a href="/detail/${item.resi}" class="block hover:shadow-lg transition-shadow duration-200 rounded-lg">
                <div class="bg-white shadow-md rounded-lg p-4 border border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        <div class="w-14 h-14 bg-gray-100 flex items-center justify-center rounded overflow-hidden">
                            <img src="${item.lampiran_url || 'https://placehold.co/600x400?text=Tidak+Ada+Lampiran'}" alt="lampiran" loading="lazy" class="w-12 h-12 object-contain" />
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">${item.resi}</p>
                            <p class="text-sm text-gray-600">${item.tanggal}</p>
                            <div class="mt-1 text-gray-700 text-sm leading-snug line-clamp-2">${item.masalah}</div>
                        </div>
                        <div class="mt-2 sm:mt-0 flex flex-col items-start sm:items-end gap-2">
                            <span class="text-xs px-2 py-1 rounded ${statusClass} inline-block">${item.status}</span>
                            <span class="text-sm text-blue-600 hover:underline">Lihat Detail</span>
                        </div>
                    </div>
                </div>
            </a>
        `;
    });

    updatePaginationInfo();
    renderPagination();
}

function updatePaginationInfo() {
    const visibleData = getVisibleData();
    const startIndex = (currentPage - 1) * itemsPerPage + 1;
    const endIndex = Math.min(currentPage * itemsPerPage, visibleData.length);
    const totalEntries = visibleData.length;

    document.getElementById("paginationInfo").textContent = `Menampilkan ${startIndex} hingga ${endIndex} dari ${totalEntries} entri`;
}

function renderPagination() {
    const paginationContainer = document.getElementById("paginationButtons");
    const visibleData = getVisibleData();
    const totalPages = Math.ceil(visibleData.length / itemsPerPage);

    if (totalPages <= 1) {
        paginationContainer.innerHTML = "";
        return;
    }

    let paginationHTML = "";

    paginationHTML += `
        <button onclick="changePage(${currentPage - 1})"
            class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded hover:bg-gray-50 transition-colors duration-200 ${currentPage === 1 ? "opacity-50 cursor-not-allowed" : ""}"
            ${currentPage === 1 ? "disabled" : ""}>
            <i class="fas fa-chevron-left"></i>
        </button>
    `;

    let pagesToShow = [];

    if (totalPages <= 5) {
        for (let i = 1; i <= totalPages; i++) {
            pagesToShow.push(i);
        }
    } else {
        if (currentPage <= 3) {
            pagesToShow = [1, 2, 3, "...", totalPages];
        } else if (currentPage >= totalPages - 2) {
            pagesToShow = [1, "...", totalPages - 2, totalPages - 1, totalPages];
        } else {
            pagesToShow = [1, "...", currentPage, "...", totalPages];
        }
    }

    pagesToShow.forEach(p => {
        if (p === "...") {
            paginationHTML += `<span class="px-3 py-2 text-sm text-gray-500">...</span>`;
        } else if (p === currentPage) {
            paginationHTML += `
                <button
                    class="px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded hover:bg-blue-700 transition-colors duration-200">
                    ${p}
                </button>`;
        } else {
            paginationHTML += `
                <button onclick="changePage(${p})"
                    class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 transition-colors duration-200">
                    ${p}
                </button>`;
        }
    });

    paginationHTML += `
        <button onclick="changePage(${currentPage + 1})"
            class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded hover:bg-gray-50 transition-colors duration-200 ${currentPage === totalPages ? "opacity-50 cursor-not-allowed" : ""}"
            ${currentPage === totalPages ? "disabled" : ""}>
            <i class="fas fa-chevron-right"></i>
        </button>
    `;

    paginationContainer.innerHTML = paginationHTML;
}

function changePage(page) {
    const totalPages = Math.ceil(filteredData.length / itemsPerPage);
    if (page >= 1 && page <= totalPages) {
        currentPage = page;
        renderCards();
    }
}

fetchDataLaporan().then((data) => {
    laporanData = data;
    filteredData = data;
    renderCards();
});
