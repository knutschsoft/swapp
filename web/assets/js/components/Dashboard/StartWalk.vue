<template>
    <div
        class="p-2"
    >
        <v-card
            v-if="hasUnfinishedWalks && !isLoading"
            class="mb-2"
        >
            <v-toolbar
                dense
            >
                <v-overflow-btn
                    v-model="selectedUnfinishedWalk"
                    :items="selectableUnfinishedWalks"
                    data-test="select-walk"
                    label="Nicht beendete Runde wählen..."
                    overflow
                    hide-details
                    dense
                    class="pa-0"
                />
                <v-btn
                    :disabled="!selectedUnfinishedWalk"
                    @click="handleWalkContinue"
                    class="rounded-0"
                    data-test="runde-fortsetzen"
                    color="secondary"
                >
                    Runde fortsetzen
                    <v-icon>mdi-shoe-print</v-icon>
                    <v-icon>mdi-walk</v-icon>
                    <v-icon>mdi-shoe-print</v-icon>
                </v-btn>
            </v-toolbar>
        </v-card>
        <v-divider
            v-if="hasUnfinishedWalks && !isLoading"
        />
        <v-card
            v-if="selectableTeams.length && !isLoading"
        >
            <v-toolbar
                dense
            >
                <v-overflow-btn
                    v-model="selectedTeam"
                    :items="selectableTeams"
                    label="Team wählen..."
                    data-test="select-team"
                    overflow
                    hide-details
                    dense
                    class="pa-0"
                />
                <v-btn
                    color="secondary"
                    @click="handleWalkPrologue"
                    :disabled="!hasSelectedTeamSystemicQuestionsAvailable"
                    class="rounded-0"
                >
                    Runde beginnen
                    <v-icon>mdi-walk</v-icon>
                    <v-icon>mdi-shoe-print</v-icon>
                </v-btn>
            </v-toolbar>
        </v-card>
        <v-alert
            v-else-if="!teams.length && !isLoading && isAllowedToCreateTeam"
            class="mb-0"
            type="info"
            outlined
            prominent
        >
            Um eine neue Runde zu erstellen, musst Du zuerst
            <v-btn
                :to="{ name: 'Teams' }"
                color="info"
                small
                outlined
                title="Teamverwaltung"
            >ein neues Team anlegen</v-btn>.
        </v-alert>
        <v-alert
            v-else-if="!isLoading"
            type="info"
            outlined
            prominent
        >
            Du bist aktuell keinem Team zugeordnet.
            <p
                v-if="isAllowedToCreateTeam"
            >
                Bitte einen Admin dich einem Team zuzuordnen um eine Runde starten zu können.
            </p>
            <template
                v-else
            >
                Ordne dich selber
                <v-btn
                    :to="{ name: 'Teams' }"
                    title="Teamverwaltung"
                    color="info"
                    outlined
                >einem Team zu</v-btn>
                um eine Runde starten zu können.
            </template>
        </v-alert>
        <v-alert
            v-if="selectedTeam && !hasSelectedTeamSystemicQuestionsAvailable && !isLoading"
            type="warning"
            prominent
            outlined
        >
            Um für dieses Team eine neue Runde zu erstellen, musst Du zuerst mindestens
            <v-btn
                :to="{ name: 'SystemicQuestions' }"
                title="Systemische Fragen"
                color="warning"
                outlined
            >eine Systemische Frage erstellen</v-btn>.
        </v-alert>
    </div>
</template>

<script>
    "use strict";
    import { useSystemicQuestionStore } from '../../stores/systemic-question';
    import { useTeamStore } from '../../stores/team';
    import { useAuthStore } from '../../stores/auth';
    import WalkAPI from '../../api/walk.js';
    import dayjs from "dayjs";

    export default {
        name: "StartWalk",
        components: {},
        props: {
        },
        data: function () {
            return {
                authStore: useAuthStore(),
                systemicQuestionStore: useSystemicQuestionStore(),
                teamStore: useTeamStore(),
                selectedTeam: null,
                selectedUnfinishedWalk: null,
                unfinishedWalks: [],
                isInnerLoading: true,
            }
        },
        computed: {
            hasTeams() {
                return this.teamStore.hasTeams;
            },
            isLoading() {
                return this.teamStore.isLoading || this.authStore.isLoading || this.systemicQuestionStore.isLoading || this.isInnerLoading;
            },
            teams() {
                return this.teamStore.getTeams;
            },
            currentUser() {
                return this.authStore.currentUser;
            },
            isAllowedToCreateTeam() {
                return this.currentUser.isAdmin;
            },
            selectableTeams() {
                let options = [];
                if (!this.currentUser) {
                    return options;
                }

                this.teams.forEach((team) => {
                    team.users.forEach(userIri => {
                        if (userIri === this.currentUser['@id']) {
                            options.push({ text: `Team '${team.name}'`, value: team });
                        }
                    });
                });

                return options;
            },
            hasUnfinishedWalks() {
                return this.unfinishedWalks.length > 0;
            },
            selectableUnfinishedWalks() {
                let options = [
                    {text: 'Runde wählen...', value: null}
                ];
                if (!this.hasUnfinishedWalks) {
                    return options;
                }
                console.log(this.unfinishedWalks);
                this.unfinishedWalks.forEach((walk) => {
                    const text = `${walk.name} - Beginn ${dayjs(walk.startTime).format('DD.MM.YYYY HH:mm:ss')} - ${walk.wayPoints.length} Runde${walk.wayPoints.length !== 1 ? 'n' : ''} - Tageskonzept: ${walk.conceptOfDay}`;
                    options.push({ text: text, value: walk });
                });

                return options;
            },
            hasSelectedTeamSystemicQuestionsAvailable() {
                if (!this.selectedTeam) {
                    return false;
                }
                if (this.selectedTeam.isWithSystemicQuestion) {
                    return this.systemicQuestionStore.getSystemicQuestions.filter(systemicQuestion => systemicQuestion.isEnabled).length > 0;
                }

                return true;
            },
        },
        async created() {
            await this.teamStore.fetchTeams();
            if (this.selectableTeams.length) {
                this.selectedTeam = this.selectableTeams[0].value;
            }
            if (this.teams.some(team => team.isWithSystemicQuestion)) {
                await this.systemicQuestionStore.fetchSystemicQuestions();
            }
            this.unfinishedWalks = (await WalkAPI.findAllUnfinishedWalks(this.currentUser.teams)).data['hydra:member'];
            this.isInnerLoading = false;
        },
        methods: {
            handleWalkPrologue: async function () {
                this.$router.push({ name: 'WalkPrologue', params: {teamId: this.selectedTeam.teamId} })
            },
            handleWalkContinue: async function () {
                if (!this.selectedUnfinishedWalk) {
                    return
                }
                this.$router.push({name: 'WalkAddWayPoint', params: { walkId: this.selectedUnfinishedWalk.walkId}} )
            },
        }
    }
</script>

<style scoped>
</style>
