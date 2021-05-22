<template>
    <div>
        <content-collapse
            title="Was ist Swapp, die Streetworkapp?"
            collapse-key="about-swapp"
            is-visible-by-default
        >
            <b-img
                :src="infoImageSrc"
                alt="Was ist Swapp, die Streetworkapp?"
                title="Was ist Swapp, die Streetworkapp?"
                center
                fluid
            />
        </content-collapse>
    </div>
</template>

<script>
    "use strict";
    import ContentCollapse from './ContentCollapse.vue';
    import infoImage from '../../images/swapp-info.png';

    export default {
        name: "Users",
        components: {
            ContentCollapse,
        },
        data: () => {
            return {
                infoImageSrc: infoImage,
            };
        },
    }
</script>

<style scoped>

</style>
