
import "./bootstrap";
window.Vue = require('vue');
window.Vuex = require('vuex');

Vue.use(Vuex);

window.Event = new Vue();

const store = new Vuex.Store({
    state: {
        user: {
            id: 0,
            name: '',
            friends: []
        },
        newGame: {
            opponentId: 0,
            maxRecur: 0,
            maxSize: 0,
            origination: 'choose',
            myWord: ''
        },
        requested: [],
        message: '',
        errors: []
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
        }
    }
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

Vue.component('start-game', {
    data() {
        return {
            friends: [],
            opponentid: 0,
            maxrecur: 3,
            maxlength: 10,
            origination: 'choose',
            myword: '',
            errors: []
        };
    },
    mounted() {
        axios.get('/user/friends')
            .then((response) => {
                this.friends = response.data;
            })
            .catch(error => {
                console.log(error);
            });
    },
    methods: {
        onSubmit() {
            axios.post('/game/create', {
                opponentid: this.opponentid,
                maxrecur: this.maxrecur,
                maxlength: this.maxlength,
                origination: this.origination,
                myword: this.myword
            })
                .then(response => {
                    Event.$emit('newgame', response.data);
                    // TODO: add a listener for this somewhere
                })
                .catch(error => {
                    this.errors = error.response.data;
                    console.log(error.response);
                });
        }
    }
});

const app = new Vue({
    el: '#app',
    store,
});
