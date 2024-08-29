import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

import Chart from 'chart.js/auto';

// Rendre Chart disponible globalement si nécessaire
window.Chart = Chart;

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
