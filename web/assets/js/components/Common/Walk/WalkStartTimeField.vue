<script setup lang="ts">
import {computed, onMounted, ref, watch} from "vue";
import {getViolationsFeedback} from "../../../utils";
import {Walk} from "../../../model";
import dayjs from "dayjs";
import {DatePicker, TimePicker} from "@/js/components/Common";

const emit = defineEmits(['update:modelValue']);

export interface Props {
    modelValue: string,
    initialWalk?: Walk | null,
    description?: string,
    error?: any,
    isLoading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    description: '',
    initialWalk: null,
    error: false,
    isLoading: false,
});
const startTimeTime = ref<{
    hours: number;
    minutes: number;
    seconds: number;
}>(props.initialWalk ? {
        hours: dayjs(props.initialWalk.startTime).hour(),
        minutes: dayjs(props.initialWalk.startTime).minute(),
        seconds: 0,
    }
    : {
        hours: dayjs().hour(),
        minutes: dayjs().minute(),
        seconds: 0,
    });
const startTimeDate = ref(props.initialWalk ? dayjs(props.initialWalk.startTime).toDate() : dayjs().toDate());

const value = computed({
    get: () => props.modelValue,
    set: (val) => emit("update:modelValue", val),
});

const startTimeTimeAsDayJs = computed(() => {
    return dayjs().hour(startTimeTime.value.hours).minute(startTimeTime.value.minutes).second(startTimeTime.value.seconds);
});
onMounted(() => {
    startTimeTime.value = props.initialWalk ? {
        hours: dayjs(props.initialWalk.startTime).hour(),
        minutes: dayjs(props.initialWalk.startTime).minute(),
        seconds: 0,
    } : {
        hours: dayjs().hour(),
        minutes: dayjs().minute(),
        seconds: 0,
    }
    startTimeDate.value = props.initialWalk ? dayjs(props.initialWalk.startTime).toDate() : dayjs().toDate()
})
watch(() => props.modelValue, () => {
    if (startTimeTimeAsDayJs.value.hour() !== dayjs(props.modelValue).hour() && startTimeTimeAsDayJs.value.minute() !== dayjs(props.modelValue).minute()) {
        startTimeTime.value.hours = dayjs(props.modelValue).hour();
        startTimeTime.value.minutes = dayjs(props.modelValue).minute();
        startTimeTime.value.seconds = 0;
    }
    if (dayjs(startTimeDate.value) !== dayjs(props.modelValue)) {
        startTimeDate.value = dayjs(props.modelValue).toDate();
    }
})
watch(() => startTimeTime.value, () => {
    let startTimeDate = dayjs(props.modelValue);
    startTimeDate = startTimeDate.hour(startTimeTimeAsDayJs.value.hour());
    startTimeDate = startTimeDate.minute(startTimeTimeAsDayJs.value.minute());
    startTimeDate = startTimeDate.startOf('minute');
    value.value = startTimeDate.format();
});
watch(() => startTimeDate.value, () => {
    const startTimeDateValue = dayjs(startTimeDate.value);
    let startTime = dayjs(props.modelValue);
    startTime = startTime.year(startTimeDateValue.year());
    startTime = startTime.month(startTimeDateValue.month());
    startTime = startTime.date(startTimeDateValue.date());
    startTime = startTime.startOf('minute');
    value.value = startTime.format();
});

const errorMessages = computed(() => {
    if (!props.error) {
        return ''
    }
    return getViolationsFeedback(['startTime', 'startTimeBeforeEndTime', 'startTimeBeforeAllWayPoints'], props.error);
})

</script>

<template>
    <div>
        Rundenstartzeit<br>
        <div class="d-flex">
            <time-picker
                v-model="startTimeTime"
                :is-loading="isLoading"
                data-test="startTimeTime"
                placeholder="Rundenstartzeit"
                :clearable="false"
            />
            <date-picker
                v-model="startTimeDate"
                :is-loading="isLoading"
                data-test="startTimeDate"
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
