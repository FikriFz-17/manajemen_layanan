// Mobile hamburger menu
let currentPage = 1;
let itemsPerPage = 5;
let filteredData = [];
let laporanData = [];

const hamburgerBtn = document.getElementById('hamburgerBtn');
const sidebar = document.getElementById('sidebar');
const mobileOverlay = document.getElementById('mobileOverlay');

hamburgerBtn.addEventListener('click', function() {
    sidebar.classList.toggle('-translate-x-full');
    mobileOverlay.classList.toggle('hidden');
});

mobileOverlay.addEventListener('click', function() {
    sidebar.classList.add('-translate-x-full');
    mobileOverlay.classList.add('hidden');
});

document.addEventListener('click', function(e) {
    if (window.innerWidth < 1024) {
        if (!sidebar.contains(e.target) && !hamburgerBtn.contains(e.target)) {
        sidebar.classList.add('-translate-x-full');
        mobileOverlay.classList.add('hidden');
        }
    }
});

window.addEventListener('resize', function() {
    if (window.innerWidth >= 1024) {
        sidebar.classList.remove('-translate-x-full');
        mobileOverlay.classList.add('hidden');
    } else {
        sidebar.classList.add('-translate-x-full');
    }
});

function toggleDropdown() {
    if (window.innerWidth < 1024) {
        const dropdown = document.getElementById('userDropdown');
        dropdown.classList.toggle('hidden');
    } else {
        const dropdown = document.getElementById('userDropdownDesktop');
        dropdown.classList.toggle('hidden');
    }
}

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

function updateStatistics() {
    const totalUsers = laporanData.length;
    const activeUsers = laporanData.filter(user => user.active !== null).length;
    const pendingUsers = laporanData.filter(user => user.active === null).length;

    document.getElementById('totalUsers').textContent = totalUsers;
    document.getElementById('activeUsers').textContent = activeUsers;
    document.getElementById('pendingUsers').textContent = pendingUsers;
}

