<template>
    <div v-if="walk">
        <div
            v-if="!excludedAttributes.includes('walk')"
        >
            <div class="d-inline-flex p-2 bd-highlight font-weight-bold">
                Runde:
            </div>
            <div
                class="d-inline-flex p-2 bd-highlight"
            >
                <router-link
                    :to="{name: 'WalkDetail', params: { walkId: walk.walkId}}"
                >
                    {{ walk.name }}
                </router-link>
            </div>
        </div>
        <div
            v-if="!excludedAttributes.includes('locationName')"
        >
            <div class="d-inline-flex p-2 bd-highlight font-weight-bold">
                Ort:
            </div>
            <div
                class="d-inline-flex p-2 bd-highlight"
            >
                <location-link
                    :value="wayPoint.locationName"
                />
            </div>
        </div>
        <div
            v-if="!excludedAttributes.includes('visitedAt')"
        >
            <div class="d-inline-flex p-2 bd-highlight font-weight-bold">
                Ankunft:
            </div>
            <div
                class="d-inline-flex p-2 bd-highlight"
            >
                {{ visitedAt }}
            </div>
        </div>
        <div
            v-if="!excludedAttributes.includes('note')"
        >
            <div class="d-inline-flex p-2 bd-highlight font-weight-bold">
                Beobachtung:
            </div>
            <nl2br
                tag="div"
                :text="wayPoint.note.trim()"
                class-name="d-inline-flex p-2 bd-highlight"
            />
        </div>
        <div
            v-if="!excludedAttributes.includes('oneOnOneInterview')"
        >
            <div class="d-inline-flex p-2 bd-highlight font-weight-bold">
                Einzelgespräch:
            </div>
            <nl2br
                tag="div"
                :text="wayPoint.oneOnOneInterview.trim()"
                class-name="d-inline-flex p-2 bd-highlight"
            />
        </div>
        <div
            v-if="!excludedAttributes.includes('imageName')"
        >
            <div class="d-inline-flex p-2 bd-highlight font-weight-bold">
                Bild:
            </div>
            <silent-box
                v-if="wayPoint.imageName"
                class="ml-2"
                :gallery="gallery"
            />
            <template v-else>
                kein Bild hochgeladen
            </template>
        </div>
        <div
            v-if="!excludedAttributes.includes('isMeeting')"
        >
            <div class="d-inline-flex p-2 bd-highlight font-weight-bold">
                Meeting:
            </div>
            {{ wayPoint.isMeeting ? 'ja' : 'nein' }}
        </div>
        <div
            v-if="!excludedAttributes.includes('wayPointTags')"
        >
            <div class="d-inline-flex p-2 bd-highlight font-weight-bold">
                Tags:
            </div>
            <div
                class="d-inline-flex flex-wrap p-2 bd-highlight"
            >
                <template
                    v-if="0 === wayPoint.wayPointTags.length"
                >
                    keine Tags vergeben
                </template>
                <div
                    v-for="tag in wayPointTags"
                    class="d-flex flex-nowrap"
                >
                    {{ tag.name }}
                    <span
                        v-if="!tag.isEnabled"
                        class="text-muted"
                    >
                        (deaktivierter Tag)
                    </span>
                    <color-badge
                        :color="tag.color"
                        class="mr-3 ml-1"
                    />
                </div>
            </div>
        </div>
        <div
            v-if="walk.isWithAgeRanges && !excludedAttributes.includes('ageRanges')"
        >
            <div
                v-for="ageGroup in ageGroups"
            >
                <div
                    class="d-inline-flex p-2 bd-highlight font-weight-bold"
                    :class="{'text-muted': !ageGroup.value}"
                >
                    {{ ageGroup.name }}:
                </div>
                {{ ageGroup.value }}
            </div>

            <div class="d-inline-flex p-2 bd-highlight font-weight-bold">
                Anzahl Personen vor Ort:
            </div>
            {{ wayPoint.peopleCount }}
        </div>
        <div
            v-else-if="walk.isWithPeopleCount && !excludedAttributes.includes('peopleCount')"
        >
            <div class="d-inline-flex p-2 bd-highlight font-weight-bold">
                Anzahl Personen vor Ort:
            </div>
            {{ wayPoint.peopleCount }}
        </div>

        <div
            v-if="walk.isWithUserGroups && !excludedAttributes.includes('userGroups')"
        >
            <div class="d-inline-flex p-2 bd-highlight font-weight-bold">
                Personenanzahl von Nutzergruppen:
            </div>

            {{ wayPoint.userGroups === null ? 'nicht erfasst' : wayPoint.userGroups.value }}
        </div>
        <div
            v-if="walk.isWithContactsCount && !excludedAttributes.includes('contactsCount')"
            class="d-flex flex-wrap"
        >
            <div class="d-inline-flex p-2 bd-highlight font-weight-bold">
                Anzahl direkter Kontakte:
            </div>
            {{ this.wayPoint.contactsCount }}
        </div>
    </div>
