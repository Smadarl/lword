
require('./bootstrap');

window.Vue = require('vue');

window.Event = new Vue();

Vue.component('letter-list', {
    data() {
        return {
            letters: [],
            temp: {}
        };
    },
    mounted() {
        for(let i in this.temp) {
            if (this.temp.hasOwnProperty(i)) {
                this.letters.push({letter: i, count: this.temp[i]});
            }
        }
    },
    created() {
        this.temp = {
            a: 0, b: 0, c: 0, d: 0, e: 0, f: 0, g: 0, h: 0, i: 0, j: 0, k: 0, l: 0, m: 0,
            n: 0, o: 0, p: 0, q: 0, r: 0, s: 0, t: 0, u: 0, v: 0, w: 0, x: 0, y: 0, z: 0
        };
    }
});

Vue.component('letter', {
    template: `
        <button class="letter" value="letter.letter" @click="handleClick">@{{ letter.letter }} <span>@{{ letter.count }}</span></button>
    `,
    data() {
        return {
            letter: {}
        };
    },
    methods: {
        handleClick() {
            if (this.letter.count == 3) {
                this.letter.count = -1;
            } else {
                this.letter.count += 1;
            }
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
    }
});

Vue.component('guess-box', {
    props: ['gameId', 'gameTurn', 'myId'],
    data() {
        return {
            guess: '',
            gameid: this.gameId,
            moves: [],
            myturn: this.gameTurn == this.myId,
            errors: {}
        };
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

const app = new Vue({
    el: '#app',
});
