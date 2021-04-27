<template>
    <div>
        <b-table
            v-if="!isLoading"
            show-empty
            small
            stacked="md"
            :items="walk.wayPoints"
            :fields="fields"
            class="mb-0"
        >
            <template #cell(locationName)="data">
                <location-link
                    :value="data.value"
                />
            </template>
            <template v-slot:cell(actions)="row">
                <router-link
                    :to="{name: 'WayPointDetail', params: { walkId: walk.id, wayPointId: row.item.id}}"
                >
                    <b-button size="sm">
                        Wegpunkt ansehen

                        <font-awesome-icon
                            icon="map-signs"
                            class="bg-secondary ml-2"
                        />
                        <font-awesome-icon icon="eye" class="ml-2" />
                    </b-button>
                </router-link>
            </template>
        </b-table>
    </div>
</template>

<script>
    "use strict";

    import LocationLink from '../LocationLink.vue';

    export default {
        name: "WayPointList",
        components: {
            LocationLink,
        },
        props: {
            walkId: {
                required: true,
            }
        },
        data: function () {
            return {
                fields: [
                    { key: 'locationName', label: 'Ort', sortable: true, sortDirection: 'desc', class: 'text-center align-middle' },
                    {
                        key: 'isMeeting',
                        label: 'Meeting',
                        formatter: (value, key, item) => {
                            return value ? 'Ja' : 'Nein'
                        },
                        sortable: true,
                        sortByFormatted: true,
                        filterByFormatted: true,
                        class: 'text-center align-middle'
                    },
                    { key: 'malesCount', label: 'Männer', sortable: true, sortDirection: 'desc', class: 'text-center align-middle' },
                    { key: 'femalesCount', label: 'Frauen', sortable: true, sortDirection: 'desc', class: 'text-center align-middle' },
                    { key: 'queerCount', label: 'Andere', sortable: true, sortDirection: 'desc', class: 'text-center align-middle' },
                    { key: 'actions', label: 'Aktionen', class: 'text-center align-middle' },
                ],
            }
        },
        computed: {
            isLoading() {
                return this.$store.getters["walk/isLoading"] || this.$store.getters["wayPoint/isLoading"];
            },
            hasWalks() {
                return this.$store.getters["walk/hasWalks"];
            },
            walks() {
                return this.$store.getters["walk/walks"];
            },
            walk() {
                return this.$store.getters["walk/getWalkById"](this.walkId);
            },
            hasWayPoints() {
                return this.$store.getters["wayPoint/hasWayPoints"];
            },
            wayPoints() {
                return this.$store.getters["wayPoint/wayPoints"];
            },
        },
        watch: {},
        mounted() {
        },
        methods: {},
    }
</script>

<style scoped>

</style>
