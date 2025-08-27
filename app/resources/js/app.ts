import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from './composables/useAppearance';
const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
import ToastService from 'primevue/toastservice';
// PrimeVue imports
import PrimeVue from 'primevue/config';
import Ripple from 'primevue/ripple';

import 'primeicons/primeicons.css'
import ConfirmationService from 'primevue/confirmationservice';
import Lara from '@primeuix/themes/lara';
import ConfirmDialog from 'primevue/confirmdialog';
import ConfirmPopup from 'primevue/confirmpopup';
import Toast from 'primevue/toast';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(ConfirmationService) // register PrimeVue ConfirmDialog
            .use(ToastService) // register PrimeVue ToastService
            .use(PrimeVue, {
                ripple: true,
                 theme: {
                    preset: Lara,
                    options: {
                        prefix: 'p',
                        darkModeSelector: 'system',
                        cssLayer: false
                    }
    }
            }) // register PrimeVue
            .component('ConfirmDialog', ConfirmDialog) // register PrimeVue ConfirmDialog component
            .component('ConfirmPopup', ConfirmPopup) // register PrimeVue ConfirmPopup component
            .component('Toast', Toast) // register PrimeVue Toast component
            .directive('ripple', Ripple)     // optional ripple directive
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
