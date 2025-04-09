import './bootstrap';

// dotUVE is a global variable that is set by the dotCMS UVE JavaScript API
if (window.dotUVE) {
    window.dotUVE.createUVESubscription('changes', (changes) => {
        window.location.reload();
    })
} else {
    console.warn('dotUVE is not available, you might experience issues with the the Universal Visual Editor');
}