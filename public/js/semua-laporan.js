let laporanData = [];
let currentPage = 1;
const itemsPerPage = 5;
let totalPages = 1;
let totalEntries = 0;
let isLoading = false;

// Event listeners
document.getElementById("filterStatus").addEventListener("change", handleFilterChange);
document.getElementById("searchInput").addEventListener("input", debounce(handleSearchChange, 500));

// Debounce function to prevent too many API calls
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Handle filter change
function handleFilterChange() {
    currentPage = 1;
    fetchFilteredData();
}

// Handle search change
function handleSearchChange() {
    currentPage = 1;
    fetchFilteredData();
}

async function fetchInitialData() {
    try {
        showLoadingState();
        const response = await fetch("/all/public/data");
        const result = await response.json();

        if (response.ok) {
            laporanData = result.all_data.data;
            totalEntries = result.all_data.total;
            totalPages = Math.ceil(totalEntries / result.all_data.per_page);
            renderCards();
            hideLoadingState();
        } else {
            throw new Error("Failed to fetch data");
        }
    } catch (error) {
        console.error("Error fetching initial data:", error);
        showErrorState();
    }
}

// Fetch filtered/searched data
async function fetchFilteredData(page = 1) {
    try {
        showLoadingState();

        const status = document.getElementById("filterStatus").value;
        const search = document.getElementById("searchInput").value.trim();

        // kalau search & status kosong -> fallback ke fetchInitialData
        if (!status && !search) {
            await fetchPageData(page);
            return;
        }

        // Build query parameters
        const params = new URLSearchParams();

        if (status) {
            params.append('status', status);
        }

        if (search) {
            params.append('search', search);
        }

        if (page){
            params.append('page', page);
        }

        const response = await fetch(`/laporan/public?${params.toString()}`);
        const result = await response.json();

        if (response.ok) {
            laporanData = result.all_data.data;
            totalEntries = result.all_data.total;
            totalPages = Math.ceil(totalEntries / result.all_data.per_page);
            currentPage = result.all_data.current_page;

            renderCards();
            hideLoadingState();
        } else {
            throw new Error("Failed to fetch filtered data");
        }
    } catch (error) {
        console.error("Error fetching filtered data:", error);
        showErrorState();
    }
}

// Fetch data for specific page
async function fetchPageData(page = currentPage) {
    try {
        const status = document.getElementById("filterStatus").value;
        const search = document.getElementById("searchInput").value.trim();

        if (status || search) {
            await fetchFilteredData(page);
        } else {
            const response = await fetch(`/all/public/data?page=${page}`);
            const result = await response.json();

            if (response.ok) {
                laporanData = result.all_data.data;
                totalEntries = result.all_data.total;
                totalPages = Math.ceil(totalEntries / result.all_data.per_page);
                renderCards();
                hideLoadingState();
            } else {
                throw new Error("Failed to fetch page data");
            }
        }
    } catch (error) {
        console.error("Error fetching page data:", error);
        showErrorState();
    }
}

// Show loading state
function showLoadingState() {
    isLoading = true;
    const container = document.getElementById("laporanCards");
    container.innerHTML = `
        <div class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            <span class="ml-3 text-gray-600">Memuat data...</span>
        </div>
    `;

    // Clear pagination saat loading
    const paginationContainer = document.getElementById("paginationButtons");
    const paginationInfo = document.getElementById("paginationInfo");
    if (paginationContainer) {
        paginationContainer.innerHTML = "";
    }
    if (paginationInfo) {
        paginationInfo.textContent = "Memuat data...";
    }
}

// Hide loading state
function hideLoadingState() {
    isLoading = false;
}

// Show error state
function showErrorState() {
    isLoading = false;
    const container = document.getElementById("laporanCards");
    container.innerHTML = `
        <div class="flex flex-col justify-center items-center py-12">
            <div class="text-red-500 mb-4">
                <i class="fas fa-exclamation-triangle text-4xl"></i>
            </div>
            <p class="text-gray-600 text-center">Terjadi kesalahan saat memuat data.</p>
            <button onclick="fetchInitialData()" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                Coba Lagi
            </button>
        </div>
    `;
}

