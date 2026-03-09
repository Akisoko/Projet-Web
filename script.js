// Menu burger mobile
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

// Gestion des filtres (page recherche)
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
        // Réinitialiser tous les champs de filtre
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
        // Logique d'application des filtres (à gérer avec le backend)
        filterPanel.classList.remove('active');
        console.log('Filtres appliqués');
    });
}

// Landing page - Transition entre les écrans
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

// Wishlist - Sélection d'items
const wishlistItems = document.querySelectorAll('.wishlist-item');
const detailPlaceholder = document.querySelector('.detail-placeholder');
const detailContent = document.querySelector('.detail-content');

wishlistItems.forEach(item => {
    item.addEventListener('click', function() {
        // Retirer la classe active de tous les items
        wishlistItems.forEach(i => i.classList.remove('active'));
        // Ajouter la classe active à l'item cliqué
        this.classList.add('active');

        // Masquer le placeholder et afficher le contenu
        if (detailPlaceholder) {
            detailPlaceholder.style.display = 'none';
        }
        if (detailContent) {
            detailContent.classList.remove('hidden');
        }

        // Ici tu pourras charger les détails de l'offre avec le backend
        const itemId = this.dataset.id;
        console.log('Offre sélectionnée:', itemId);

        // Exemple de mise à jour des détails (à remplacer par les vraies données du backend)
        // document.getElementById('detailEntreprise').textContent = 'Nom entreprise';
        // document.getElementById('detailOffre').textContent = 'Nom de l\'offre';
    });
});