<script setup lang="ts">
import {computed, ref} from "vue";
import {ErrorData, getViolationsFeedback} from "@/js/utils";

const emit = defineEmits(['update:modelValue', 'cleared']);

export interface Props {
    modelValue: boolean | string,
    defaultValue?: boolean | string,
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
    modelValue: 'null',
    defaultValue: 'null',
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

const items = ref<{ title: string; value: boolean|string }[]>([
    {title: 'egal', value: 'null'},
    {title: 'ja', value: true},
    {title: 'nein', value: false}
]);

const errorMessages = computed(() => {
      return props.error ? getViolationsFeedback(props.violationFields, props.error) : "";
})

</script>

<template>
    <v-select
        v-model="value"
        :items="items"
        :label="label"
        :clearable="value !== defaultValue"
        :data-test="dataTest"
        variant="outlined"
        :persistent-clear="value !== defaultValue"
        persistent-placeholder
        placeholder="egal"
        :class="value !== defaultValue ? 'text-primary' : ''"
        :hint="hint"
        :persistent-hint="!!hint"
        :hide-details="!hint"
        density="compact"
        :disabled="isLoading"
        :error-messages="errorMessages"
        :error="!!errorMessages?.length"
        item-value="value"
        item-title="title"
        @click:clear="emit('cleared')"
    />
</template>

<style scoped>
</style>