// Render cards
function renderCards() {
    const container = document.getElementById("laporanCards");

    if (laporanData.length === 0) {
        container.innerHTML = `
            <div class="flex flex-col justify-center items-center py-12">
                <div class="text-gray-400 mb-4">
                    <i class="fas fa-inbox text-4xl"></i>
                </div>
                <p class="text-gray-600 text-center">Tidak ada data yang ditemukan.</p>
            </div>
        `;
        updatePaginationInfo();
        renderPagination();
        return;
    }

    container.innerHTML = "";

    laporanData.forEach((item) => {
        const statusClass = {
            progress: "bg-yellow-100 text-yellow-800",
            selesai: "bg-green-100 text-green-800",
            pengajuan: "bg-red-100 text-red-800",
        }[item.status.toLowerCase()] || "bg-gray-100 text-gray-800";

        // Format date
        const formattedDate = new Date(item.tanggal_pengajuan).toLocaleDateString("id-ID", {
            year: "numeric",
            month: "long",
            day: "numeric"
        });

        const cardHTML = `
            <a href="/detail/${item.resi}" class="block hover:shadow-lg transition-shadow duration-200 rounded-lg">
                <!-- Desktop Layout (Horizontal) - Hidden on mobile -->
                <div class="hidden md:block bg-white shadow-md rounded-lg border border-gray-200">
                    <div class="flex items-center p-4 gap-4">
                        <div class="w-14 h-14 bg-gray-100 flex items-center justify-center rounded overflow-hidden flex-shrink-0">
                            <img src="${item.lampiran_url || 'https://placehold.co/600x400?text=Tidak+Ada+Lampiran'}"
                                 alt="lampiran"
                                 loading="lazy"
                                 class="w-12 h-12 object-contain"
                                 onerror="this.src='https://placehold.co/600x400?text=Tidak+Ada+Lampiran'" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900">${item.resi}</p>
                            <p class="text-sm text-gray-600">${formattedDate}</p>
                            <div class="mt-1 text-gray-700 text-sm leading-snug line-clamp-2">${item.masalah || 'Laporan Layanan Publik'}</div>
                        </div>
                        <div class="flex flex-col items-end gap-2 flex-shrink-0">
                            <span class="text-xs px-3 py-1 rounded ${statusClass} inline-block capitalize">${item.status}</span>
                            <span class="text-sm text-blue-600 hover:underline">Lihat Detail</span>
                        </div>
                    </div>
                </div>

                <!-- Mobile Layout (Vertical Card) - Hidden on desktop -->
                <div class="block md:hidden bg-white shadow-md rounded-lg border border-gray-200 p-4">
                    <!-- Image at the top -->
                    <div class="w-full h-36 bg-gray-100 flex items-center justify-center rounded overflow-hidden mb-3">
                        <img src="${item.lampiran_url || 'https://placehold.co/600x400?text=Tidak+Ada+Lampiran'}"
                             alt="lampiran"
                             loading="lazy"
                             class="w-32 h-32 object-contain"
                             onerror="this.src='https://placehold.co/600x400?text=Tidak+Ada+Lampiran'" />
                    </div>
                    <!-- Resi and Date below image -->
                    <div class="mb-3">
                        <p class="font-semibold text-gray-900 text-base">${item.resi}</p>
                        <p class="text-sm text-gray-600">${formattedDate}</p>
                    </div>
                    <div class="text-gray-700 text-sm leading-relaxed mb-3 line-clamp-3">${item.masalah || 'Laporan Layanan Publik'}</div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs px-2 py-1 rounded ${statusClass} inline-block capitalize">${item.status}</span>
                        <span class="text-sm text-blue-600 hover:underline font-medium">Lihat Detail</span>
                    </div>
                </div>
            </a>
        `;

        container.insertAdjacentHTML('beforeend', cardHTML);
    });

    updatePaginationInfo();
    renderPagination();
}

// Update pagination info
function updatePaginationInfo() {
    const startIndex = totalEntries > 0 ? ((currentPage - 1) * itemsPerPage) + 1 : 0;
    const endIndex = Math.min(currentPage * itemsPerPage, totalEntries);

    document.getElementById("paginationInfo").textContent =
        `Menampilkan ${startIndex} hingga ${endIndex} dari ${totalEntries} entri`;
}

// Render pagination
function renderPagination() {
    const paginationContainer = document.getElementById("paginationButtons");

    if (totalPages <= 1) {
        paginationContainer.innerHTML = "";
        return;
    }

    let paginationHTML = "";

    // Previous button
    const isPrevDisabled = currentPage === 1;
    paginationHTML += `
        <button onclick="changePage(${currentPage - 1})"
            class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded hover:bg-gray-50 transition-colors duration-200 ${isPrevDisabled ? "opacity-50 cursor-not-allowed" : ""}"
            ${isPrevDisabled ? "disabled" : ""}>
            <i class="fas fa-chevron-left"></i>
        </button>
    `;

    // Page numbers
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

    // Next button
    const isNextDisabled = currentPage === totalPages;
    paginationHTML += `
        <button onclick="changePage(${currentPage + 1})"
            class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded hover:bg-gray-50 transition-colors duration-200 ${isNextDisabled ? "opacity-50 cursor-not-allowed" : ""}"
            ${isNextDisabled ? "disabled" : ""}>
            <i class="fas fa-chevron-right"></i>
        </button>
    `;

    paginationContainer.innerHTML = paginationHTML;
}

// Change page
function changePage(page) {
    if (page >= 1 && page <= totalPages && page !== currentPage && !isLoading) {
        currentPage = page;

        // Tampilkan loading dan clear pagination
        showLoadingState();

        // Cek apakah sedang dalam mode filter/search
        const status = document.getElementById("filterStatus").value;
        const search = document.getElementById("searchInput").value.trim();
        const isFilterActive = status || search;

        if (isFilterActive) {
            fetchFilteredData(page);
        } else {
            fetchPageData(page);
        }
    }
}

// Initialize on page load
document.addEventListener("DOMContentLoaded", function() {
    fetchInitialData();
});
