<template>
    <v-form
        @submit.prevent="handleSubmit"
        class="pa-1 pa-sm-2 pa-md-4 pa-lg-5 pa-xl-6 pa-xxl-7"
    >
        <v-card class="mb-4">
            <v-card-title class="grey lighten-2 mb-5">Allgemeine Daten des Teams</v-card-title>
            <v-card-text>
                <v-row>
                    <v-col
                        v-if="isSuperAdmin"
                        cols="12"
                    >
                        <v-select
                            v-model="team.client"
                            variant="outlined"
                            density="compact"
                            label="Klient"
                            data-test="clients"
                            item-value="@id"
                            item-title="name"
                            @change="team.users = []"
                            :items="availableClients"
                            :disabled="isDisabled"
                        />
                    </v-col>
                    <v-col sm="6">
                        <v-text-field
                            v-model="team.name"
                            label="Name"
                            variant="outlined"
                            density="compact"
                            :disabled="isDisabled"
                            :state="nameState"
                            :data-test="`${ initialTeam ? 'name-change' : 'name'}`"
                            trim
                        />
                    </v-col>
                    <v-col sm="6">
                        <walk-team-members-field
                            v-model="team.users"
                            :users="users"
                            :label="`Mitglieder`"
                            description=""
                        />
                        <v-alert
                            v-if="!users.length"
                            type="info"
                            class="mb-0"
                        >
                            Dieser Klient hat noch keine Benutzer.
                        </v-alert>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <v-card class="mb-4">
            <v-card-title class="grey lighten-2">Einstellungen für die Dokumentation einer Runde</v-card-title>
            <v-card-text class="grey lighten-3 pt-5">
                <v-row>
                    <v-col cols="12" md="3" lg="2">
                        <v-card outlined class="mb-2">
                            <v-card-subtitle class="font-weight-bold">Welche Mitglieder sollen beim Rundenstart vorausgewählt sein?</v-card-subtitle>
                            <v-card-text>
                                <v-radio-group
                                    v-model="team.initialMembersConfig"
                                    hide-details
                                >
                                    <v-radio
                                        label="nur der aktuelle Rundenersteller"
                                        value="rundenersteller"
                                        class="secondary--text"
                                    />
                                    <v-radio
                                        label="die Mitglieder, welche bei der letzten Runde dabei waren"
                                        value="mitglieder"
                                    />
                                </v-radio-group>
                            </v-card-text>
                        </v-card>
                    </v-col>
                    <v-col cols="12" md="4" lg="5">
                        <v-card outlined class="mb-0 pt-3">
                            <v-card-subtitle class="font-weight-bold">Autocomplete-Vorschläge für das Tageskonzept einer Runde</v-card-subtitle>
                            <v-card-text>
                                <v-list density="compact">
                                    <v-list-item
                                        v-for="(conceptOfDaySuggestion, i) in team.conceptOfDaySuggestions"
                                        :key="i"
                                        density="compact"
                                    >
                                        <v-text-field
                                            v-model="team.conceptOfDaySuggestions[i]"
                                            :disabled="isDisabled"
                                            type="text"
                                            :state="team.conceptOfDaySuggestions[i] === '' ? null : (team.conceptOfDaySuggestions[i].length > 1 && team.conceptOfDaySuggestions[i].length <= 300)"
                                            trim
                                            required
                                            density="compact"
                                            clearable
                                            hide-details
                                            variant="outlined"
                                            autocomplete="off"
                                            placeholder="neues Tageskonzept..."
                                        />
                                        <template v-slot:append>
                                            <v-btn
                                                class="ml-2"
                                                icon
                                                @click="removeConceptOfDaySuggestion(i)"
                                            >
                                                <v-icon
                                                    icon="mdi-trash-can"
                                                />
                                            </v-btn>
                                        </template>
                                    </v-list-item>
                                    <v-list-item density="compact">
                                        <div
                                            class="cursor-pointer mt-1 mb-2"
                                            @click="addConceptOfDaySuggestion()"
                                        >
                                            <mdicon
                                                name="PlusCircleOutline"
                                            />
                                            neuen Autocomplete-Vorschlag hinzufügen
                                        </div>
                                    </v-list-item>
                                    <v-list-subheader>Beim Erstellen der Runde ist eine Mehrfachauswahl sowie Freitexteingabe möglich.</v-list-subheader>
                                </v-list>
                            </v-card-text>
                        </v-card>
                    </v-col>
                    <v-col cols="12" md="5" lg="5">
                        <v-card outlined class="mb-0 pt-3">
                            <v-card-subtitle class="font-weight-bold">Autocomplete-Vorschläge für den Namen einer Runde</v-card-subtitle>
                            <v-card-text>
                                <v-list density="compact">
                                    <v-list-item
                                        v-for="(walkName, i) in team.walkNames"
                                        :key="i"
                                        density="compact"
                                    >
                                        <v-list-item-title>
                                            <v-text-field
                                                v-model="team.walkNames[i]"
                                                :disabled="isDisabled"
                                                type="text"
                                                :state="team.walkNames[i] === '' ? null : (team.walkNames[i].length > 1 && team.walkNames[i].length <= 300)"
                                                trim
                                                density="compact"
                                                variant="outlined"
                                                required
                                                hide-details
                                                clearable
                                                autocomplete="off"
                                                placeholder="neuer Rundenname..."
                                            />
                                        </v-list-item-title>
                                        <template v-slot:append>
                                            <v-btn
                                                icon
                                                class="ml-2"
                                                @click="removeWalkName(i)"
                                            >
                                                <v-icon
                                                    icon="mdi-trash-can"
                                                />
                                            </v-btn>
                                        </template>
                                    </v-list-item>
                                    <v-list-item>
                                        <div
                                            class="cursor-pointer mt-1 mb-2"
                                            @click="addWalkName()"
                                        >
                                            <mdicon
                                                name="PlusCircleOutline"
                                            />
                                            neuen Autocomplete-Vorschlag hinzufügen
                                        </div>
                                    </v-list-item>
                                    <v-list-subheader>Beim Erstellen der Runde ist eine Freitexteingabe zusätzlich möglich.</v-list-subheader>
                                </v-list>
                            </v-card-text>
                        </v-card>
                    </v-col>
                    <v-col cols="12">
                        <v-card outlined class="mb-0 pt-3">
                            <v-card-subtitle class="font-weight-bold  grey lighten-5">Optionale Felder - Welche Daten sollen zusätzlich mit erfasst werden?</v-card-subtitle>
                            <v-card-text class=" grey lighten-5">
                                <v-row>
                                    <v-col lg="6">
                                        <v-card outlined class="mb-0 pt-3">
                                            <v-card-text>
                                                <v-switch
                                                    v-model="team.isWithGuests"
                                                    :disabled="isDisabled"
                                                    label="Weitere Teilnehmende"
                                                    color="primary"
                                                    density="compact"
                                                />
                                                <div
                                                    v-if="team.isWithGuests"
                                                    @keyup.alt.a="addGuestName"
                                                >
                                                    <v-card-subtitle class="font-weight-bold pb-1">Autocomplete-Vorschläge für weitere Teilnehmende</v-card-subtitle>
                                                    <v-divider class="mt-0 mb-0"></v-divider>
                                                    <v-list density="compact">
                                                        <v-list-item
                                                            v-for="(guestName, i) in team.guestNames"
                                                            :key="i"
                                                        >
                                                            <v-text-field
                                                                v-model="team.guestNames[i]"
                                                                :disabled="isDisabled"
                                                                type="text"
                                                                :state="team.guestNames[i] === '' ? null : (team.guestNames[i].length > 1 && team.guestNames[i].length <= 300)"
                                                                trim
                                                                ref="guestNameInputs"
                                                                required
                                                                density="compact"
                                                                clearable
                                                                hide-details
                                                                variant="outlined"
                                                                autocomplete="off"
                                                                placeholder="Vorname, Nachname, Pseudonym"
                                                            />
                                                            <template v-slot:append>
                                                                <v-btn
                                                                    icon
                                                                    class="ml-2"
                                                                    @click="removeGuestName(i)"
                                                                >
                                                                    <v-icon
                                                                        icon="mdi-trash-can"
                                                                    />
                                                                </v-btn>
                                                            </template>
                                                        </v-list-item>
                                                        <v-list-item>
                                                            <div
                                                                class="cursor-pointer mt-1 mb-2"
                                                                @click="addGuestName()"
                                                            >
                                                                <mdicon
                                                                    name="PlusCircleOutline"
                                                                />
                                                                neuen Autocomplete-Vorschl<u>a</u>g hinzufügen
                                                            </div>
                                                        </v-list-item>
                                                        <v-list-item>
                                                            <v-list-subheader>Beim Erstellen der Runde ist es zusätzlich zu den Autocomplete-Vorschlägen möglich eigene weitere
                                                                Teilnehmende einzugeben.
                                                            </v-list-subheader>
                                                        </v-list-item>
                                                    </v-list>
                                                </div>
                                            </v-card-text>
                                        </v-card>
                                    </v-col>
                                    <v-col md="6">
                                        <v-card outlined>
                                            <v-card-text>
                                                <v-switch
                                                    v-model="team.isWithSystemicQuestion"
                                                    :disabled="isDisabled"
                                                    color="primary"
                                                    label="Systemische Frage und Antwort darauf"
                                                    density="compact"
                                                />
                                                <v-alert
                                                    v-if="team.isWithSystemicQuestion"
                                                    class="text-muted mb-0"
                                                    variant="text"
                                                >
                                                    <b>Hinweis:</b>
                                                    <ul class="mb-0">
                                                        <li>
                                                            Beim Abschluss einer Runde gibt es ein Reflexionsprotokoll mit einer systemischen Reflexionsfrage, welche u.a. einen
                                                            psychohygienischen Beitrag zum Abschluss der Streetwork leistet.
                                                        </li>
                                                        <li>
                                                            Dazu wird bei Abschluss einer Runde zufällig eine dieser Fragen gestellt, welche beantwortet werden muss.
                                                        </li>
                                                        <li>
                                                            Diese Fragen können im Navigations-Tab "Systemische Fragen" übergreifend für alle Teams definiert werden.
                                                        </li>
                                                    </ul>
                                                </v-alert>
                                            </v-card-text>
                                        </v-card>
                                    </v-col>
                                </v-row>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>
        <v-card class="mb-4">
            <v-card-title class="grey lighten-2">Einstellungen für die Dokumentation eines Wegpunktes</v-card-title>
            <v-card-text class="grey lighten-4 pt-2">
                <v-card outlined class="mb-0 pt-3">
                    <v-card-subtitle class="font-weight-bold">Autocomplete-Vorschläge für den Ort eines Wegpunktes</v-card-subtitle>
                    <v-card-text>
                        <v-list density="compact">
                            <v-list-item
                                v-for="(locationName, i) in team.locationNames"
                                :key="i"
                                density="compact"
                            >
                                <v-text-field
                                    v-model="team.locationNames[i]"
                                    :disabled="isDisabled"
                                    type="text"
                                    :state="team.locationNames[i] === '' ? null : (team.locationNames[i].length > 1 && team.locationNames[i].length <= 300)"
                                    trim
                                    required
                                    clearable
                                    hide-details
                                    variant="outlined"
                                    density="compact"
                                    autocomplete="off"
                                    placeholder="neuer Ort..."
                                />
                                <template v-slot:append>
                                    <v-btn
                                        icon
                                        class="ml-2"
                                        @click="removeLocationName(i)"
                                    >
                                        <v-icon
                                            icon="mdi-trash-can"
                                        />
                                    </v-btn>
                                </template>
                            </v-list-item>
                            <v-list-item>
                                <div
                                    class="cursor-pointer mt-1"
                                    @click="addLocationName()"
                                >
                                    <mdicon
                                        name="PlusCircleOutline"
                                    />
                                    neuen Autocomplete-Vorschlag hinzufügen
                                </div>
                            </v-list-item>
                        </v-list>
                    </v-card-text>
                </v-card>
                <v-card class="mt-3 mb-0 pt-3">
                    <v-card-subtitle class="font-weight-bold grey lighten-5">Optionale Felder - Welche Daten sollen zusätzlich mit erfasst werden?</v-card-subtitle>
                    <v-card-text class="grey lighten-5">
                        <v-row>
                            <v-col cols="12" md="6" lg="2">
                                <v-card outlined class="mb-0 pt-3">
                                    <v-card-text>
                                        <v-switch
                                            v-model="team.isWithContactsCount"
                                            :disabled="isDisabled"
                                            color="primary"
                                            label="Anzahl direkter Kontakte"
                                            density="compact"
                                        />
                                        <div class="text-muted">
                                            Eine Person gilt als direkter Kontakt, wenn mit ihr an diesem Wegpunkt gesprochen wurde.
                                        </div>
                                    </v-card-text>
                                </v-card>
                            </v-col>
                            <v-col cols="12" md="6" lg="5">
                                <v-card outlined class="mb-0 pt-3">
                                    <v-card-text>
                                        <v-switch
                                            v-model="team.isWithPeopleCount"
                                            :disabled="isPeopleCountDisabled"
                                            color="primary"
                                            label="Anzahl Personen vor Ort"
                                        />
                                        <v-switch
                                            v-model="team.isWithAgeRanges"
                                            :disabled="isDisabled"
                                            color="primary"
                                            label="Altersgruppen"
                                        />
                                        <v-card-subtitle v-if="team.isWithAgeRanges" class="font-weight-bold mb-0 pb-1">Altersgruppen definieren</v-card-subtitle>
                                        <v-divider v-if="team.isWithAgeRanges" class="mt-0 mb-0"></v-divider>
                                        <v-list
                                            v-if="team.isWithAgeRanges"
                                            density="compact"
                                        >
                                            <v-list-item
                                                v-for="(ageRange, i) in team.ageRanges"
                                                :key="i"
                                            >
                                                <v-list-item-title>{{ ageRange.rangeStart }} - {{ ageRange.rangeEnd }} Jahre</v-list-item-title>
                                                <v-row no-gutters>
                                                    <v-col cols="6">
                                                        <v-text-field
                                                            v-model="team.ageRanges[i].rangeStart"
                                                            :disabled="isDisabled"
                                                            type="number"
                                                            min="0"
                                                            max="120"
                                                            trim
                                                            number
                                                            density="compact"
                                                            variant="outlined"
                                                            hide-details
                                                            step="1"
                                                            required
                                                            placeholder="von"
                                                            label="von"
                                                            class="mr-2"
                                                        />
                                                    </v-col>
                                                    <v-col cols="6">
                                                        <v-text-field
                                                            v-model="team.ageRanges[i].rangeEnd"
                                                            :disabled="isDisabled"
                                                            type="number"
                                                            min="0"
                                                            max="120"
                                                            trim
                                                            number
                                                            variant="outlined"
                                                            hide-details
                                                            step="1"
                                                            required
                                                            placeholder="bis"
                                                            label="bis"
                                                        />
                                                    </v-col>
                                                </v-row>
                                                <template v-slot:append>
                                                    <v-btn
                                                        icon
                                                        class="ml-2 mt-4"
                                                        @click="removeAgeRange(i)"
                                                    >
                                                        <v-icon
                                                            icon="mdi-trash-can"
                                                        />
                                                    </v-btn>
                                                </template>
                                            </v-list-item>

                                            <v-list-item>
                                                <div
                                                    class="cursor-pointer mt-1 mb-2"
                                                    @click="addAgeRange()"
                                                >
                                                    <mdicon
                                                        name="PlusCircleOutline"
                                                    />
                                                    neue Altersgruppe hinzufügen
                                                </div>
                                            </v-list-item>
                                        </v-list>
                                        <div class="text-muted">
                                            Wenn Altersgruppen gewählt sind, wird die Anzahl der Personen daraus automatisch ermittelt.
                                        </div>
                                    </v-card-text>
                                </v-card>
                            </v-col>
                            <v-col cols="12" md="6" lg="5">
                                <v-card outlined class="mb-0 pt-3">
                                    <v-card-text>
                                        <v-switch
                                            v-model="team.isWithUserGroups"
                                            :disabled="isDisabled"
                                            color="primary"
                                            label="Personenanzahl von Nutzergruppen"
                                            density="compact"
                                        />
                                        <v-card-subtitle v-if="team.isWithUserGroups" class="font-weight-bold pb-1">Nutzergruppen definieren</v-card-subtitle>
                                        <v-divider v-if="team.isWithUserGroups" class="mt-0 mb-0"></v-divider>
                                        <v-list v-if="team.isWithUserGroups">
                                            <v-list-item
                                                v-for="(userGroupName, i) in team.userGroupNames"
                                                density="compact"
                                                :key="i"
                                            >
                                                <v-text-field
                                                    v-model="team.userGroupNames[i].name"
                                                    :disabled="isDisabled"
                                                    type="text"
                                                    :state="team.userGroupNames[i].name === '' ? null : (team.userGroupNames[i].name.length > 1 && team.userGroupNames[i].name.length <= 300)"
                                                    trim
                                                    required
                                                    variant="outlined"
                                                    density="compact"
                                                    clearable
                                                    hide-details
                                                    autocomplete="off"
                                                    placeholder="Name der Nutzergruppe eingeben..."
                                                />
                                                <template v-slot:append>
                                                    <v-col>
                                                        <v-btn
                                                            icon
                                                            @click="removeUserGroupName(i)"
                                                        >
                                                            <v-icon
                                                                icon="mdi-trash-can"
                                                            />
                                                        </v-btn>
                                                        <v-btn
                                                            v-if="i !== 0"
                                                            icon
                                                            class="ml-2"
                                                            @click="moveUserGroupUp(i)"
                                                        >
                                                            <v-icon
                                                                icon="mdi-arrow-up-drop-circle-outline"
                                                            />
                                                        </v-btn>
                                                        <v-btn
                                                            v-if="i !== (team.userGroupNames.length - 1)"
                                                            icon
                                                            class="ml-2"
                                                            @click="moveUserGroupDown(i)"
                                                        >
                                                            <v-icon
                                                                icon="mdi-arrow-down-drop-circle-outline"
                                                            />
                                                        </v-btn>
                                                    </v-col>
                                                </template>
                                            </v-list-item>
                                            <v-list-item>
                                                <div
                                                    class="cursor-pointer mt-1 mb-2"
                                                    @click="addUserGroupName()"
                                                >
                                                    <mdicon
                                                        name="PlusCircleOutline"
                                                    />
                                                    neuen Autocomplete-Vorschlag hinzufügen
                                                </div>
                                            </v-list-item>
                                        </v-list>
                                        <v-alert
                                            v-if="team.isWithUserGroups"
                                            class="text-muted mb-0"
                                            variant="text"
                                        >
                                            Beispiele für Nutzergruppen sind:
                                            <ul class="pl-5 mb-0">
                                                <li>Aktuell Nutzende</li>
                                                <li>jemals genutzt - nutzungsberechtigt</li>
                                                <li>jemals genutzt - nicht nutzungsberechtigt</li>
                                                <li>nie genutzt - nutzungsberechtigt</li>
                                                <li>nie genutzt - nicht nutzungsberechtigt</li>
                                            </ul>
                                            Alternativ können auch herausgegegebene Utensilien erfasst werden:
                                            <ul class="pl-5 mb-0">
                                                <li>Spritzenvergabe</li>
                                                <li>Alkoholtupfer</li>
                                                <li>Filteraufsätze</li>
                                                <li>sterile Wasserampullen</li>
                                                <li>Einmallöffel</li>
                                                <li>Kondome</li>
                                            </ul>
                                            Hinweis: Die Werte werden beim Runden-CSV-Export zusammenaddiert.
                                        </v-alert>
                                    </v-card-text>
                                </v-card>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-card-text>
        </v-card>
        <v-btn
            type="submit"
            color="secondary"
            class="btn btn-secondary"
            :data-test="`${ initialTeam ? 'button-team-form-change' : 'button-team-form-create'}`"
            block
            :disabled="isFormInvalid || isDisabled"
            :tabindex="isFormInvalid ? '-1' : ''"
        >
            {{ buttonLabel }}
        </v-btn>
        <form-error
            :error="error"
        />
    </v-form>
