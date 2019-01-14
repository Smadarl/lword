
require("./bootstrap");

window.Vue = require('vue');
window.Vuex = require('vuex');

Vue.use(Vuex);

window.Event = new Vue();

// Vue.component('chooseWord', require('./components/ChooseWord.vue'));
import GenericForm from './components/GenericForm.vue';
import SelectInput from './components/SelectInput.vue';
import ChooseWord from './components/ChooseWord.vue';
import TextInput from './components/TextInput.vue';

Vue.component('start-game', {
    data() {
        return {
            friends: [],
            errors: []
        };
    },
    mounted() {
        axios.get('/api/user/friends')
            .then((response) => {
                response.data.forEach((f) => { this.friends.push({label: f.friend_name, value: f.friend_id}); });
            })
            .catch(error => {
                console.log(error);
            });
    },
    components: { GenericForm, SelectInput, ChooseWord, TextInput }
});

const app = new Vue({
    el: '#app'
});
