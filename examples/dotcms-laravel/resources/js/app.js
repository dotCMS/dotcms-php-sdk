import {createUVESubscription} from '@dotcms/uve';

import './bootstrap';

try {
    createUVESubscription('changes', (changes) => {
        window.location.reload();
    });
} catch (error) {
    console.warn('dotUVE is not available, you might experience issues with the the Universal Visual Editor', error);
}