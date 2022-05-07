<template>
    <b-form
        @submit.prevent.stop="handleSubmit"
        class="p-1 p-sm-2 p-lg-3"
    >
        <div class="card-columns">
            <b-card
                bg-variant="light"
                header="Allgemeine Daten"
            >
                <b-form-group
                    label="Teamname"
                    v-slot="{ ariaDescribedby }"
                    :state="nameState"
                >
                    <b-input
                        v-model="team.name"
                        :aria-describedby="ariaDescribedby"
                        :disabled="isDisabled"
                        :state="nameState"
                        trim
                    />
                </b-form-group>

                <b-form-group
                    v-if="isSuperAdmin"
                    label="Klient"
                >
                    <b-form-select
                        v-model="team.client"
                        data-test="clients"
                        value-field="@id"
                        text-field="name"
                        @change="team.users = []"
                        :options="availableClients"
                        :disabled="isDisabled"
                    />
                </b-form-group>

                <b-form-group
                    label="Benutzer"
                    v-slot="{ ariaDescribedby }"
                >
                    <b-form-checkbox-group
                        v-model="team.users"
                        class="check-boxes d-flex flex-row flex-wrap justify-content-start"
                        switch
                        data-test="users"
                        button-variant="secondary rounded-0 mt-1 mr-1 px-4"
                        :options="users"
                        :aria-describedby="ariaDescribedby"
                        name="users"
                        :disabled="isDisabled"
                        value-field="@id"
                        text-field="username"
                    >
                    </b-form-checkbox-group>
                    <b-alert
                        v-model="users.length === 0"
                        class="mb-0"
                    >
                        Dieser Klient hat noch keine Benutzer.
                    </b-alert>
                </b-form-group>
            </b-card>

            <b-card
                bg-variant="light"
                header="Einstellungen für die Dokumentation eines Wegpunktes"
            >
                <b-form-group
                    label="Optionale Felder"
                >
                    <b-form-checkbox
                        v-model="team.isWithContactsCount"
                        :disabled="isDisabled"
                        switch
                    >
                        Die Anzahl direkter Kontakte eines Wegpunktes soll mit erfasst werden.
                    </b-form-checkbox>
                    <b-form-text>
                        Eine Person gilt als direkter Kontakt, wenn mit ihr an diesem Wegpunkt gesprochen wurde.
                    </b-form-text>
                </b-form-group>

                <b-form-group
                    label="Autocomplete-Vorschläge für den Ort eines Wegpunktes"
                    v-slot="{ ariaDescribedby }"
                >
                    <b-row
                        v-for="(locationName, i) in team.locationNames"
                        :key="i"
                    >
                        <b-col cols="8" class="mb-1">
                            <b-input
                                v-model="team.locationNames[i]"
                                :aria-describedby="ariaDescribedby"
                                :disabled="isDisabled"
                                type="text"
                                :state="team.locationNames[i] === '' ? null : (team.locationNames[i].length > 1 && team.locationNames[i].length <= 300)"
                                trim
                                required
                                autocomplete="new-password"
                                placeholder="neuer Ort..."
                            />
                        </b-col>
                        <b-col cols="3" class="mb-1">
                            <div
                                class="cursor-pointer mt-1"
                                @click="removeLocationName(i)"
                            >
                                <mdicon
                                    name="DeleteCircleOutline"
                                />
                            </div>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col cols="12">
                            <div
                                class="cursor-pointer mt-1"
                                @click="addLocationName()"
                            >
                                <mdicon
                                    name="PlusCircleOutline"
                                />
                                neuen Autocomplete-Vorschlag hinzufügen
                            </div>
                        </b-col>
                    </b-row>
                </b-form-group>
                <b-form-group
                    label="Altersgruppen"
                    v-slot="{ ariaDescribedby }"
                >
                    <b-row
                        v-for="(ageRange, i) in team.ageRanges"
                        :key="i"
                    >
                        <b-col cols="12">
                            {{ ageRange.rangeStart }} - {{ ageRange.rangeEnd }} Jahre
                        </b-col>
                        <b-col cols="4" class="mb-2">
                            <b-input
                                v-model="team.ageRanges[i].rangeStart"
                                :aria-describedby="ariaDescribedby"
                                :disabled="isDisabled"
                                type="number"
                                min="0"
                                max="120"
                                trim
                                number
                                step="1"
                                required
                                placeholder="von"
                            />
                        </b-col>
                        <b-col cols="4" class="mb-2">
                            <b-input
                                v-model="team.ageRanges[i].rangeEnd"
                                :aria-describedby="ariaDescribedby"
                                :disabled="isDisabled"
                                type="number"
                                min="0"
                                max="120"
                                trim
                                number
                                step="1"
                                required
                                placeholder="bis"
                            />
                        </b-col>
                        <b-col cols="3">
                            <div
                                class="cursor-pointer mt-2"
                                @click="removeAgeRange(i)"
                            >
                                <mdicon
                                    name="DeleteCircleOutline"
                                />
                            </div>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col cols="12">
                            <div
                                class="cursor-pointer mt-1"
                                @click="addAgeRange()"
                            >
                                <mdicon
                                    name="PlusCircleOutline"
                                />
                                neue Altersgruppe hinzufügen
                            </div>
                        </b-col>
                    </b-row>
                </b-form-group>
            </b-card>
        </div>
        <b-button
            type="submit"
            variant="secondary"
            data-test="button-team-form"
            block
            class="col-12"
            :tabindex="isFormInvalid ? '-1' : ''"
        >
            {{ buttonLabel }}
        </b-button>
        <form-error
            :error="error"
        />
    </b-form>
