// Pagination variables
let currentPage = 1;
let itemsPerPage = 5;
let laporanData = [];
let paginationInfo = {};

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

// Update statistics cards
function updateStatistics(stats) {
    document.getElementById('progressCount').textContent = stats.progress || 0;
    document.getElementById('successCount').textContent = stats.selesai || 0;
    document.getElementById('pengajuanCount').textContent = stats.pengajuan || 0;
}

// Show loading state
function showFilterLoading() {
    const tableBody = document.getElementById("laporanTable");
    if (tableBody) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="px-4 py-12 text-center">
                    <div class="flex flex-col items-center justify-center space-y-3">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        <p class="text-gray-500 text-sm">Memuat data...</p>
                    </div>
                </td>
            </tr>
        `;
    }

    // Disable pagination saat loading
    const paginationContainer = document.getElementById("paginationButtons");
    if (paginationContainer) {
        paginationContainer.innerHTML = "";
    }

    // Update pagination info
    document.getElementById("paginationInfo").textContent = "Memuat data...";
}

// Show error state
function showErrorState(message = "Terjadi kesalahan saat memuat data") {
    const tableBody = document.getElementById("laporanTable");
    if (tableBody) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="px-4 py-12 text-center">
                    <div class="flex flex-col items-center justify-center space-y-3">
                        <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                        <p class="text-red-500 text-sm">${message}</p>
                        <button onclick="fetchData(1)" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                            Coba Lagi
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }

    // Clear pagination
    const paginationContainer = document.getElementById("paginationButtons");
    if (paginationContainer) {
        paginationContainer.innerHTML = "";
    }

    // Update pagination info
    document.getElementById("paginationInfo").textContent = "Gagal memuat data";
}

// Show empty state
function showEmptyState() {
    const tableBody = document.getElementById("laporanTable");
    if (tableBody) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="px-4 py-12 text-center">
                    <div class="flex flex-col items-center justify-center space-y-3">
                        <i class="fas fa-inbox text-gray-400 text-3xl"></i>
                        <p class="text-gray-500 text-sm">Tidak ada data yang ditemukan</p>
                    </div>
                </td>
            </tr>
        `;
    }

    // Clear pagination
    const paginationContainer = document.getElementById("paginationButtons");
    if (paginationContainer) {
        paginationContainer.innerHTML = "";
    }

    // Update pagination info
    document.getElementById("paginationInfo").textContent = "Menampilkan 0 dari 0 data";
}

// Fetch data from backend
async function fetchData(page = 1, filters = {}) {
    // Show loading state
    showFilterLoading();

    try {
        // Determine which endpoint to use based on filters
        let url = '/laporan/user';
        const params = new URLSearchParams({
            per_page: itemsPerPage,
            page: page
        });

        // Check if we have filters
        const hasFilters = filters.search || filters.status || filters.start_date || filters.end_date;

        if (hasFilters) {
            url = '/laporan/searchFilterLaporanUser';

            // Add filter parameters
            if (filters.search) params.append('search', filters.search);
            if (filters.status) params.append('status', filters.status);
            if (filters.start_date) params.append('start_date', filters.start_date);
            if (filters.end_date) params.append('end_date', filters.end_date);
        }

        const response = await fetch(`${url}?${params}`);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const result = await response.json();

        if (hasFilters) {
            // For filtered data
            if (result.all_data && result.all_data.data) {
                laporanData = result.all_data.data.map(item => ({
                    id: item.id,
                    resi: item.resi,
                    masalah: item.masalah,
                    status: item.status,
                    kategori: item.kategori,
                    tanggal: item.tanggal_pengajuan,
                    tanggal_selesai: item.tanggal_selesai,
                    estimasi: item.estimasi,
                    deskripsi: item.deskripsi,
                    lampiran: item.lampiran,
                }));

                paginationInfo = {
                    total: result.all_data.total,
                    per_page: result.all_data.per_page,
                    current_page: result.all_data.current_page,
                    last_page: Math.ceil(result.all_data.total / result.all_data.per_page)
                };

                if (result.statistics) {
                    updateStatistics(result.statistics);
                }
            } else {
                laporanData = [];
                paginationInfo = { total: 0, per_page: itemsPerPage, current_page: 1, last_page: 1 };
            }
        } else {
            // For regular data
            if (result.data && result.data.all_data) {
                laporanData = result.data.all_data.map(item => ({
                    id: item.id,
                    resi: item.resi,
                    masalah: item.judul_masalah,
                    status: item.status,
                    kategori: item.kategori,
                    tanggal: item.tanggal_pengajuan,
                    tanggal_selesai: item.tanggal_selesai,
                    estimasi: item.estimasi,
                    deskripsi: item.deskripsi,
                    lampiran: item.lampiran,
                }));

                paginationInfo = {
                    total: result.data.total,
                    per_page: itemsPerPage,
                    current_page: page,
                    last_page: Math.ceil(result.data.total / itemsPerPage)
                };

                if (result.statistics) {
                    updateStatistics(result.statistics);
                }
            } else {
                laporanData = [];
                paginationInfo = { total: 0, per_page: itemsPerPage, current_page: 1, last_page: 1 };
            }
        }

        currentPage = paginationInfo.current_page;

        // Check if data is empty and show appropriate state
        if (laporanData.length === 0) {
            showEmptyState();
        } else {
            renderTable();
        }
    } catch (error) {
        console.error('Gagal memuat data laporan:', error);
        showErrorState(error.message || 'Terjadi kesalahan saat memuat data');
    }
}

