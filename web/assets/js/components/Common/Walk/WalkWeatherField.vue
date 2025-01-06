<script setup lang="ts">
import {computed, ref} from "vue";
import {getViolationsFeedback} from "../../../utils";

// const props = defineProps(['modelValue', 'value']); // vue3
// const emit = defineEmits(['update:modelValue']); // vue3
const emit = defineEmits(['input']);

export interface Props {
    value: string,
    label?: string,
    description?: string,
    validationErrors?: Object | undefined,
    isLoading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    label: 'Wetter',
    description: '',
    isLoading: false,
});

const weatherOptions = ref<string[]>(['', 'Sonne', 'Wolken', 'Regen', 'Schnee', 'Arschkalt']);

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
    return getViolationsFeedback(['weather'], props.error);
})

</script>

<template>
    <div>
    <v-select
        v-model="value"
        :items="weatherOptions"
        :label="label"
        data-test="Wetter"
        outlined
        :hint="description"
        :persistent-hint="!!description"
        dense
        :disabled="isLoading"
        :error-messages="errorMessages"
        :error="!!errorMessages?.length"
    />
    </div>

</template>

<style scoped>
</style>
