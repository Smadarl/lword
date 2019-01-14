<template>
    <div>
        <div class="row">
            <label :for="name" class="col-3">
                {{ label }}
            </label>
            <input :id="name" type="text" v-model="value" :placeholder="prompt" class="col-3" @change="changed" />
        </div>
        <span class="offset-sm-3 small text-danger" v-if="errorDescription" v-text="errorDescription">
        </span>
    </div>
</template>
<script>
export default {
    props: {
        name: '',
        label: '',
        prompt: '',
        onChange: null
    },
    data() {
        return {
            value: '',
            errorDescription: ''
        }
    },
    mounted() {
        Event.$on('newError', (error) => {
            if (this.name != error[0])
                return;
            this.errorDescription = error[1][0];
        });

        Event.$on('clearError', (field) => {
            if (this.name != field && field != '*')
                return;
            this.errorDescription = '';
        });

    },
    methods: {
        changed() {
            if (this.onChange) {
                this.onChange(this.value);
            }
        }
    }
}
</script>
<style>
</style>