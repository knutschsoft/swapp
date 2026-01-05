<script setup lang="ts">
import {computed} from 'vue';
import {nl2br} from "@/js/utils";

const props = withDefaults(defineProps<{
    text: string;
    nl2br?: boolean;
}>(), {
    nl2br: false
});

const displayText = computed(() => String(props.text).trim() || '-');
const nl2brText = computed(() => nl2br(props.text));
const isSingleLine = computed(() => displayText.value.split('\n').length === 1);
</script>

<template>
    <v-tooltip
        v-if="displayText"
        :open-on-click="true"
        location="bottom"
        max-width="600"
    >
        <template #activator="{ props: activatorProps }">
            <div
                :class="{
                  'text-clamp-4': !isSingleLine,
                  'text-truncate': isSingleLine,
                  'mw-25': true
                }"
                v-bind="activatorProps"
                v-html="nl2br ? nl2brText : displayText"
            />
        </template>
        <div class="" v-html="nl2br ? nl2brText : displayText" />
    </v-tooltip>
</template>

<style>
.nl2br {
    white-space: pre;
}
</style>
