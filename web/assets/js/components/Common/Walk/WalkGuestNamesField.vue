<script setup lang="ts">
import {computed, ref} from "vue";
import {Team, Walk} from "../../../model";
import {getViolationsFeedback} from "../../../utils";

const emit = defineEmits(['update:modelValue']);

export interface Props {
    modelValue: string[],
    team: Team,
    initialWalk?: Walk | null,
    label?: string,
    description?: string,
    error?: any,
    isLoading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    label: 'Weitere Teilnehmende',
    description: '',
    initialWalk: null,
    error: false,
    isLoading: false,
});
const guestNameSearch = ref<string>('');
const value = computed({
  get: () => props.modelValue,
  set: (val) => emit("update:modelValue", val),
});

const guestNames = computed(() => {
    if (!props.team || !props.initialWalk || !props.initialWalk.isWithGuests) {
        return [];
    }

    return [
        ...new Set(props.initialWalk.guestNames.concat(props.team.guestNames))
    ];
});

const errorMessages = computed(() => {
    if (!props.error) {
        return ''
    }

    return getViolationsFeedback(['guestNames'], props.error);
})
</script>

<template>
    <v-combobox
        v-model="value"
        :items="guestNames"
        chips
        deletable-chips
        clearable
        multiple
        variant="outlined"
        density="compact"
        small-chips
        :label="label"
        :hint="description"
        :persistent-hint="!!description"
        :hide-details="!description && !errorMessages?.length"
        placeholder="Namen eintragen..."
        :hide-no-data="!guestNameSearch"
        :search-input.sync="guestNameSearch"
        :disabled="isLoading"
        :loading="isLoading"
        :error-messages="errorMessages"
        :error="!!errorMessages?.length"
    >
        <template v-slot:no-data>
            <v-list-item density="compact">
                <v-list-item-title>
                    Füge "<strong>{{ guestNameSearch }}</strong>" hinzu.
                </v-list-item-title>
            </v-list-item>
        </template>
    </v-combobox>
</template>

<style scoped>
</style>
