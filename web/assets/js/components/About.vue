<template>
    <div>
        <content-collapse
            title="Was ist Swapp?"
            collapse-key="about-swapp"
            is-visible-by-default
        >
            <b-img
                :src="infoImageSrc"
                alt="Was ist Swapp?"
                title="Was ist Swapp?"
                center
                fluid
            />
        </content-collapse>
    </div>
</template>

<script>
    "use strict";
    import ContentCollapse from './ContentCollapse.vue';
    import infoImage from '../../images/swapp-info.jpeg';

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
