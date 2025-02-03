import '../css/global.scss';
import './plugins/vue-silentbox';
import './plugins/dayjs.ts';

import vuetify from './plugins/vuetify';
import Vue from 'vue';
import router from './router';
import VueClipboard from 'vue-clipboard2';
import axios from 'axios';
import VueAxios from 'vue-axios';
import {createPinia, PiniaVuePlugin} from 'pinia';
import mdiVue from 'mdi-vue';
import * as mdijs from '@mdi/js';
import VuePageTransition from 'vue-page-transition';
import Nl2br from 'vue-nl2br';
import Swapp from './Swapp.vue';
import VueRouter from 'vue-router';
import { useAuthStore } from './stores/auth';

Vue.component('nl2br', Nl2br);
Vue.use(VueAxios, axios);
Vue.use(PiniaVuePlugin);
Vue.use(VueRouter);
Vue.use(VuePageTransition);
Vue.use(mdiVue, {
    icons: mdijs
});
Vue.use(VueClipboard);

Vue.config.errorHandler = function (err, vm, info) {
    const authStore = useAuthStore();
    let user = authStore.currentUser;
    let username = user ? user.email : 'anonymous';
    let message = err.message ? err.message : JSON.stringify(err);
    nelmioLog('error', message, {info: info, location: window.location, user: username});
    throw new Error(err);
};
const pinia = createPinia();

new Vue({
    components:
        {
            Swapp
        },
    async mounted() {
    },
    async created() {
    },
    render: h => h(Swapp),
    router: router,
    pinia,
    vuetify,
}).$mount('#swapp');
