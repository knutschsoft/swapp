<script setup lang="ts">
import {computed} from "vue";
import {ErrorData, getViolationsFeedback} from "../../utils";

const emit = defineEmits(['update:modelValue']);

export interface Props {
    caption: string,
    modelValue: boolean,
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
  get: () => props.modelValue,
  set: (val) => emit("update:modelValue", val),
});

const errorMessages = computed(() => {
      return props.error ? getViolationsFeedback(props.violationFields, props.error) : "";
})

</script>

<template>
    <div class="mb-2">
        {{ caption }}<br>
        <v-switch
            v-model="value"
            :disabled="isLoading || disabled"
            :label="label"
            density="compact"
            color="primary"
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
