<script setup lang="ts">
import {computed, ref, watchEffect} from "vue";
import {ErrorData, getViolationsFeedback} from "../../utils";
export type SuggestionItem =
    | { type: 'item'; title: string }
    | { type: 'divider'; text?: string };

const emit = defineEmits(['update:modelValue']);

const suggestion = ref<string>('');

export interface Props {
    modelValue: string[],
    suggestions?: SuggestionItem[],
    label?: string,
    hint?: string,
    placeholder?: string,
    dataTest?: string,
    error?: ErrorData,
    violationFields?: string[],
    isLoading?: boolean
    disabled?: boolean
    hideNoData?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    label: '',
    hint: '',
    placeholder: '',
    violationFields: () => [] as string[],
    suggestions: () => [] as SuggestionItem[],
    dataTest: '',
    error: () => {return {} as ErrorData} ,
    isLoading: false,
    disabled: false,
    hideNoData: false,
});

const value = computed({
  get: () => props.modelValue,
  set: (val) => emit("update:modelValue", val ?? []),
});

const errorMessages = computed(() => {
    if (!props.error) return []

    try {
        return getViolationsFeedback(props.violationFields, props.error)
    } catch (e) {
        console.warn('Invalid error object', props.error)
        return ['Unbekannter Fehler']
    }

      return props.error ? getViolationsFeedback(props.violationFields, props.error) : "";
})

</script>

<template>
    <v-combobox
        v-model="value"
        :items="suggestions"
        chips
        closable-chips
        clearable
        variant="outlined"
        multiple
        density="compact"
        small-chips
        item-value="title"
        item-title="title"
        auto-select-first="exact"
        :class="value?.length > 0 ? 'text-primary' : ''"
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
        :hide-no-data="hideNoData"
        :return-object="false"
        v-model:search="suggestion"
    >
        <template #item="{ item, props }">
            <v-list-item v-if="item.raw.type === 'item'" v-bind="props" density="compact">
            </v-list-item>
            <v-divider v-if="item.raw.type === 'divider'">
                <template v-if="item.raw.text">
                    <v-list-item-title class="text-caption font-weight-medium">{{ item.raw.text }}</v-list-item-title>
                </template>
            </v-divider>
        </template>
        <template #no-data>
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
