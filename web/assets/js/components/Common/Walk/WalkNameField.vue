<script setup lang="ts">
import {computed, ref} from "vue";
import {getViolationsFeedback, type ErrorData } from "../../../utils";
import {Team, Walk} from "@/js/model";

// const props = defineProps(['modelValue', 'value']); // vue3
// const emit = defineEmits(['update:modelValue']); // vue3
const emit = defineEmits(['input']);

export interface Props {
    value: string,
    label?: string,
    team?: Team | null,
    walk?: Walk | null,
    description?: string,
    error?: ErrorData | boolean,
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

const weatherOptions = ref<string[]>(['', 'Sonne', 'Wolken', 'Regen', 'Schnee', 'Arschkalt']);

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

const walkNameSuggestions = computed(() => {
    let walkNames = <string[]>[];
    if (!props.team) {
        return walkNames;
    }
    if (props.walk?.name) {
        walkNames = [props.walk.name, ...new Set(props.team.walkNames)];
    }

    return walkNames.filter((walkName: string) => {
        return walkName.toLowerCase().startsWith(props.walk?.name.toLowerCase()) && walkName !== props.walk?.name;
    }).map((walkName: string) => walkName);
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
        outlined
        :label="label"
        placeholder="Wie ist der Name der Runde?"
        :disabled="isLoading"
        :loading="isLoading"
        :hint="description"
        :persistent-hint="!!description"
        :hide-details="!description"
        dense
        small-chips
        data-test="Name"
    />
</template>

<style scoped>
</style>
