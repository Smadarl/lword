
require('./bootstrap');

window.Vue = require('vue');

window.Event = new Vue();

let letters = {
    a: 0, b: 0, c: 0, d: 0, e: 0, f: 0, g: 0, h: 0, i: 0, j: 0, k: 0, l: 0, m: 0,
    n: 0, o: 0, p: 0, q: 0, r: 0, s: 0, t: 0, u: 0, v: 0, w: 0, x: 0, y: 0, z: 0
};

Vue.component('letter-list', {
    props: ['maxCount'],
    data() {
        return {
            letters: {}
        };
    },
    created() {
        this.letters = letters;
        Event.$on('letterIncrement', (letter) => {
            this.letters[letter.letter] = letter.count;
            console.log(letter.letter + ': ' + this.letters[letter.letter]);
        });
    }
});

Vue.component('letter', {
    props: ['theLetter', 'theCount'],
    data() {
        return {
            count: this.theCount
        };
    },
    template: `<li @click="handleClick" :class="'letter clickable letter-state-' + count">{{ theLetter }}:{{ count }}</li>`,
    methods: {
        handleClick() {
            if (this.count == 3) {
                this.count = -1;
            } else {
                this.count += 1;
            }
            Event.$emit('letterIncrement', { letter: this.theLetter, count: this.count });
        }
    }
});

Vue.component('move-list', {
    props: ['gameId'],
    data() {
        return {
            moves: [],
            gameid: this.gameId
        };
    },
    mounted() {
        Event.$on('newmove', (move) => {
            this.moves.push(move);
        });
        axios.get('/game/' + this.gameid + '/moves')
            .then(response => this.moves = response.data)
            .catch(error => {
                console.log(error.response);
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
`
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
                if ((letters[char] > 0) && (used[char] > letters[char])) {
                    coloredWord += "<span>" + char + "</span>";
                } else {
                    coloredWord += "<span class='letter-state-" + letters[char] + "'>" + char + "</span>";
                }
            }
            return coloredWord;
        }
    }
});

Vue.component('guess-box', {
    props: ['gameId', 'gameTurn', 'myId'],
    data() {
        return {
            guess: '',
            gameid: this.gameId,
            moves: [],
            currentturn: this.gameTurn,
            myturn: this.gameTurn == this.myId,
            errors: {}
        };
    },
    mounted() {
        Event.$on('gameslistupdate', (data) => {
            for (let game in data) {
                if (game.id == this.gameid) {
                    this.currentturn = game.turn;
                    myturn = this.currentturn == this.myId;
                }
            }
        });
    },
    methods: {
        onSubmit() {
            axios.post('/game/move', {
                gameId: this.gameid,
                guess: this.guess
            })
                .then(response => {
                    Event.$emit('newmove', response.data)
                    this.guess = '';
                    this.myturn = false;
                })
                .catch(error => {
                    this.errors = error.response.data;
                    console.log(error.response);
                });
        }
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
});
