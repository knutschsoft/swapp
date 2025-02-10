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

const endTimeTime = ref<{
    hours: number;
    minutes: number;
    seconds: number;
}>(props.modelValue ? {
        hours: dayjs(props.modelValue).hour(),
        minutes: dayjs(props.modelValue).minute(),
        seconds: 0,
    }
    : {
        hours: dayjs().hour(),
        minutes: dayjs().minute(),
        seconds: 0,
    });
const endTimeDate = ref(props.modelValue ? dayjs(props.modelValue).toDate() : dayjs().toDate());


const value = computed({
  get: () => props.modelValue,
  set: (val) => emit("update:modelValue", val),
});

const endTimeTimeAsDayJs = computed(() => {
    return dayjs().hour(endTimeTime.value.hours).minute(endTimeTime.value.minutes).second(endTimeTime.value.seconds);
});

onMounted(() => {
    endTimeTime.value = props.modelValue ? {
            hours: dayjs(props.modelValue).hour(),
            minutes: dayjs(props.modelValue).minute(),
            seconds: 0,
        }
        : {
            hours: dayjs().hour(),
            minutes: dayjs().minute(),
            seconds: 0,
        }
    endTimeDate.value = props.modelValue ? dayjs(props.modelValue).toDate() : dayjs().toDate()
})

watch(() => props.modelValue, () => {
    if (endTimeTimeAsDayJs.value.hour() !== dayjs(props.modelValue).hour() && endTimeTimeAsDayJs.value.minute() !== dayjs(props.modelValue).minute()) {
        endTimeTime.value.hours = dayjs(props.modelValue).hour();
        endTimeTime.value.minutes = dayjs(props.modelValue).minute();
        endTimeTime.value.seconds = 0;
    }
    if (dayjs(endTimeDate.value) !== dayjs(props.modelValue)) {
        endTimeDate.value = dayjs(props.modelValue).toDate();
    }
})
watch(() => endTimeTime.value, () => {
    let endTimeDate = dayjs(props.modelValue);
    endTimeDate = endTimeDate.hour(endTimeTimeAsDayJs.value.hour());
    endTimeDate = endTimeDate.minute(endTimeTimeAsDayJs.value.minute());
    endTimeDate = endTimeDate.startOf('minute');
    value.value = endTimeDate.format();
});
watch(() => endTimeDate.value, () => {
    const endTimeDateValue = dayjs(endTimeDate.value);
    let endTime = dayjs(props.modelValue);
    endTime = endTime.year(endTimeDateValue.year());
    endTime = endTime.month(endTimeDateValue.month());
    endTime = endTime.date(endTimeDateValue.date());
    endTime = endTime.endOf('minute');
    value.value = endTime.format();
});

const errorMessages = computed(() => {
    if (!props.error) {
        return ''
    }
    return getViolationsFeedback(['endTime', 'endTimeBeforeEndTime', 'endTimeBeforeAllWayPoints'], props.error);
})

</script>

<template>
    <div>
        Rundenendzeit<br>
        <div class="">
            <time-picker
                v-model="endTimeTime"
                :is-loading="isLoading"
                data-test="endTimeTime"
                placeholder="Rundenendzeit"
                :clearable="false"
                class="mb-2"
            />
            <date-picker
                v-model="endTimeDate"
                :is-loading="isLoading"
                data-test="endTimeDate"
                placeholder="Rundenendzeit"
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
