<script setup lang="ts">
import {computed, ref, watch} from "vue";
import {getViolationsFeedback} from "../../../utils";
import {WayPoint} from "../../../model";
import DatePicker from "vue2-datepicker";
import 'vue2-datepicker/index.css';
import 'vue2-datepicker/locale/de';
import dayjs from "dayjs";

// const props = defineProps(['modelValue', 'value']); // vue3
// const emit = defineEmits(['update:modelValue']); // vue3
const emit = defineEmits(['input']);

export interface Props {
    value: string,
    initialWayPoint?: WayPoint | null,
    description?: string,
    error?: any,
    isLoading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    description: '',
    initialWayPoint: null,
    error: false,
    isLoading: false,
});

const visitedAtTime = ref(props.initialWayPoint ? dayjs(props.initialWayPoint.visitedAt).toDate() : dayjs().toDate());
const visitedAtDate = ref(props.initialWayPoint ? dayjs(props.initialWayPoint.visitedAt).toDate() : dayjs().toDate());
const datePickerLang = ref({
    formatLocale: {
        firstDayOfWeek: 1,
    },
    monthBeforeYear: true,
});

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

watch(() => props.value, () => {
    if (dayjs(visitedAtTime.value) !== dayjs(props.value)) {
        visitedAtTime.value = dayjs(props.value).toDate();
    }
    if (dayjs(visitedAtDate.value) !== dayjs(props.value)) {
        visitedAtDate.value = dayjs(props.value).toDate();
    }
})
watch(() => visitedAtTime.value, () => {
    let visitedAtDate = dayjs(props.value);
    let visitedAt = dayjs(visitedAtTime.value);
    visitedAtDate = visitedAtDate.hour(visitedAt.hour());
    visitedAtDate = visitedAtDate.minute(visitedAt.minute());
    visitedAtDate = visitedAtDate.startOf('minute');
    value.value = visitedAtDate.format();
});
watch(() => visitedAtDate.value, () => {
    const visitedAtDateValue = dayjs(visitedAtDate.value);
    let visitedAt = dayjs(props.value);
    visitedAt = visitedAt.year(visitedAtDateValue.year());
    visitedAt = visitedAt.month(visitedAtDateValue.month());
    visitedAt = visitedAt.date(visitedAtDateValue.date());
    visitedAt = visitedAt.startOf('minute');
    value.value = visitedAt.format();
});

const errorMessages = computed(() => {
    if (!props.error) {
        return ''
    }
    return getViolationsFeedback(['visitedAt', 'visitedAtBeforeEndTime', 'visitedAtBeforeAllWayPoints'], props.error);
})

</script>

<template>
    <div>
        Ankunft<br>
        <date-picker
            v-model="visitedAtDate"
            :disabled="isLoading"
            format="DD.MM.YYYY"
            title-format="DD.MM.YYYY"
            show-week-number
            data-test="visitedAtDate"
            :lang="datePickerLang"
            :clearable="false"
        />
        <div class="d-none d-md-block mb-2"></div>
        <date-picker
            v-model="visitedAtTime"
            type="time"
            :disabled="isLoading"
            data-test="visitedAtTime"
            :minute-step="5"
            format="HH:mm"
            title-format="HH:mm"
            :show-second="false"
            :lang="datePickerLang"
            :clearable="false"
        />
        <div class="text-disabeld text-caption">{{ description }}</div>
        <v-alert
            v-if="!!errorMessages?.length"
            type="error"
        >
            {{ errorMessages }}
        </v-alert>
    </div>
</template>

<style scoped>
</style>
