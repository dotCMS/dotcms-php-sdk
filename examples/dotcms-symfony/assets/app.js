import {createUVESubscription, initUVE} from '@dotcms/uve';
import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

// Initialize UVE using the official package
try {
    initUVE();
    createUVESubscription('changes', (changes) => {
        console.log('🔄 UVE changes detected:', changes);
        window.location.reload();
    });
    console.log('✅ UVE initialized successfully');
} catch (error) {
    console.warn('dotUVE is not available, you might experience issues with the Universal Visual Editor', error);
}