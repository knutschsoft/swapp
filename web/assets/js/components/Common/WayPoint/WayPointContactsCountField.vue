<script setup lang="ts">
import {computed, ref} from "vue";
import {getViolationsFeedback} from "../../../utils";

// const props = defineProps(['modelValue', 'value']); // vue3
// const emit = defineEmits(['update:modelValue']); // vue3
const emit = defineEmits(['input']);

export interface Props {
    value: number,
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
    return getViolationsFeedback(['contactsCount'], props.error);
})

</script>

<template>
    <v-select
        v-model="value"
        :items="contactsCountOptions"
        :label="label"
        data-test="contactsCount"
        outlined
        :hint="hint"
        :persistent-hint="!!hint"
        dense
        :disabled="isLoading"
        :error-messages="errorMessages"
        :error="!!errorMessages?.length"
    />
</template>

<style scoped>
</style>
