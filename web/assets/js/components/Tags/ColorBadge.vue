<script setup lang="ts">
import { defineProps } from "vue";

defineProps<{
    color: string;
}>();
</script>

<template>
    <v-chip
        class="font-weight-bold d-flex align-items-center"
        :style="`background-color: ${color}`"
        small
        density="compact"
        pill
    >
    <span :style="`background-color: ${color}`">
      <span
          :data-title="color"
          style="
          height: inherit;
          background: inherit;
          -webkit-background-clip: text;
          background-clip: text;
          color: transparent;
          text-align: center;
          filter: invert(1) grayscale(1) contrast(999);
        "
      >
        {{ color }}
      </span>
    </span>
    </v-chip>
</template>
