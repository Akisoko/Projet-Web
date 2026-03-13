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

// Documents - Gestion des actions
const fileUpload = document.getElementById('fileUpload');
const fileName = document.getElementById('fileName');
const downloadBtn = document.getElementById('downloadBtn');
const previewBtn = document.getElementById('previewBtn');
const previewZone = document.getElementById('previewZone');
const closePreview = document.getElementById('closePreview');

// Upload de fichier
if (fileUpload) {
    fileUpload.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            if (fileName) {
                fileName.textContent = `Fichier sélectionné : ${file.name}`;
                fileName.classList.add('show');
            }
            console.log('Fichier uploadé:', file.name);
            // Ici tu enverras le fichier au backend
        }
    });
}

// Télécharger le document
if (downloadBtn) {
    downloadBtn.addEventListener('click', function() {
        console.log('Téléchargement du document...');
        // Ici tu déclencheras le téléchargement depuis le backend
        alert('Téléchargement de la lettre de motivation...');
    });
}

// Afficher l'aperçu
if (previewBtn) {
    previewBtn.addEventListener('click', function() {
        if (previewZone) {
            previewZone.classList.remove('hidden');
            console.log('Affichage de l\'aperçu');
            // Ici tu chargeras le document depuis le backend pour l'afficher
        }
    });
}

// Fermer l'aperçu
if (closePreview) {
    closePreview.addEventListener('click', function() {
        if (previewZone) {
            previewZone.classList.add('hidden');
        }
    });
}

// Upload logo entreprise
const logoUpload = document.getElementById('logoUpload');
const logoFileName = document.getElementById('logoFileName');

if (logoUpload) {
    logoUpload.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            if (logoFileName) {
                logoFileName.value = file.name;
            }
            console.log('Logo uploadé:', file.name);
        }
    });
}

// Upload CV
const cvUpload = document.getElementById('cvUpload');
const cvFileName = document.getElementById('cvFileName');

if (cvUpload) {
    cvUpload.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            if (cvFileName) {
                cvFileName.value = file.name;
            }
            console.log('CV uploadé:', file.name);
        }
    });
}

// Upload Lettre de motivation
const lmUpload = document.getElementById('lmUpload');
const lmFileName = document.getElementById('lmFileName');

if (lmUpload) {
    lmUpload.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            if (lmFileName) {
                lmFileName.value = file.name;
            }
            console.log('Lettre de motivation uploadée:', file.name);
        }
    });
}

// Rating stars
const starRatings = document.querySelectorAll('.star-rating');

starRatings.forEach(rating => {
    const stars = rating.querySelectorAll('i');

    stars.forEach(star => {
        star.addEventListener('click', function() {
            const ratingValue = parseInt(this.dataset.rating);

            // Reset all stars in this rating group
            stars.forEach(s => {
                s.classList.remove('fa-solid', 'active');
                s.classList.add('fa-regular');
            });

            // Activate stars up to clicked one
            for (let i = 0; i < ratingValue; i++) {
                stars[i].classList.remove('fa-regular');
                stars[i].classList.add('fa-solid', 'active');
            }

            console.log(`Note attribuée: ${ratingValue}/5 pour ${rating.id}`);
        });

        // Hover effect
        star.addEventListener('mouseenter', function() {
            const ratingValue = parseInt(this.dataset.rating);
            stars.forEach((s, index) => {
                if (index < ratingValue) {
                    s.classList.add('fa-solid');
                    s.classList.remove('fa-regular');
                } else {
                    s.classList.remove('fa-solid');
                    s.classList.add('fa-regular');
                }
            });
        });
    });

    // Reset on mouse leave
    rating.addEventListener('mouseleave', function() {
        stars.forEach(s => {
            if (!s.classList.contains('active')) {
                s.classList.remove('fa-solid');
                s.classList.add('fa-regular');
            }
        });
    });
});