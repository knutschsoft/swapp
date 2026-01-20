<script setup lang="ts">
import {computed, ref} from "vue";
import {Team, Walk} from "../../../model";
import {getViolationsFeedback} from "../../../utils";
import {useDisplay} from "vuetify";

const emit = defineEmits(['update:modelValue']);

export interface Props {
    modelValue: string[],
    team?: Team | null,
    initialWalk?: Walk | null,
    label?: string,
    description?: string,
    error?: any,
    isLoading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    label: 'Tageskonzept',
    description: '',
    team: null,
    initialWalk: null,
    error: false,
    isLoading: false,
});
const comboRef = ref<InstanceType<typeof VCombobox> | null>(null)
const { mobile } = useDisplay()
const conceptOfDaySearch = ref<string>('');
const value = computed({
  get: () => props.modelValue,
  set: (val) => emit("update:modelValue", val),
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
function closeCombobox () {
    comboRef.value?.blur()
}
</script>

<template>
    <v-combobox
        ref="comboRef"
        v-model="value"
        :items="conceptOfDaySuggestions"
        chips
        deletable-chips
        clearable
        variant="outlined"
        multiple
        density="compact"
        small-chips
        :label="label"
        :hint="description"
        :persistent-hint="!!description"
        :hide-details="!description && !errorMessages?.length"
        data-test="Tageskonzept"
        placeholder="Tageskonzept eintragen..."
        :disabled="isLoading"
        :loading="isLoading"
        :hide-no-data="false"
        v-model:search="conceptOfDaySearch"
        :error-messages="errorMessages"
        :error="!!errorMessages?.length"
    >
        <template v-slot:no-data>
            <v-list-item density="compact">
                <v-list-item-title>
                    Füge "<strong>{{ conceptOfDaySearch }}</strong>" hinzu.
                </v-list-item-title>
            </v-list-item>
        </template>
        <template v-if="mobile" #append-item>
            <v-divider />

            <v-list-item class="px-2">
                <v-btn
                    block
                    variant="text"
                    size="large"
                    @click="closeCombobox"
                >
                    Fertig
                </v-btn>
            </v-list-item>
        </template>
    </v-combobox>
</template>

<style scoped>
</style>
