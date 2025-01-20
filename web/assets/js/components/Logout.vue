<template>
    <div />
</template>

<script>
import {useAuthStore, useClientStore, useSystemicQuestionStore, useTagStore, useTeamStore, useUserStore, useWalkStore, useWayPointStore} from '../stores';
    export default {
        name: "Logout",
        data() {
            return {
                authStore: useAuthStore(),
                clientStore: useClientStore(),
                teamStore: useTeamStore(),
                systemicQuestionStore: useSystemicQuestionStore(),
                tagStore: useTagStore(),
                userStore: useUserStore(),
                walkStore: useWalkStore(),
                wayPointStore: useWayPointStore(),
            };
        },
        created() {
            this.clientStore.$reset();
            this.systemicQuestionStore.$reset();
            this.teamStore.$reset();
            this.tagStore.$reset();
            this.userStore.$reset();
            this.walkStore.$reset();
            this.wayPointStore.$reset();
            this.authStore.logout();
            this.$router.push({name: 'Login'});
        },
    }
</script>
