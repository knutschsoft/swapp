<script setup lang="ts">
import {computed} from "vue";
import {getViolationsFeedback} from "../../../utils";
import {Team, Walk} from "../../../model";

const emit = defineEmits(['update:modelValue']);

export interface Props {
    modelValue: string,
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
  get: () => props.modelValue,
  set: (val) => emit("update:modelValue", val ?? ""),
});

const walkNameSuggestions = computed(() => {
    let walkNames = <string[]>[];
    if (!props.team) {
        return walkNames;
    }
    if (props.walk?.name) {
        walkNames = [props.walk.name, ...new Set(props.team.walkNames)];
    } else {
        walkNames = [...new Set(props.team.walkNames)];
    }

    return walkNames;
})

const errorMessages = computed(() => {
    if (!props.error) {
        return ''
    }

    return getViolationsFeedback(['name'], props.error);
})

</script>

<template>
    <v-combobox
        v-model="value"
        :items="walkNameSuggestions"
        clearable
        variant="outlined"
        autocomplete="off"
        :label="label"
        placeholder="Wie ist der Name der Runde?"
        :disabled="isLoading"
        :loading="isLoading"
        :hint="description"
        :persistent-hint="!!description"
        :hide-details="!description && !errorMessages?.length"
        density="compact"
        small-chips
        :error-messages="errorMessages"
        :error="!!errorMessages?.length"
        data-test="Name"
    />
</template>

<style scoped>
</style>
