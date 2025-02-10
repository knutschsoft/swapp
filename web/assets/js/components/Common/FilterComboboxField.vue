<script setup lang="ts">
import {computed, ref} from "vue";
import {ErrorData, getViolationsFeedback} from "../../utils";

const emit = defineEmits(['update:modelValue']);

const suggestion = ref<string>('');

export interface Props {
    modelValue: string[],
    suggestions?: string[],
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
    suggestions: () => [] as string[],
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
    <v-combobox
        v-model="value"
        :items="suggestions"
        chips
        deletable-chips
        clearable
        variant="outlined"
        multiple
        density="compact"
        small-chips
        :class="value.length > 0 ? 'text-primary' : ''"
        :label="label"
        :hint="hint"
        :persistent-clear="value?.length > 0"
        :persistent-hint="!!hint"
        :hide-details="!hint && !errorMessages?.length"
        :error-messages="errorMessages"
        :error="!!errorMessages?.length"
        :data-test="dataTest"
        :placeholder="placeholder"
        :loading="isLoading"
        :hide-no-data="!suggestion"
        :search-input.sync="suggestion"
    >
        <template v-slot:no-data>
            <v-list-item density="compact">
                <v-list-item-title>
                    Füge "<strong>{{ suggestion }}</strong>" hinzu.
                </v-list-item-title>
            </v-list-item>
        </template>
    </v-combobox>
</template>

<style scoped>
</style>
