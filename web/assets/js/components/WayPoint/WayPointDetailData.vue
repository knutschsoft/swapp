<template>
    <div>
        <div class="d-inline-flex p-2 bd-highlight font-weight-bold">
            Runde:
        </div>
        <div
            class="d-inline-flex p-2 bd-highlight"
        >
            <router-link
                :to="{name: 'WalkDetail', params: { walkId: walk.id}}"
            >
                {{ walk.name }}
            </router-link>
        </div>
        <div
            v-for="(field, index2) in fields"
            :key="index2"
            :class="{'text-muted': field.isAgeGroup && !field.value}"
        >
            <div class="d-inline-flex p-2 bd-highlight font-weight-bold">
                {{ field.name }}:
            </div>
            <div
                class="d-inline-flex p-2 bd-highlight"
            >
                <location-link
                    v-if="field.name === 'Ort'"
                    :value="field.value"
                />
                <div
                    v-else-if="field.name === 'Beobachtung'"
                    class="text-left"
                    style="white-space: pre;"
                >{{ field.value }}</div>
                <template
                    v-else-if="field.name === 'Bild'"
                >
                    <b-img
                        v-if="field.value"
                        :src="`/images/way_points/${field.value}`"
                        :alt="field.value"
                        :title="field.value"
                        fluid
                        class=""
                    />
                    <template v-else>
                        kein Bild hochgeladen
                    </template>
                </template>
                <template v-else>
                    {{ field.value }}
                </template>
            </div>
        </div>
    </div>
</template>

<script>
    "use strict";
    import ModulHeader from './../ModulHeader';
    import LocationLink from '../LocationLink.vue';

    export default {
        name: "WayPointDetailData",
        components: {
            LocationLink,
            ModulHeader,
            Error,
        },
        props: {
            walkId: {
                required: true,
            },
            wayPointId: {
                required: true,
            }
        },
        data: function () {
            return {
            }
        },
        computed: {
            isLoading() {
                return this.$store.getters["wayPoint/isLoading"] || this.$store.getters["wayPoint/isLoading"];
            },
            hasError() {
                return this.$store.getters["wayPoint/hasError"] || this.$store.getters["wayPoint/hasError"];
            },
            error() {
                return this.$store.getters["wayPoint/error"] || this.$store.getters["wayPoint/error"];
            },
            hasWayPoints() {
                return this.$store.getters["wayPoint/hasWayPoints"];
            },
            wayPoints() {
                return this.$store.getters["wayPoint/wayPoints"];
            },
            walk() {
                return this.$store.getters["walk/getWalkById"](this.walkId);
            },
            wayPoint() {
                return this.$store.getters["wayPoint/getWayPointById"](this.wayPointId);
            },
            fields() {
                if (!this.wayPoint) {
                    return [];
                }
                let ageGroups = [];
                let ageGroupsSorted = [];
                this.wayPoint.ageGroups.forEach(ageGroup => {
                        ageGroupsSorted[String(ageGroup.ageRange.rangeEnd)+String(ageGroup.gender.gender.charCodeAt(0))] = ageGroup;
                    });
                ageGroupsSorted
                    .forEach(ageGroup => {
                    ageGroups.push({
                        name: ageGroup.ageRange.rangeStart+'-'+ageGroup.ageRange.rangeEnd+ageGroup.gender.gender,
                        value: ageGroup.peopleCount.count,
                        isAgeGroup: true,
                    })
                });
                return [
                    {name: 'Ort', value: this.wayPoint.locationName},
                    {name: 'Beobachtung', value: this.wayPoint.note},
                    {name: 'Bild', value: this.wayPoint.imageName},
                    {name: 'Meeting', value: this.wayPoint.isMeeting ? 'ja' : 'nein'},
                ].concat(ageGroups);
            },
        },
        watch: {},
        async mounted() {
            const promises = [];
            if (!this.walk) {
                promises.push(this.$store.dispatch('walk/findById', this.walkId));
            }
            if (!this.wayPoint) {
                promises.push(this.$store.dispatch('wayPoint/findById', this.wayPointId));
            }
            await Promise.all(promises);
        },
        created() {
            if (!this.wayPoint || !this.walk) {
                console.error('route to 404');
            }
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