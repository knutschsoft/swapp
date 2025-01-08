<script setup lang="ts">
import {computed} from "vue";
import {getViolationsFeedback} from "../../../utils";
import {Team, Walk} from "../../../model";

// const props = defineProps(['modelValue', 'value']); // vue3
// const emit = defineEmits(['update:modelValue']); // vue3
const emit = defineEmits(['input']);

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
        emit('input', value === null ? '' : value)
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
        outlined
        :label="label"
        placeholder="Wie ist der Name der Runde?"
        :disabled="isLoading"
        :loading="isLoading"
        :hint="description"
        :persistent-hint="!!description"
        :hide-details="!description && !errorMessages?.length"
        dense
        small-chips
        :error-messages="errorMessages"
        :error="!!errorMessages?.length"
        data-test="Name"
    />
</template>

<style scoped>
</style>
