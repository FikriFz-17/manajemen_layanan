// Pagination variables
let laporanData = [];
let paginationInfo = {};
let currentPage = 1;
let itemsPerPage = 5;
let currentEditingIndex = null;

// Filter parameters
let currentFilters = {
    search: '',
    status: '',
    kategori: '',
    jenis_instansi: '',
    start_date: '',
    end_date: ''
};

// Track if we're currently filtering
let isFilterActive = false;

// Mobile hamburger menu
const hamburgerBtn = document.getElementById("hamburgerBtn");
const sidebar = document.getElementById("sidebar");
const mobileOverlay = document.getElementById("mobileOverlay");

hamburgerBtn.addEventListener("click", function () {
    sidebar.classList.toggle("-translate-x-full");
    mobileOverlay.classList.toggle("hidden");
});

mobileOverlay.addEventListener("click", function () {
    sidebar.classList.add("-translate-x-full");
    mobileOverlay.classList.add("hidden");
});

document.addEventListener("click", function (e) {
    if (window.innerWidth < 1024) {
        if (!sidebar.contains(e.target) && !hamburgerBtn.contains(e.target)) {
            sidebar.classList.add("-translate-x-full");
            mobileOverlay.classList.add("hidden");
        }
    }
});

window.addEventListener("resize", function () {
    if (window.innerWidth >= 1024) {
        sidebar.classList.remove("-translate-x-full");
        mobileOverlay.classList.add("hidden");
    } else {
        sidebar.classList.add("-translate-x-full");
    }
});

function toggleDropdown() {
    if (window.innerWidth < 1024) {
        const dropdown = document.getElementById("userDropdown");
        dropdown.classList.toggle("hidden");
    } else {
        const dropdown = document.getElementById("userDropdownDesktop");
        dropdown.classList.toggle("hidden");
    }
}

window.addEventListener("click", function (e) {
    const dropdownMobile = document.getElementById("userDropdown");
    const dropdownDesktop = document.getElementById("userDropdownDesktop");

    if (window.innerWidth < 1024) {
        if (
            !e.target.closest('button[onclick="toggleDropdown()"]') &&
            !dropdownMobile.contains(e.target)
        ) {
            dropdownMobile.classList.add("hidden");
        }
    } else {
        if (
            !e.target.closest('button[onclick="toggleDropdown()"]') &&
            !dropdownDesktop.contains(e.target)
        ) {
            dropdownDesktop.classList.add("hidden");
        }
    }
});

function updateStatistics(stats = null) {
    if (stats) {
        // Update dengan data statistik yang diterima
        document.getElementById("progressCount").textContent = stats.progress || 0;
        document.getElementById("successCount").textContent = stats.selesai || 0;
        document.getElementById("pengajuanCount").textContent = stats.pengajuan || 0;
    } else {
        // Fallback jika tidak ada data statistik
        document.getElementById("progressCount").textContent = 0;
        document.getElementById("successCount").textContent = 0;
        document.getElementById("pengajuanCount").textContent = 0;
    }
}

