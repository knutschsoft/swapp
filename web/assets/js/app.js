import '../css/global.scss';
import '../css/vuetify-compatibility.scss';
import './plugins/vue-silentbox';
import './plugins/dayjs.ts';

import vuetify from './plugins/vuetify';
import {library} from '@fortawesome/fontawesome-svg-core';
import {faEye, faMapSigns, faShoePrints, faWalking} from '@fortawesome/free-solid-svg-icons';
import {FontAwesomeIcon, FontAwesomeLayers} from '@fortawesome/vue-fontawesome';
import Vue from 'vue';
import router from './router';
import {BootstrapVue, IconsPlugin, NavbarPlugin} from 'bootstrap-vue';
import {useStorage} from '@vueuse/core';
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

library.add(faWalking, faShoePrints, faEye, faMapSigns)

Vue.component('nl2br', Nl2br);
Vue.component('font-awesome-icon', FontAwesomeIcon)
Vue.component('font-awesome-layers', FontAwesomeLayers)
Vue.use(VueAxios, axios);
Vue.use(PiniaVuePlugin);
Vue.use(VueRouter);
Vue.use(BootstrapVue);
Vue.use(IconsPlugin);
Vue.use(NavbarPlugin);
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
        this.$root.$on('bv::collapse::state', (collapseId, isJustShown) => {
            const state = useStorage(`swapp-store-${collapseId}`, isJustShown);
            state.value = isJustShown;
        });
    },
    async created() {
    },
    render: h => h(Swapp),
    router: router,
    pinia,
    vuetify,
}).$mount('#swapp');
