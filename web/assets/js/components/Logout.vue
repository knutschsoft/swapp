<template>
    <div />
</template>

<script>
    import { useAuthStore } from '../stores/auth';
    export default {
        name: "Logout",
        data() {
            return {
                authStore: useAuthStore()
            };
        },
        created() {
            // reset stores
            this.authStore.logout();
            this.$router.push({name: 'Login'});
        },
    }
</script>
