<template>
    <b-form
        @submit.prevent.stop="handleSubmit"
        ref="form"
        class="p-1 p-sm-2 p-lg-3"
    >
        <b-card
            bg-variant="light"
            header="Allgemeine Daten des Teams"
            class="mb-4"
        >

            <b-row>
                <b-col sm="12">
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
                </b-col>
                <b-col sm="6">
                    <b-form-group
                        label="Name"
                        v-slot="{ ariaDescribedby }"
                        :state="nameState"
                        content-cols="12"
                    >
                        <b-input
                            v-model="team.name"
                            :aria-describedby="ariaDescribedby"
                            :disabled="isDisabled"
                            :state="nameState"
                            data-test="name"
                            trim
                        />
                    </b-form-group>
                </b-col>
                <b-col sm="6">
                    <b-form-group
                        label="Mitglieder"
                        v-slot="{ ariaDescribedby }"
                        content-cols="12"
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
                </b-col>
            </b-row>
        </b-card>

        <b-card
            bg-variant="light"
            header="Einstellungen für die Dokumentation einer Runde"
            class="mb-4"
        >
            <b-form-group
                label="Autocomplete-Vorschläge für den Namen einer Runde"
                description="Eine Freitexteingabe ist zusätzlich möglich."
                v-slot="{ ariaDescribedby }"
            >
                <b-row
                    v-for="(walkName, i) in team.walkNames"
                    :key="i"
                >
                    <b-col cols="8" class="mb-1">
                        <b-input
                            v-model="team.walkNames[i]"
                            :aria-describedby="ariaDescribedby"
                            :disabled="isDisabled"
                            type="text"
                            :state="team.walkNames[i] === '' ? null : (team.walkNames[i].length > 1 && team.walkNames[i].length <= 300)"
                            trim
                            required
                            autocomplete="off"
                            placeholder="neuer Rundenname..."
                        />
                    </b-col>
                    <b-col cols="3" class="mb-1">
                        <div
                            class="cursor-pointer mt-1"
                            @click="removeWalkName(i)"
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
                            @click="addWalkName()"
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
                label="Autocomplete-Vorschläge für das Tageskonzept einer Runde"
                description="Eine Mehrfachauswahl sowie Freitexteingabe ist möglich."
                v-slot="{ ariaDescribedby }"
            >
                <b-row
                    v-for="(conceptOfDaySuggestion, i) in team.conceptOfDaySuggestions"
                    :key="i"
                >
                    <b-col cols="8" class="mb-1">
                        <b-input
                            v-model="team.conceptOfDaySuggestions[i]"
                            :aria-describedby="ariaDescribedby"
                            :disabled="isDisabled"
                            type="text"
                            :state="team.conceptOfDaySuggestions[i] === '' ? null : (team.conceptOfDaySuggestions[i].length > 1 && team.conceptOfDaySuggestions[i].length <= 300)"
                            trim
                            required
                            autocomplete="off"
                            placeholder="neues Tageskonzept..."
                        />
                    </b-col>
                    <b-col cols="3" class="mb-1">
                        <div
                            class="cursor-pointer mt-1"
                            @click="removeConceptOfDaySuggestion(i)"
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
                            @click="addConceptOfDaySuggestion()"
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
                label="Optionale Felder - Welche Daten sollen zusätzlich mit erfasst werden?"
                class="mb-0"
            >
                <b-row>
                    <b-col
                        lg="6"
                    >
                        <b-card
                            bg-variant="light"
                            no-body
                        >
                            <b-card-header>
                                <b-form-checkbox
                                    v-model="team.isWithGuests"
                                    :disabled="isDisabled"
                                    switch
                                >
                                    Weitere Teilnehmende
                                </b-form-checkbox>
                            </b-card-header>
                            <b-card-body
                                v-if="team.isWithGuests"
                                @keyup.alt.a="addGuestName"
                                tabindex="0"
                            >
                                <b-form-group
                                    label="Autocomplete-Vorschläge für die weitere Teilnehmende"
                                    v-slot="{ ariaDescribedby }"
                                    description="Zusätzlich zu den Autocomplete-Vorschlägen ist es auch möglich bei der Rundenerstellung eigene weitere Teilnehmende einzugeben."
                                    class="mb-0"
                                >
                                    <b-row
                                        v-for="(guestName, i) in team.guestNames"
                                        :key="i"
                                    >
                                        <b-col cols="10" class="mb-1">
                                            <b-input
                                                v-model="team.guestNames[i]"
                                                :aria-describedby="ariaDescribedby"
                                                :disabled="isDisabled"
                                                type="text"
                                                :state="team.guestNames[i] === '' ? null : (team.guestNames[i].length > 1 && team.guestNames[i].length <= 300)"
                                                trim
                                                ref="guestNameInputs"
                                                required
                                                autocomplete="off"
                                                placeholder="Vorname, Nachname, Pseudonym"
                                            />
                                        </b-col>
                                        <b-col cols="2" class="mb-1">
                                            <div
                                                class="cursor-pointer mt-1"
                                                @click="removeGuestName(i)"
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
                                                @click="addGuestName()"
                                            >
                                                <mdicon
                                                    name="PlusCircleOutline"
                                                />
                                                neuen Autocomplete-Vorschl<u>a</u>g hinzufügen
                                            </div>
                                        </b-col>
                                    </b-row>
                                </b-form-group>
                            </b-card-body>
                        </b-card>
                    </b-col>
                    <b-col
                        lg="6"
                    >
                        <b-card
                            bg-variant="light"
                            no-body
                        >
                            <b-card-header>
                                <b-form-checkbox
                                    v-model="team.isWithSystemicQuestion"
                                    :disabled="isDisabled"
                                    switch
                                >
                                    Systemische Frage und Antwort darauf
                                </b-form-checkbox>
                            </b-card-header>
                            <b-card-body
                                v-if="team.isWithSystemicQuestion"
                                tabindex="0"
                                class="p-0"
                            >
                                <b-alert
                                    show
                                    class="w-100 text-muted mb-0"
                                    variant="debug"
                                >
                                    <b>Hinweis:</b>
                                    <ul class="mb-0">
                                        <li>
                                            Beim Abschluss einer Runde gibt es ein Reflexionsprotokoll mit einer systemischen Reflexionsfrage, welche u.a. einen
                                            "psychohygienischen" Beitrag zum Abschluss der Streetwork leistet.
                                        </li>
                                        <li>
                                            Dazu wird bei Abschluss einer Runde zufällig eine dieser Fragen gestellt, welche beantwortet werden muss.
                                        </li>
                                        <li>
                                            Diese Fragen können im Navigations-Tab "Systemische Fragen" übergreifend für alle Teams definiert werden.
                                        </li>
                                    </ul>
                                </b-alert>
                            </b-card-body>
                        </b-card>
                    </b-col>
                </b-row>
            </b-form-group>
        </b-card>

        <b-card
            bg-variant="light"
            header="Einstellungen für die Dokumentation eines Wegpunktes"
            class="mb-4"
        >
            <b-form-group
                label="Optionale Felder - Welche Daten sollen zusätzlich mit erfasst werden?"
                class="mb-0"
            >
                <b-row>
                    <b-col
                        md="6"
                        lg="4"
                        class="mb-2"
                    >
                        <b-card
                            bg-variant="light"
                            no-body
                        >
                            <b-card-header>
                                <b-form-checkbox
                                    v-model="team.isWithContactsCount"
                                    :disabled="isDisabled"
                                    switch
                                >
                                    Anzahl direkter Kontakte
                                </b-form-checkbox>
                                <b-form-text>
                                    Eine Person gilt als direkter Kontakt, wenn mit ihr an diesem Wegpunkt gesprochen wurde.
                                </b-form-text>
                            </b-card-header>
                        </b-card>
                    </b-col>
                    <b-col
                        md="6"
                        lg="4"
                        class="mb-2"
                    >
                        <b-card
                            bg-variant="light"
                            no-body
                        >
                            <b-card-header>
                                <b-form-checkbox
                                    v-model="team.isWithPeopleCount"
                                    :disabled="isPeopleCountDisabled"
                                    switch
                                >
                                    Anzahl Personen vor Ort
                                </b-form-checkbox>
                                <b-form-checkbox
                                    v-model="team.isWithAgeRanges"
                                    :disabled="isDisabled"
                                    switch
                                >
                                    Altersgruppen
                                </b-form-checkbox>
                                <b-form-text>
                                    Wenn Altersgruppen gewählt sind, wird die Anzahl der Personen daraus automatisch ermittelt.
                                </b-form-text>
                            </b-card-header>
                            <b-card-body
                                v-if="team.isWithAgeRanges"
                            >
                                <b-form-group
                                    label="Altersgruppen definieren"
                                    v-slot="{ ariaDescribedby }"
                                    class="mb-0"
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
                            </b-card-body>
                        </b-card>
                    </b-col>
                    <b-col
                        md="6"
                        lg="4"
                        class="mb-2"
                    >
                        <b-card
                            bg-variant="light"
                            no-body
                        >
                            <b-card-header>
                                <b-form-checkbox
                                    v-model="team.isWithUserGroups"
                                    :disabled="isDisabled"
                                    switch
                                >
                                    Personenanzahl von Nutzergruppen
                                </b-form-checkbox>
                            </b-card-header>
                            <b-card-body
                                v-if="team.isWithUserGroups"
                                header="Nutzergruppen definieren"
                            >
                                <b-form-group
                                    label="Nutzergruppen definieren"
                                    class="mb-0"
                                >
                                    <template #description>
                                        <b-form-text>
                                            Beispiele für Nutzergruppen sind:
                                            <ul class="mb-0">
                                                <li>Aktuell Nutzende</li>
                                                <li>jemals genutzt - nutzungsberechtigt</li>
                                                <li>jemals genutzt - nicht nutzungsberechtigt</li>
                                                <li>nie genutzt - nutzungsberechtigt</li>
                                                <li>nie genutzt - nicht nutzungsberechtigt</li>
                                            </ul>
                                            Alternativ können auch herausgegegebene Utensilien erfasst werden:
                                            <ul class="mb-0">
                                                <li>Spritzenvergabe</li>
                                                <li>Alkoholtupfer</li>
                                                <li>Filteraufsätze</li>
                                                <li>sterile Wasserampullen</li>
                                                <li>Einmallöffel</li>
                                                <li>Kondome</li>
                                            </ul>
                                            Hinweis: Die Werte werden beim Runden-CSV-Export zusammenaddiert.
                                        </b-form-text>
                                    </template>
                                    <template
                                        #default
                                        v-slot="ariaDescribedby"
                                    >
                                        <b-row
                                            v-for="(userGroupName, i) in team.userGroupNames"
                                            :key="i"
                                        >
                                            <b-col cols="8" class="mb-1">
                                                <b-input
                                                    v-model="team.userGroupNames[i].name"
                                                    :disabled="isDisabled"
                                                    type="text"
                                                    :state="team.userGroupNames[i].name === '' ? null : (team.userGroupNames[i].name.length > 1 && team.userGroupNames[i].name.length <= 300)"
                                                    trim
                                                    required
                                                    autocomplete="off"
                                                    placeholder="Name der Nutzergruppe eingeben..."
                                                />
                                            </b-col>
                                            <b-col cols="4" class="mb-1">
                                                <div class="mt-1">
                                                    <span
                                                        class="cursor-pointer"
                                                        @click="removeUserGroupName(i)"
                                                    >
                                                        <mdicon
                                                            name="DeleteCircleOutline"
                                                        />
                                                    </span>
                                                    <span
                                                        v-if="i !== 0"
                                                        class="cursor-pointer mt-1"
                                                        @click="moveUserGroupUp(i)"
                                                    >
                                                        <mdicon
                                                            name="ArrowUpDropCircleOutline"
                                                        />
                                                    </span>
                                                    <span
                                                        v-if="i !== (team.userGroupNames.length - 1)"
                                                        class="cursor-pointer mt-1"
                                                        @click="moveUserGroupDown(i)"
                                                    >
                                                        <mdicon
                                                            name="ArrowDownDropCircleOutline"
                                                        />
                                                    </span>
                                                </div>
                                            </b-col>
                                        </b-row>
                                        <b-row class="mb-2">
                                            <b-col cols="12">
                                                <div
                                                    class="cursor-pointer mt-1"
                                                    @click="addUserGroupName()"
                                                >
                                                    <mdicon
                                                        name="PlusCircleOutline"
                                                    />
                                                    neue Nutzergruppe hinzufügen
                                                </div>
                                            </b-col>
                                        </b-row>
                                    </template>
                                </b-form-group>
                            </b-card-body>
                        </b-card>
                    </b-col>
                </b-row>
            </b-form-group>

            <b-form-group
                label="Autocomplete-Vorschläge für den Ort eines Wegpunktes"
                v-slot="{ ariaDescribedby }"
                class="mb-0"
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
                            autocomplete="off"
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
        </b-card>
        <b-button
            type="submit"
            variant="secondary"
            data-test="button-team-form"
            block
            :disabled="isFormInvalid || isDisabled"
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
'use strict'
import FormError from '../Common/FormError.vue'
import { useClientStore } from '../../stores/client';
import { useTeamStore } from '../../stores/team';
import { useUserStore } from '../../stores/user';
import { useAuthStore } from '../../stores/auth';

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
        FormError,
    },
    data: function () {
        let isWithPeopleCountDefault = true;

        return {
            authStore: useAuthStore(),
            clientStore: useClientStore(),
            teamStore: useTeamStore(),
            userStore: useUserStore(),
            team: {
                team: null,
                client: '',
                name: '',
                isWithAgeRanges: !isWithPeopleCountDefault,
                isWithPeopleCount: isWithPeopleCountDefault,
                isWithContactsCount: false,
                isWithGuests: false,
                isWithSystemicQuestion: false,
                isWithUserGroups: false,
                users: [],
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
        users () {
            return this.userStore.getUsers.slice(0).filter(user => {
                return user.client === this.team.client
            }).sort((a, b) => {
                return (a.username.toLowerCase() > b.username.toLowerCase()) ? 1 : -1
            })
        },
        isPeopleCountDisabled () {
            return this.team.isWithAgeRanges || this.isDisabled;
        },
        isDisabled () {
            return this.teamStore.isLoadingCreate || this.teamStore.isLoadingChange(this.team['@id']);
        },
        nameState () {
            if (null === this.team.name || '' === this.team.name) {
                return
            }

            return this.team.name.length >= 3 && this.team.name.length <= 100
        },
        isLoading () {
            return this.teamStore.isLoading
        },
        currentUser () {
            return this.authStore.currentUser
        },
        isSuperAdmin () {
            return this.authStore.isSuperAdmin
        },
        isFormInvalid () {
            return !(this.nameState && this.team.client && !this.isLoading)
        },
        error () {
            return this.teamStore.getErrors.change;
        },
        availableClients () {
            return this.clientStore.getClients;
        },
    },
    created () {
        if (this.initialTeam) {
            this.team = JSON.parse(JSON.stringify(this.initialTeam));
        }
        this.team.client = this.team.client || this.currentUser.client;
    },
    watch: {
        'team.isWithAgeRanges': function (newValue) {
            if (newValue) {
                this.team.isWithPeopleCount = true;
            }
        },
    },
    methods: {
        async handleSubmit () {
            if (this.isFormInvalid) {
                return false
            }
            this.$emit('submit', this.team)
        },
        removeAgeRange (index) {
            this.team.ageRanges.splice(index, 1)
        },
        addAgeRange () {
            this.team.ageRanges = [...this.team.ageRanges, { rangeStart: '', rangeEnd: '' }]
        },
        removeLocationName (index) {
            this.$delete(this.team.locationNames, index)
        },
        addLocationName () {
            this.team.locationNames = [...this.team.locationNames, '']
        },
        removeWalkName (index) {
            this.$delete(this.team.walkNames, index)
        },
        addWalkName () {
            this.team.walkNames = [...this.team.walkNames, '']
        },
        removeConceptOfDaySuggestion (index) {
            this.$delete(this.team.conceptOfDaySuggestions, index)
        },
        addConceptOfDaySuggestion () {
            this.team.conceptOfDaySuggestions = [...this.team.conceptOfDaySuggestions, '']
        },
        removeGuestName (index) {
            this.$delete(this.team.guestNames, index)
        },
        addGuestName () {
            this.team.guestNames = [...this.team.guestNames, ''];
            this.$nextTick(() => {
                this.$refs.guestNameInputs[this.$refs.guestNameInputs.length - 1].focus();
            });
        },
        removeUserGroupName (index) {
            this.$delete(this.team.userGroupNames, index)
        },
        addUserGroupName () {
            this.team.userGroupNames = [...this.team.userGroupNames, { name: '' }]
        },
        moveUserGroupUp (index) {
            const tempUserGroupName = this.team.userGroupNames[index]
            let newUserGroups = []
            this.$delete(this.team.userGroupNames, index)
            this.team.userGroupNames.forEach((userGroupName, key) => {
                if (key === index - 1) {
                    newUserGroups.push(tempUserGroupName)
                }
                newUserGroups.push(userGroupName)
            })
            this.team.userGroupNames = newUserGroups
        },
        moveUserGroupDown (index) {
            const tempUserGroupName = this.team.userGroupNames[index]
            let newUserGroups = []
            this.$delete(this.team.userGroupNames, index)
            this.team.userGroupNames.forEach((userGroupName, key) => {
                newUserGroups.push(userGroupName)
                if (key === index) {
                    newUserGroups.push(tempUserGroupName)
                }
            })
            this.team.userGroupNames = newUserGroups
        },
        resetForm() {
            this.$refs.form.reset();
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
    },
}
</script>

<style scoped lang="scss">

</style>
