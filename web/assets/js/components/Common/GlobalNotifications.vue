<script setup>
import { computed } from 'vue';
import { useAlertStore } from '@/js/stores';

const alertStore = useAlertStore();
const snackbars = computed(() => alertStore.alerts);
</script>

<template>
    <v-container>
        <v-snackbar
            v-for="(alert, index) in snackbars"
            :key="index"
            v-model="alert.show"
            multi-line
            timeout="6000"
            location="top right"
            elevation="24"
            rounded
            :color="alert.type ?? undefined"
            @update:modelValue="alertStore.remove(index)"
        >
            <div v-if="alert.title" class="pb-2 text-overline">{{ alert.title }}</div>
            {{ alert.message }}
            <template v-slot:actions>
                <v-btn variant="text" @click="alertStore.remove(index)">
                    <v-icon icon="mdi-close"></v-icon>
                </v-btn>
            </template>
        </v-snackbar>
    </v-container>
</template>
