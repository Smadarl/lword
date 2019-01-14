
import "./bootstrap";
window.Vue = require('vue');
window.Vuex = require('vuex');

Vue.use(Vuex);

window.Event = new Vue();

const allLetters = {
    a: 0, b: 0, c: 0, d: 0, e: 0, f: 0, g: 0, h: 0, i: 0, j: 0, k: 0, l: 0, m: 0,
    n: 0, o: 0, p: 0, q: 0, r: 0, s: 0, t: 0, u: 0, v: 0, w: 0, x: 0, y: 0, z: 0
};

const store = new Vuex.Store({
    state: {
        user: {
            id: 0,
            name: '',
            friends: []
        },
        game: {
            id: 0,
            opponentId: 0,
            opponent: '',
            maxSize: 0,
            maxRecur: 0,
            turn: 0,
        },
        moves: [],
        letters: allLetters,
        errors: []
    },
    mutations: {
        setGameId(state, id) {
            state.game.id = id;
        },
        setUser(state, user) {
            state.user = user;
        },
        updateGameData(state, game) {
            state.game = game;
        },
        updateMoves(state, moves) {
            state.moves = moves;
        },
        addMove(state, move) {
            state.moves.push(move);
        },
        setLetterCounts(state, letters) {
            state.letters = Object.assign(state.letters, letters);
        },
        incrementLetter(state, letter) {
            if (state.letters[letter] == state.game.maxRecur) {
                state.letters[letter] = -1;
            } else {
                state.letters[letter]++;
            }
        },
        opponentsTurn(state) {
            state.game.turn = state.game.opponentId;
        },
        myTurn(state) {
            state.game.turn = state.user.id;
        },
        errors(state, errors) {
            state.errors = errors;
        }
    }
});

Vue.component('game', {
    props: ['gameId'],
    created() {
        this.$store.commit('setGameId', this.gameId);
        axios.get('/api/game/' + this.gameId)
            .then(response => {
                this.$store.commit('updateGameData', response.data.game);
                this.$store.commit('setLetterCounts', JSON.parse(response.data.letters));
                this.$store.commit('updateMoves', response.data.moves);
                this.$store.commit('setUser', response.data.user);
            })
            .catch(error => {
                this.$store.commit('errors', error.errors);
            })
    }
});

Vue.component('letter-list', {
    methods: {
        save() {
            axios.post('/api/game/' + this.$store.state.game.id + '/letters', { game_id: this.$store.state.game.id, letters: this.$store.state.letters })
        }
    },
    created() {
        Event.$on('letterIncrement', (letter) => {
            this.$store.commit('incrementLetter', letter.letter);
        });
    }
});

Vue.component('letter', {
    props: ['theLetter'],
    template: `<li @click="handleClick" :class="'letter clickable letter-state-' + count">{{ theLetter }}:{{ count }}</li>`,
    methods: {
        handleClick() {
            Event.$emit('letterIncrement', { letter: this.theLetter });
        }
    },
    computed: {
        count() {
            return this.$store.state.letters[this.theLetter];
        }
    }
});

Vue.component('move-list', {
    mounted() {
        Event.$on('newmove', (move) => {
            this.$store.commit('addMove', move);
        });
    },
    template: `
    <div>
        <h3>Moves: {{ moves.length }}</h3>
        <table>
            <tbody>
                <tr v-for="move in moves">
                    <guess-word v-bind:word="move.guess"></guess-word>
                    <td>{{ move.result }} </td>
                </tr>
            </tbody>
        </table>
    </div>
    `,
    computed: {
        moves() {
            return this.$store.state.moves;
        }
    }
});

Vue.component('guess-word', {
    template: `
        <td v-html="coloredWord"></td>
    `,
    props: ['word'],
    mounted() {
    },
    computed: {
        coloredWord() {
            let strChars = this.word.split("");
            let coloredWord = '';
            let used = {};
            for (let idx in strChars) {
                let char = strChars[idx];
                if (!used[char]) {
                    used[char] = 0;
                }
                used[char]++;
                if ((this.$store.state.letters[char] > 0) && (used[char] > this.$store.state.letters[char])) {
                    coloredWord += "<span>" + char + "</span>";
                } else {
                    coloredWord += "<span class='letter-state-" + this.$store.state.letters[char] + "'>" + char + "</span>";
                }
            }
            return coloredWord;
        }
    }
});

// TODO: Fix all api calls in this component.
Vue.component('guess-box', {
    data() {
        return {
            guess: '',
            errors: {}
        };
    },
    computed: {
        myturn() {
            return this.$store.state.game.turn == this.$store.state.user.id;
        },
        currentturn() {
            return this.$store.state.game.turn;
        }
    },
    methods: {
        onSubmit() {
            axios.post('/api/game/' + this.$store.state.game.id + '/move', {
                guess: this.guess
            })
                .then(response => {
                    // TODO: fix new move showing up twice in move list
                    this.guess = '';
                    this.$store.commit('addMove', response.data);
                    Event.$emit('newmove', response.data)
                    this.$store.commit('opponentsTurn');
                })
                .catch(error => {
                    this.errors = error.response.data;
                    console.log(error.response);
                });
        },
        checkTurn() {
            axios.get('/api/game/' + this.$store.state.game.id + '/update')
                .then(response => {
                    console.log(response);
                    // if (response.data.turn == this.$store.state.user.id) {
                        this.$store.commit('updateGameData', response.data.game);
                    // }
                })
                .catch(error => {
                    this.errors = error.response.data;
                    console.log(error);
                });
        }
    }
});

const app = new Vue({
    el: '#app',
    store,
});
