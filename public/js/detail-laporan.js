const shareUrl = window.location.href;
const shareText = "Pengaduan mengenai layanan.";

function toggleShareMenu() {
    const menu = document.getElementById("shareMenu");

    if (menu.classList.contains("hidden")) {
        // Show menu
        menu.classList.remove("hidden");
        setTimeout(() => {
            menu.classList.remove("opacity-0", "scale-90");
            menu.classList.add("opacity-100", "scale-100");
        }, 10);
    } else {
        // Hide menu
        menu.classList.add("opacity-0", "scale-90");
        menu.classList.remove("opacity-100", "scale-100");
        setTimeout(() => {
            menu.classList.add("hidden");
        }, 300);
    }
}

function shareToFacebook() {
    const url = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(
        shareUrl
    )}`;
    window.open(url, "_blank", "width=600,height=400");
}

function shareToWhatsApp() {
    const url = `https://wa.me/?text=${encodeURIComponent(
        shareText + " " + shareUrl
    )}`;
    window.open(url, "_blank", "width=600,height=400");
}

function shareToX() {
    const url = `https://twitter.com/intent/tweet?text=${encodeURIComponent(shareText)}&url=${encodeURIComponent(shareUrl)}`;
    window.open(url, "_blank", "width=600,height=400");
}

function copyLink() {
    const linkButtonSvg = document.querySelector(
        '#shareMenu button[onclick="copyLink()"] svg'
    );
    const originalIconHTML = linkButtonSvg.outerHTML;
    const linkButtonParent = linkButtonSvg.parentElement; // simpan parent biar gampang ganti balik

    navigator.clipboard
        .writeText(shareUrl)
        .then(() => {
            // Ganti icon jadi centang
            linkButtonParent.innerHTML = `<i class="fas fa-check"></i>`;

            // Kembalikan icon setelah 3 detik
            setTimeout(() => {
                linkButtonParent.innerHTML = originalIconHTML;
            }, 3000);
        })
        .catch(() => {
            // Fallback untuk browser lama
            const textArea = document.createElement("textarea");
            textArea.value = shareUrl;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand("copy");
            document.body.removeChild(textArea);
        });
}

// Close menu when clicking outside
document.addEventListener("click", function (event) {
    const menu = document.getElementById("shareMenu");
    const button = event.target.closest('button[onclick="toggleShareMenu()"]');

    if (
        !menu.contains(event.target) &&
        !button &&
        !menu.classList.contains("hidden")
    ) {
        menu.classList.add("opacity-0", "scale-90");
        menu.classList.remove("opacity-100", "scale-100");
        setTimeout(() => {
            menu.classList.add("hidden");
        }, 300);
    }
});
