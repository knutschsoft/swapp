<script setup lang="ts">
import {computed, ref} from "vue";
import {getViolationsFeedback} from "../../../utils";

const emit = defineEmits(['update:modelValue']);

export interface Props {
    modelValue: number,
    label?: string,
    hint?: string,
    error?: Object | boolean,
    isLoading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    label: 'Wetter',
    hint: '',
    error: false,
    isLoading: false,
});

const contactsCountOptions = ref<number[]>(Array.from(Array(41), (x, i) => i));

const value = computed({
  get: () => props.modelValue,
  set: (val) => emit("update:modelValue", val),
});

const errorMessages = computed(() => {
    return getViolationsFeedback(['contactsCount'], props.error);
})

</script>

<template>
    <v-select
        v-model="value"
        :items="contactsCountOptions"
        :label="label"
        data-test="contactsCount"
        variant="outlined"
        :hint="hint"
        :persistent-hint="!!hint"
        density="compact"
        :disabled="isLoading"
        :error-messages="errorMessages"
        :error="!!errorMessages?.length"
    />
</template>

<style scoped>
</style>
