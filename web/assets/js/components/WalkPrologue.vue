<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import ContentCollapse from './ContentCollapse.vue';
import WalkAPI from '../api/walk.js';
import dayjs from 'dayjs';
import { useAlertStore, useAuthStore, useTeamStore, useUserStore, useWalkStore } from '../stores';
import {
    WalkConceptOfDayField,
    WalkGuestNamesField,
    WalkHolidaysField,
    WalkNameField,
    WalkStartTimeField,
    WalkTeamMembersField,
    WalkWalkCreatorField,
    WalkWeatherField
} from './Common/Walk';
import { type User } from '@/js/model';
import router from '@/js/router'

interface Props { teamId: string; }
const props = defineProps<Props>();

const alertStore = useAlertStore();
const authStore = useAuthStore();
const teamStore = useTeamStore();
const userStore = useUserStore();
const walkStore = useWalkStore();

const name = ref<string>('');
const teamIdVal = ref<string | null>(null);
const walkTeamMembers = ref<string[]>([]);
const guestNames = ref<string[]>([]);
const conceptOfDay = ref<string[]>([]);
const startTime = ref<string>(dayjs().startOf('minute').format());
const holidays = ref<boolean>(false);
const weather = ref<string>('');
const walkCreator = ref<string>('');
const isFormLoading = ref<boolean>(false);

const team = computed(() => teamStore.getTeamById(props.teamId));
const currentUser = computed(() => authStore.currentUser);
const usersOfTeam = computed<User[]>(() => {
    if (!team.value) return [];
    return team.value.users
        .map(iri => userStore.getUserByIri(iri))
        .filter((u): u is User => Boolean(u))
        .sort((a, b) => a.username.toLowerCase() > b.username.toLowerCase() ? 1 : -1);
});
const isLoading = computed(() => teamStore.isLoading || userStore.isLoading || walkStore.isLoadingCreate);
const error = computed(() => walkStore.getErrors.create);
const hasError = computed(() => Boolean(error.value));
const validationErrors = computed<Record<string, string>>(() => {
    const errs: Record<string, string> = {};
    if (!hasError.value) return errs;
    const errData = error.value?.data;
    if (errData?.violations) {
        errData.violations.forEach((v: any) => errs[v.propertyPath ?? 'global'] = v.message);
        return errs;
    }
    if (errData?.description) errs.global = errData.description;
    return errs;
});
const isFormInvalid = computed<boolean>(() => {
    return (
        name.value.trim() === '' ||
        conceptOfDay.value.length === 0 ||
        startTime.value.trim() === '' ||
        walkTeamMembers.value.length === 0 ||
        walkCreator.value.trim() === '' ||
        (team.value?.isWithWeather && weather.value.trim() === '') ||
        isLoading.value
    );
});

function handleWalkCreatorChange(newCreator: string) {
    if (!walkTeamMembers.value.includes(newCreator)) {
        walkTeamMembers.value.push(newCreator);
    }
}

async function getEnabledWalkTeamMembersOfLastWalkOfTeam(t: any): Promise<string[]> {
    const resp = await WalkAPI.findLastWalkByTeam(t);
    const hits = resp.data.totalItems;
    const source = hits ? resp.data.member[0].walkTeamMembers : t.users;
    const filtered = source
        .filter((iri: string) => t.users.includes(iri))
        .filter((iri: string) => {
            const user = userStore.getUserByIri(iri);
            return Boolean(user && user.isEnabled);
        });
    const me = currentUser.value['@id'];
    if (!filtered.includes(me)) filtered.push(me);
    return filtered;
}

