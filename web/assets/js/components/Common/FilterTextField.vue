<script setup lang="ts">
import {computed} from "vue";
import {ErrorData, getViolationsFeedback} from "../../utils";

const emit = defineEmits(['update:modelValue']);

export interface Props {
    modelValue: string,
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
  set: (val) => emit("update:modelValue", val ?? ""),
});

const errorMessages = computed(() => {
      return props.error ? getViolationsFeedback(props.violationFields, props.error) : "";
})

</script>

<template>
    <v-text-field
        v-model="value"
        :label="label"
        :placeholder="placeholder"
        :data-test="dataTest"
        trim
        variant="outlined"
        clearable
        :persistent-clear="!!value"
        class=""
        autocomplete="off"
        :class="!!value ? 'text-primary' : ''"
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
