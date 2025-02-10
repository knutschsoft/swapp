<script setup lang="ts">
import {computed, onMounted, ref, watch} from "vue";
import {getViolationsFeedback} from "../../../utils";
import {WayPoint} from "../../../model";
import dayjs from "dayjs";
import {DatePicker, TimePicker} from "@/js/components/Common";

const emit = defineEmits(['update:modelValue']);

export interface Props {
    modelValue: string,
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

const visitedAtTime = ref<{
    hours: number;
    minutes: number;
    seconds: number;
}>({hours: 0, minutes: 0, seconds: 0});
const visitedAtDate = ref(props.initialWayPoint ? dayjs(props.initialWayPoint.visitedAt).toDate() : dayjs().toDate());

const value = computed({
    get: () => props.modelValue,
    set: (val) => emit("update:modelValue", val),
});

const visitedAtTimeAsDayJs = computed(() => {
    return dayjs().hour(visitedAtTime.value.hours).minute(visitedAtTime.value.minutes).second(visitedAtTime.value.seconds);
});

onMounted(() => {
    visitedAtTime.value = props.initialWayPoint ? {
        hours: dayjs(props.initialWayPoint.visitedAt).hour(),
        minutes: dayjs(props.initialWayPoint.visitedAt).minute(),
        seconds: 0,
    } : {
        hours: dayjs().hour(),
        minutes: dayjs().minute(),
        seconds: 0,
    }
    visitedAtDate.value = props.initialWayPoint ? dayjs(props.initialWayPoint.visitedAt).toDate() : dayjs().toDate()
})

watch(() => props.modelValue, () => {
    if (visitedAtTimeAsDayJs.value.hour() !== dayjs(props.modelValue).hour() && visitedAtTimeAsDayJs.value.minute() !== dayjs(props.modelValue).minute()) {
        visitedAtTime.value.hours = dayjs(props.modelValue).hour();
        visitedAtTime.value.minutes = dayjs(props.modelValue).minute();
        visitedAtTime.value.seconds = 0;
    }
    if (dayjs(visitedAtDate.value) !== dayjs(props.modelValue)) {
        visitedAtDate.value = dayjs(props.modelValue).toDate();
    }
})
watch(() => visitedAtTime.value, () => {
    let visitedAtDate = dayjs(props.modelValue);
    visitedAtDate = visitedAtDate.hour(visitedAtTimeAsDayJs.value.hour());
    visitedAtDate = visitedAtDate.minute(visitedAtTimeAsDayJs.value.minute());
    visitedAtDate = visitedAtDate.startOf('minute');
    value.value = visitedAtDate.format();
});
watch(() => visitedAtDate.value, () => {
    const visitedAtDateValue = dayjs(visitedAtDate.value);
    let visitedAt = dayjs(props.modelValue);
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
        <div class="d-flex flex-md-column">
            <time-picker
                v-model="visitedAtTime"
                :is-loading="isLoading"
                data-test="visitedAtTime"
                placeholder="Rundenstartzeit"
                :clearable="false"
                class="mb-2"
            />
            <date-picker
                v-model="visitedAtDate"
                :is-loading="isLoading"
                data-test="visitedAtDate"
                placeholder="Rundenstartzeit"
                :clearable="false"
            />
        </div>
        <div class="text-disabled text-caption">{{ description }}</div>
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
