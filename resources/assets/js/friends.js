
require('./bootstrap');

window.Vue = require('vue');
window.Vuex = require('vuex');

Vue.use(Vuex);

window.Event = new Vue();

const store = new Vuex.Store({
    state: {
        title: ''
    },
    mutations: {
        setTitle(state, title) {
            state.title = title;
        }
    }
});

Vue.component('rob-test-box', {
    props: ['header'],
    template: `
        <div class="card">
            <div class="card-header">{{ header }}</div>
            <div class="card-body"><slot></slot></div>
        </div>
    `,
});

Vue.component('add-friend', {
    data() {
        return {
            email: ''
        }
    },
    template: `
        <div class="card-body">
            <h3>Add A New Friend</h3>
            <form @submit.prevent="onSubmit">
                <label for="nfEmail">Friend's Email</label>
                <input id="nfEmail" type="text" v-model="email">
                <br/>
                <button class="button is-primary">Add Friend</button>
            </form>
        </div>
    `,
    methods: {
        onSubmit() {
            axios.post('/api/user/friend', { email: this.email })
                .then(response => {
                    this.$store.commit('message', response.data.message);
                })
                .catch(error => {
                    this.$store.commit('errors', error);
                })
        }
    }
});

Vue.component('pending-friend-requests', {
    template: `
        <div class="card-body" v-if="requests">
            <h2>Friend Requests</h2>
            <ul>
                <li v-for="(request, index) in requests" key="request.friend_id">
                    <button @click.prevent="respond(request, index, 'confirmed')">Accept</button>
                    <button @click.prevent="respond(request, index, 'rejected')">Reject</button>
                    <span class="friend-request">{{ request.name }}</span>
                </li>
            </ul>
        </div>
    `,
    data() {
        return {
            requests: []
        }
    },
    methods: {
        respond(request, index, answer) {
            axios.post('/api/user/friend_respond', { friend_id: request.user_id, response: answer })
            this.requests.splice(index, 1);
        },
    },
    mounted() {
        axios.get('/api/user/requests')
            .then(response => {
                this.requests = response.data;
            })
            .catch(error => {
                this.$store.commit('errors', error.errors);
            });
    }
});

import friends from './components/Friends.vue';

const app = new Vue({
    el: '#app',
    components: { friends }
});
