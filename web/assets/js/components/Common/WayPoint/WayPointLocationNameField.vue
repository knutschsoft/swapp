<script setup lang="ts">
import {computed} from "vue";
import {getViolationsFeedback} from "../../../utils";
import {Team, Walk, WayPoint} from "../../../model";

const emit = defineEmits(['update:modelValue']);

export interface Props {
    modelValue: string,
    walk: Walk,
    initialWayPoint?: WayPoint | null,
    label?: string,
    team?: Team | null,
    description?: string,
    error?: any,
    isLoading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    label: 'Name',
    description: '',
    team: null,
    initialWayPoint: null,
    error: false,
    isLoading: false,
});

const value = computed({
  get: () => props.modelValue,
  set: (val) => emit("update:modelValue", val ?? ""),
});

const locationNameSuggestions = computed(() => {
    const initialLocation = props.initialWayPoint?.locationName ? [props.initialWayPoint.locationName] : [];
    const teamLocations = props.team?.locationNames ?? [];

    return [...new Set([...initialLocation, ...teamLocations])];
})

const errorMessages = computed(() => {
    if (!props.error) {
        return ''
    }

    return getViolationsFeedback(['locationName'], props.error);
})

</script>

<template>
    <v-combobox
        v-model="value"
        :items="locationNameSuggestions"
        clearable
        variant="outlined"
        :label="'Ort'"
        :placeholder="walk ? 'Wo seid ihr gerade?' : 'Ort eingeben...'"
        :disabled="isLoading"
        :loading="isLoading"
        :hint="description"
        :persistent-hint="!!description"
        :hide-details="!description && !errorMessages?.length"
        density="compact"
        small-chips
        :error-messages="errorMessages"
        :error="!!errorMessages?.length"
        data-test="locationName"
    />
</template>

<style scoped>
</style>
