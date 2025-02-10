<script setup lang="ts">
import {computed} from "vue";
import {getViolationsFeedback} from "../../../utils";

const emit = defineEmits(['update:modelValue']);

export interface Props {
    modelValue: boolean,
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
  get: () => props.modelValue,
  set: (val) => emit("update:modelValue", val),
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
            density="compact"
            color="primary"
            class="mt-0"
            :error-messages="errorMessages"
            :error="!!errorMessages?.length"
            :hint="description"
            :persistent-hint="!!description"
            :hide-details="!description && !errorMessages?.length"
            data-test="holidays"
            :loading="isLoading"
        ></v-switch>
    </div>
</template>

<style scoped>
</style>
