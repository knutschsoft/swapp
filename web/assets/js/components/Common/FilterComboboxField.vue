<script setup lang="ts">
import {computed, ref} from "vue";
import {ErrorData, getViolationsFeedback} from "../../utils";

// const props = defineProps(['modelValue', 'value']); // vue3
// const emit = defineEmits(['update:modelValue']); // vue3
const emit = defineEmits(['input']);

const suggestion = ref<string>('');

export interface Props {
    value: string[],
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
    get() {
        // return props.modelValue // vue3
        return props.value
    },
    set(value) {
        // emit('update:modelValue', value); // vue3
        emit('input', value === null ? '' : value)
    }
});

const errorMessages = computed(() => {
    if (!props.error) {
        return ''
    }

    return getViolationsFeedback(props.violationFields, props.error);
})

</script>

<template>
    <v-combobox
        v-model="value"
        :items="suggestions"
        chips
        deletable-chips
        clearable
        outlined
        multiple
        dense
        small-chips
        :success="value && value.length > 0"
        :label="label"
        :hint="hint"
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
            <v-list-item dense>
                <v-list-item-content>
                    <v-list-item-title>
                        Füge "<strong>{{ suggestion }}</strong>" hinzu.
                    </v-list-item-title>
                </v-list-item-content>
            </v-list-item>
        </template>
    </v-combobox>
</template>

<style scoped>
</style>
