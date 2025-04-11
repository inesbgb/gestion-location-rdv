// Fonction pour le chargement paresseux de la vidéo
export function setupVideoLazyLoad(video) {
    if (!video) return;

    // Attendre que la page soit chargée
    if (video.dataset.src) {
        video.src = video.dataset.src;
        video.load();
    }

    // Jouer la vidéo quand elle est prête
    video.addEventListener('canplaythrough', function() {
        video.play();
    });

    // Gérer les erreurs
    video.addEventListener('error', function(e) {
        console.error('Erreur de chargement de la vidéo:', e);
    });
} 