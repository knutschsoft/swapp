<script setup lang="ts">
import {computed} from "vue";
import {getViolationsFeedback} from "../../../utils";

// const props = defineProps(['modelValue', 'value']); // vue3
// const emit = defineEmits(['update:modelValue']); // vue3
const emit = defineEmits(['input']);

export interface Props {
    value: string,
    label?: string,
    description?: string,
    error?: any,
    isLoading?: boolean
    disabled?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    label: 'Termine, Besorgungen, Verabredungen',
    description: '',
    error: false,
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

    return getViolationsFeedback(['commitments'], props.error);
})

</script>

<template>
    <div>
        <v-textarea
            v-model="value"
            :disabled="isLoading || disabled"
            minlength="1"
            maxlength="2500"
            :label="label"
            placeholder="Termine, Besorgungen, Verabredungen"
            data-test="commitments"
            rows="3"
            trim
            max-rows="15"
            outlined
            dense
            :hint="description"
            :persistent-hint="!!description"
            :hide-details="!description"
            :error-messages="errorMessages"
            :error="!!errorMessages?.length"
        />
    </div>
</template>

<style scoped>
</style>
