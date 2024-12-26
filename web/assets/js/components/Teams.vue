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
    import TeamList from './Teams/TeamList.vue';
    import TeamForm from './Teams/TeamForm.vue';
    import ContentCollapse from './ContentCollapse.vue';
    import { useAlertStore, useTeamStore } from '../stores';

    export default {
        name: "Teams",
        components: {
            ContentCollapse,
            TeamList,
            TeamForm,
        },
        data: () => {
            return {
                alertStore: useAlertStore(),
                teamStore: useTeamStore()
            };
        },
        mounted() {
        },
        methods: {
            async handleSubmit(team) {
                const createdTeam = await this.teamStore.create({
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
                    initialMembersConfig: team.initialMembersConfig,
                });

                if (createdTeam) {
                    this.$refs.teamForm.resetForm();
                    this.alertStore.success(`Das Team ${createdTeam.name} wurde erfolgreich erstellt.`);
                    this.$root.$emit('bv::hide::modal', 'edit-modal-team');
                } else {
                    this.alertStore.error(`Team erstellen fehlgeschlagen`, `Upps! :-(`);
                }
            },
        },
    }
</script>

<style scoped>

</style>
