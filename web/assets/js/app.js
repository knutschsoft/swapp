import '../css/global.scss';
import './plugins/dayjs.ts';

import VueSilentbox from 'vue-silentbox'
import 'vue-silentbox/dist/style.css'
import vuetify from './plugins/vuetify';
import {createApp} from 'vue'
import router from './router';
import VueClipboard from 'vue-clipboard2';
import axios from 'axios';
import VueAxios from 'vue-axios';
import {createPinia} from 'pinia';
import mdiVue from 'mdi-vue/v3';
import * as mdijs from '@mdi/js';
import Swapp from './Swapp.vue';
import {registerErrorHandler} from "./utils";
import VueDatePicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'

const pinia = createPinia();

const app = createApp(Swapp)

registerErrorHandler(app);

app.component('VueDatePicker', VueDatePicker)
app.use(pinia)
app.use(mdiVue, {  icons: mdijs})
app.use(vuetify)
app.use(VueAxios, axios)
app.use(router)
app.use(VueSilentbox)
app.use(VueClipboard)
app.mount('#swapp')
