<script setup lang="ts">
import {computed} from "vue";
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

const errorMessages = computed(() => {
    if (!props.error) {
        return ''
    }

    return getViolationsFeedback(props.violationFields, props.error);
})

</script>

<template>
    <v-text-field
        v-model="value"
        :label="label"
        :placeholder="placeholder"
        :data-test="dataTest"
        trim
        outlined
        dense
        clearable
        :persistent-clear="!!value"
        class=""
        autocomplete="off"
        :success="!!value"
        list="team-name-for-walk-list"
        density="compact"
        :hint="hint"
        :persistent-hint="!!hint"
        :hide-details="!hint && !errorMessages?.length"
        :error-messages="errorMessages"
        :error="!!errorMessages?.length"
    />
</template>

<style scoped>
</style>
