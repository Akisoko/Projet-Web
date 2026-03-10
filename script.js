const burgerMenu = document.getElementById('burgerMenu');
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('overlay');

function toggleMenu() {
    sidebar.classList.toggle('active');
    overlay.classList.toggle('active');
    document.body.classList.toggle('menu-open');
}

if (burgerMenu) {
    burgerMenu.addEventListener('click', toggleMenu);
}

if (overlay) {
    overlay.addEventListener('click', toggleMenu);
}

const filterBtn = document.getElementById('filterBtn');
const filterPanel = document.getElementById('filterPanel');
const closeFilter = document.getElementById('closeFilter');
const resetFilters = document.getElementById('resetFilters');
const applyFilters = document.getElementById('applyFilters');

if (filterBtn) {
    filterBtn.addEventListener('click', () => {
        filterPanel.classList.toggle('active');
    });
}

if (closeFilter) {
    closeFilter.addEventListener('click', () => {
        filterPanel.classList.remove('active');
    });
}

if (resetFilters) {
    resetFilters.addEventListener('click', () => {
        const inputs = filterPanel.querySelectorAll('input, select');
        inputs.forEach(input => {
            if (input.tagName === 'SELECT') {
                input.selectedIndex = 0;
            } else {
                input.value = '';
            }
        });
    });
}

if (applyFilters) {
    applyFilters.addEventListener('click', () => {
        filterPanel.classList.remove('active');
        console.log('Filtres appliqués');
    });
}

const welcomeScreen = document.getElementById('welcomeScreen');
const loginScreen = document.getElementById('loginScreen');
const startBtn = document.getElementById('startBtn');

function goToLoginScreen() {
    if (welcomeScreen && loginScreen) {
        welcomeScreen.classList.add('fade-out');

        setTimeout(() => {
            welcomeScreen.classList.add('hidden');
            loginScreen.classList.remove('hidden');
            loginScreen.classList.add('fade-in');
        }, 1000);
    }
}

if (startBtn) {
    startBtn.addEventListener('click', goToLoginScreen);
}

const wishlistItems = document.querySelectorAll('.wishlist-item');
const detailPlaceholder = document.querySelector('.detail-placeholder');
const detailContent = document.querySelector('.detail-content');

wishlistItems.forEach(item => {
    item.addEventListener('click', function() {
        wishlistItems.forEach(i => i.classList.remove('active'));
        this.classList.add('active');

        if (detailPlaceholder) {
            detailPlaceholder.style.display = 'none';
        }
        if (detailContent) {
            detailContent.classList.remove('hidden');
        }

        const itemId = this.dataset.id;
        console.log('Offre sélectionnée:', itemId);


    });
});

const fileUpload = document.getElementById('fileUpload');
const fileName = document.getElementById('fileName');
const downloadBtn = document.getElementById('downloadBtn');
const previewBtn = document.getElementById('previewBtn');
const previewZone = document.getElementById('previewZone');
const closePreview = document.getElementById('closePreview');

if (fileUpload) {
    fileUpload.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            if (fileName) {
                fileName.textContent = `Fichier sélectionné : ${file.name}`;
                fileName.classList.add('show');
            }
            console.log('Fichier uploadé:', file.name);
        }
    });
}

if (downloadBtn) {
    downloadBtn.addEventListener('click', function() {
        console.log('Téléchargement du document...');
        // Ici tu déclencheras le téléchargement depuis le backend
        alert('Téléchargement de la lettre de motivation...');
    });
}

if (previewBtn) {
    previewBtn.addEventListener('click', function() {
        if (previewZone) {
            previewZone.classList.remove('hidden');
            console.log('Affichage de l\'aperçu');
        }
    });
}

if (closePreview) {
    closePreview.addEventListener('click', function() {
        if (previewZone) {
            previewZone.classList.add('hidden');
        }
    });
}