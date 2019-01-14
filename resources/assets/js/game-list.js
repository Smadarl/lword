
require("./bootstrap");

window.Vue = require('vue');
window.Vuex = require('vuex');

Vue.use(Vuex);

window.Event = new Vue();
import { updateField, getField, mapFields } from 'vuex-map-fields';

const store = new Vuex.Store({
    state: {
        user: {
            id: 0,
            name: '',
            friends: []
        },
        newGame: {
            opponentId: 0,
            maxRecur: 2,
            maxLength: 12,
            origination: 'choose',
            myWord: ''
        },
        requested: [],
        message: '',
        errors: []
    },
    getters: {
        getField,
    },
    mutations: {
        setUser(state, user) {
            state.user = user;
        },
        message(state, message) {
            state.message = message;
        },
        errors(state, errors) {
            state.errors = errors;
        },
        set(state, key, val) {
            state.key = val;
        },
        updateField,
    }
});

const app = new Vue({
    el: '#app',
    store
});
