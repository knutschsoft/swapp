<template>
    <div />
</template>

<script>
    export default {
        name: "Logout",
        created() {
            if (this.$store.getters['security/isUserSwitched']) {
                this.$store.dispatch('security/exitSwitchUser');
            }
            this.$store.commit('reset');
            this.$store.dispatch(
                'security/onRefresh',
                {
                    token: null,
                    isAuthenticated: false,
                    user: null,
                }
            );
            this.$router.push({name: 'Login'});
        },
    }
</script>