</template>

<script>
'use strict'
import FormError from '../Common/FormError.vue'
import {useAuthStore, useClientStore, useTeamStore, useUserStore} from '../../stores';
import {WalkTeamMembersField} from "../Common/Walk";

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
        },
    },
    components: {
        WalkTeamMembersField,
        FormError,
    },
    data: function () {
        let isWithPeopleCountDefault = true;

        return {
            authStore: useAuthStore(),
            clientStore: useClientStore(),
            teamStore: useTeamStore(),
            userStore: useUserStore(),
            items: ['Arial', 'Calibri', 'Courier', 'Verdana'],
            team: {
                team: null,
                client: '',
                name: '',
                initialMembersConfig: 'rundenersteller',
                isWithAgeRanges: !isWithPeopleCountDefault,
                isWithPeopleCount: isWithPeopleCountDefault,
                isWithContactsCount: false,
                isWithGuests: false,
                isWithSystemicQuestion: false,
                isWithUserGroups: false,
                ageRanges: [],
                locationNames: [],
                walkNames: [],
                conceptOfDaySuggestions: [],
                guestNames: [],
                userGroupNames: [],
            },
            client: null,
        }
    },
    computed: {
        users() {
            return this.userStore.getUsers.slice(0).filter(user => {
                return user.client === this.team.client
            }).sort((a, b) => {
                return (a.username.toLowerCase() > b.username.toLowerCase()) ? 1 : -1
            })
        },
        hasDisabledUser() {
            return this.users.some(user => !user.isEnabled);
        },
        isPeopleCountDisabled() {
            return this.team.isWithAgeRanges || this.isDisabled;
        },
        isDisabled() {
            return this.teamStore.isLoadingCreate || this.teamStore.isLoadingChange(this.team['@id']);
        },
        nameState() {
            if (null === this.team.name || '' === this.team.name) {
                return
            }

            return this.team.name.length >= 3 && this.team.name.length <= 100
        },
        isLoading() {
            return this.teamStore.isLoading
        },
        currentUser() {
            return this.authStore.currentUser
        },
        isSuperAdmin() {
            return this.authStore.isSuperAdmin
        },
        isFormInvalid() {
            return !(this.nameState && this.team.client && !this.isLoading)
        },
        error() {
            return this.teamStore.getErrors.change;
        },
        availableClients() {
            return this.clientStore.getClients;
        },
    },
    created() {
        this.setInitialValues();
        this.team.client = this.team.client || this.currentUser.client;
        this.userStore.fetchUsers();
    },
    watch: {
        'team.isWithAgeRanges': function (newValue) {
            if (newValue) {
                this.team.isWithPeopleCount = true;
            }
        },
    },
    methods: {
        async handleSubmit() {
            if (this.isFormInvalid) {
                return false
            }
            this.$emit('submitted', this.team)
        },
        removeAgeRange(index) {
            this.team.ageRanges.splice(index, 1)
        },
        addAgeRange() {
            this.team.ageRanges = [...this.team.ageRanges, {rangeStart: '', rangeEnd: ''}]
        },
        removeLocationName(index) {
            this.team.locationNames.splice(index, 1)
        },
        addLocationName() {
            this.team.locationNames = [...this.team.locationNames, '']
        },
        removeWalkName(index) {
            this.team.walkNames.splice(index, 1)
        },
        addWalkName() {
            this.team.walkNames = [...this.team.walkNames, '']
        },
        removeConceptOfDaySuggestion(index) {
            this.team.conceptOfDaySuggestions.splice(index, 1)
        },
        addConceptOfDaySuggestion() {
            this.team.conceptOfDaySuggestions = [...this.team.conceptOfDaySuggestions, '']
        },
        removeGuestName(index) {
            this.team.guestNames.splice(index, 1)
        },
        addGuestName() {
            this.team.guestNames = [...this.team.guestNames, ''];
            this.$nextTick(() => {
                this.$refs.guestNameInputs[this.$refs.guestNameInputs.length - 1].focus();
            });
        },
        removeUserGroupName(index) {
            this.team.userGroupNames.splice(index, 1)
        },
        addUserGroupName() {
            this.team.userGroupNames = [...this.team.userGroupNames, {name: ''}]
        },
        moveUserGroupUp(index) {
            const tempUserGroupName = this.team.userGroupNames[index]
            let newUserGroups = []
            this.team.userGroupNames.splice(index, 1)
            this.team.userGroupNames.forEach((userGroupName, key) => {
                if (key === index - 1) {
                    newUserGroups.push(tempUserGroupName)
                }
                newUserGroups.push(userGroupName)
            })
            this.team.userGroupNames = newUserGroups
        },
        moveUserGroupDown(index) {
            const tempUserGroupName = this.team.userGroupNames[index]
            let newUserGroups = []
            this.team.userGroupNames.splice(index, 1)
            this.team.userGroupNames.forEach((userGroupName, key) => {
                newUserGroups.push(userGroupName)
                if (key === index) {
                    newUserGroups.push(tempUserGroupName)
                }
            })
            this.team.userGroupNames = newUserGroups
        },
        setInitialValues() {
            if (this.initialTeam) {
                this.team = JSON.parse(JSON.stringify(this.initialTeam));
            } else {
                this.team.name = null;
                this.team.isWithAgeRanges = false;
                this.team.isWithPeopleCount = false;
                this.team.isWithContactsCount = false;
                this.team.isWithGuests = false;
                this.team.isWithSystemicQuestion = false;
                this.team.isWithUserGroups = false;
                this.team.initialMembersConfig = 'rundenersteller';
                this.team.users = [];
                this.team.ageRanges = [];
                this.team.locationNames = [];
                this.team.walkNames = [];
                this.team.conceptOfDaySuggestions = [];
                this.team.guestNames = [];
                this.team.userGroupNames = [];
            }
            this.team.client = this.team.client || this.currentUser.client;
        },
        resetForm() {
            this.$refs.form.reset();
            this.setInitialValues();
        },
    },
}
</script>

<style scoped lang="scss">

</style>