</template>

<script>
    "use strict";
    import LocationLink from '../LocationLink.vue';
    import ColorBadge from '../Tags/ColorBadge.vue';
    import dayjs from 'dayjs';
    import { useTagStore } from '../../stores/tag';
    import { useWayPointStore } from '../../stores/way-point';
    import { useWalkStore } from '../../stores/walk';

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
            },
            excludedAttributes: {
                required: false,
                default: () => [],
            },
            hideEmptyAgeGroups: {
                required: false,
                default: false,
            }
        },
        data: function () {
            return {
                tagStore: useTagStore(),
                walkStore: useWalkStore(),
                wayPointStore: useWayPointStore(),
            };
        },
        computed: {
            isLoading() {
                return this.wayPointStore.isLoading;
            },
            hasError() {
                return this.wayPointStore.hasError;
            },
            error() {
                return this.wayPointStore.getErrors;
            },
            hasWayPoints() {
                return this.wayPointStore.hasWayPoints;
            },
            wayPoints() {
                return this.wayPointStore.getWayPoints;
            },
            walk() {
                return this.walkStore.getWalkById(this.walkId);
            },
            wayPoint() {
                return this.wayPointStore.getWayPointById(this.wayPointId);
            },
            wayPointTags() {
                let wayPointTags = [];
                this.wayPoint.wayPointTags.forEach(iri => {
                    const tag = this.getTagByIri(iri);
                    if (tag) {
                        wayPointTags.push(tag);
                    }
                })

                return wayPointTags.sort((a, b) => a.name > b.name ? 1 : -1);
            },
            gallery() {
                if (!this.wayPoint.imageName) {
                    return [];
                }
                return [{
                    src: this.wayPoint.imageSrc,
                    thumbnailHeight: '50px',
                    description: this.wayPoint.imageName,
                }];
            },
            ageGroupsSorted() {
                let ageGroupsSorted = [];
                this.wayPoint.ageGroups.forEach(ageGroup => {
                    ageGroupsSorted[String(ageGroup.ageRange.rangeEnd)+String(ageGroup.gender.gender.charCodeAt(0))] = ageGroup;
                });

                return ageGroupsSorted;
            },
            ageGroups() {
                let ageGroups = [];
                this.ageGroupsSorted
                    .forEach(ageGroup => {
                        if (this.hideEmptyAgeGroups && !ageGroup.peopleCount.count) {
                            return;
                        }

                        ageGroups.push({
                            name: ageGroup.ageRange.rangeStart+'-'+ageGroup.ageRange.rangeEnd+ageGroup.gender.gender,
                            value: ageGroup.peopleCount.count,
                        })
                    });

                return ageGroups;
            },
            sumPeopleCount() {
                let sumPeopleCount = 0;
                this.ageGroups.forEach(ageGroup => {
                    sumPeopleCount += ageGroup.peopleCount.count;
                });

                return sumPeopleCount;
            },
            visitedAt() {
                return dayjs(this.wayPoint.visitedAt).format('ddd, DD.MM.YYYY HH:mm');
            },
        },
        watch: {},
        async mounted() {
            const promises = [];
            if (!this.walk) {
                promises.push(this.walkStore.fetchById(this.walkId));
            }
            if (!this.wayPoint) {
                promises.push(this.wayPointStore.fetchById(this.wayPointId));
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
                return this.tagStore.getTagByIri(iri);
            },
        },
    }
</script>

<style scoped>

</style>
