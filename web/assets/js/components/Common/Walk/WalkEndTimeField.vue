<script setup lang="ts">
import {computed, ref, watch} from "vue";
import {getViolationsFeedback} from "../../../utils";
import {Walk} from "../../../model";
import DatePicker from "vue2-datepicker";
import 'vue2-datepicker/index.css';
import 'vue2-datepicker/locale/de';
import dayjs from "dayjs";

// const props = defineProps(['modelValue', 'value']); // vue3
// const emit = defineEmits(['update:modelValue']); // vue3
const emit = defineEmits(['input']);

export interface Props {
    value: string,
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

const endTimeTime = ref(props.value ? dayjs(props.value).toDate() : dayjs().toDate());
const endTimeDate = ref(props.value ? dayjs(props.value).toDate() : dayjs().toDate());
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
    if (dayjs(endTimeTime.value) !== dayjs(props.value)) {
        endTimeTime.value = dayjs(props.value).toDate();
    }
    if (dayjs(endTimeDate.value) !== dayjs(props.value)) {
        endTimeDate.value = dayjs(props.value).toDate();
    }
})
watch(() => endTimeTime.value, () => {
    let endTimeDate = dayjs(props.value);
    let endTime = dayjs(endTimeTime.value);
    endTimeDate = endTimeDate.hour(endTime.hour());
    endTimeDate = endTimeDate.minute(endTime.minute());
    endTimeDate = endTimeDate.endOf('minute');
    value.value = endTimeDate.format();
});
watch(() => endTimeDate.value, () => {
    const endTimeDateValue = dayjs(endTimeDate.value);
    let endTime = dayjs(props.value);
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
        <date-picker
            v-model="endTimeDate"
            label="Rundenendzeit"
            :disabled="isLoading"
            format="DD.MM.YYYY"
            title-format="DD.MM.YYYY"
            show-week-number
            data-test="endTimeDate"
            :lang="datePickerLang"
            :clearable="false"
        />
        <date-picker
            v-model="endTimeTime"
            type="time"
            :disabled="isLoading"
            data-test="endTimeTime"
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
