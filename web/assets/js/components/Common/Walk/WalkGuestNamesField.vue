<script setup lang="ts">
import {computed, ref} from "vue";
import {Team, Walk} from "../../../model";
import {getViolationsFeedback} from "../../../utils";
import {useDisplay} from "vuetify";

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
const comboRef = ref<InstanceType<typeof VCombobox> | null>(null)
const { mobile } = useDisplay()
const guestNameSearch = ref<string>('');
const value = computed({
  get: () => props.modelValue,
  set: (val) => emit("update:modelValue", val),
});

const guestNames = computed(() => {
    const names = [];

    if (props.team && Array.isArray(props.team.guestNames)) {
        names.push(...props.team.guestNames);
    }

    if (
        props.initialWalk &&
        props.initialWalk.isWithGuests &&
        Array.isArray(props.initialWalk.guestNames)
    ) {
        names.push(...props.initialWalk.guestNames);
    }

    return [...new Set(names)].sort((a, b) => a.localeCompare(b));
});

const errorMessages = computed(() => {
    if (!props.error) {
        return ''
    }

    return getViolationsFeedback(['guestNames'], props.error);
})
function closeCombobox () {
    comboRef.value?.blur()
}
</script>

<template>
    <v-combobox
        ref="comboRef"
        v-model="value"
        :items="guestNames"
        chips
        deletable-chips
        clearable
        multiple
        data-test="walk-guest-names-field"
        variant="outlined"
        density="compact"
        small-chips
        :label="label"
        :hint="description"
        :persistent-hint="!!description"
        :hide-details="!description && !errorMessages?.length"
        placeholder="Namen eintragen..."
        :hide-no-data="false"
        v-model:search="guestNameSearch"
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
