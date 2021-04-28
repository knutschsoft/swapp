<template>
    <div>
        <content-collapse
            v-if="walk"
            :title="`Runde &quot;${walk.name}&quot; abschließen`"
            collapse-key="walk-epilogue"
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
'use strict';
import ContentCollapse from './ContentCollapse.vue';

export default {
    name: 'WalkEpilogue',
    components: {
        ContentCollapse,
    },
    props: {
        walkId: {
            required: true,
        },
    },
    data: function () {
        return {
        };
    },
    computed: {
        isLoading() {
            return this.$store.getters['walk/isLoading'];
        },
        hasWalks() {
            return this.$store.getters['walk/hasWalks'];
        },
        walks() {
            return this.$store.getters['walk/walks'];
        },
        walk() {
            return this.$store.getters["walk/getWalkById"](this.walkId);
        },
    },
    watch: {},
    async mounted() {
        await this.refreshWalk();
        let { data } = await this.axios.get(`/form/walk-epilogue/${this.walkId}`);
        this.$refs.forms.innerHTML = data.form;
    },
    methods: {
        refreshWalk: async function() {
            await this.$store.dispatch('walk/findById', this.walkId);
        },
        onSubmit: async function (e) {
            let formData = new FormData(e.target);
            let result = await this.axios.post(`/form/walk-epilogue/${this.walkId}`, formData);
            window.scrollTo({
                top: 0,
                left: 0,
                behavior: 'smooth'
            });
            if (200 === result.status) {
                this.$router.push({ name: 'Dashboard' });
            }
        },
    },
};
</script>

<style scoped>

</style>
