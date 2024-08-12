<script>
import useRegisterSW from './../mixins/useRegisterSW'
import { pwaInfo } from 'virtual:pwa-info'

console.log(pwaInfo)


export default {
    name: 'ReloadPrompt',
    mixins: [useRegisterSW]
}
</script>

<template>
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
                    <li>Ggfs. funktioniert die aktuelle Version momentan nicht mehr ordnungsgemäß.</li>
                    <li>Bitte sichere vorher deine ungespeicherten Eingaben.</li>
                </ul>
            </v-col>
            <v-col align-self="center" cols="12">
                <v-btn
                    small
                    color="secondary"
                    block
                    :disabled="isUpdateLoading"
                    @click="updateServiceWorker()"
                >
                    <v-progress-circular
                        v-if="isUpdateLoading"
                        :width="2"
                        :size="20"
                        indeterminate
                        class="mr-2"
                    />
                    <v-icon v-if="!isUpdateLoading" class="mr-2">mdi-update</v-icon>
                    Versionsupdate
                </v-btn>
            </v-col>
        </v-row>
    </v-alert>
</template>

<style>

</style>
