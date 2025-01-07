<script setup lang="ts">
import {computed, ref, watch} from "vue";
import {getViolationsFeedback} from "../../../utils";
import {Team, Walk} from "../../../model";
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

const startTimeTime = ref(props.initialWalk ? dayjs(props.initialWalk.startTime).toDate() : dayjs().toDate());
const startTimeDate = ref(props.initialWalk ? dayjs(props.initialWalk.startTime).toDate() : dayjs().toDate());
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


watch(() => startTimeTime.value, () => {
    let startTimeDate = dayjs(props.value);
    let startTime = dayjs(startTimeTime.value);
    startTimeDate = startTimeDate.hour(startTime.hour());
    startTimeDate = startTimeDate.minute(startTime.minute());
    startTimeDate = startTimeDate.startOf('minute');
    value.value = startTimeDate.format();
});
watch(() => startTimeDate.value, () => {
    const startTimeDateValue = dayjs(startTimeDate.value);
    let startTime = dayjs(props.value);
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
        <date-picker
            v-model="startTimeDate"
            label="Rundenstartzeit"
            :disabled="isLoading"
            format="DD.MM.YYYY"
            title-format="DD.MM.YYYY"
            show-week-number
            data-test="startTimeDate"
            :lang="datePickerLang"
            :clearable="false"
        />
        <date-picker
            v-model="startTimeTime"
            type="time"
            :disabled="isLoading"
            data-test="startTimeTime"
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
