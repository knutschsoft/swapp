<script setup lang="ts">
import {computed, ref} from "vue";
import {Team, Walk} from "../../../model";
import {getViolationsFeedback} from "../../../utils";

// const props = defineProps(['modelValue', 'value']); // vue3
// const emit = defineEmits(['update:modelValue']); // vue3
const emit = defineEmits(['input']);

export interface Props {
    value: string[],
    team: Team,
    initialWalk?: Walk | null,
    label?: string,
    description?: string,
    error?: any,
    isLoading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    label: 'Tageskonzept',
    description: '',
    initialWalk: null,
    error: false,
    isLoading: false,
});
const conceptOfDaySearch = ref<string>('');
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

const conceptOfDaySuggestions = computed(() => {
    if (!props.team) return [];

    const initialConcepts = props.initialWalk?.conceptOfDay ?? [];
    const teamConcepts = props.team.conceptOfDaySuggestions;

    return [...new Set([...initialConcepts, ...teamConcepts])];
});

const errorMessages = computed(() => {
    if (!props.error) {
        return ''
    }

    return getViolationsFeedback(['conceptOfDay'], props.error);
})
</script>

<template>
    <v-combobox
        v-model="value"
        :items="conceptOfDaySuggestions"
        chips
        deletable-chips
        clearable
        outlined
        multiple
        dense
        small-chips
        :label="label"
        :hint="description"
        :persistent-hint="!!description"
        :hide-details="!description && !errorMessages?.length"
        data-test="Tageskonzept"
        placeholder="Tageskonzept eintragen..."
        :disabled="isLoading"
        :loading="isLoading"
        :hide-no-data="!conceptOfDaySearch"
        :search-input.sync="conceptOfDaySearch"
        :error-messages="errorMessages"
        :error="!!errorMessages?.length"
    >
        <template v-slot:no-data>
            <v-list-item dense>
                <v-list-item-content>
                    <v-list-item-title>
                        Füge "<strong>{{ conceptOfDaySearch }}</strong>" hinzu.
                    </v-list-item-title>
                </v-list-item-content>
            </v-list-item>
        </template>
    </v-combobox>
</template>

<style scoped>
</style>
