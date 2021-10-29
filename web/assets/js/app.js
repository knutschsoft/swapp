/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */


// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';

// import missing ie11 polyfills from core-js
import 'core-js';
import 'regenerator-runtime/runtime';
import 'css/global.scss';

import { library } from '@fortawesome/fontawesome-svg-core';
import { faEye, faMapSigns, faShoePrints, faWalking } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon, FontAwesomeLayers } from '@fortawesome/vue-fontawesome';
import Vue from 'vue';
import router from './router';
import store from './store';
import { AlertPlugin, BootstrapVue, CollapsePlugin, IconsPlugin, NavbarPlugin } from 'bootstrap-vue';
import Storage from 'vue-web-storage';
import axios from 'axios';
import VueAxios from 'vue-axios';
import VuePictureSwipe from 'vue-picture-swipe';
import mdiVue from 'mdi-vue';
import * as mdijs from '@mdi/js';
import VuePageTransition from 'vue-page-transition';
import Nl2br from 'vue-nl2br';
import Swapp from './Swapp';

import dayjs from 'dayjs';
require('dayjs/locale/de');
dayjs.locale('de');

library.add(faWalking, faShoePrints, faEye, faMapSigns)

Vue.component('nl2br', Nl2br);
Vue.component('font-awesome-icon', FontAwesomeIcon)
Vue.component('font-awesome-layers', FontAwesomeLayers)
Vue.use(Storage, {
    prefix: 'swapp-store-',
    drivers: ['local', 'session'],
});
Vue.use(VueAxios, axios);
Vue.use(BootstrapVue);
Vue.use(IconsPlugin);
Vue.use(CollapsePlugin);
Vue.use(AlertPlugin);
Vue.use(NavbarPlugin);
Vue.component('vue-picture-swipe', VuePictureSwipe);
Vue.use(VuePageTransition);
Vue.use(mdiVue, {
    icons: mdijs
});

Vue.config.errorHandler = function (err, vm, info) {
    let user = vm.$store.getters['security/currentUser'];
    let username = user ? user.email : 'anonymous';
    let message = err.message ? err.message : JSON.stringify(err);
    nelmioLog('error', message, {info: info, location: window.location, user: username});
    console.error(message);
};

const vueApp = (params) => {
    return new Vue({
        components:
            {
                Swapp
            },
        data: {
            parameter: () => {
                return params;
            }
        },
        async mounted() {
            this.$root.$on('bv::collapse::state', (collapseId, isJustShown) => {
                this.$localStorage.set(collapseId, isJustShown);
            });
        },
        async created() {
        },
        render: h => h(Swapp),
        router: router,
        store
    })
};

class App {
    constructor(params) {
        this.wrapper = '#swapp';
        vueApp(params).$mount('#swapp');
    }
}

window.App = App;
