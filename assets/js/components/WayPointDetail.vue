<template>
    <div class="row mx-auto mb-4">
        <div
            class="col-lg-10 offset-lg-1 col-12 pl-1 pr-1 pl-sm-2 pr-sm-2 d-flex flex-column align-items-center"
        >
            <WayPointDetailData
                :class="cardClass"
                :walk-id="walkId"
                :way-point-id="wayPointId"
            />
        </div>
    </div>
</template>

<script>
    "use strict";
    import Error from './Error';
    import ModulHeader from './ModulHeader';
    import WayPointDetailData from './WayPointDetail/WayPointDetailData';

    export default {
        name: "WalkDetail",
        components: {
            WayPointDetailData,
            ModulHeader,
            Error,
        },
        props: {
            walkId: {
                required: true
            },
            wayPointId: {
                required: true
            },
        },
        data: function () {
            return {
                cardClass: 'w-100 border border-dark p-0 mt-3 flex-shrink-1',
            }
        },
        computed: {
            isLoading() {
                return this.$store.getters["studiengang/isLoading"] || this.$store.getters["walk/isLoading"];
            },
            hasError() {
                return this.$store.getters["studiengang/hasError"] || this.$store.getters["walk/hasError"];
            },
            error() {
                return this.$store.getters["studiengang/error"] || this.$store.getters["walk/error"];
            },
            hasWalks() {
                return this.$store.getters["walk/hasWalks"];
            },
            walks() {
                return this.$store.getters["walk/walks"];
            },
            getCollapseId() {
                return 'collapse-way-point-detail';
            },
            isVisible() {
                return this.$localStorage.get(
                    this.getCollapseId,
                    false
                );
            },
        },
        watch: {},
        mounted() {
        },
        methods: {
        },
    }
</script>

<style scoped>

</style>
