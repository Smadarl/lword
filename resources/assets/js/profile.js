
require('./bootstrap');

window.Vue = require('vue');

//window.Event = new Vue();

import playerprofile from './components/PlayerProfile.vue';

const app = new Vue({
    el: '#app',
    components: { playerprofile }
});
