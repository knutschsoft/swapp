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
                <nl2br
                    v-else-if="field.name === 'Beobachtung'"
                    tag="div"
                    :text="field.value.trim()"
                    class-name="text-left"
                />
                <div
                    v-else-if="field.name === 'Tags'"
                    class="text-left"
                >
                    <template
                        v-for="tag in field.value"
                    >
                        {{ tag.name }}
                        <color-badge
                            :color="tag.color"
                            class="mr-2"
                        />
                    </template>
                </div>
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
    import ColorBadge from '../Tags/ColorBadge.vue';

    export default {
        name: "WayPointDetailData",
        components: {
            ColorBadge,
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
            wayPointTags() {
                let wayPointTags = [];
                this.wayPoint.wayPointTags.forEach(iri => {
                    wayPointTags.push(this.getTagByIri(iri));
                })

                return wayPointTags.sort((a, b) => a.name > b.name ? 1 : -1);
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
                    { name: 'Ort', value: this.wayPoint.locationName },
                    { name: 'Beobachtung', value: this.wayPoint.note },
                    { name: 'Bild', value: this.wayPoint.imageName },
                    { name: 'Meeting', value: this.wayPoint.isMeeting ? 'ja' : 'nein' },
                    { name: 'Tags', value: this.wayPointTags },
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
            getTagByIri(iri) {
                return this.$store.getters['tag/getTagByIri'](iri);
            },
        },
    }
</script>

<style scoped>

</style>
