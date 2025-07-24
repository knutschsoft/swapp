<script>
import useRegisterSW from './../mixins/useRegisterSW'

export default {
    name: 'ReloadPrompt',
    mixins: [useRegisterSW]
}
</script>

<template>
    <div>
        <v-alert
            v-if="needRefresh"
            type="info"
            prominent
            class="mb-0 rounded-0"
        >
            <v-row no-gutters>
                <v-col align-self="center">
                    <div class="ml-2 mb-1 mb-sm-0">
                        <b>Es gibt eine neue Version von Swapp!</b>
                    </div>
                    <ul class="ml-0 mb-0 mr-4">
                        <li>Ggfs. funktioniert die aktuelle Version nicht mehr ordnungsgemäß.</li>
                        <li>Bitte sichere vorher deine ungespeicherten Eingaben.</li>
                    </ul>
                </v-col>
                <v-col align-self="center" cols="12">
                    <v-btn
                        density="compact"
                        color="secondary"
                        block
                        :disabled="isUpdateLoading"
                        :loading="isUpdateLoading"
                        prepend-icon="mdi-update"
                        @click="updateServiceWorker()"
                    >
                        Versionsupdate
                    </v-btn>
                </v-col>
            </v-row>
        </v-alert>
        <v-snackbar v-model="showErrorSnackbar" timeout="6000" color="error" top>
            Fehler beim Aktualisieren des Service Workers. Bitte versuche es später erneut.
        </v-snackbar>
    </div>
</template>

<style>

</style>
