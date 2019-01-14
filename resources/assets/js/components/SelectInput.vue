<template>
    <div>
        <div class="row">
            <label :for="name" class="col-3">
                {{ label }}
            </label>
            <select :name="name" :id="name" v-model="value">
                <option v-for="item in items" v-bind:value="item.value" v-text="item.label" v-bind:key="item.value" />
            </select>
        </div>
        <span class="offset-sm-3 small text-danger" v-if="errorDescription" v-text="errorDescription">
        </span>
    </div>
</template>
<script>
export default {
    props: {
        items: {
            type: Array,
            default() {
                return [];
            }
        },
        name: '',
        label: '',
        onChange: null
    },
    data() {
        return {
            value: null,
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