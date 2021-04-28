<template>
    <div>
        <b-alert
            :show="showSuccess"
            variant="success"
            class="mt-1 mt-sm-2 mt-lg-3 mb-0"
        >
            <mdicon
                name="check-circle-outline"
                class="mr-2"
            />
            Wegpunkt erfolgreich hinzugefügt.
        </b-alert>
        <b-button
            v-if="walk && walk.wayPoints.length"
            :to="{name: 'WalkEpilogue', params: { walkId: walk.id } }"
            class="btn btn-secondary mt-1 mt-sm-2 mt-lg-3"
            :disabled="isLoading"
            block
        >
            Runde abschließen (kein weiterer Wegpunkt)
        </b-button>

        <content-collapse
            :title="`Wegpunkt zur Runde &quot;${walk.name}&quot; hinzufügen`"
            collapse-key="walk-add-waypoint"
            is-visible-by-default
        >
            <div
                id="form-holder"
                ref="forms"
                v-on:submit.prevent="onSubmit"
                class="p-2"
            />
            <hr class="mx-2 mt-0">
            <div
                class="mx-2 my-1 my-sm-2 my-lg-3"
            >
                <b-button
                    :to="{name: 'WalkEpilogue', params: { walkId: walk.id } }"
                    class="btn btn-secondary"
                    :disabled="isLoading"
                    block
                >
                    Wegpunkt speichern und Runde abschließen
                </b-button>
            </div>
        </content-collapse>

        <content-collapse
            :title="`Wegpunkte der Runde ${walk.name}`"
            collapse-key="waypoints-of-round"
            is-visible-by-default
        >
            <WayPointList
                :walk-id="walkId"
            />
        </content-collapse>
    </div>
</template>

<script>
    "use strict";
    import WayPointList from './WalkDetail/WayPointList';
    import ContentCollapse from './ContentCollapse.vue';

    export default {
        name: "WalkAddWayPoint",
        components: {
            ContentCollapse,
            WayPointList,
        },
        props: {
            walkId: {
                required: true,
            }
        },
        data: function () {
            return {
                showSuccess: false,
                isFormLoading: false,
            }
        },
        computed: {
            isLoading() {
                return this.$store.getters["walk/isLoading"] || this.isFormLoading;
            },
            hasWalks() {
                return this.$store.getters["walk/hasWalks"];
            },
            walk() {
                return this.$store.getters["walk/getWalkById"](this.walkId);
            },
            walks() {
                return this.$store.getters["walk/walks"];
            },
        },
        watch: {},
        async mounted() {
            await this.refreshWalk();
            this.refreshForm();
        },
        methods: {
            refreshWalk: async function() {
                await this.$store.dispatch('walk/findById', this.walkId);
            },
            refreshForm: async function() {
                this.isFormLoading = true;
                let {data} = await this.axios.get(`/form/addWayPointToWalk/${this.walkId}`);
                this.isFormLoading = false;
                this.$refs.forms.innerHTML = data.form;
            },
            onSubmit: async function (e) {
                let formData = new FormData(e.target);
                this.isFormLoading = true;
                let result = await this.axios.post(`/form/waypointcreated/${this.walkId}`, formData);
                this.isFormLoading = false;
                window.scrollTo({
                    top: 0,
                    left: 0,
                    behavior: 'smooth'
                });
                if (200 === result.status) {
                    this.showSuccess = true;
                    await this.refreshWalk();
                    await this.refreshForm();
                } else {
                    this.showSuccess = false;
                    this.$refs.forms.innerHTML = result.data.form;
                }
            }
        },
    }
</script>

<style scoped>

</style>
