// Toggle confirm password visibility
    function togglePassword() {
        const passwordInput = document.getElementById("password");
        const toggleIcon = document.getElementById("toggleIcon");

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

    function togglePasswordConfirm() {
        const passwordConfirm = document.getElementById("password_confirmation");
        const toggleIconConfirm = document.getElementById("toggleConfirmIcon");

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
