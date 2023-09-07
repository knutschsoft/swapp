<template>
    <div />
</template>

<script>
    import { useAuthStore } from '../stores/auth';
    import { useChangelogStore } from '../stores/changelog';
    import { useClientStore } from '../stores/client';
    import { useTeamStore } from '../stores/team';
    import { useSystemicQuestionStore } from '../stores/systemic-question';
    import { useTagStore } from '../stores/tag';
    import { useUserStore } from '../stores/user';
    import { useWalkStore } from '../stores/walk';
    import { useWayPointStore } from '../stores/way-point';
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
