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

function togglePasswordLama() {
    const passwordInput = document.getElementById("password_lama");
    const toggleIcon = document.getElementById("togglePassLama");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        toggleIcon.classList.remove("fa-eye");
        toggleIcon.classList.add("fa-eye-slash");
    } else {
        passwordInput.type = "password";
        toggleIcon.classList.remove("fa-eye-slash");
        toggleIcon.classList.add("fa-eye");
    }
}

function togglePasswordBaru() {
    const passwordInput = document.getElementById("password_baru");
    const toggleIcon = document.getElementById("togglePassbaru");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        toggleIcon.classList.remove("fa-eye");
        toggleIcon.classList.add("fa-eye-slash");
    } else {
        passwordInput.type = "password";
        toggleIcon.classList.remove("fa-eye-slash");
        toggleIcon.classList.add("fa-eye");
    }
}

function toggleConfirmPasswordBaru() {
    const passwordConfirm = document.getElementById("password_baru_conformation");
    const toggleIconConfirm = document.getElementById("toggleConfirmIconPassBaru");

    if (passwordConfirm.type === "password") {
        passwordConfirm.type = "text";
        toggleIconConfirm.classList.remove("fa-eye");
        toggleIconConfirm.classList.add("fa-eye-slash");
    } else {
        passwordConfirm.type = "password";
        toggleIconConfirm.classList.remove("fa-eye-slash");
        toggleIconConfirm.classList.add("fa-eye");
    }
}

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
