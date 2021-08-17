<template>
    <div v-if="!isLoading">
        <div
            v-if="walk"
            v-for="(field, index2) in fields"
            :key="index2"
        >
            <div class="d-inline-flex p-2 bd-highlight font-weight-bold">
                {{ field.name }}:
            </div>
            <div
                class="d-inline-flex p-2 bd-highlight"
            >
                <nl2br
                    v-if="field.nl2br"
                    :text="field.value"
                    tag="div"
                    class-name="text-left"
                />
                <template
                    v-else
                >
                    {{ field.value }}
                </template>
            </div>
        </div>
    </div>
</template>

<script>
    "use strict";
    import formatter from '../../utils/formatter.js';

    export default {
        name: "WalkDetailData",
        components: {
        },
        props: {
            walkId: {
                required: true,
            }
        },
        data: function () {
            return {
            }
        },
        computed: {
            isLoading() {
                return this.$store.getters["walk/isLoading"] || this.$store.getters["wayPoint/isLoading"];
            },
            walk() {
                return this.$store.getters["walk/getWalkById"](this.walkId);
            },
            personCount() {
                return this.walk.malesCount + this.walk.femalesCount + this.walk.queerCount;
            },
            walkTeamMembers() {
                let users = [];
                if (!this.walk) {
                    return users;
                }
                this.walk.walkTeamMembers.forEach(iri => {
                    users.push(this.getUserByIri(iri).username);
                })

                return users.sort((a, b) => a > b ? 1 : -1);
            },
            fields() {
                return [
                    { name: 'Name', value: this.walk.name },
                    { name: 'angetroffene männliche Personen', value: this.walk.malesCount ? this.walk.malesCount : 'keine männlichen Personen angetroffen' },
                    { name: 'angetroffene weibliche Personen', value: this.walk.femalesCount ? this.walk.femalesCount : 'keine weiblichen Personen angetroffen' },
                    { name: 'angetroffene diverse Personen', value: this.walk.queerCount ? this.walk.queerCount : 'keine diversen Personen angetroffen' },
                    { name: 'angetroffene Personen', value: this.personCount ? this.personCount : 'keine Personen angetroffen' },
                    { name: 'Tageskonzept', value: this.walk.conceptOfDay, nl2br: true },
                    { name: 'Ferien', value: this.walk.holiday ? 'ja' : 'nein' },
                    { name: 'Wetter', value: this.walk.weather },
                    { name: 'Beginn', value: this.formatDate(this.walk.startTime) },
                    { name: 'Ende', value: this.formatDate(this.walk.endTime) },
                    { name: 'Systemische Frage', value: this.walk.systemicQuestion },
                    { name: 'Systemische Antwort', value: this.walk.systemicAnswer, nl2br: true },
                    { name: 'Reflexion', value: this.walk.walkReflection, nl2br: true },
                    { name: 'Bewertung', value: formatter.formatRating(this.walk.rating) },
                    { name: 'Termine, Besorgungen, Verabredungen', value: this.walk.commitments, nl2br: true },
                    { name: 'Erkenntnisse, Überlegungen, Zielsetzungen', value: this.walk.insights, nl2br: true },
                    { name: 'Wiedervorlage Dienstberatung', value: this.walk.isResubmission ? 'ja' : 'nein' },
                    { name: 'Team', value: this.walk.teamName },
                    { name: 'Teilnehmer', value: this.walkTeamMembers.length ? this.walkTeamMembers.join(', ') : 'keine Teilnehmer' },
                    { name: 'Gäste', value: this.walk.guests && this.walk.guests.length ? this.walk.guests.join(', ') : 'keine Gäste' },
                ]
            },
        },
        watch: {},
        mounted() {
        },
        methods: {
            getUserByIri(userIri) {
                return this.$store.getters['user/getUserByIri'](userIri);
            },
            formatDate: function(dateString) {
                let date = new Date(dateString);
                return date.toLocaleDateString('de-DE', { weekday: 'short', hour: '2-digit', minute: '2-digit', year: 'numeric', month: '2-digit', day: '2-digit' })
            },
        },
    }
</script>

<style scoped>

</style>