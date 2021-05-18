<template>
    <div>
        <content-collapse
            :title="`Neue Streetwork-Runde`"
            collapse-key="walk-prologue"
            is-visible-by-default
        >
            <div
                id="form-holder"
                ref="forms"
                v-on:submit.prevent="onSubmit"
                class="p-2"
            />
        </content-collapse>
    </div>
</template>

<script>
    "use strict";
    import ContentCollapse from './ContentCollapse.vue';

    export default {
        name: "WalkPrologue",
        components: {
            ContentCollapse,
        },
        props: {
            walkId: {
                required: true,
            }
        },
        data: function () {
            return {
                isFormLoading: false,
            }
        },
        computed: {
            isLoading() {
                return this.$store.getters["walk/isLoading"] || this.isFormLoading;
            },
            hasWalks() {
                return this.$store.getters["walk/hasWalks"];
            },
            walks() {
                return this.$store.getters["walk/walks"];
            },
        },
        watch: {},
        async mounted() {
            this.isFormLoading = true;
            let {data} = await this.axios.get(`/form/walk-prologue/${this.walkId}`);
            this.isFormLoading = false;
            this.$refs.forms.innerHTML = data.form;
        },
        methods: {
            onSubmit: async function (e) {
                let formData = new FormData(e.target);
                this.isFormLoading = true;
                let result = await this.axios.post(`/form/walk-prologue/${this.walkId}`, formData);
                this.isFormLoading = false;
                window.scrollTo({
                    top: 0,
                    left: 0,
                    behavior: 'smooth'
                });
                if (200 === result.status && result.data.form) {
                    this.$refs.forms.innerHTML = result.data.form;
                } else if (200 === result.status && result.data.form === undefined) {
                    await this.$store.dispatch('walk/findById', this.walkId);
                    this.$router.push({ name: 'WalkAddWayPoint', params: {walkId: this.walkId} })
                }
            }
        },
    }
</script>

<style scoped>

</style>
