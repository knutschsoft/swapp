<template>
    <div>
        <content-collapse
            title="Liste der Klienten"
            collapse-key="header-client-list"
            is-visible-by-default
        >
            <client-list />
        </content-collapse>
        <content-collapse
            title="Neuen Klienten erstellen"
            collapse-key="header-client-create"
            is-visible-by-default
        >
            <client-create />
        </content-collapse>
    </div>
</template>

<script>
    "use strict";
    import ClientCreate from './Clients/ClientCreate.vue';
    import ClientList from './Clients/ClientList.vue';
    import ContentCollapse from './ContentCollapse.vue';

    export default {
        name: "Clients",
        components: {
            ContentCollapse,
            ClientCreate,
            ClientList,
        },
        mounted() {
        }
    }
</script>

<style scoped>

</style>