</template>

<script>
'use strict';
import FormError from '../Common/FormError.vue';

export default {
    name: 'TeamForm',
    props: {
        initialTeam: {
            type: Object,
            required: false,
            default: null,
        },
        buttonLabel: {
            type: String,
            required: true,
        }
    },
    components: {
        FormError,
    },
    data: function () {
        return {
            team: {
                team: '',
                client: '',
                name: '',
                users: [],
                ageRanges: [],
                locationNames: [],
            },
            client: null,
        };
    },
    computed: {
        users() {
            return this.$store.getters['user/users']
                .slice(0)
                .filter(user => {
                    return user.client === this.team.client;
                })
                .sort((a, b) => {
                    return (a.username.toLowerCase() > b.username.toLowerCase()) ? 1 : -1;
                });
        },
        isDisabled() {
            return this.$store.getters["team/changeTeamIsLoading"];
        },
        colors() {
            return this.$store.getters['tag/tags'].map(tag => tag.color);
        },
        names() {
            return this.$store.getters['tag/tags'].map(tag => tag.name);
        },
        colorState() {
            if (null === this.color) {
                return;
            }

            return -1 === this.colors.indexOf(this.color);
        },
        nameState() {
            if (null === this.team.name || '' === this.team.name) {
                return;
            }

            return this.team.name.length >= 3 && this.team.name.length <= 100;
        },
        isLoading() {
            return this.$store.getters['team/isLoading'];
        },
        currentUser() {
            return this.$store.getters['security/currentUser'];
        },
        isSuperAdmin() {
            return this.$store.getters['security/isSuperAdmin'];
        },
        isFormInvalid() {
            return !(this.nameState && this.team.client && !this.isLoading);
        },
        error() {
            return this.$store.getters['team/changeTeamError'];
        },
        availableClients() {
          return this.$store.getters['client/clients']  ;
        },
    },
    created() {
        if (this.initialTeam) {
            this.team = JSON.parse(JSON.stringify(this.initialTeam));
        }
        this.team.client = this.team.client || this.currentUser.client;
    },
    methods: {
        async handleSubmit() {
            if (this.isFormInvalid) {
                return false;
            }
            this.$emit('submit', this.team);
        },
        removeAgeRange(index) {
            this.team.ageRanges.splice(index, 1);
        },
        addAgeRange() {
            this.team.ageRanges = [ ...this.team.ageRanges, { rangeStart: '', rangeEnd: '' } ];
        },
        removeLocationName(index) {
            this.$delete(this.team.locationNames, index);
        },
        addLocationName() {
            this.team.locationNames = [ ...this.team.locationNames, '' ];
        },
    },
};
</script>

<style scoped lang="scss">

</style>
