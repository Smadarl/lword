
require("./bootstrap");

window.Vue = require('vue');
window.Vuex = require('vuex');

Vue.use(Vuex);

window.Event = new Vue();


import GenericForm from './components/GenericForm.vue';
import TextInput from './components/TextInput.vue';
import ChooseWord from './components/ChooseWord.vue';

const app = new Vue({
    el: '#app',
    components: { GenericForm, TextInput, ChooseWord }
});
