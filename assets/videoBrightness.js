// Fonction pour ajuster la luminosité du contenu
export function adjustContentBrightness(video, content) {
    if (!video || !content) return;

    // Ajouter une overlay sombre par défaut
    const overlay = document.createElement('div');
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.3);
        z-index: 0;
    `;
    video.parentElement.insertBefore(overlay, content);
} 