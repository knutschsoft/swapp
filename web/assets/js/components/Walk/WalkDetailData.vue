<template>
    <div v-if="!isLoading && walk">
        <div
            v-if="!field.isHidden"
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
                <walk-rating
                    v-else-if="'Bewertung' === field.name && walkClient"
                    :rating="field.value"
                    :client="walkClient"
                    :read-only="true"
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
    import WalkRating from './WalkRating.vue';
    import { useClientStore } from '../../stores/client';
    import { useWayPointStore } from '../../stores/way-point';
    import { useWalkStore } from '../../stores/walk';

    export default {
        name: "WalkDetailData",
        components: {
            WalkRating,
        },
        props: {
            walkId: {
                required: true,
            }
        },
        data: function () {
            return {
                clientStore: useClientStore(),
                walkStore: useWalkStore(),
                wayPointStore: useWayPointStore(),
            }
        },
        computed: {
            walkClient() {
                return this.clientStore.getClientByIri(this.walk.client);
            },
            isLoading() {
                return this.walkStore.isLoading || this.wayPointStore.isLoading;
            },
            walk() {
                return this.walkStore.getWalkById(this.walkId);
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
                let fields = [
                    { name: 'Name', value: this.walk.name },
                    { name: 'angetroffene männliche Personen', isHidden: !this.walk.isWithAgeRanges, value: this.walk.malesCount ? this.walk.malesCount : 'keine männlichen Personen angetroffen' },
                    { name: 'angetroffene weibliche Personen', isHidden: !this.walk.isWithAgeRanges, value: this.walk.femalesCount ? this.walk.femalesCount : 'keine weiblichen Personen angetroffen' },
                    { name: 'angetroffene diverse Personen', isHidden: !this.walk.isWithAgeRanges, value: this.walk.queerCount ? this.walk.queerCount : 'keine diversen Personen angetroffen' },
                    { name: 'angetroffene Personen', isHidden: !this.walk.isWithPeopleCount, value: this.walk.peopleCount ? this.walk.peopleCount : 'keine Personen angetroffen' },
                    { name: 'Tageskonzept', value: this.walk.conceptOfDay ? this.walk.conceptOfDay.join(', ') : '' },
                    { name: 'Ferien', value: this.walk.holiday ? 'ja' : 'nein' },
                    { name: 'Wetter', value: this.walk.weather },
                    { name: 'Beginn', value: this.formatDate(this.walk.startTime) },
                    { name: 'Ende', value: this.walk.isUnfinished ? '-' : this.formatDate(this.walk.endTime) },
                 ];
                if (this.walk.isWithSystemicQuestion) {
                    fields.push({ name: 'Systemische Frage', value: this.walk.systemicQuestion });
                    fields.push({ name: 'Systemische Antwort', value: this.walk.systemicAnswer, nl2br: true });
                }
                fields = fields.concat([
                    { name: 'Reflexion', value: this.walk.walkReflection, nl2br: true },
                    { name: 'Bewertung', value: this.walk.rating },
                    { name: 'Termine, Besorgungen, Verabredungen', value: this.walk.commitments, nl2br: true },
                    { name: 'Erkenntnisse, Überlegungen, Zielsetzungen', value: this.walk.insights, nl2br: true },
                    { name: 'Wiedervorlage Dienstberatung', value: this.walk.isResubmission ? 'ja' : 'nein' },
                    { name: 'Team', value: this.walk.teamName },
                    { name: 'Teilnehmende', value: this.walkTeamMembers.length ? this.walkTeamMembers.join(', ') : 'keine Teilnehmenden' },
                ]);
                if (this.walk.isWithGuests) {
                    fields.push({ name: 'Weitere Teilnehmende', value: this.walk.guestNames && this.walk.guestNames.length ? this.walk.guestNames.join(', ') : 'keine weiteren Teilnehmenden' });
                }

                return fields;
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
