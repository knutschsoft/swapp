<template>
    <div>
        <content-collapse
            title="Liste der Teams"
            collapse-key="header-team-list"
            is-visible-by-default
        >
            <team-list/>
        </content-collapse>
        <content-collapse
            title="Neues Team erstellen"
            data-test="header-team-create"
            collapse-key="team-create"
        >
            <team-form ref="teamForm" button-label="Team erstellen" @submitted="handleSubmit"/>
        </content-collapse>
    </div>
</template>

<script setup>
import {onMounted, ref} from 'vue';
import TeamList from './Teams/TeamList.vue';
import TeamForm from './Teams/TeamForm.vue';
import ContentCollapse from './ContentCollapse.vue';
import {useAlertStore, useClientStore, useTeamStore} from '../stores';

const alertStore = useAlertStore();
const clientStore = useClientStore();
const teamStore = useTeamStore();

const teamForm = ref(null);

onMounted(() => {
    clientStore.fetchClients();
});

const handleSubmit = async (team) => {
    const createdTeam = await teamStore.create({
        client: team.client,
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
        teamForm.value?.resetForm();
        alertStore.success(`Das Team ${createdTeam.name} wurde erfolgreich erstellt.`);
    } else {
        alertStore.error('Team erstellen fehlgeschlagen', 'Upps! :-(');
    }
};
</script>

<style scoped>
</style>
