import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

// dotUVE is a global variable that is set by the dotCMS UVE JavaScript API
if (window.dotUVE) {
    window.dotUVE.createUVESubscription('changes', (changes) => {
        window.location.reload();
    })
} else {
    console.warn('dotUVE is not available, you might experience issues with the the Universal Visual Editor');
}