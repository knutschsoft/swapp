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
            class="mb-0"
            prominent
        >
            Du bist aktuell keinem Team zugeordnet.
            <p
                v-if="isAllowedToCreateTeam"
                class="mb-0"
            >
                Ordne dich selber
                <v-btn
                    :to="{ name: 'Teams' }"
                    title="Teamverwaltung"
                    color="info"
                    outlined
                >einem Team zu</v-btn>
                um eine Runde starten zu können.
            </p>
            <p
                v-else
                class="mb-0"
            >
                Bitte einen Admin dich einem Team zuzuordnen um eine Runde starten zu können.
            </p>
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
    import { useUserStore } from '../../stores/user';
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
                userStore: useUserStore(),
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
                this.unfinishedWalks.forEach((walk) => {
                    const walkCreator = walk.walkCreator ? this.getUserByUserIri(walk.walkCreator)?.username : 'nicht gesetzt';
                    const text = `${walk.name} - Beginn ${dayjs(walk.startTime).format('DD.MM.YYYY HH:mm:ss')} - Rundenersteller: ${walkCreator} - ${walk.wayPoints.length} Wegpunkt${walk.wayPoints.length !== 1 ? 'e' : ''} - Tageskonzept: ${walk.conceptOfDay}`;
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
            if (this.currentUser.teams.length) {
                this.unfinishedWalks = (await WalkAPI.findAllUnfinishedWalks(this.currentUser.teams)).data['hydra:member'];
            }
            const promises = [];
            this.unfinishedWalks.forEach(unfinishedWalk => {
                if (!unfinishedWalk.walkCreator) {
                    return
                }
                promises.push(this.userStore.fetchByIri(unfinishedWalk.walkCreator));
            })
            await Promise.all(promises);
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
            getUserByUserIri: function (userIri) {
                return this.userStore.getUserByIri(userIri);
            },
        }
    }
</script>

<style scoped>
</style>
