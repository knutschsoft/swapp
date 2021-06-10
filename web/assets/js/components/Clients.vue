<template>
    <div>
        <content-collapse
            title="Liste der Klienten"
            collapse-key="client-list"
            is-visible-by-default
        >
            <client-list />
        </content-collapse>
    </div>
</template>

<script>
    "use strict";
    import ClientList from './Clients/ClientList';
    import ContentCollapse from './ContentCollapse.vue';

    export default {
        name: "Clients",
        components: {
            ContentCollapse,
            ClientList
        },
        mounted() {
        }
    }
</script>

<style scoped>

</style>
