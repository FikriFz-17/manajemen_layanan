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

function openInstanceModal() {
    const modal = document.getElementById('InstanceModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeInstanceModal() {
    const modal = document.getElementById('InstanceModal');
    modal.classList.remove('flex');
    modal.classList.add('hidden');
}

function saveInstance(){
    const kecamatanSelect = document.getElementById('kecamatan');
    const desaSelect = document.getElementById('desa');
    const pemdaSelect = document.getElementById('pemda');
    const instanceToggle = document.getElementById('InstanceToggle');

    // Cek apakah desa atau pemda yang sedang ditampilkan
    const isDesa = !document.getElementById('desa-options').classList.contains('hidden');
    const isPemda = !document.getElementById('pemda-options').classList.contains('hidden');

    if (isDesa) {
        const kecamatanText = kecamatanSelect.options[kecamatanSelect.selectedIndex].text;
        const desaText = desaSelect.options[desaSelect.selectedIndex].text;

        if (!kecamatanSelect.value || !desaSelect.value) {
            closeInstanceModal()
            return
        }

        instanceToggle.value = `Kecamatan ${kecamatanText}, Desa ${desaText}`;
    }

    if (isPemda) {
        const pemdaText = pemdaSelect.options[pemdaSelect.selectedIndex].text;

        if (!pemdaSelect.value) {
            closeInstanceModal()
            return
        }

        instanceToggle.value = pemdaText;
    }

    closeInstanceModal();
}

function resetInstanceSelection(){
    // Reset TomSelect UI
    kecamatanInstance.clear();
    desaSelectInstance.clear();
    pemdaInstance.clear();

    // Set kembali value input ke default
    const input = document.getElementById('InstanceToggle');
    const defaultValue = input.getAttribute('data-default');
    input.value = defaultValue;
}

function setDesa() {
    document.getElementById('desa-options')?.classList.remove('hidden');
    document.getElementById('pemda-options')?.classList.add('hidden');
    document.getElementById('jenisInstansi').value = 'desa';
}

function setPemda() {
    document.getElementById('desa-options')?.classList.add('hidden');
    document.getElementById('pemda-options')?.classList.remove('hidden');
    document.getElementById('jenisInstansi').value = 'pemda';
}


// === Load Dropdown ===
let kecamatanData = []
document.addEventListener('DOMContentLoaded', () => {
    fetch('/api/kecamatan')
        .then(response => response.json())
        .then(data => {
            kecamatanData = data.data.map(item => ({
                id: item.kecamatan_id,
                nama_kecamatan: item.kecamatan_nama
            }));
            loadKecamatan()
        })
        .catch(err => console.error('Gagal mengambil data kecamatan:', err));
})

function loadKecamatan() {
    const kecamatanSelect = document.getElementById('kecamatan');
    kecamatanSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';

    kecamatanData.forEach(k => {
        const option = document.createElement('option');
        option.value = k.id;
        option.textContent = k.nama_kecamatan;
        kecamatanSelect.appendChild(option);
    });

    // Inisialisasi TomSelect setelah opsi dimasukkan
    kecamatanInstance = new TomSelect("#kecamatan", {
        maxOptions: kecamatanData.length
    });
}

let desaSelectInstance;
document.addEventListener('DOMContentLoaded', () => {
    desaSelectInstance = new TomSelect("#desa", {
        placeholder: "-- Pilih Desa --",
        persist: false,
        create: false,
    });

});

function loadDesa() {
    const kecamatanId = document.getElementById('kecamatan').value;
    const desaSelect = document.getElementById('desa');

    // Bersihkan elemen <select> asli
    desaSelect.innerHTML = '';

    // Clear TomSelect internal state
    desaSelectInstance.clear(true); // hapus value
    desaSelectInstance.clearOptions(); // hapus opsi
    desaSelectInstance.setValue('');

    if (!kecamatanId) return;

    fetch(`/api/desa?kecamatan_id=${kecamatanId}`)
        .then(res => res.json())
        .then(data => {
            const dataDesa = data.data.map(item => ({
                value: item.kode_desa,
                text: item.nama_desa
            }));

            desaSelectInstance.addOptions(dataDesa);
        })
        .catch(err => console.error('Gagal mengambil data desa:', err));
}


let pemdaData = [];
document.addEventListener('DOMContentLoaded', () => {
    fetch('/api/pemda')
        .then(res => res.json())
        .then(data => {
            pemdaData = data.data.map(item => ({
                id: item.pemda_id,
                nama: item.pemda_nama
            }));
            loadPemda();
        })
        .catch(err => console.error('Gagal mengambil data pemda:', err));
});

function loadPemda() {
    const pemdaSelect = document.getElementById('pemda');
    pemdaSelect.innerHTML = '<option value="">-- Pilih Instansi --</option>';

    pemdaData.forEach(p => {
        const option = document.createElement('option');
        option.value = p.id;
        option.textContent = p.nama;
        pemdaSelect.appendChild(option);
    });

    // Inisialisasi TomSelect setelah opsi dimasukkan
    pemdaInstance = new TomSelect("#pemda", {
        maxOptions: pemdaData.length
    })
}

let cropper;
const input = document.getElementById('photo-upload');
const modal = document.getElementById('cropModal');
const cropperImg = document.getElementById('cropper-image');

input.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        cropperImg.src = e.target.result;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        cropper = new Cropper(cropperImg, {
            aspectRatio: 1,
            viewMode: 1,
            autoCropArea: 1,
        });
    };
    reader.readAsDataURL(file);
});

function closeCropModal() {
    cropper.destroy();
    modal.classList.add('hidden');
    input.value = '';
}

function uploadCroppedImage() {
    cropper.getCroppedCanvas().toBlob((blob) => {
        const file = new File([blob], "cropped_profile.jpg", { type: "image/jpeg" });

        // Masukkan file ke input type="file"
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);

        const inputFile = document.getElementById('photo-upload');
        inputFile.files = dataTransfer.files;

        // Optional: tutup modal dan destroy cropper
        modal.classList.add('hidden');
        cropper.destroy();

        // Submit form
        inputFile.form.submit();
    }, 'image/jpeg');
}
