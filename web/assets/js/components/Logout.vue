<template>
    <div />
</template>

<script>
    export default {
        name: "Logout",
        created() {
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