// Render table
function renderTable() {
    const tableBody = document.getElementById('laporanTable');
    tableBody.innerHTML = '';

    laporanData.forEach((item, index) => {
        let statusClass = '';
        if (item.status === 'Progress') {
            statusClass = 'bg-yellow-100 text-blue-800';
        } else if (item.status === 'Selesai') {
            statusClass = 'bg-green-100 text-green-800';
        } else if (item.status === 'Pengajuan') {
            statusClass = 'bg-red-100 text-yellow-800';
        }

        const row = `
            <tr class="border-b border-gray-200 hover:bg-gray-50" data-index="${index}">
                <td class="px-2 lg:px-4 py-2 text-sm lg:text-base">${item.resi}</td>
                <td class="px-2 lg:px-4 py-2 text-sm lg:text-base">${item.masalah}</td>
                <td class="px-2 lg:px-4 py-2 text-sm lg:text-base"><span class="px-2 py-1 ${statusClass} rounded text-xs">${item.status}</span></td>
                <td class="px-2 lg:px-4 py-2 text-sm lg:text-base">
                    ${item.status === 'Pengajuan' ? '0 Hari' : item.status === 'Progress' ? (item.estimasi ? item.estimasi + ' Hari' : '-') : (item.tanggal_selesai ?? '-')}
                </td>
                <td class="px-2 lg:px-4 py-2"><i class="fas fa-eye text-blue-500 hover:text-blue-700 cursor-pointer view-btn"></i></td>
            </tr>
        `;
        tableBody.innerHTML += row;
    });

    updatePaginationInfo();
    renderPagination();
}

// Update pagination info
function updatePaginationInfo() {
    const startIndex = ((currentPage - 1) * itemsPerPage) + 1;
    const endIndex = Math.min(currentPage * itemsPerPage, paginationInfo.total);
    const totalEntries = paginationInfo.total;

    document.getElementById('paginationInfo').textContent = `Menampilkan ${startIndex} sampai ${endIndex} dari ${totalEntries} data`;
}

// Render pagination buttons
function renderPagination() {
    const paginationContainer = document.getElementById('paginationButtons');
    const totalPages = paginationInfo.last_page || 1;

    if (totalPages <= 1) {
        paginationContainer.innerHTML = '';
        return;
    }

    let paginationHTML = '';

    // Previous button
    paginationHTML += `
        <button onclick="changePage(${currentPage - 1})"
                class="px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 transition-colors duration-200 ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}"
                ${currentPage === 1 ? 'disabled' : ''}>
        <i class="fas fa-chevron-left"></i>
        </button>
    `;

    // Page numbers logic
    if (totalPages <= 5) {
        // Show all pages if <= 5
        for (let i = 1; i <= totalPages; i++) {
            paginationHTML += renderPageButton(i);
        }
    } else {
        // Page 1
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

        // Last page
        paginationHTML += renderPageButton(totalPages);
    }

    // Next button
    paginationHTML += `
        <button onclick="changePage(${currentPage + 1})"
                class="px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 transition-colors duration-200 ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''}"
                ${currentPage === totalPages ? 'disabled' : ''}>
        <i class="fas fa-chevron-right"></i>
        </button>
    `;

    paginationContainer.innerHTML = paginationHTML;

    // Helper functions
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
    if (page >= 1 && page <= paginationInfo.last_page) {
        // Show loading before fetching new page
        showFilterLoading();
        const filters = getCurrentFilters();
        fetchData(page, filters);
    }
}

// Get current filters from form inputs
function getCurrentFilters() {
    return {
        search: document.getElementById('searchInput').value.toLowerCase().trim(),
        status: document.getElementById('filterStatus').value,
        start_date: document.getElementById('startDate').value,
        end_date: document.getElementById('endDate').value
    };
}

