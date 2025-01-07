<script setup lang="ts">
import {computed} from "vue";
import {getViolationsFeedback} from "../../../utils";

// const props = defineProps(['modelValue', 'value']); // vue3
// const emit = defineEmits(['update:modelValue']); // vue3
const emit = defineEmits(['input']);

export interface Props {
    value: boolean,
    label?: string,
    description?: string,
    error?: any,
    isLoading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    label: 'Wiedervorlage Dienstberatung',
    description: '',
    error: false,
    isLoading: false,
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

    return getViolationsFeedback(['resubmission'], props.error);
})
</script>

<template>
    <div>
        Wiedervorlage Dienstberatung<br>
        <v-switch
            v-model="value"
            :disabled="isLoading"
            :label="label"
            dense
            class="mt-0"
            :error-messages="errorMessages"
            :error="!!errorMessages?.length"
            :hint="description"
            :persistent-hint="!!description"
            :hide-details="!description"
            data-test="holidays"
            :loading="isLoading"
        ></v-switch>
    </div>
</template>

<style scoped>
</style>
