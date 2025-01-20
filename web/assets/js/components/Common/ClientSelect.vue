<script setup lang="ts">
import {computed} from "vue";
import {ErrorData, getViolationsFeedback} from "../../utils";
import {useClientStore} from "@/js/stores";

// const props = defineProps(['modelValue', 'value']); // vue3
// const emit = defineEmits(['update:modelValue']); // vue3
const emit = defineEmits(['input']);

export interface Props {
    value: string,
    label?: string,
    hint?: string,
    placeholder?: string,
    dataTest?: string,
    error?: ErrorData,
    violationFields?: string[],
    isLoading?: boolean
    disabled?: boolean
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
    get() {
        // return props.modelValue // vue3
        return props.value
    },
    set(value) {
        // emit('update:modelValue', value); // vue3
        emit('input', value === null ? '' : value)
    }
});

const availableClients = computed(() => {
    return clientStore.getClients;
})

const errorMessages = computed(() => {
    if (!props.error) {
        return ''
    }

    return getViolationsFeedback(props.violationFields, props.error);
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
        item-text="name"
        :disabled="isLoading || disabled"
        outlined
        dense
        :hint="hint"
        :hide-details="!hint && !errorMessages?.length"
        :error-messages="errorMessages"
        :error="!!errorMessages?.length"
    />
</template>

<style scoped>
</style>