async function onSubmit() {
    isFormLoading.value = true;
    const formData = {
        name: name.value,
        team: teamIdVal.value,
        walkTeamMembers: walkTeamMembers.value,
        guestNames: guestNames.value,
        conceptOfDay: conceptOfDay.value,
        startTime: startTime.value,
        holidays: holidays.value,
        weather: weather.value,
        walkCreator: walkCreator.value,
    };
    const walk = await walkStore.create(formData);
    window.scrollTo({ top: 0, behavior: 'smooth' });
    if (walk) {
        alertStore.success(`Die Runde "${walk.name}" wurde erfolgreich erstellt.`, 'Runde erstellt');
        isFormLoading.value = false;
        await router.push({ name: 'WalkAddWayPoint', params: { walkId: walk.walkId } })
    } else {
        alertStore.error('Runde erstellen fehlgeschlagen', 'Upps! :-(');
        isFormLoading.value = false;
    }
}

onMounted(async () => {
    if (!team.value) await teamStore.fetchTeams();
    if (!team.value) {
        await router.push({ name: 'Dashboard', query: { redirect: 'Dieses Team existiert nicht.' }});
        return;
    }
    const me = currentUser.value['@id'];
    if (!team.value.users.includes(me)) {
        await router.push({ name: 'Dashboard', query: { redirect: 'Kein Mitglied des Teams.' }});
        return;
    }
    await Promise.all(
        team.value.users
            .filter(iri => !userStore.getUserByIri(iri))
            .map(iri => userStore.fetchByIri(iri))
    );
    if (team.value.initialMembersConfig === 'mitglieder') {
        walkTeamMembers.value = await getEnabledWalkTeamMembersOfLastWalkOfTeam(team.value);
    } else {
        walkTeamMembers.value = [me];
    }
    teamIdVal.value = team.value['@id'];
    walkCreator.value = me;
});
</script>

<template>
    <content-collapse
        :title="`Neue Streetwork-Runde`"
        collapse-key="walk-prologue"
        is-visible-by-default
    >
        <v-form
            v-if="team"
            ref="form"
            lazy-validation
            @submit.prevent="onSubmit"
        >
            <v-col class="my-2">
                <walk-walk-creator-field
                    v-model="walkCreator"
                    :team="team"
                    :is-loading="isLoading"
                    :error="error"
                    :show-disabled="false"
                    @change="handleWalkCreatorChange"
                />
            </v-col>
            <v-col>
                <walk-team-members-field
                    v-model="walkTeamMembers"
                    :users="usersOfTeam"
                    :walk-creator="walkCreator"
                    :is-loading="isLoading"
                    :error="error"
                    :label="`Teilnehmende des Teams &quot;${team?.name}&quot;`"
                    :show-disabled="false"
                    description="Wer ist heute mit dabei?"
                />
            </v-col>
            <v-col v-if="team.isWithGuests">
                <walk-guest-names-field
                    v-model="guestNames"
                    :team="team"
                    :is-loading="isLoading"
                    :error="error"
                />
            </v-col>
            <v-col>
                <walk-name-field
                    v-model="name"
                    :is-loading="isLoading"
                    :team="team"
                    :error="error"
                />
            </v-col>
            <v-col>
                <walk-concept-of-day-field
                    v-model="conceptOfDay"
                    :team="team"
                    :is-loading="isLoading"
                    :error="error"
                />
            </v-col>
            <v-col>
                <walk-start-time-field
                    v-model="startTime"
                    :is-loading="isLoading"
                    :error="error"
                    description="Die aktuelle Zeit ist vorausgewählt."
                />
            </v-col>
            <v-col>
                <walk-holidays-field v-model="holidays" :is-loading="isLoading" :error="error" />
            </v-col>
            <v-col>
                <walk-weather-field
                    v-if="team.isWithWeather"
                    v-model="weather"
                    :is-loading="isLoading"
                    :error="error"
                />
            </v-col>
            <v-col class="mb-2">
                <v-btn type="submit" :disabled="isFormInvalid" block color="secondary" data-test="btn-Runde beginnen">
                    Runde beginnen
                </v-btn>
            </v-col>
        </v-form>
    </content-collapse>
</template>

<style scoped></style>
