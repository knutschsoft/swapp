<script setup lang="ts">
import {computed} from "vue";
import {ErrorData, getViolationsFeedback} from "@/js/utils";
import {useClientStore} from "@/js/stores";

const emit = defineEmits(['update:modelValue']);

export interface Props {
    modelValue: string | null;
    label?: string;
    hint?: string;
    placeholder?: string;
    dataTest?: string;
    error?: ErrorData;
    violationFields?: string[];
    isLoading?: boolean;
    disabled?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    label: 'Klient',
    hint: 'Nur bestimmten Klient anzeigen.',
    placeholder: 'Für welchen Klienten?',
    violationFields: () => [] as string[],
    dataTest: 'client',
    error: () => {return {} as ErrorData} ,
    isLoading: false,
    disabled: false,
});

const clientStore = useClientStore();

const value = computed({
  get: () => props.modelValue,
  set: (val) => emit("update:modelValue", val ?? ""),
});

const availableClients = computed(() => clientStore.getClients)

const errorMessages = computed(() => {
      return props.error ? getViolationsFeedback(props.violationFields, props.error) : "";
})

</script>

<template>
    <v-select
        v-model="value"
        :label="label"
        :placeholder="placeholder"
        :data-test="dataTest"
        clearable
        :items="availableClients"
        item-value="@id"
        item-title="name"
        :disabled="isLoading || disabled"
        variant="outlined"
        density="compact"
        :hint="hint"
        :hide-details="!hint && !errorMessages?.length"
        :error-messages="errorMessages"
        :error="!!errorMessages?.length"
    />
</template>

<style scoped>
</style>
