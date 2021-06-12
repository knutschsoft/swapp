<template>
    <div>
        <content-collapse
            title="Liste der Systemischen Fragen"
            collapse-key="list-of-systemic-questions"
            is-visible-by-default
        >
            <systemic-question-list />
        </content-collapse>
        <content-collapse
            title="Neue Systemische Frage erstellen"
            data-test="header-systemic-question-create"
            collapse-key="systemic-question-create"
        >
            <systemic-question-create />
        </content-collapse>
    </div>
</template>

<script>
    "use strict";
    import SystemicQuestionList from './SystemicQuestions/SystemicQuestionList';
    import SystemicQuestionCreate from './SystemicQuestions/SystemicQuestionCreate';
    import ContentCollapse from './ContentCollapse.vue';

    export default {
        name: "Teams",
        components: {
            ContentCollapse,
            SystemicQuestionList,
            SystemicQuestionCreate,
        },
        async mounted() {
            await this.$store.dispatch('client/findAll');
        },
    }
</script>

<style scoped>

</style>