function renderTable() {
    const tableBody = document.getElementById("laporanTable");
    tableBody.innerHTML = "";

    if (!laporanData || laporanData.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                    Tidak ada data yang ditemukan
                </td>
            </tr>
        `;
        updatePaginationInfo();
        renderPagination();
        return;
    }

    laporanData.forEach((item, index) => {
        let statusClass = "";
        if (item.status === "Progress") {
            statusClass = "bg-yellow-100 text-blue-800";
        } else if (item.status === "Selesai") {
            statusClass = "bg-green-100 text-green-800";
        } else if (item.status === "Pengajuan") {
            statusClass = "bg-red-100 text-yellow-800";
        }

        const row = document.createElement('tr');
        row.className = "border-b border-gray-200 hover:bg-gray-50";
        row.setAttribute('data-id', item.id);

        row.innerHTML = `
            <td class="px-2 lg:px-4 py-2 text-sm lg:text-base">${item.resi || ''}</td>
            <td class="px-2 lg:px-4 py-2 text-sm lg:text-base">${item.judul_masalah || item.masalah || ''}</td>
            <td class="px-2 lg:px-4 py-2 text-sm lg:text-base"><span class="px-2 py-1 ${statusClass} rounded text-xs">${item.status || ''}</span></td>
            <td class="px-2 lg:px-4 py-2 text-sm lg:text-base">${item.tanggal_pengajuan || ''}</td>
            <td class="px-2 lg:px-4 py-2"><i class="fas fa-edit text-blue-500 hover:text-blue-700 cursor-pointer edit-btn" data-id="${item.id}"></i></td>
        `;

        tableBody.appendChild(row);
    });

    updatePaginationInfo();
    renderPagination();
}

function updatePaginationInfo() {
    if (!paginationInfo.total) {
        document.getElementById("paginationInfo").textContent = "Showing 0 to 0 of 0 entries";
        return;
    }

    const startIndex = ((paginationInfo.current_page - 1) * paginationInfo.per_page) + 1;
    const endIndex = Math.min(paginationInfo.current_page * paginationInfo.per_page, paginationInfo.total);

    document.getElementById("paginationInfo").textContent = `Showing ${startIndex} to ${endIndex} of ${paginationInfo.total} entries`;
}

function renderPagination() {
    const paginationContainer = document.getElementById("paginationButtons");

    if (!paginationInfo.total || paginationInfo.total <= paginationInfo.per_page) {
        paginationContainer.innerHTML = "";
        return;
    }

    const totalPages = Math.ceil(paginationInfo.total / paginationInfo.per_page);
    let paginationHTML = "";

    // Previous button
    paginationHTML += `
        <button onclick="changePage(${currentPage - 1})"
                class="px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 transition-colors duration-200 ${
                    currentPage === 1 ? "opacity-50 cursor-not-allowed" : ""
                }"
                ${currentPage === 1 ? "disabled" : ""}>
        <i class="fas fa-chevron-left"></i>
        </button>
    `;

    // Page numbers logic
    if (totalPages <= 5) {
        for (let i = 1; i <= totalPages; i++) {
            paginationHTML += renderPageButton(i);
        }
    } else {
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

        paginationHTML += renderPageButton(totalPages);
    }

    // Next button
    paginationHTML += `
        <button onclick="changePage(${currentPage + 1})"
                class="px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 transition-colors duration-200 ${
                    currentPage === totalPages ? "opacity-50 cursor-not-allowed" : ""
                }"
                ${currentPage === totalPages ? "disabled" : ""}>
        <i class="fas fa-chevron-right"></i>
        </button>
    `;

    paginationContainer.innerHTML = paginationHTML;

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

function changePage(page) {
    const totalPages = Math.ceil(paginationInfo.total / paginationInfo.per_page);
    if (page >= 1 && page <= totalPages && page !== currentPage) {
        currentPage = page;

        showFilterLoading();

        if (isFilterActive) {
            fetchFilteredData();
        } else {
            fetchData();
        }
    }
}

// Filter panel toggle
document.getElementById("filterBtn").addEventListener("click", function () {
    const panel = document.getElementById("filterPanel");
    const isHidden = panel.classList.contains("hidden");

    if (isHidden) {
        panel.classList.remove("hidden");
        panel.classList.add("animate-slideDown");
    } else {
        panel.classList.add("hidden");
        panel.classList.remove("animate-slideDown");
    }
});

function openImportModal() {
    const modal = document.getElementById("uploadModal");
    modal.classList.remove("hidden");
    modal.classList.add("flex");
}

function openExportModal(){
    const modal = document.getElementById("exportModal");
    modal.classList.remove("hidden");
    modal.classList.add("flex");
}

let bulanTomSelect = null;
let tahunTomSelect = null;

function onWaktuChange() {
    const waktu = document.getElementById('waktu').value;
    const tahunContainer = document.getElementById('tahunContainer');
    const bulanContainer = document.getElementById('bulanContainer');

    if (waktu === 'per_tahun') {
        tahunContainer.classList.remove('hidden');
        bulanContainer.classList.add('hidden');

        if (!tahunTomSelect) {
            tahunTomSelect = new TomSelect("#tahun", {
                create: false,
                placeholder: "--- Pilih Tahun ---",
            });
        }
    } else if (waktu === 'per_bulan') {
        tahunContainer.classList.remove('hidden');
        bulanContainer.classList.remove('hidden');

        if (!tahunTomSelect) {
            tahunTomSelect = new TomSelect("#tahun", {
                create: false,
                placeholder: "--- Pilih Tahun ---",
            });
        }

        if (!bulanTomSelect) {
            bulanTomSelect = new TomSelect("#bulan", {
                create: false,
                placeholder: "--- Pilih Bulan ---",
            });
        }
    } else {
        tahunContainer.classList.add('hidden');
        bulanContainer.classList.add('hidden');
    }
}

function isiDropdownTahun(data){
    const tahunSelect = document.getElementById('tahun');
    const tahunSet = new Set();

    data.forEach((item) => {
        tahunSet.add(item)
    });

    const sortedTahun = Array.from(tahunSet).sort();
    tahunSelect.innerHTML = '<option value="">-- Tahun --</option>';

    sortedTahun.forEach((tahun) => {
        const option = document.createElement("option");
        option.value = tahun;
        option.textContent = tahun;
        tahunSelect.appendChild(option);
    });
}

function closeImportExportModal() {
    const importModal = document.getElementById("uploadModal");
    const exportModal = document.getElementById("exportModal");

    if (!importModal.classList.contains("hidden")) {
        importModal.classList.remove("flex");
        importModal.classList.add("hidden");
    } else if (!exportModal.classList.contains("hidden")) {
        exportModal.classList.remove("flex");
        exportModal.classList.add("hidden");
        const waktuSelect = document.getElementById("waktu");

        if (waktuSelect){
            waktuSelect.value = "";
            onWaktuChange();
        }

        if (tahunTomSelect) {
            tahunTomSelect.clear(true);
            tahunTomSelect.setValue('');
        }
        if (bulanTomSelect) {
            bulanTomSelect.clear(true);
            bulanTomSelect.setValue('');
        }
    }
}

function submitExport() {
    const waktu = document.getElementById('waktu').value;
    const tahun = document.getElementById('tahun').value;
    const bulan = document.getElementById('bulan').value;

    if (!waktu) {
        return;
    }

    let url = '';

    if (waktu === 'semua') {
        url = '/export-laporan/All';
    } else if (waktu === 'per_tahun') {
        if (!tahun) {
            return;
        }
        url = `/export-laporan/perTahun?tahun=${tahun}`;
    } else if (waktu === 'per_bulan') {
        if (!tahun || !bulan) {
            return;
        }
        url = `/export-laporan/perBulan?tahun=${tahun}&bulan=${bulan}`;
    }

    window.location.href = url;
}

document.getElementById("uploadModal").addEventListener("click", function (e) {
    if (e.target === this) {
        closeImportExportModal();
    }
});

function toggleDatePicker() {
    const datePicker = document.getElementById("datePicker");
    datePicker.classList.toggle("hidden");
}

document.addEventListener("click", function (e) {
    const datePicker = document.getElementById("datePicker");
    const dateRangeInput = document.getElementById("dateRange");

    if (!datePicker.contains(e.target) && !dateRangeInput.contains(e.target)) {
        datePicker.classList.add("hidden");
    }
});

function filterTable() {
    const formatDate = (date) => {
        if (!date) return "";
        const yyyy = date.getFullYear();
        const mm = String(date.getMonth() + 1).padStart(2, "0");
        const dd = String(date.getDate()).padStart(2, "0");
        return `${yyyy}/${mm}/${dd}`;
    };

    // Update current filters
    currentFilters.search = document.getElementById("searchInput").value.trim();
    currentFilters.status = document.getElementById("filterStatus").value;
    currentFilters.kategori = document.getElementById("filterKategori").value;
    currentFilters.jenis_instansi = document.getElementById("filterJenisInstansi").value;

    const startDateInput = document.getElementById("startDate").value;
    const endDateInput = document.getElementById("endDate").value;

    currentFilters.start_date = startDateInput;
    currentFilters.end_date = endDateInput;

    // Update date range display
    if (startDateInput || endDateInput) {
        const startDate = startDateInput ? new Date(startDateInput) : null;
        const endDate = endDateInput ? new Date(endDateInput) : null;

        document.getElementById("dateRange").value = `${formatDate(startDate)} - ${formatDate(endDate)}`;
    }

    // Check if any filter is active
    const hasActiveFilters = Object.values(currentFilters).some(value => value && value.trim() !== '');

    if (hasActiveFilters) {
        isFilterActive = true;
        currentPage = 1;
        showFilterLoading();
        fetchFilteredData();
    } else {
        isFilterActive = false;
        currentPage = 1;
        showFilterLoading();
        fetchData();
    }
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
        const mm = String(date.getMonth() + 1).padStart(2, "0");
        const dd = String(date.getDate()).padStart(2, "0");
        return `${yyyy}-${mm}-${dd}`;
    };

    document.getElementById("startDate").value = formatDate(start);
    document.getElementById("endDate").value = formatDate(today);
    document.getElementById("dateRange").value = `${formatDate(start)} - ${formatDate(today)}`;

    filterTable();
}

function clearDateRange() {
    document.getElementById("startDate").value = "";
    document.getElementById("endDate").value = "";
    document.getElementById("dateRange").value = "";
    document.getElementById("datePicker").classList.add("hidden");

    filterTable();
}

function handleEntriesChange() {
    const showEntries = parseInt(document.getElementById("showEntries").value);
    itemsPerPage = showEntries;
    currentPage = 1;

    showFilterLoading();

    if (isFilterActive) {
        fetchFilteredData();
    } else {
        fetchData();
    }
}

document.getElementById("searchInput").addEventListener("input", debounce(filterTable, 500));
document.getElementById("filterStatus").addEventListener("change", filterTable);
document.getElementById("filterKategori").addEventListener("change", filterTable);
document.getElementById("filterJenisInstansi").addEventListener("change", filterTable);
document.getElementById("datePicker").addEventListener("change", filterTable);
document.getElementById("showEntries").addEventListener("change", handleEntriesChange);

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

function openModal(itemId) {
    const data = laporanData.find(item => item.id == itemId);
    if (!data) {
        console.error('Data not found for ID:', itemId);
        console.log('Available IDs:', laporanData.map(item => item.id));
        showToast("error", "Data tidak ditemukan.");
        return;
    }

    currentEditingIndex = itemId;

    document.getElementById("modalUserNama").innerText = data.user_nama || '-';
    document.getElementById("modalUserEmail").innerText = data.user_email || '-';
    document.getElementById("modalUserInstansi").innerText = data.user_instansi || '-';
    document.getElementById("modalUserJenisInstansi").innerText =
        data.user_jenis_instansi === "desa" ? "Desa" :
        data.user_jenis_instansi === "pemda" ? "Perangkat Daerah" : "-";
    document.getElementById("modalResi").innerText = data.resi || '-';
    document.getElementById("modalNama").innerText = data.judul_masalah || data.masalah || '-';
    document.getElementById("modalTanggal").innerText = data.tanggal_pengajuan || '-';
    document.getElementById("modalDeskripsi").innerText = data.deskripsi || '-';

    const lampiranUrl = data.lampiran_url || (data.lampiran ? `/storage/${data.lampiran}` : null);
    document.getElementById("modalLampiran").innerHTML = lampiranUrl
        ? `<a href="${lampiranUrl}" target="_blank" class="text-blue-600 underline hover:text-blue-800">
            Lihat Lampiran
        </a>`
        : "-";

    const statusSelect = document.getElementById("modalSetStatus");
    const currentStatus = data.status || "";

    const allOptions = [
        { value: "", label: "--- Set Status ---" },
        { value: "Pengajuan", label: "Pengajuan" },
        { value: "Progress", label: "Progress" },
        { value: "Selesai", label: "Selesai" },
    ];

    let filteredOptions = [];

    if (currentStatus.toLowerCase() === "pengajuan") {
        filteredOptions = allOptions;
    } else if (currentStatus.toLowerCase() === "progress") {
        filteredOptions = allOptions.filter(opt =>
            ["", "Progress", "Selesai"].includes(opt.value)
        );
    } else if (currentStatus.toLowerCase() === "selesai") {
        filteredOptions = allOptions.filter(opt =>
            ["", "Selesai"].includes(opt.value)
        );
    } else {
        filteredOptions = allOptions;
    }

    statusSelect.innerHTML = "";
    filteredOptions.forEach(opt => {
        const optionEl = document.createElement("option");
        optionEl.value = opt.value;
        optionEl.textContent = opt.label;
        statusSelect.appendChild(optionEl);
    });

    statusSelect.value = currentStatus;

    statusSelect.setAttribute('data-original-status', currentStatus);

    document.getElementById("modalSetKategori").value = data.kategori || "";
    document.getElementById("modalSetTanggalSelesai").value = data.tanggal_selesai || "";
    document.getElementById("modalDeskripsiPenanganan").value = data.penyelesaian || "";

    document.getElementById("editForm").action = `/admin/laporan/${data.id}/update`;

    toggleButtonByStatus();

    const modal = document.getElementById("editModal");
    if (modal) {
        modal.classList.remove("hidden");
        modal.classList.add("flex");
        console.log('Modal opened successfully');
    } else {
        console.error('Modal element not found');
    }
}

document.getElementById("editForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    const form = e.target;
    const actionUrl = form.action;
    const formData = new FormData(form);

    formData.append("_method", "PUT");
    const submitBtn = document.getElementById("adminEditBtn");
    const loadingBtn = document.getElementById("loadingBtn");

    submitBtn.classList.add("hidden");
    loadingBtn.classList.remove("hidden");
    loadingBtn.classList.add("inline-flex");

    try {
        const response = await fetch(actionUrl, {
            method: "POST",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            },
            body: formData,
        });

        const result = await response.json();

        if (response.ok) {
            if (isFilterActive) {
                await fetchFilteredData();
            } else {
                await fetchData();
            }
            closeModal();
            showToast("success", result.message || "Perubahan berhasil disimpan.");
        } else {
            if (result.errors) {
                showInputErrors(result.errors);
            } else {
                showToast("error", result.message || "Terjadi kesalahan.");
            }
        }
    } catch (error) {
        console.error("Terjadi kesalahan saat submit:", error);
        showToast("error", "Terjadi kesalahan sistem.");
    } finally {
        loadingBtn.classList.add("hidden");
        loadingBtn.classList.remove("inline-flex");
        submitBtn.classList.remove("hidden");
    }
});

function closeModal() {
    currentEditingIndex = null;
    const modal = document.getElementById("editModal");
    modal.classList.add("hidden");
    modal.classList.remove("flex");
}

document.getElementById("editModal").addEventListener("click", function (e) {
    if (e.target === this) {
        closeModal();
    }
});

async function fetchData() {
    try {
        const params = new URLSearchParams({
            page: currentPage,
            per_page: itemsPerPage
        });

        const response = await fetch(`/laporan/all?${params.toString()}`);
        const result = await response.json();

        laporanData = result.data?.all_data || [];
        paginationInfo = {
            total: result.data?.total || 0,
            per_page: itemsPerPage,
            current_page: currentPage
        };

        renderTable();

        if (result.statistics) {
            updateStatistics(result.statistics);
        }
    } catch (error) {
        console.error("Gagal memuat data laporan:", error);
        showToast("error", "Gagal memuat data laporan.");
        laporanData = [];
        paginationInfo = { total: 0, per_page: itemsPerPage, current_page: 1 };
        renderTable();
        updateStatistics();
    } finally {
        hideFilterLoading();
    }
}

async function fetchAllYears() {
    try {
        const response = await fetch(`/all/years`);
        const result = await response.json();

        if (result.data) {
            isiDropdownTahun(result.data);
        }

    } catch (error) {
        console.error("Error fetch years:", error);
    }
}

async function fetchFilteredData() {
    try {
        const params = new URLSearchParams({
            page: currentPage,
            per_page: itemsPerPage
        });

        Object.keys(currentFilters).forEach(key => {
            if (currentFilters[key] && currentFilters[key].trim() !== '') {
                params.append(key, currentFilters[key].trim());
            }
        });

        const response = await fetch(`/laporan/searchFilter?${params.toString()}`);
        const result = await response.json();

        laporanData = result.all_data?.data || [];
        paginationInfo = {
            total: result.all_data?.total || 0,
            per_page: result.all_data?.per_page || itemsPerPage,
            current_page: result.all_data?.current_page || currentPage
        };

        renderTable();

        if (result.statistics) {
            updateStatistics(result.statistics);
        }

        if (laporanData.length > 0) {
            isiDropdownTahun(laporanData);
        }
    } catch (error) {
        console.error("Gagal memuat data filter:", error);
        showToast("error", "Gagal memuat data filter.");

        laporanData = [];
        paginationInfo = { total: 0, per_page: itemsPerPage, current_page: 1 };
        renderTable();
    } finally {
        hideFilterLoading();
    }
}

// Function to clear all filters and return to initial data
function clearAllFilters() {
    // Reset filter form
    document.getElementById("searchInput").value = "";
    document.getElementById("filterStatus").value = "";
    document.getElementById("filterKategori").value = "";
    document.getElementById("filterJenisInstansi").value = "";
    document.getElementById("startDate").value = "";
    document.getElementById("endDate").value = "";
    document.getElementById("dateRange").value = "";

    // Reset filter object
    currentFilters = {
        search: '',
        status: '',
        kategori: '',
        jenis_instansi: '',
        start_date: '',
        end_date: ''
    };

    // Reset filter state
    isFilterActive = false;
    currentPage = 1;

    // Show loading dan fetch original data
    showFilterLoading();
    fetchData();
}

document.addEventListener("DOMContentLoaded", function () {
    fetchData();
    fetchAllYears();
    const tableContainer = document.getElementById("laporanTable");
    if (tableContainer) {
        tableContainer.addEventListener("click", function (e) {
            const editBtn = e.target.closest(".edit-btn") || (e.target.classList.contains("edit-btn") ? e.target : null);

            if (editBtn) {
                const itemId = editBtn.getAttribute("data-id");

                if (itemId) {
                    const numericId = parseInt(itemId);
                    openModal(numericId);
                }
            }
        });
    } else {
        console.error('Table container not found');
    }

    const statusSelect = document.getElementById("modalSetStatus");
    if (statusSelect) {
        statusSelect.addEventListener("change", toggleButtonByStatus);
    }
});

function toggleButtonByStatus() {
    const statusSelect = document.getElementById("modalSetStatus");
    const submitBtn = document.getElementById("adminEditBtn");
    const status = statusSelect.value;

    const originalStatus = statusSelect.getAttribute('data-original-status') || '';

    if (originalStatus.toLowerCase() === "selesai" && status === "Selesai") {
        submitBtn.disabled = true;
        submitBtn.classList.add("opacity-50", "cursor-not-allowed");
        submitBtn.title = "Status sudah 'Selesai', tidak perlu disimpan ulang.";
    } else {
        submitBtn.disabled = false;
        submitBtn.classList.remove("opacity-50", "cursor-not-allowed");
        submitBtn.removeAttribute("title");
    }
}

function onJenisChange() {
    const jenis = document.getElementById("jenisData").value;
    const form = document.getElementById("uploadForm");

    if (jenis === "kecamatan") {
        form.action = "{{ route ('import.kecamatan') }}";
    } else if (jenis === "desa") {
        form.action = "{{ route ('import.desa') }}";
    } else if (jenis === "pemda") {
        form.action = "{{ route ('import.pemda') }}";
    }
}

function showToast(type, message) {
    document.getElementById("dynamicToast")?.remove();

    const isSuccess = type === "success";
    const toast = document.createElement("div");
    toast.id = "dynamicToast";
    toast.className = `fixed top-0 left-1/2 z-50 transform -translate-x-1/2 -translate-y-full opacity-0 transition duration-500 ease-out`;

    toast.innerHTML = `
        <div class="bg-gradient-to-r ${
            isSuccess
                ? "from-green-50 to-emerald-100 border-green-500"
                : "from-red-50 to-rose-100 border-red-500"
        } border-l-4 p-4 rounded-lg shadow-lg w-80">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-${
                        isSuccess ? "check" : "times"
                    }-circle text-${
        isSuccess ? "green" : "red"
    }-500 text-lg"></i>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-${
                        isSuccess ? "green" : "red"
                    }-800">${message}</p>
                </div>
                <button type="button" onclick="document.getElementById('dynamicToast')?.remove()"
                        class="flex-shrink-0 ml-3 text-${
                            isSuccess ? "green" : "red"
                        }-400 hover:text-${
        isSuccess ? "green" : "red"
    }-600 transition-colors duration-200">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.classList.remove("-translate-y-full", "opacity-0");
        toast.classList.add("translate-y-10", "opacity-100");
    }, 100);

    setTimeout(() => {
        toast.classList.remove("translate-y-10", "opacity-100");
        toast.classList.add("-translate-y-full", "opacity-0");
    }, 5000);
}

function showInputErrors(errors) {
    document.querySelectorAll('.input-error').forEach(el => el.remove());

    for (const [field, messages] of Object.entries(errors)) {
        const input = document.querySelector(`[name="${field}"]`);
        if (input) {
            const errorEl = document.createElement('div');
            errorEl.className = 'text-sm text-red-600 mt-1 input-error';
            errorEl.textContent = messages.join(', ');

            input.insertAdjacentElement('afterend', errorEl);

            setTimeout(() => {
                errorEl.remove();
            }, 3000);
        }
    }
}

// Loading functions untuk filter
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

function hideFilterLoading() {
    // Loading akan otomatis hilang ketika renderTable() dipanggil
    // Fungsi ini dibuat untuk konsistensi dan kemungkinan penggunaan future
}

function changePage(page) {
    const totalPages = Math.ceil(paginationInfo.total / paginationInfo.per_page);
    if (page >= 1 && page <= totalPages && page !== currentPage) {
        currentPage = page;

        showFilterLoading();

        if (isFilterActive) {
            fetchFilteredData();
        } else {
            fetchData();
        }
    }
}
