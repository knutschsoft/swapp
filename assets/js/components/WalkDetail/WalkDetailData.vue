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
                 {{ field.value }}
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
            fields() {
                return [
                    {name: 'Name', value: this.walk.name},
                    {name: 'angetroffene männliche Personen', value: this.walk.malesCount ? this.walk.malesCount : 'keine männlichen Personen angetroffen'},
                    {name: 'angetroffene weibliche Personen', value: this.walk.femalesCount ? this.walk.femalesCount : 'keine weiblichen Personen angetroffen'},
                    {name: 'angetroffene diverse Personen', value: this.walk.queerCount ? this.walk.queerCount : 'keine diversen Personen angetroffen'},
                    {name: 'angetroffene Personen', value: this.personCount ? this.personCount : 'keine Personen angetroffen'},
                    {name: 'Tageskonzept', value: this.walk.conceptOfDay},
                    {name: 'Ferien', value: this.walk.holiday ? 'ja' : 'nein'},
                    {name: 'Wetter', value: this.walk.weather},
                    {name: 'Beginn', value: this.formatDate(this.walk.startTime)},
                    {name: 'Ende', value: this.formatDate(this.walk.endTime)},
                    {name: 'Systemische Frage', value: this.walk.systemicQuestion},
                    {name: 'Systemische Antwort', value: this.walk.systemicAnswer},
                    {name: 'Reflexion', value: this.walk.walkReflection},
                    {name: 'Bewertung', value: formatter.formatRating(this.walk.rating)},
                    {name: 'Termine, Besorgungen, Verabredungen', value: this.walk.commitments},
                    {name: 'Erkenntnisse, Überlegungen, Zielsetzungen', value: this.walk.insights},
                    {name: 'Wiedervorlage Dienstberatung', value: this.walk.isResubmission ? 'ja' : 'nein'},
                    {name: 'Teilnehmer', value: this.walk.walkTeamMembers && this.walk.walkTeamMembers.length ? this.walk.walkTeamMembers.join(', ') : 'keine Teilnehmer'},
                    {name: 'Gäste', value: this.walk.guests && this.walk.guests.length ? this.walk.guests.join(', ') : 'keine Gäste'},
                ]
            },
        },
        watch: {},
        mounted() {
        },
        methods: {
            formatDate: function(dateString) {
                let date = new Date(dateString);
                return date.toLocaleDateString('de-DE', { weekday: 'short', hour: '2-digit', minute: '2-digit', year: 'numeric', month: '2-digit', day: '2-digit' })
            },
        },
    }
</script>

<style scoped>

</style>
