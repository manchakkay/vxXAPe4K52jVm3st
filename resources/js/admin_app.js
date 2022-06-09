import { createApp } from 'vue';
import componentREDACT from './redact/Redact.vue';
import componentPopup from './components/admin/popup';
import componentNestable from './components/admin/nestable';
import componentGallery from './components/admin/gallery';

window.axios = require('axios');

const app = createApp({
    components: {
        "redact": componentREDACT,
        "popup": componentPopup,
        "nestable": componentNestable,
        "gallery": componentGallery,
    },
});

app.mount('#app');
