// Pagination variables
let laporanData = [];
let currentPage = 1;
let itemsPerPage = 5;
let filteredData = [];
let currentEditingIndex = null;

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

    document.getElementById("progressCount").textContent = progressCount;
    document.getElementById("successCount").textContent = successCount;
    document.getElementById("pengajuanCount").textContent = pengajuanCount;
}

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
                <td class="px-2 lg:px-4 py-2 text-sm lg:text-base"><span class="px-2 py-1 ${statusClass} rounded text-xs">${item.status}</span></td>
                <td class="px-2 lg:px-4 py-2 text-sm lg:text-base">${item.tanggal}</td>
                <td class="px-2 lg:px-4 py-2"><i class="fas fa-edit text-blue-500 hover:text-blue-700 cursor-pointer edit-btn"></i></td>
            </tr>
        `;
        tableBody.innerHTML += row;
    });

    updatePaginationInfo();
    renderPagination();
}

function updatePaginationInfo() {
    const startIndex = (currentPage - 1) * itemsPerPage + 1;
    const endIndex = Math.min(currentPage * itemsPerPage, filteredData.length);
    const totalEntries = filteredData.length;

    document.getElementById(
        "paginationInfo"
    ).textContent = `Showing ${startIndex} to ${endIndex} of ${totalEntries} entries`;
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

function changePage(page) {
    const totalPages = Math.ceil(filteredData.length / itemsPerPage);
    if (page >= 1 && page <= totalPages) {
        currentPage = page;
        renderTable();
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

// Export function placeholder (untuk implementasi selanjutnya)
document.getElementById("exportBtn").addEventListener("click", function () {
    window.location.href = "/export-laporan";
});

function openImportModal() {
    const modal = document.getElementById("uploadModal");
    modal.classList.remove("hidden");
    modal.classList.add("flex");
}

function closeImportModal() {
    const modal = document.getElementById("uploadModal");
    modal.classList.remove("flex");
    modal.classList.add("hidden");
}

document.getElementById("uploadModal").addEventListener("click", function (e) {
    if (e.target === this) {
        closeImportModal();
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

    const keyword = document
        .getElementById("searchInput")
        .value.toLowerCase()
        .trim();
    const status = document.getElementById("filterStatus").value;
    const kategori = document.getElementById("filterKategori").value;
    const jenis_instansi = document.getElementById("filterJenisInstansi").value;

    const startDateInput = document.getElementById("startDate").value;
    const endDateInput = document.getElementById("endDate").value;

    const startDate = startDateInput ? new Date(startDateInput) : null;
    const endDate = endDateInput ? new Date(endDateInput) : null;

    filteredData = laporanData.filter((item) => {
        const idMatch = item.resi.toString().includes(keyword);
        const masalahMatch = item.masalah.toLowerCase().includes(keyword);
        const statusMatch = status === "" || item.status === status;
        const kategoriMatch = kategori === "" || item.kategori === kategori;
        const jenisInstansiMatch =
            jenis_instansi === "" || item.jenis_instansi === jenis_instansi;

        let tanggalMatch = true;
        if (startDate || endDate) {
            // Update text input dateRange
            document.getElementById("dateRange").value = `${formatDate(
                startDate
            )} - ${formatDate(endDate)}`;

            const resiDateStr = item.resi.substring(0, 6); // "ddmmyy"
            const day = resiDateStr.substring(0, 2);
            const month = resiDateStr.substring(2, 4);
            const year = "20" + resiDateStr.substring(4, 6);

            const resiDate = new Date(`${year}-${month}-${day}`);

            if (startDate && resiDate < startDate) tanggalMatch = false;
            if (endDate && resiDate > endDate) tanggalMatch = false;
        }

        return (
            (idMatch || masalahMatch) &&
            statusMatch &&
            kategoriMatch &&
            tanggalMatch &&
            jenisInstansiMatch
        );
    });

    currentPage = 1;
    renderTable();
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

    // Update tampilan teks range
    document.getElementById("dateRange").value = `${formatDate(
        start
    )} - ${formatDate(today)}`;

    // Apply filter
    filterTable();
}

function clearDateRange() {
    document.getElementById("startDate").value = "";
    document.getElementById("endDate").value = "";
    document.getElementById("dateRange").value = "";

    // Sembunyikan date picker jika sedang terbuka
    document.getElementById("datePicker").classList.add("hidden");

    // Render ulang tabel tanpa filter tanggal
    filterTable();
}

function handleEntriesChange() {
    const showEntries = parseInt(document.getElementById("showEntries").value);
    itemsPerPage = showEntries;
    currentPage = 1;
    renderTable();
}

document.getElementById("searchInput").addEventListener("input", filterTable);
document.getElementById("filterStatus").addEventListener("change", filterTable);
document
    .getElementById("filterKategori")
    .addEventListener("change", filterTable);
document
    .getElementById("filterJenisInstansi")
    .addEventListener("change", filterTable);
document.getElementById("datePicker").addEventListener("change", filterTable);
document
    .getElementById("showEntries")
    .addEventListener("change", handleEntriesChange);

function openModal(dataIndex) {
    currentEditingIndex = dataIndex;
    const data = laporanData[currentEditingIndex];

    document.getElementById("modalUserNama").innerText = data.nama;
    document.getElementById("modalUserEmail").innerText = data.email;
    document.getElementById("modalUserInstansi").innerText = data.instansi;
    document.getElementById("modalUserJenisInstansi").innerText =
        data.jenis_instansi === "desa"
            ? "Desa"
            : data.jenis_instansi === "pemda"
            ? "Perangkat Daerah"
            : "-";
    document.getElementById("modalResi").innerText = data.resi;
    document.getElementById("modalNama").innerText = data.masalah;
    document.getElementById("modalTanggal").innerText = data.tanggal;
    document.getElementById("modalDeskripsi").innerText = data.deskripsi;
    document.getElementById("modalLampiran").innerHTML = data.lampiran
        ? `<a href="/storage/${data.lampiran}" target="_blank" class="text-blue-600 underline hover:text-blue-800">
        Lihat Lampiran
    </a>`
        : "-";

    // Fill form fields
    document.getElementById("modalSetStatus").value = data.status || "";
    document.getElementById("modalSetKategori").value = data.kategori || "";
    document.getElementById("modalSetTanggalSelesai").value =
        data.tanggal_selesai || "";
    document.getElementById("modalDeskripsiPenanganan").value =
        data.penyelesaian || "";

    document.getElementById(
        "editForm"
    ).action = `/admin/laporan/${data.id}/update`;

    toggleButtonByStatus();

    const modal = document.getElementById("editModal");
    modal.classList.remove("hidden");
    modal.classList.add("flex");
}

document.getElementById("editForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    const form = e.target;
    const actionUrl = form.action;
    const formData = new FormData(form);

    formData.append("_method", "PUT");
    const submitBtn = document.getElementById("adminEditBtn");
    const loadingBtn = document.getElementById("loadingBtn");

    // Tampilkan loader, sembunyikan tombol submit
    submitBtn.classList.add("hidden");
    loadingBtn.classList.remove("hidden");
    loadingBtn.classList.add("inline-flex");
    try {
        const response = await fetch(actionUrl, {
            method: "POST",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
            body: formData,
        });

        const result = await response.json();

        if (response.ok) {
            // Jika sukses
            await fetchData();
            closeModal();
            filterTable();
            showToast(
                "success",
                result.message || "Perubahan berhasil disimpan."
            );
        } else {
            if (result.errors) {
                // Jika error validasi dari Laravel
                showInputErrors(result.errors); // Fungsi ini akan tampilkan error di bawah input
            } else {
                // Jika error umum
                showToast("error", result.message || "Terjadi kesalahan.");
            }
        }

    } catch (error) {
        console.error("Terjadi kesalahan saat submit:", error);
    } finally {
        // Kembalikan tombol submit dan sembunyikan loader
        loadingBtn.classList.add("hidden");
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
        const response = await fetch("/laporan/all");
        const data = await response.json();

        laporanData = data.map((item) => ({
            id: item.id,
            resi: item.resi,
            masalah: item.judul_masalah,
            status: item.status,
            kategori: item.kategori,
            tanggal: item.tanggal_pengajuan,
            tanggal_selesai: item.tanggal_selesai,
            estimasi: item.estimasi,
            deskripsi: item.deskripsi,
            penyelesaian: item.penyelesaian,
            lampiran: item.lampiran,
            nama: item.user_nama,
            instansi: item.user_instansi,
            jenis_instansi: item.user_jenis_instansi,
            email: item.user_email,
        }));

        filteredData = [...laporanData];
        updateStatistics();
        renderTable();
    } catch (error) {
        console.error("Gagal memuat data laporan:", error);
    }
}

document.addEventListener("DOMContentLoaded", function () {
    fetchData();
    document
        .getElementById("laporanTable")
        .addEventListener("click", function (e) {
            const editBtn = e.target.closest(".edit-btn");
            if (editBtn) {
                const row = editBtn.closest("tr");
                const dataIndex = parseInt(row.getAttribute("data-index"));
                openModal(dataIndex);
            }
        });
});

function toggleButtonByStatus() {
    const statusSelect = document.getElementById("modalSetStatus");
    const submitBtn = document.getElementById("adminEditBtn");
    const status = statusSelect.value;
    if (status === "Selesai") {
        submitBtn.disabled = true;
        submitBtn.classList.add("opacity-50", "cursor-not-allowed");
        submitBtn.title = "Status 'Selesai' tidak bisa disimpan ulang.";
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
    // Hapus toast lama jika ada
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

    // Animasi masuk
    setTimeout(() => {
        toast.classList.remove("-translate-y-full", "opacity-0");
        toast.classList.add("translate-y-10", "opacity-100");
    }, 100);

    // Sembunyikan otomatis setelah 5 detik
    setTimeout(() => {
        toast.classList.remove("translate-y-10", "opacity-100");
        toast.classList.add("-translate-y-full", "opacity-0");
    }, 5000);
}

function showInputErrors(errors) {
    // Hapus semua error sebelumnya
    document.querySelectorAll('.input-error').forEach(el => el.remove());

    for (const [field, messages] of Object.entries(errors)) {
        const input = document.querySelector(`[name="${field}"]`);
        if (input) {
            const errorEl = document.createElement('div');
            errorEl.className = 'text-sm text-red-600 mt-1 input-error';
            errorEl.textContent = messages.join(', ');

            input.insertAdjacentElement('afterend', errorEl);

            // Tambahkan timeout agar error menghilang otomatis
            setTimeout(() => {
                errorEl.remove();
            }, 3000); // 3 detik
        }
    }
}


