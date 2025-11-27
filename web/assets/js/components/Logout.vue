<script setup lang="ts">
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import {
    useAuthStore,
    useClientStore,
    useSystemicQuestionStore,
    useTagStore,
    useTeamStore,
    useUserStore,
    useWalkStore,
    useWayPointStore
} from '../stores';

const router = useRouter();
const authStore = useAuthStore();
const clientStore = useClientStore();
const teamStore = useTeamStore();
const systemicQuestionStore = useSystemicQuestionStore();
const tagStore = useTagStore();
const userStore = useUserStore();
const walkStore = useWalkStore();
const wayPointStore = useWayPointStore();

onMounted(() => {
    clientStore.$reset();
    systemicQuestionStore.$reset();
    teamStore.$reset();
    tagStore.$reset();
    userStore.$reset();
    walkStore.$reset();
    wayPointStore.$reset();
    authStore.logout();
    router.push({ name: 'Login' });
});
</script>

<template>
    <div />
</template>
