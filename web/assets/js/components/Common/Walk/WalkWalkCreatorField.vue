<script setup lang="ts">
import {computed} from "vue";
import {getViolationsFeedback} from "../../../utils";
import {Team, User, Walk} from "../../../model";
import {useUserStore} from "../../../stores";

// const props = defineProps(['modelValue', 'value']); // vue3
// const emit = defineEmits(['update:modelValue']); // vue3
const emit = defineEmits(['input', 'change']);

export interface Props {
    value: string,
    label?: string,
    team?: Team | null,
    walk?: Walk | null,
    description?: string,
    error?: any,
    isLoading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    label: 'Name',
    description: '',
    team: null,
    walk: null,
    error: false,
    isLoading: false,
});

const value = computed({
    get() {
        // return props.modelValue // vue3
        return props.value
    },
    set(value) {
        // emit('update:modelValue', value); // vue3
        emit('input', value)
    }
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
        .filter((user: User) => undefined !== user);
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
        item-text="username"
        label="Rundenersteller"
        required
        dense
        outlined
        :loading="isLoading"
        :hint="description"
        :persistent-hint="!!description"
        :hide-details="!description"
        :error-messages="errorMessages"
        :error="!!errorMessages?.length"
        @change="handleWalkCreatorChange"
    />
</template>

<style scoped>
</style>
