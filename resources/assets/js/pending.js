require("./bootstrap");

window.Vue = require('vue');

window.Event = new Vue();

import ChooseWord from './components/ChooseWord';
import GenericForm from './components/GenericForm.vue';

const app = new Vue({
    el: '#app',
    components: { ChooseWord, GenericForm },
    mounted() {
        Event.$on('submitSuccess', (data) => {
            this.submitSuccess(data)
        });
    },
    methods: {
        submitSuccess(data) {
            // Event.$emit('newgame', response.data);
            let url = window.location.href.replace('\/pending', '');
            window.location.href = url;
        },
        // TODO: canceling doesn't redirect after success.  Not sure why this isn't getting called.
        gameCanceled(data) {
            console.log(data.message);
            window.location.href = '/games';
        }
    }
});