<script setup lang="ts">
import {computed, ref} from "vue";
import {getViolationsFeedback} from "../../../utils";

const emit = defineEmits(['update:modelValue']);

export interface Props {
    modelValue: string,
    label?: string,
    description?: string,
    error?: Object | boolean,
    isLoading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    label: 'Wetter',
    description: '',
    error: false,
    isLoading: false,
});

const weatherOptions = ref<string[]>(['', 'Sonne', 'Wolken', 'Regen', 'Schnee', 'Arschkalt']);

const value = computed({
  get: () => props.modelValue,
  set: (val) => emit("update:modelValue", val),
});

const errorMessages = computed(() => {
    return getViolationsFeedback(['weather'], props.error);
})

</script>

<template>
    <v-select
        v-model="value"
        :items="weatherOptions"
        :label="label"
        data-test="Wetter"
        variant="outlined"
        :hint="description"
        :persistent-hint="!!description"
        density="compact"
        :disabled="isLoading"
        :error-messages="errorMessages"
        :error="!!errorMessages?.length"
    />
</template>

<style scoped>
</style>
