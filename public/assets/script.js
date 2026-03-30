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
            // Ici tu enverras le fichier au backend
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
            // Ici tu chargeras le document depuis le backend pour l'afficher
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

const starRatings = document.querySelectorAll('.star-rating');

starRatings.forEach(rating => {
    const stars = rating.querySelectorAll('i');

    stars.forEach(star => {
        star.addEventListener('click', function() {
            const ratingValue = parseInt(this.dataset.rating);

            stars.forEach(s => {
                s.classList.remove('fa-solid', 'active');
                s.classList.add('fa-regular');
            });

            for (let i = 0; i < ratingValue; i++) {
                stars[i].classList.remove('fa-regular');
                stars[i].classList.add('fa-solid', 'active');
            }

            console.log(`Note attribuée: ${ratingValue}/5 pour ${rating.id}`);
        });

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

    rating.addEventListener('mouseleave', function() {
        stars.forEach(s => {
            if (!s.classList.contains('active')) {
                s.classList.remove('fa-solid');
                s.classList.add('fa-regular');
            }
        });
    });
});

// ============================================================
// VALIDATION DES FORMULAIRES
// ============================================================

function showError(input, message) {
    input.style.borderColor = '#e74c3c';
    let error = input.parentElement.querySelector('.field-error');
    if (!error) {
        error = document.createElement('p');
        error.classList.add('field-error');
        error.style.color = '#e74c3c';
        error.style.fontSize = '0.8rem';
        error.style.marginTop = '4px';
        input.parentElement.appendChild(error);
    }
    error.textContent = message;
}

function clearError(input) {
    input.style.borderColor = '';
    const error = input.parentElement.querySelector('.field-error');
    if (error) error.remove();
}

function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function validateTel(tel) {
    return /^[0-9\s\+\-\.]{8,15}$/.test(tel);
}

// --- Formulaire connexion ---
const formConnexion = document.querySelector('form[action="/connexion"]');
if (formConnexion) {
    formConnexion.addEventListener('submit', function(e) {
        let valid = true;

        const email = this.querySelector('input[name="email"]');
        const mdp = this.querySelector('input[name="mot_de_passe"]');

        clearError(email); clearError(mdp);

        if (!email.value.trim() || !validateEmail(email.value)) {
            showError(email, 'Email invalide.'); valid = false;
        }
        if (!mdp.value.trim()) {
            showError(mdp, 'Mot de passe requis.'); valid = false;
        }

        if (!valid) e.preventDefault();
    });
}

// --- Formulaire inscription ---
const formInscription = document.querySelector('form[action="/inscription"]');
if (formInscription) {
    formInscription.addEventListener('submit', function(e) {
        let valid = true;

        const prenom = this.querySelector('input[name="prenom"]');
        const nom = this.querySelector('input[name="nom"]');
        const email = this.querySelector('input[name="email"]');
        const tel = this.querySelector('input[name="telephone"]');
        const mdp = this.querySelector('input[name="mot_de_passe"]');
        const mdpConfirm = this.querySelector('input[name="mot_de_passe_confirmation"]');
        const jour = this.querySelector('select[name="jour"]');
        const mois = this.querySelector('select[name="mois"]');
        const annee = this.querySelector('select[name="annee"]');

        [prenom, nom, email, tel, mdp, mdpConfirm, jour, mois, annee].forEach(clearError);

        if (!prenom.value.trim()) { showError(prenom, 'Prénom requis.'); valid = false; }
        if (!nom.value.trim()) { showError(nom, 'Nom requis.'); valid = false; }
        if (!email.value.trim() || !validateEmail(email.value)) { showError(email, 'Email invalide.'); valid = false; }
        if (!tel.value.trim() || !validateTel(tel.value)) { showError(tel, 'Téléphone invalide.'); valid = false; }
        if (!mdp.value.trim() || mdp.value.length < 6) { showError(mdp, 'Mot de passe trop court (6 caractères min).'); valid = false; }
        if (mdp.value !== mdpConfirm.value) { showError(mdpConfirm, 'Les mots de passe ne correspondent pas.'); valid = false; }
        if (!jour.value) { showError(jour, 'Jour requis.'); valid = false; }
        if (!mois.value) { showError(mois, 'Mois requis.'); valid = false; }
        if (!annee.value) { showError(annee, 'Année requise.'); valid = false; }

        if (!valid) e.preventDefault();
    });
}

// --- Formulaire ajouter/modifier entreprise ---
const formEntreprise = document.querySelector('form[action="/ajouter_entreprise"], form[action*="/modifier_entreprise"]');
if (formEntreprise) {
    formEntreprise.addEventListener('submit', function(e) {
        let valid = true;

        const champs = [
            { name: 'nom', label: 'Nom requis.' },
            { name: 'site_web', label: 'Site web requis.' },
            { name: 'date_creation', label: 'Date de création requise.' },
            { name: 'domaine', label: 'Domaine requis.' },
            { name: 'nb_employes', label: 'Nombre d\'employés requis.' },
            { name: 'nb_stagiaires', label: 'Nombre de stagiaires requis.' },
            { name: 'email', label: 'Email requis.' },
            { name: 'telephone', label: 'Téléphone requis.' },
            { name: 'description', label: 'Description requise.' },
        ];

        champs.forEach(({ name, label }) => {
            const input = this.querySelector(`[name="${name}"]`);
            if (input) {
                clearError(input);
                if (!input.value.trim()) { showError(input, label); valid = false; }
            }
        });

        const email = this.querySelector('input[name="email"]');
        if (email && email.value && !validateEmail(email.value)) {
            showError(email, 'Email invalide.'); valid = false;
        }

        if (!valid) e.preventDefault();
    });
}

// --- Formulaire ajouter/modifier offre ---
const formOffre = document.querySelector('form[action="/ajouter_offre"], form[action*="/modifier_offre"]');
if (formOffre) {
    formOffre.addEventListener('submit', function(e) {
        let valid = true;

        const champs = [
            { name: 'nom', label: 'Nom de l\'offre requis.' },
            { name: 'domaine', label: 'Domaine requis.' },
            { name: 'profil', label: 'Profil recherché requis.' },
            { name: 'remuneration', label: 'Rémunération requise.' },
            { name: 'date_offre', label: 'Date requise.' },
            { name: 'nb_etudiants', label: 'Nombre d\'étudiants requis.' },
            { name: 'id_entreprise', label: 'Entreprise requise.' },
            { name: 'description', label: 'Description requise.' },
        ];

        champs.forEach(({ name, label }) => {
            const input = this.querySelector(`[name="${name}"]`);
            if (input) {
                clearError(input);
                if (!input.value.trim()) { showError(input, label); valid = false; }
            }
        });

        if (!valid) e.preventDefault();
    });
}

// --- Formulaire modifier profil ---
const formProfil = document.querySelector('form[action="/modifier_profil"]');
if (formProfil) {
    formProfil.addEventListener('submit', function(e) {
        let valid = true;

        const prenom = this.querySelector('input[name="prenom"]');
        const nom = this.querySelector('input[name="nom"]');
        const email = this.querySelector('input[name="email"]');
        const tel = this.querySelector('input[name="telephone"]');
        const mdp = this.querySelector('input[name="mot_de_passe"]');
        const mdpConfirm = this.querySelector('input[name="mot_de_passe_confirmation"]');

        [prenom, nom, email, tel, mdp, mdpConfirm].forEach(clearError);

        if (!prenom.value.trim()) { showError(prenom, 'Prénom requis.'); valid = false; }
        if (!nom.value.trim()) { showError(nom, 'Nom requis.'); valid = false; }
        if (!email.value.trim() || !validateEmail(email.value)) { showError(email, 'Email invalide.'); valid = false; }
        if (tel && tel.value && !validateTel(tel.value)) { showError(tel, 'Téléphone invalide.'); valid = false; }
        if (mdp && mdp.value && mdp.value.length < 6) { showError(mdp, 'Mot de passe trop court (6 caractères min).'); valid = false; }
        if (mdp && mdpConfirm && mdp.value !== mdpConfirm.value) { showError(mdpConfirm, 'Les mots de passe ne correspondent pas.'); valid = false; }

        if (!valid) e.preventDefault();
    });
}

// --- Formulaire modifier utilisateur (admin) ---
const formModifierUtilisateur = document.querySelector('form[action*="/modifier_utilisateur"]');
if (formModifierUtilisateur) {
    formModifierUtilisateur.addEventListener('submit', function(e) {
        let valid = true;

        const prenom = this.querySelector('input[name="prenom"]');
        const nom = this.querySelector('input[name="nom"]');
        const email = this.querySelector('input[name="email"]');
        const tel = this.querySelector('input[name="telephone"]');

        [prenom, nom, email].forEach(clearError);
        if (tel) clearError(tel);

        if (!prenom.value.trim()) { showError(prenom, 'Prénom requis.'); valid = false; }
        if (!nom.value.trim()) { showError(nom, 'Nom requis.'); valid = false; }
        if (!email.value.trim() || !validateEmail(email.value)) { showError(email, 'Email invalide.'); valid = false; }
        if (tel && tel.value && !validateTel(tel.value)) { showError(tel, 'Téléphone invalide.'); valid = false; }

        if (!valid) e.preventDefault();
    });
}
/*
// --- Formulaire postuler ---
const formPostuler = document.querySelector('form[action="/postuler"]');
if (formPostuler) {
    formPostuler.addEventListener('submit', function(e) {
        let valid = true;

        const cv = this.querySelector('input[name="cv"]');
        if (cv) {
            clearError(cv);
            if (!cv.files || cv.files.length === 0) {
                showError(cv, 'CV requis.'); valid = false;
            }
        }

        if (!valid) e.preventDefault();
    });
}
 */