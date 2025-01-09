<script setup lang="ts">
import {computed} from "vue";
import {ErrorData, getViolationsFeedback} from "../../utils";

// const props = defineProps(['modelValue', 'value']); // vue3
// const emit = defineEmits(['update:modelValue']); // vue3
const emit = defineEmits(['input']);

export interface Props {
    caption: string,
    value: boolean,
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
        emit('input', value)
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
    <div class="mb-2">
        {{ caption }}<br>
        <v-switch
            v-model="value"
            :disabled="isLoading || disabled"
            :label="label"
            dense
            class="mt-0"
            :error-messages="errorMessages"
            :error="!!errorMessages?.length"
            :hint="hint"
            :persistent-hint="!!hint"
            :hide-details="!hint && !errorMessages?.length"
            :data-test="dataTest"
            :loading="isLoading"
        ></v-switch>
    </div>
</template>

<style scoped>
</style>
