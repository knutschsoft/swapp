<script setup lang="ts">
import {computed, ref} from "vue";
import {ErrorData, getViolationsFeedback} from "../../utils";

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
    label: '',
    hint: '',
    placeholder: '',
    violationFields: () => [] as string[],
    dataTest: '',
    error: () => {return {} as ErrorData} ,
    isLoading: false,
    disabled: false,
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

const items = ref<{ title: string; value: string }[]>([
    {title: 'ja', value: '1'},
    {title: 'nein', value: '0'}
]);

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
        :items="items"
        :label="label"
        clearable
        :data-test="dataTest"
        outlined
        persistent-placeholder
        placeholder="egal"
        :success="value !== ''"
        :hint="hint"
        :persistent-hint="!!hint"
        :hide-details="!hint"
        dense
        :disabled="isLoading"
        :error-messages="errorMessages"
        :error="!!errorMessages?.length"
        item-value="value"
        item-text="title"
    />
</template>

<style scoped>
</style>
