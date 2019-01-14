<template>
    <div>
        <input type="radio" id="random" value="random" v-model="origination">
        <label for="random">Random word</label><br/>
        <input type="radio" id="choose" value="choose" v-model="origination">
        <label for="choose">Choose my own word</label><br/>
        <div v-bind:class="{hide: isRandom}">
            <text-input name="chosenWord" label="My word" prompt="A word" :onChange="wordChanged"></text-input>
        </div>
    </div>
</template>

<script>
import TextInput from './TextInput.vue';

export default {
    data() {
        return {
            name: 'chooseWord',
            myWord: null,
            origination: 'choose',
        }
    },
    computed: {
        value() {
            if (this.origination == 'choose') {
                return {type: 'choose', word: this.myWord};
            }
            return {type: 'random'};
        },
        isChoose() {
            return this.origination == 'choose';
        },
        isRandom() {
            return this.origination == 'random';
        }
    },
    mounted() {
        Event.$on('newError', (error) => {
            if (error[0] == 'chooseWord.word') {
                Event.$emit('newError', ['chosenWord', [error[1][0]]]);
            }
        });
        Event.$on('clearError', (error) => {
            if (error[0] == 'chooseWord.word') {
                Event.$emit('clearError', 'chosenWord');
            }
        });
    },
    methods: {
        wordChanged(word) {
            this.myWord = word;
        }
    },
    components: { TextInput }
};
</script>

<style>
    .hide {
        display: none;
    }
</style>