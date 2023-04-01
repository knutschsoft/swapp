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
                    v-else-if="field.name === 'Anzahl direkter Kontakte'"
                >
                    {{ field.value === null ? 'nicht erfasst' : field.value}}
                </div>
                <nl2br
                    v-else-if="field.name === 'Einzelgespräch'"
                    tag="div"
                    :text="field.value.trim()"
                    class-name="text-left"
                />
                <div
                    v-else-if="field.name === 'Tags'"
                    class="text-left"
                >
                    <template
                        v-if="0 === field.value.length"
                    >
                        keine Tags vergeben
                    </template>
                    <template
                        v-for="tag in field.value"
                    >
                        {{ tag.name }}
                        <span
                            v-if="!tag.isEnabled"
                            class="text-muted"
                        >
                            (deaktivierter Tag)
                        </span>
                        <color-badge
                            v-if="tag"
                            :color="tag.color"
                            class="mr-2"
                        />
                    </template>
                </div>
                <template
                    v-else-if="field.name === 'Bild'"
                >
                    <silent-box
                        v-if="field.value"
                        :gallery="gallery"
                    />

                    <template v-else>
                        kein Bild hochgeladen
                    </template>
                </template>
                <div
                    v-else-if="field.name === 'Personenanzahl von Nutzergruppen'"
                    class="d-flex flex-wrap"
                >
                    <div
                        v-for="userGroup in field.value"
                        class="mr-2 flex-fill"
                        :class="{'text-muted': userGroup.peopleCount.count === 0}"
                    >
                        {{ userGroup.userGroupName.name }}:&nbsp;{{ userGroup.peopleCount.count }}
                    </div>
                </div>
                <template v-else>
                    {{ field.value }}
                </template>
            </div>
        </div>
    </div>
</template>

<script>
    "use strict";
    import LocationLink from '../LocationLink.vue';
    import ColorBadge from '../Tags/ColorBadge.vue';
    import dayjs from 'dayjs';

    export default {
        name: "WayPointDetailData",
        components: {
            ColorBadge,
            LocationLink,
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
            };
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
            gallery() {
                if (!this.wayPoint.imageName) {
                    return [];
                }
                return [{
                    src: this.wayPoint.imageSrc,
                    thumbnailHeight: '220px',
                    description: this.wayPoint.imageName,
                }];
            },
            fields() {
                if (!this.wayPoint) {
                    return [];
                }
                let ageGroups = [];
                let ageGroupsSorted = [];
                let sumPeopleCount = 0;
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
                    sumPeopleCount += ageGroup.peopleCount.count;
                });

                let fields = [
                    { name: 'Ort', value: this.wayPoint.locationName },
                    { name: 'Ankunft', value: dayjs(this.wayPoint.visitedAt).format('ddd, DD.MM.YYYY HH:mm') },
                    { name: 'Beobachtung', value: this.wayPoint.note },
                    { name: 'Einzelgespräch', value: this.wayPoint.oneOnOneInterview },
                    { name: 'Bild', value: this.wayPoint.imageName },
                    { name: 'Meeting', value: this.wayPoint.isMeeting ? 'ja' : 'nein' },
                    { name: 'Tags', value: this.wayPointTags },
                ];

                if (this.walk.isWithAgeRanges) {
                    fields = fields.concat(ageGroups);

                    fields.push({ name: 'Anzahl Personen vor Ort', value: sumPeopleCount});
                } else if (this.walk.isWithPeopleCount) {
                    fields.push({ name: 'Anzahl Personen vor Ort', value: this.wayPoint.peopleCount});
                }

                if (this.walk.isWithUserGroups) {
                    fields.push({ name: 'Personenanzahl von Nutzergruppen', value: this.wayPoint.userGroups });
                }
                if (this.walk.isWithContactsCount) {
                    fields.push({ name: 'Anzahl direkter Kontakte', value: this.wayPoint.contactsCount });
                }

                return fields;
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