// Filter & Search
function toggleDatePicker() {
    const datePicker = document.getElementById('datePicker');
    datePicker.classList.toggle('hidden');
}

document.addEventListener('click', function (e) {
    const datePicker = document.getElementById('datePicker');
    const dateRangeInput = document.getElementById('dateRange');

    if (!datePicker.contains(e.target) && !dateRangeInput.contains(e.target)) {
        datePicker.classList.add('hidden');
    }
});

function filterTable() {
    const filters = getCurrentFilters();

    // Update date range display
    if (filters.start_date || filters.end_date) {
        const formatDate = (date) => {
            if (!date) return '';
            const dateObj = new Date(date);
            const yyyy = dateObj.getFullYear();
            const mm = String(dateObj.getMonth() + 1).padStart(2, '0');
            const dd = String(dateObj.getDate()).padStart(2, '0');
            return `${yyyy}/${mm}/${dd}`;
        };

        document.getElementById('dateRange').value = `${formatDate(filters.start_date)} - ${formatDate(filters.end_date)}`;
    }

    // Show loading before filtering
    showFilterLoading();

    // Reset to page 1 when filtering
    fetchData(1, filters);
}

function setQuickRange(range) {
    const today = new Date();
    let start = new Date();

    if (range === "today") {
        start = new Date(today);
    } else if (range === "week") {
        start.setDate(today.getDate() - 6);
    } else if (range === "month") {
        start.setDate(today.getDate() - 29);
    }

    const formatDate = (date) => {
        const yyyy = date.getFullYear();
        const mm = String(date.getMonth() + 1).padStart(2, '0');
        const dd = String(date.getDate()).padStart(2, '0');
        return `${yyyy}-${mm}-${dd}`;
    };

    document.getElementById('startDate').value = formatDate(start);
    document.getElementById('endDate').value = formatDate(today);

    // Update display text
    document.getElementById('dateRange').value = `${formatDate(start)} - ${formatDate(today)}`;

    // Apply filter
    filterTable();
}

function clearDateRange() {
    document.getElementById('startDate').value = '';
    document.getElementById('endDate').value = '';
    document.getElementById('dateRange').value = '';

    // Hide date picker if open
    document.getElementById('datePicker').classList.add('hidden');

    // Re-render table without date filter
    filterTable();
}

// Handle entries per page change
function handleEntriesChange() {
    const showEntries = parseInt(document.getElementById('showEntries').value);
    itemsPerPage = showEntries;

    // Show loading before changing entries
    showFilterLoading();

    const filters = getCurrentFilters();
    fetchData(1, filters); // Reset to page 1 with new per_page
}

// Debounce function for search input
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

// Event listeners with debounce for search
const debouncedFilter = debounce(filterTable, 300);
document.getElementById('searchInput').addEventListener('input', debouncedFilter);
document.getElementById('filterStatus').addEventListener('change', filterTable);
document.getElementById('showEntries').addEventListener('change', handleEntriesChange);
document.getElementById('startDate').addEventListener('change', filterTable);
document.getElementById('endDate').addEventListener('change', filterTable);

// Modal functions
function openModal(dataIndex) {
    const data = laporanData[dataIndex];

    document.getElementById('modalNama').innerText = data.masalah;
    document.getElementById('modalTanggal').innerText = data.tanggal;
    document.getElementById('modalStatus').innerText = data.status;
    document.getElementById('modalDeskripsi').innerText = data.deskripsi;
    document.getElementById('modalLampiran').innerHTML = data.lampiran ? `<a href="/storage/${data.lampiran}" target="_blank" class="text-blue-600 underline hover:text-blue-800">
        Lihat Lampiran
    </a>` : '-';
    document.getElementById('modalEstimasi').innerText =
        data.status === 'Pengajuan' ? '0 Hari' :
        data.status === 'Progress' ? (data.estimasi || 0) + ' Hari' :
        data.status === 'Selesai' ? (data.tanggal_selesai + " ( selesai )" || '-') : '-';

    document.getElementById('detailModal').classList.remove('hidden');
    document.getElementById('detailModal').classList.add('flex');
}

function closeModal() {
    document.getElementById('detailModal').classList.add('hidden');
    document.getElementById('detailModal').classList.remove('flex');
}

// Close modal when clicking outside
document.getElementById('detailModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Event listener after DOM loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initial data load
    fetchData(1);

    // Add click event listener for view buttons
    document.getElementById('laporanTable').addEventListener('click', function(e) {
        const editBtn = e.target.closest('.view-btn');
        if (editBtn) {
            const row = editBtn.closest('tr');
            const dataIndex = parseInt(row.getAttribute('data-index'));
            openModal(dataIndex);
        }
    });
});
