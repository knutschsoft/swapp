import '@vuepic/vue-datepicker/dist/main.css';
import '../css/global.scss';
import './plugins/dayjs.ts';

import { createApp } from 'vue';
import vuetify from './plugins/vuetify';
import router from './router';
import { createPinia } from 'pinia';
import axios from 'axios';
import VueAxios from 'vue-axios';
import VueSilentbox from 'vue-silentbox';
import 'vue-silentbox/dist/style.css';
import VueClipboard from 'vue-clipboard2';
import mdiVue from 'mdi-vue/v3';
// Tree-shakable named imports statt `import * as mdijs from '@mdi/js'` -
// der Star-Import zog ALLE ~7000 MDI-Icons in den Bundle, hier kommen nur
// die rein, die in Templates per <mdicon name="..."/> verwendet werden.
// Wenn ein neues Icon ergänzt wird: hier einkommentieren UND in mdiIcons
// unten registrieren.
import {
    mdiAccount,
    mdiAccountCircleOutline,
    mdiAccountOff,
    mdiAccountSupervisor,
    mdiCheckCircleOutline,
    mdiContentCopy,
    mdiEmoticonHappyOutline,
    mdiLockOutline,
    mdiPlusCircleOutline,
    mdiTagOff,
    mdiWifiOff,
} from '@mdi/js';
import { VueDatePicker } from '@vuepic/vue-datepicker';

import Swapp from './Swapp.vue';
import { registerErrorHandler } from './utils';

// Create Pinia store
const pinia = createPinia();

// Create Vue application
const app = createApp(Swapp);

// Global error handler
registerErrorHandler(app);

// Register global components & plugins
app.component('VueDatePicker', VueDatePicker);
app.use(pinia);
app.use(mdiVue, {
    icons: {
        mdiAccount,
        mdiAccountCircleOutline,
        mdiAccountOff,
        mdiAccountSupervisor,
        mdiCheckCircleOutline,
        mdiContentCopy,
        mdiEmoticonHappyOutline,
        mdiLockOutline,
        mdiPlusCircleOutline,
        mdiTagOff,
        mdiWifiOff,
    },
});
app.use(vuetify);
app.use(VueAxios, axios);
app.use(router);
app.use(VueSilentbox);
app.use(VueClipboard);

// Mount application
app.mount('#swapp');
