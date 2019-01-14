<template>
    <form @submit.prevent="onSubmit" @keydown="errors.clear($event.target.id)">
        <slot></slot>
    </form>
</template>
<script>
class Errors {
    constructor() {
        this.data = [];
    }

    has(field) {
        return this.data.hasOwnProperty(field);
    }
    get(field) {
        if (this.data[field]) {
            return this.data[field][0];
        }
    }

    record(data) {
        this.data = data.errors;
        Object.entries(this.data).forEach((error) => {
            Event.$emit('newError', error);
        });
    }

    clear(field) {
        if (field) {
            if (this.data[field]) {
                delete this.data[field];
            } else {
                Event.$emit('clearError', field);
            }
            return;
        }
        Event.$emit('clearError', '*');
        this.data = [];
    }
}

export default {
    props: ['posturl', 'success', 'fail' ],
    data() {
        return {
            errors: new Errors()
        }
    },
    methods: {
        onSubmit() {
            let postData = {};
            this.$children.forEach((child) => {
                postData[child.name] = child.value;
            });
            console.log(postData);
            axios.post(this.posturl, postData)
            .then((response) => {
                if (this.success) {
                    Event.$emit(this.success, response.data);
                }
            })
            .catch((error) => {
                console.log(error.response);
                this.errors.record(error.response.data);
                if (this.fail) {
                    Event.$emit(this.fail, error.response.data);
                }
            })
        }
    }
};
</script>
<style>
</style>