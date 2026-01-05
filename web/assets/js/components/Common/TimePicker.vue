<script setup lang="ts">
import {defineProps, computed, ref} from "vue";
import { de } from 'date-fns/locale'
import {breakpointsVuetifyV3, useBreakpoints} from "@vueuse/core";

interface Props {
    modelValue: any;
    placeholder: string;
    dataTest?: string;
    enableTimePicker?: boolean;
    clearable?: boolean;
    isLoading?: boolean;
    isFilter?: boolean;
}
type CustomClass = string | string[];

interface UIOptions {
    navBtnNext: CustomClass;
    navBtnPrev: CustomClass;
    calendar: CustomClass;
    calendarCell: CustomClass;
    menu: CustomClass;
    input: CustomClass;
}

const props = withDefaults(defineProps<Props>(), {
    placeholder: 'Zeitraum wählen...',
    isFilter: false,
    clearable: false,
});

const activeUi = ref<UIOptions>({
    navBtnNext: '',
    navBtnPrev: '',
    calendar: '',
    calendarCell: '',
    menu: '',
    input: props.isFilter ? 'active-bg text-primary' : '',
});

const emit = defineEmits(["update:modelValue", "cleared"]);

const dateRange = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});
const breakpoints = useBreakpoints(breakpointsVuetifyV3)
const isGreaterThanMd = computed(() => breakpoints.greater("md"));
const count = computed(() => (breakpoints.greater("md").value ? 2 : 0));
const multiCalendars = computed(() => {
    return {
        solo: false,
        static: true,
        count: count.value,
    }
})
</script>

<template>
    <VueDatePicker
        v-model="dateRange"
        :week-numbers="{ type: 'iso' }"
        :placeholder="placeholder"
        :multi-calendars="multiCalendars"
        time-picker
        :minutes-increment="5"
        :start-time="dateRange"
        :ui="dateRange ? activeUi : {}"
        :data-test="dataTest"
        :text-input="isGreaterThanMd.value"
        auto-apply
        :locale="de"
        cancel-text="abbrechen"
        select-text="auswählen"
        :teleport="true"
        six-weeks="center"
        :action-row="{ showPreview: true }"
        :loading="isLoading"
        @cleared="$emit('cleared')"
        :input-attrs="{autocomplete: 'off', clearable: clearable}"
    >
<!--        <template #left-sidebar>-->
<!--        </template>-->
        <template #clear-icon="{ clear }">
            <v-icon icon="mdi-close-circle" color="primary-lighten-2" class="mr-2" @click="clear" />
        </template>
        <template #input-icon>
            <v-icon icon="mdi-clock-outline" color="" class="ml-2" />
        </template>
    </VueDatePicker>
</template>

<style>
.active-bg {
    background-color: rgba(24, 103, 192, 0.04);
    /*
    background-color: #1867C0;
    opacity: 0.04;
    transition: opacity 250ms cubic-bezier(0.4, 0, 0.2, 1);
    */
}
</style>
