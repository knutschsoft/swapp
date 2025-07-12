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
            timeout="7000"
            location="top right"
            elevation="24"
            rounded
            :color="alert.type ?? undefined"
            @update:modelValue="alertStore.remove(index)"
        >
            <div v-if="alert.title" class="pb-2 text-overline">{{ alert.title }}</div>
            <template v-if="alert.message === 'globalError'">
                Das hätte nicht passieren dürfen. Wende dich bitte mit einer Beschreibung zur Reproduktion des Fehlers an <a href="mailto:info@streetworkapp.de?subject=Supportanfrage Swapp // <Problembeschreibung hier eintragen>&body=    %0D%0A
    %0D%0A
    Mein Benutzername: %0D%0A
    Nennung des Bereichs, um den es geht:%0D%0A
    Was wurde gemacht:%0D%0A
    Was wurde erwartet:%0D%0A
    Was ist stattdessen passiert:%0D%0A
    %0D%0A
    Relevante Dateien bzw. Screenshots habe ich beigefügt: ja/nicht relevant">info@streetworkapp.de</a>
            </template>
            <template v-else>
                {{ alert.message }}
            </template>
            <template v-slot:actions>
                <v-btn variant="text" @click="alertStore.remove(index)">
                    <v-icon icon="mdi-close"></v-icon>
                </v-btn>
            </template>
        </v-snackbar>
    </v-container>
</template>
