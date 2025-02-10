<script setup lang="ts">
import {computed} from "vue";
import {getViolationsFeedback} from "../../../utils";

const emit = defineEmits(['update:modelValue']);

export interface Props {
    modelValue: string,
    label?: string,
    description?: string,
    error?: any,
    isLoading?: boolean
    disabled?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    label: 'Systemische Antwort',
    description: '',
    error: false,
    isLoading: false,
    disabled: false,
});

const value = computed({
  get: () => props.modelValue,
  set: (val) => emit("update:modelValue", val),
});


const errorMessages = computed(() => {
    if (!props.error) {
        return ''
    }

    return getViolationsFeedback(['systemicAnswer'], props.error);
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
            placeholder="Systemische Antwort"
            data-test="systemicAnswer"
            rows="3"
            trim
            max-rows="15"
            auto-grow
            variant="outlined"
            density="compact"
            :hint="description"
            :persistent-hint="!!description"
            :hide-details="!description && !errorMessages?.length"
            :error-messages="errorMessages"
            :error="!!errorMessages?.length"
        />
    </div>
</template>

<style scoped>
</style>
