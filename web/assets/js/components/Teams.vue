<template>
    <div>
        <content-collapse
            title="Liste der Teams"
            collapse-key="header-team-list"
            is-visible-by-default
        >
            <team-list />
        </content-collapse>
        <content-collapse
            title="Neues Team erstellen"
            data-test="header-team-create"
            collapse-key="team-create"
        >
            <team-form
                ref="teamForm"
                button-label="Team erstellen"
                @submit="handleSubmit"
            />
        </content-collapse>
    </div>
</template>

<script>
    "use strict";
    import TeamList from './Teams/TeamList';
    import TeamForm from './Teams/TeamForm';
    import ContentCollapse from './ContentCollapse.vue';

    export default {
        name: "Teams",
        components: {
            ContentCollapse,
            TeamList,
            TeamForm,
        },
        mounted() {
        },
        methods: {
            async handleSubmit(team) {
                const createdTeam = await this.$store.dispatch('team/create', {
                    client: team['client'],
                    name: team.name,
                    locationNames: team.locationNames,
                    walkNames: team.walkNames,
                    conceptOfDaySuggestions: team.conceptOfDaySuggestions,
                    users: team.users,
                    ageRanges: team.ageRanges,
                    isWithAgeRanges: team.isWithAgeRanges,
                    isWithPeopleCount: team.isWithPeopleCount,
                    isWithContactsCount: team.isWithContactsCount,
                    isWithUserGroups: team.isWithUserGroups,
                    userGroupNames: team.userGroupNames,
                    isWithGuests: team.isWithGuests,
                    isWithSystemicQuestion: team.isWithSystemicQuestion,
                    guestNames: team.guestNames,
                });

                if (createdTeam) {
                    this.$refs.teamForm.resetForm();
                    const message = `Das Team ${createdTeam.name} wurde erfolgreich erstellt.`;
                    this.$bvToast.toast(message, {
                        title: 'Team erstellt',
                        toaster: 'b-toaster-top-right',
                        variant: 'success',
                        autoHideDelay: 10000,
                        appendToast: true,
                    });
                    this.$root.$emit('bv::hide::modal', 'edit-modal-team');
                } else {
                    this.$bvToast.toast('Upps! :-(', {
                        title: 'Team erstellen fehlgeschlagen',
                        toaster: 'b-toaster-top-right',
                        autoHideDelay: 10000,
                        variant: 'danger',
                        appendToast: true,
                    });
                }
            },
        },
    }
</script>

<style scoped>

</style>
