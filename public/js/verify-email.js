document.addEventListener('DOMContentLoaded', async function () {
        try {
            const response = await fetch('/expiration');
            const data = await response.json();

            // Ambil waktu expire dalam detik
            let seconds = parseInt(data.time) * 60;
            const countdownEl = document.getElementById('countdown');

            function updateCountdown() {
                const minutes = Math.floor(seconds / 60);
                const remainingSeconds = seconds % 60;
                countdownEl.textContent = `${String(minutes).padStart(2, '0')}:${String(remainingSeconds).padStart(2, '0')}`;

                if (seconds > 0) {
                    seconds--;
                    setTimeout(updateCountdown, 1000);
                } else {
                    countdownEl.textContent = "00:00";
                }
            }

            updateCountdown();
        } catch (error) {
            console.error("Gagal mengambil waktu expire:", error);
            document.getElementById('countdown').textContent = "00:00";
        }
    });
