// Pagination variables
let currentPage = 1;
let itemsPerPage = 5;
let laporanData = [];
let filteredData = [];

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

// Update statistics cards - Fixed
function updateStatistics() {
    const progressCount = laporanData.filter(item => item.status === 'Progress').length;
    const successCount = laporanData.filter(item => item.status === 'Selesai').length;
    const pengajuanCount = laporanData.filter(item => item.status === 'Pengajuan').length;

    document.getElementById('progressCount').textContent = progressCount;
    document.getElementById('successCount').textContent = successCount;
    document.getElementById('pengajuanCount').textContent = pengajuanCount;
}

// Render table
function renderTable() {
    const tableBody = document.getElementById('laporanTable');
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = itemsPerPage === 50 ? filteredData.length : startIndex + itemsPerPage;
    const currentData = filteredData.slice(startIndex, endIndex);

    tableBody.innerHTML = '';

    currentData.forEach((item, index) => {
        let statusClass = '';
        if (item.status === 'Progress') {
            statusClass = 'bg-yellow-100 text-blue-800';
        } else if (item.status === 'Selesai') {
            statusClass = 'bg-green-100 text-green-800';
        } else if (item.status === 'Pengajuan') {
            statusClass = 'bg-red-100 text-yellow-800';
        }

        const globalIndex = laporanData.findIndex(d => d.id === item.id);
        const row = `
            <tr class="border-b hover:bg-gray-50" data-index="${globalIndex}">
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
    const startIndex = (currentPage - 1) * itemsPerPage + 1;
    const endIndex = Math.min(currentPage * itemsPerPage, filteredData.length);
    const totalEntries = filteredData.length;

    document.getElementById('paginationInfo').textContent = `Menampilkan ${startIndex} sampai ${endIndex} dari ${totalEntries} data`;
}

// Render pagination buttons
function renderPagination() {
    const paginationContainer = document.getElementById('paginationButtons');
    const totalPages = Math.ceil(filteredData.length / itemsPerPage);

    if (totalPages <= 1) {
        paginationContainer.innerHTML = '';
        return;
    }

    let paginationHTML = '';

    // Tombol Previous
    paginationHTML += `
        <button onclick="changePage(${currentPage - 1})"
                class="px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 transition-colors duration-200 ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}"
                ${currentPage === 1 ? 'disabled' : ''}>
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
                class="px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 transition-colors duration-200 ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''}"
                ${currentPage === totalPages ? 'disabled' : ''}>
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

// Filter & Search - Fixed
function toggleDatePicker() {
    const datePicker = document.getElementById('datePicker');
    datePicker.classList.toggle('hidden');
}

document.addEventListener('click', function (e) {
    const datePicker = document.getElementById('datePicker');
    const dateRangeInput = document.getElementById('dateRange');

    if (
        !datePicker.contains(e.target) &&
        !dateRangeInput.contains(e.target)
    ) {
        datePicker.classList.add('hidden');
    }
});

function filterTable() {
    const formatDate = (date) => {
        if (!date) return '';
        const yyyy = date.getFullYear();
        const mm = String(date.getMonth() + 1).padStart(2, '0');
        const dd = String(date.getDate()).padStart(2, '0');
        return `${yyyy}/${mm}/${dd}`;
    };

    const keyword = document.getElementById('searchInput').value.toLowerCase().trim();
    const status = document.getElementById('filterStatus').value;

    const startDateInput = document.getElementById('startDate').value;
    const endDateInput = document.getElementById('endDate').value;

    const startDate = startDateInput ? new Date(startDateInput) : null;
    const endDate = endDateInput ? new Date(endDateInput) : null;

    filteredData = laporanData.filter(item => {
        const idMatch = item.resi.toString().includes(keyword);
        const masalahMatch = item.masalah.toLowerCase().includes(keyword);
        const statusMatch = status === "" || item.status === status;

        let tanggalMatch = true;
        if (startDate || endDate) {
            // Update text input dateRange
            document.getElementById('dateRange').value = `${formatDate(startDate)} - ${formatDate(endDate)}`;

            const resiDateStr = item.resi.substring(0, 6); // "ddmmyy"
            const day = resiDateStr.substring(0, 2);
            const month = resiDateStr.substring(2, 4);
            const year = '20' + resiDateStr.substring(4, 6);

            const resiDate = new Date(`${year}-${month}-${day}`);

            if (startDate && resiDate < startDate) tanggalMatch = false;
            if (endDate && resiDate > endDate) tanggalMatch = false;
        }

        return (idMatch || masalahMatch) && statusMatch  && tanggalMatch;
    });

    currentPage = 1;
    renderTable();
}

function setQuickRange(range){
    const today = new Date()

    let start = new Date()

    if (range === "today"){
        start = new Date(today)
    } else if (range === "week"){
        start.setDate(today.getDate() - 6)
    } else if (range === "month"){
        start.setDate(today.getDate() - 29)
    }

    const formatDate = (date) => {
        const yyyy = date.getFullYear();
        const mm = String(date.getMonth() + 1).padStart(2, '0');
        const dd = String(date.getDate()).padStart(2, '0');
        return `${yyyy}-${mm}-${dd}`;
    };

    document.getElementById('startDate').value = formatDate(start);
    document.getElementById('endDate').value = formatDate(today);

    // Update tampilan teks range
    document.getElementById('dateRange').value = `${formatDate(start)} - ${formatDate(today)}`;

    // Apply filter
    filterTable();
}

function clearDateRange(){
    document.getElementById('startDate').value = '';
    document.getElementById('endDate').value = '';
    document.getElementById('dateRange').value = '';

    // Sembunyikan date picker jika sedang terbuka
    document.getElementById('datePicker').classList.add('hidden');

    // Render ulang tabel tanpa filter tanggal
    filterTable();
}

// Handle entries per page change
function handleEntriesChange() {
    const showEntries = parseInt(document.getElementById('showEntries').value);
    itemsPerPage = showEntries;
    currentPage = 1;
    renderTable();
}

// Event listeners
document.getElementById('searchInput').addEventListener('input', filterTable);
document.getElementById('filterStatus').addEventListener('change', filterTable);
document.getElementById('showEntries').addEventListener('change', handleEntriesChange);
document.getElementById('datePicker').addEventListener('change', filterTable);

function openModal(dataIndex) {
    currentEditingIndex = dataIndex;
    const data = laporanData[currentEditingIndex];

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

// Event listener setelah DOM loaded
document.addEventListener('DOMContentLoaded', function() {
    fetch('/laporan/user')
    .then(response => response.json())
    .then(data => {
        laporanData = data.map(item => ({
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
        filteredData = [...laporanData];
        updateStatistics();
        renderTable();
    })
    .catch(error => {
        console.error('Gagal memuat data laporan:', error);
    });
    document.getElementById('laporanTable').addEventListener('click', function(e) {
        const editBtn = e.target.closest('.view-btn');
        if (editBtn) {
            const row = editBtn.closest('tr');
            const dataIndex = parseInt(row.getAttribute('data-index'));
            openModal(dataIndex);
        }
    });
});
