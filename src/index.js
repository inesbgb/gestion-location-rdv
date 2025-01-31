import './styles/app.css';
import './styles/footer.css';
import './styles/navbar.css';
import './navbar.js'; // Importer le fichier navbar
import './utils.js';
import './video.js';


// video page home 
import { setupVideoLazyLoad } from './videoLazyLoad.js';
import { adjustContentBrightness } from './videoBrightness.js';

document.addEventListener('DOMContentLoaded', function() {
    const video = document.getElementById('background-video');
    const content = document.getElementById('hero-content');

    setupVideoLazyLoad(video);
    adjustContentBrightness(video, content);
});