function renderTable() {
    const tableBody = document.getElementById('usersTable');
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = itemsPerPage === 50 ? filteredData.length : startIndex + itemsPerPage;
    const currentData = filteredData.slice(startIndex, endIndex);

    tableBody.innerHTML = '';

    currentData.forEach((user, index) => {
        const statusText = user.active ? 'Active' : 'Pending';
        const statusClass = user.active ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';

        const emailBtnClass = user.active ? 'text-gray-400 cursor-not-allowed' : 'text-green-500 hover:text-green-700 cursor-pointer';
        const emailBtnTitle = user.active ? 'User already verified' : 'Send Verification Email';

        const row = `
            <tr class="border-b hover:bg-gray-50" data-user-id="${user.id}">
                <td class="px-2 lg:px-4 py-2 text-sm lg:text-base">${user.nama}</td>
                <td class="px-2 lg:px-4 py-2 text-sm lg:text-base">${user.email}</td>
                <td class="px-2 lg:px-4 py-2 text-sm lg:text-base">${user.instansi}</td>
                <td class="px-2 lg:px-4 py-2 text-sm lg:text-base">
                    <span class="px-2 py-1 ${statusClass} rounded text-xs">${statusText}</span>
                </td>
                <td class="px-2 lg:px-4 py-2 text-sm lg:text-base">${user.created}</td>
                <td class="px-2 lg:px-4 py-2">
                    <div class="flex flex-row gap-2 justify-center">
                        <i class="fas fa-envelope ${emailBtnClass} email-btn" title="${emailBtnTitle}" ${user.active ? 'data-disabled="true"' : ''}></i>
                        <i class="fa-solid fa-trash-can text-red-500 delete-btn cursor-pointer" title="Delete User"></i>
                    </div>
                </td>
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

    document.getElementById('paginationInfo').textContent = `Showing ${startIndex} to ${endIndex} of ${totalEntries} entries`;
}

// Render pagination
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

function changePage(page) {
    const totalPages = Math.ceil(filteredData.length / itemsPerPage);
    if (page >= 1 && page <= totalPages) {
        currentPage = page;
        renderTable();
    }
}

function filterTable() {
    const keyword = document.getElementById('searchInput').value.toLowerCase().trim();
    const status = document.getElementById('filterStatus').value;

    filteredData = laporanData.filter(item => {
        const nameMatch = item.nama.toLowerCase().includes(keyword);
        const emailMatch = item.email.toLowerCase().includes(keyword);

        // Filter by status
        let statusMatch = true;
        if (status === 'Active') {
            statusMatch = item.active !== null;
        } else if (status === 'Pending') {
            statusMatch = item.active === null;
        }

        return (nameMatch || emailMatch) && statusMatch;
    });

    currentPage = 1;
    renderTable();
}

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

document.addEventListener('DOMContentLoaded', function() {
    fetch('/user/all')
    .then(response => response.json())
    .then(data => {
        laporanData = data.map(user => ({
            id: user.id,
            nama: user.nama,
            instansi: user.instansi,
            email: user.email,
            active: user.email_verified_at,
            created: user.created_at
        }));
        filteredData = [...laporanData];
        updateStatistics();
        renderTable();
    })
    .catch(error => {
        console.error('Gagal memuat data laporan:', error);
    });

    // Event listener for email buttons
    document.getElementById('usersTable').addEventListener('click', function(e) {
        const emailBtn = e.target.closest('.email-btn');
        if (emailBtn) {
            const row = emailBtn.closest('tr');
            const userId = row.getAttribute('data-user-id');
            const user = laporanData.find(u => u.id == userId);
            if (user) {
                if (user.active !== null) {
                    console.log("user ini active");
                } else {
                    console.log("user ini tidak active - sending verification email");
                    // Buat form dinamis untuk submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/email/verification-notification';
                    form.style.display = 'none';
                    // Tambahkan CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    if (csrfToken) {
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = csrfToken;
                        form.appendChild(csrfInput);
                    }
                    // Tambahkan email input
                    const emailInput = document.createElement('input');
                    emailInput.type = 'hidden';
                    emailInput.name = 'email';
                    emailInput.value = user.email;
                    form.appendChild(emailInput);
                    // Append form ke body dan submit
                    document.body.appendChild(form);
                    form.submit();
                }
                console.log('Email button clicked for user ID:', userId);
                console.log('User data:', user);
            }
        }
    });

    document.getElementById('usersTable').addEventListener('click', function(e){
        const deleteBtn = e.target.closest('.delete-btn');
        if (deleteBtn) {
            const row = deleteBtn.closest('tr');
            const userId = row.getAttribute('data-user-id');
            const user = laporanData.find(u => u.id == userId);
            if (user) {
                console.log('Delete button clicked for user ID:', userId);
                console.log('User data:', user);
                // Buat form dinamis untuk submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/delete-user/' + userId;
                form.style.display = 'none';
                // Tambahkan CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (csrfToken) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    form.appendChild(csrfInput);

                    // Method spoofing DELETE
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);
                }

                // Append form ke body dan submit
                document.body.appendChild(form);
                form.submit();
            }
        }
    })
});

document.addEventListener('DOMContentLoaded', function () {
    ['successToast', 'errorToast'].forEach(function (id) {
        const toast = document.getElementById(id);
        if (toast) {
            // Munculkan dengan animasi slide-down
            setTimeout(() => {
                toast.classList.remove('-translate-y-full', 'opacity-0');
                toast.classList.add('translate-y-10', 'opacity-100');
            }, 100);

            // Hilangkan setelah 5 detik
            setTimeout(() => {
                toast.classList.remove('translate-y-10', 'opacity-100');
                toast.classList.add('-translate-y-full', 'opacity-0');
            }, 5000);
        }
    });
});
