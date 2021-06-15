<template>
    <div class="row m-auto pt-4 mt-4">
        <div
            v-if="!user"
            class="col-sm-10 offset-sm-1 col-md-8 offset-md-2 offset-lg-3 col-lg-6 border border-dark p-4 mt-4"
        >
            Upps! Der von dir genutzte Link ist nicht länger gültig.
            <br>
            Bitte schaue nach, ob in deinem E-Mail-Postfach eine neuere E-Mail mit Link vorhanden ist oder melde dich bei deinem Administrator.
        </div>
        <div
            v-else
            class="col-sm-10 offset-sm-1 col-md-8 offset-md-2 offset-lg-3 col-lg-6 border border-dark p-4 mt-4"
        >
            <div
                class="alert alert-success w-100 mb-0"
                role="alert"
            >
                <p class="font-weight-bold">
                    Herzlichen Glückwunsch {{ user.username }}!
                </p>
                <ul class="text-left mt-3">
                    <li>
                        Du hast deine E-Mail-Adresse <b>{{ user.email }}</b> bestätigt und dein Konto erfolgreich aktiviert.
                    </li>
                    <li>
                        Du solltest auch eine E-Mail bekommen haben um dein Passwort neu zu setzen.
                    </li>
                    <li>
                        Du kannst diese Seite jetzt schließen.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
<script>
'use strict';
import SecurityAPI from '../api/security';

export default {
    name: 'UserEmailConfirm',
    components: {},
    props: {
        'userId': {
            required: true,
            type: String,
        },
        'confirmationToken': {
            required: true,
            type: String,
        },
    },
    data: () => ({
        user: false,
    }),
    computed: {
        isLoading() {
            return this.$store.getters['security/isLoading'];
        },
    },
    async created() {
        try {
            await SecurityAPI.isConfirmationTokenValid(
                `/api/users/${this.userId}`,
                this.confirmationToken,
            );
            const result = await SecurityAPI.userEmailConfirm(
                `/api/users/${this.userId}`,
                this.confirmationToken,
            );
            this.user = result.data;
        } catch (e) {
            this.user = false;
        }
    },
    methods: {},
};
</script>

<style scoped>

</style>
