<script setup lang="ts">
import {defineProps, computed, ref} from "vue";
import { de } from 'date-fns/locale'
import { breakpointsVuetifyV3, useBreakpoints } from '@vueuse/core'
import {useThemeMode} from '@/js/composables/useThemeMode'
import dayjs from "dayjs";

interface Props {
    modelValue: [string, string] | null;
    placeholder?: string;
    dataTest?: string;
    enableTimePicker?: boolean;
    isLoading?: boolean;
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

const activeUi = ref<UIOptions>({
    navBtnNext: '',
    navBtnPrev: '',
    calendar: '',
    calendarCell: '',
    menu: '',
    input: 'active-bg text-primary',
})

const props = withDefaults(defineProps<Props>(), {
    placeholder: 'Zeitraum wählen...'
});
const emit = defineEmits(["update:modelValue", "cleared"]);

const dateRange = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
})

const { isDark } = useThemeMode()

const now = dayjs()
let presetDatesValue =[
    { label: 'Dieser Monat', value: [now.startOf('month').toDate(), now.endOf('month').toDate()] },
    { label: 'Letzter Monat', value: [now.subtract(1, 'month').startOf('month').toDate(), now.subtract(1, 'month').endOf('month').toDate()] },
    { label: 'Letzte 6 Monate', value: [now.subtract(5, 'month').startOf('month').toDate(), now.endOf('month').toDate()] },
    { label: 'Dieses Jahr', value: [now.startOf('year').toDate(), now.endOf('year').toDate()] },
    { label: 'Letztes Jahr', value: [now.subtract(1, 'year').startOf('year').toDate(), now.subtract(1, 'year').endOf('year').toDate()] },
    { label: 'Vorletztes Jahr', value: [now.subtract(2, 'year').startOf('year').toDate(), now.subtract(2, 'year').endOf('year').toDate()] },
]

for (let i = -1; i <= 10; i++) {
    if (i % 2 === 0) {
        continue;
    }
    const quarter = now.subtract(i, 'quarter').quarter();
    let halfOfYear;
    switch (quarter) {
        case 1:
            halfOfYear = 1;
            break;
        case 2:
            halfOfYear = 1;
            break;
        case 3:
            halfOfYear = 2;
            break;
        case 4:
            halfOfYear = 2;
            break;
        default:
            halfOfYear = 1;
    }

    presetDatesValue.push({
        label: `${now.subtract(i, 'quarter').year()} ${halfOfYear}. Halbjahr`,
        value: [
            now.subtract(i + 1, 'quarter').startOf('quarter').toDate(),
            now.subtract(i, 'quarter').endOf('quarter').toDate()
        ]
    });
}
for (let i = 1; i <= 4; i++) {
    presetDatesValue.push({
        label: `${now.subtract(i, 'quarter').year()} ${now.subtract(i, 'quarter').quarter()}. Quartal`,
        value: [
            now.subtract(i, 'quarter').startOf('quarter').toDate(),
            now.subtract(i, 'quarter').endOf('quarter').toDate()
        ]
    });
}
const presetDates = ref(presetDatesValue);
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
        model-type="iso"
        :week-numbers="{ type: 'iso' }"
        :placeholder="placeholder"
        :multi-calendars="multiCalendars"
        range
        :ui="dateRange ? activeUi : {}"
        :data-test="dataTest"
        auto-apply
        :locale="de"
        :time-config="{ enableTimePicker: enableTimePicker }"
        :formats="{input: 'dd.LL.y'}"
        cancel-text="abbrechen"
        select-text="auswählen"
        :teleport="true"
        six-weeks="center"
        :action-row="{ showPreview: true }"
        :loading="isLoading"
        @cleared="$emit('cleared')"
        :input-attrs="{autocomplete: 'off', clearable: true}"
        :month-change-on-scroll="false"
        :preset-dates="isGreaterThanMd.value ? presetDates : []"
        :dark="isDark"
    >
        <template #clear-icon="{ clear }">
            <v-icon icon="mdi-close-circle" color="primary-lighten-2" class="mr-2" @click="clear" />
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
