<script setup lang="ts">
import {computed} from "vue";
import {getViolationsFeedback} from "../../../utils";
import {Team, User, Walk} from "../../../model";
import {useUserStore} from "@/js/stores";

const emit = defineEmits(['input', 'change']);

export interface Props {
    modelValue: string | null,
    label?: string,
    team?: Team | null,
    walk?: Walk | null,
    description?: string,
    showDisabled?: boolean
    error?: any,
    isLoading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    label: 'Name',
    description: '',
    team: null,
    walk: null,
    showDisabled: true,
    error: false,
    isLoading: false,
});

const value = computed({
  get: () => props.modelValue,
  set: (val) => emit("update:modelValue", val ?? ''),
});

const userStore = useUserStore();

const getUserByIri = (userIri: string) => {
    return userStore.getUserByIri(userIri);
}
const users = computed<User[]>(() => {
    return userStore.getUsers;
})

const isInitiallyWithoutWalkCreator = computed<boolean>(() => {
    return !Boolean(props.walk?.walkCreator)
})

const walkCreatorOptions = computed(() => {
    if (!props.team) {
        const innerUsers = users.value.slice();
        if (isInitiallyWithoutWalkCreator.value) {
            innerUsers.unshift({'@id': null, 'username': '-- Rundenersteller ist unbekannt --'})
        }

        return innerUsers;
    }
    const innerUsers = props.team.users
        .map((userIri: string) => getUserByIri(userIri))
        .filter((user: User) => undefined !== user)
        .filter((user: User) => {
            if (props.showDisabled) {
                return true
            }

            return user.isEnabled;
        });
    if (isInitiallyWithoutWalkCreator.value) {
        innerUsers.unshift({'@id': null, 'username': '-- Rundenersteller ist unbekannt --'})
    }

    return innerUsers;
})

const errorMessages = computed(() => {
    if (!props.error) {
        return ''
    }
    return getViolationsFeedback(['walkCreator'], props.error);
})

const handleWalkCreatorChange = (newWalkCreator: string) => {
    emit('change', newWalkCreator)
}

</script>

<template>
    <v-select
        v-model="value"
        :items="walkCreatorOptions"
        :disabled="isLoading"
        item-value="@id"
        item-title="username"
        label="Rundenersteller"
        required
        density="compact"
        variant="outlined"
        :loading="isLoading"
        :hint="description"
        :persistent-hint="!!description"
        :hide-details="!description && !errorMessages?.length"
        :error-messages="errorMessages"
        :error="!!errorMessages?.length"
        @change="handleWalkCreatorChange"
    />
</template>

<style scoped>
</style>
