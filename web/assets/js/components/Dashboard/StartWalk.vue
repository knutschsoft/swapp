<template>
    <div>
        <b-input-group
            v-if="hasUnfinishedWalks"
            class="p-2"
        >
            <b-form-select
                v-model="selectedUnfinishedWalk"
                :options="selectableUnfinishedWalks"
                aria-placeholder="Runde wählen..."
                class="w-10"
                data-test="select-walk"
            />
            <b-input-group-append>
                <b-button
                    :disabled="!selectedUnfinishedWalk"
                    variant=""
                >
                    Runde fortsetzen
                </b-button>
                <b-input-group-text>
                    <font-awesome-layers>
                        <font-awesome-icon animation="fade" icon="shoe-prints" class="faa-blink animated" size="xs" transform="shrink-8 down-7" style="animation-delay: 1s;"  flip="vertical" />
                        <font-awesome-icon animation="fade" icon="shoe-prints" class="faa-blink animated" size="xs" transform="shrink-8 down-7" />
                    </font-awesome-layers>
                    <font-awesome-icon icon="walking" />
                    <font-awesome-layers>
                        <font-awesome-icon animation="fade" icon="shoe-prints" class="faa-blink animated" size="xs" transform="shrink-8 down-7" flip="vertical" />
                        <font-awesome-icon animation="fade" icon="shoe-prints" class="faa-blink animated" style="animation-delay: 1s;" size="xs" transform="shrink-8 down-7" />
                    </font-awesome-layers>
                </b-input-group-text>
            </b-input-group-append>
        </b-input-group>
        <b-input-group
            v-if="selectableTeams.length"
            class="p-2"
        >
            <b-form-select
                v-model="selectedTeam"
                :options="selectableTeams"
                aria-placeholder="Team wählen..."
                class="w-10"
                data-test="select-team"
            />
            <b-input-group-append>
                <b-button
                    @click="handleWalkPrologue"
                    :disabled="!selectedTeam"
                    variant=""
                >
                    Runde beginnen
                </b-button>
                <b-input-group-text>
                    <font-awesome-icon icon="walking" />
                    <font-awesome-layers>
                        <font-awesome-icon animation="fade" icon="shoe-prints" class="faa-blink animated" size="xs" transform="shrink-8 down-7" flip="vertical" />
                        <font-awesome-icon animation="fade" icon="shoe-prints" class="faa-blink animated" style="animation-delay: 1s;" size="xs" transform="shrink-8 down-7" />
                    </font-awesome-layers>
                </b-input-group-text>
            </b-input-group-append>
        </b-input-group>
        <div
            v-else
            class="p-2 text-muted"
        >
            Du bist aktuell keinem Team zugeordnet.
        </div>
    </div>
</template>

<script>
    "use strict";

    export default {
        name: "StartWalk",
        components: {},
        props: {
            // teams: {
            //     required: true,
            //     type: Object,
            // },
        },
        data: function () {
            return {
                selectedTeam: null,
                selectedUnfinishedWalk: null,
            }
        },
        computed: {
            hasTeams() {
                return this.$store.getters["team/hasTeams"];
            },
            teams() {
                return this.$store.getters["team/teams"];
            },
            currentUser() {
                return this.$store.getters["security/currentUser"];
            },
            selectableTeams() {
                let options = [];
                this.teams.forEach((team) => {
                    team.users.forEach(user => {
                        if (user.id === this.currentUser.id) {
                            options.push({ text: `Team '${team.name}'`, value: team });
                        }
                    });
                });

                return options;
            },
            hasUnfinishedWalks() {
                return false;
            },
            unfinishedWalks() {
                return [];
            },
            selectableUnfinishedWalks() {
                let options = [
                    {text: 'Runde wählen...', value: null}
                ];
                this.unfinishedWalks.forEach((walk) => {
                    options.push({ text: walk.name, value: walk });
                });

                return options;
            },
        },
        async created() {
            await this.$store.dispatch("team/findAll");
            if (this.selectableTeams.length) {
                this.selectedTeam = this.selectableTeams[0].value;
            }
        },
        methods: {
            handleWalkPrologue: async function () {
                this.$router.push({ name: 'WalkPrologue', params: {teamId: this.selectedTeam.id} })
            }
        }
    }
</script>

<style scoped>
</style>
