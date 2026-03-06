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

// Option : Transition automatique après 3 secondes (décommenter si souhaité)
// if (welcomeScreen) {
//     setTimeout(goToLoginScreen, 3000);
// }