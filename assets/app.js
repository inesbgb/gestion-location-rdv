// Import des styles
import './styles/app.css';


// Import des modules JavaScript
import { setupVideoLazyLoad } from './video.js';
import { adjustContentBrightness } from './videoBrightness.js';
// import * as utils from './utils.js';
import './navbar.js';

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    const video = document.getElementById('background-video');
    const content = document.getElementById('hero-content');

    if (video && content) {
        setupVideoLazyLoad(video);
        adjustContentBrightness(video, content);
    }
});