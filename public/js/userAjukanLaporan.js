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

// File upload handling
const fileInput = document.getElementById('lampiran');
const fileLabel = document.querySelector('label[for="lampiran"]');

//tombol hapus
const deleteButton = document.createElement('button');
deleteButton.textContent = 'Hapus File';
deleteButton.type = 'button';
deleteButton.className = 'mt-2 text-red-600 text-sm underline hover:text-red-800 hidden'; // Disembunyikan awal
fileLabel.parentElement.appendChild(deleteButton);

fileInput.addEventListener('change', function () {
    if (this.files.length > 0) {
        const fileName = this.files[0].name;
        fileLabel.innerHTML = `
            <i class="fas fa-check-circle text-3xl mb-2 text-green-500"></i>
            <span class="text-sm text-center text-green-600">File terpilih: ${fileName}</span>
            <span class="text-xs text-gray-400 mt-1">Klik lagi untuk mengganti file</span>
        `;
        deleteButton.classList.remove('hidden');
    }
});

// Saat tombol "Hapus File" diklik
deleteButton.addEventListener('click', function () {
    fileInput.value = '';
    fileLabel.innerHTML=''
    deleteButton.classList.add('hidden');
});

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-ajuan');
    const submitButton = document.getElementById('submitButton');

    function validateForm() {
        const nama = document.getElementById('nama').value.trim();
        const instansi = document.getElementById('instansi').value.trim();

        const namaValid = nama !== '' ;
        const instansiValid = instansi !== '';

        return namaValid && instansiValid;
    }

    function updateButtonState() {
        submitButton.disabled = !validateForm();
        console.log('Button state updated:', submitButton.disabled ? 'DISABLED' : 'ENABLED');
    }

    // Listener
    ['nama', 'instansi'].forEach(id => {
        const input = document.getElementById(id);
        input.addEventListener('input', updateButtonState);
    });

    updateButtonState();
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

const lampiran = document.getElementById('lampiran')
lampiran.addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;

    // Pastikan hanya preview untuk jenis file tertentu (PDF, gambar, dll)
    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
    if (!allowedTypes.includes(file.type)) {
        alert("File tidak bisa dipreview otomatis.");
    return;
    }

    // Buat URL lokal untuk file
    const fileURL = URL.createObjectURL(file);

    // Buka tab baru
    setTimeout(() => {
        const newTab = window.open('', '_blank');
        if (file.type.includes('image')) {
            newTab.document.write(`<img src="${fileURL}" style="max-width:100%;">`);
        } else if (file.type === 'application/pdf') {
            newTab.document.write(`
                <iframe src="${fileURL}" style="width:100%;height:100%;position:absolute;top:0;left:0;border:none;"></iframe>
            `);
        }
    }, 100);
});
