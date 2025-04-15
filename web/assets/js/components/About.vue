<template>
    <div>
        <content-collapse
            title="Was ist Swapp, die Streetworkapp?"
            collapse-key="about-swapp"
            is-visible-by-default
        >
            <v-img
                :src="infoImageSrc"
                alt="Was ist Swapp, die Streetworkapp?"
                title="Was ist Swapp, die Streetworkapp?"
                center
                cover
            />
        </content-collapse>
    </div>
</template>

<script lang="ts" setup>
import {ref} from 'vue';
import ContentCollapse from './ContentCollapse.vue';
import infoImage from '../../images/swapp-info.png';

const infoImageSrc = ref(infoImage);
</script>

<style scoped>

</style>
