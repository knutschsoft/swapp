<template>
    <div>
        <content-collapse
            title="Liste der Benutzer"
            collapse-key="user-list"
            is-visible-by-default
        >
            <user-list />
        </content-collapse>
        <content-collapse
            v-if="isSuperAdmin"
            title="Liste der aktiven Benutzer"
            collapse-key="active-users"
            is-visible-by-default
        >
            <active-user-list />
        </content-collapse>
        <content-collapse
            title="Neuen Benutzer erstellen"
            collapse-key="user-create"
            is-visible-by-default
        >
            <user-create />
        </content-collapse>
    </div>
</template>

<script>
    "use strict";
    import ActiveUserList from './Users/ActiveUserList.vue';
    import UserCreate from './Users/UserCreate.vue';
    import UserList from './Users/UserList.vue';
    import ContentCollapse from './ContentCollapse.vue';
    import { useAuthStore, useClientStore } from '../stores';

    export default {
        name: "Users",
        data() {
            return {
                authStore: useAuthStore(),
                clientStore: useClientStore(),
            };
        },
        components: {
            ActiveUserList,
            ContentCollapse,
            UserCreate,
            UserList,
        },
        computed: {
            isSuperAdmin() {
                return this.authStore.isSuperAdmin;
            },
        },
        async mounted() {
            await this.clientStore.fetchClients();
        },
    }
</script>

<style scoped>

</style>
