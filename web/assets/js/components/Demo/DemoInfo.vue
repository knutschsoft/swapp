<template>
    <div
        v-if="isOnDemoPage"
        class="text-muted mt-5 mb-3"
    >
        <v-alert
            ref="wurst"
            color="info"
        >
            <p class="d-flex align-items-center">
                <mdicon
                    class="mr-1"
                    name="EmoticonHappyOutline"
                />
                <b>Willkommen auf der Demo-Seite von Swapp</b>
            </p>
            <p>
                Folgende Benutzende können sich anmelden:
            </p>
            <ul class="pl-5">
                <li>
                    <span
                        class="cursor-pointer"
                        @click="doCopyAdelheid()"
                    >
                        <b>adelheid.administrator</b>
                        <mdicon
                            v-if="!isCopiedAdelheid"
                            class="mr-1"
                            name="ContentCopy"

                        />
                        <mdicon
                            v-else
                            class="mr-1"
                            name="CheckCircleOutline"
                        />
                    </span>
                    <ul class="pl-5">
                        <li>
                            <mdicon
                                class="mr-1"
                                name="AccountCircleOutline"
                                size="22"
                                title="Benutzername oder E-Mail"
                            /><span :class="{'bg-info text-white': isCopiedAdelheid}">adelheid.administrator</span>
                            <br>
                            <mdicon
                                class="mr-1"
                                name="LockOutline"
                                size="22"
                                title="Passwort"
                            /><span :class="{'bg-info text-white': isCopiedAdelheid}">adelheid.administrator</span>
                        </li>
                        <li>adelheid.administrator ist <b>Administratorin</b> und kann Teams definieren sowie die Altersgruppen definieren, die für Wegpunkte einer Runde zu erfassen sind.</li>
                        <li>Zugleich ist adelheid.administrator auch Mitglied des Teams "Team Nord". Sie kann damit auch eigene Runden starten.</li>
                    </ul>
                </li>
                <li>
                    <span
                        class="cursor-pointer"
                        @click="doCopyBenno()"
                    >
                        <b>benno.benutzer</b>
                        <mdicon
                            v-if="!isCopiedBenno"
                            class="mr-1"
                            name="ContentCopy"

                        />
                        <mdicon
                            v-else
                            class="mr-1"
                            name="CheckCircleOutline"
                        />
                    </span>
                    <ul class="pl-5">
                        <li>
                            <mdicon
                                class="mr-1"
                                name="AccountCircleOutline"
                                size="22"
                                title="Benutzername oder E-Mail"
                            /><span :class="{'bg-info text-white': isCopiedBenno}">benno.benutzer</span>
                            <br>
                            <mdicon
                                class="mr-1"
                                name="LockOutline"
                                size="22"
                                title="Passwort"
                            /><span :class="{'bg-info text-white': isCopiedBenno}">benno.benutzer</span>
                        </li>
                        <li>benno.benutzer ist ein normaler Nutzender. Er ist dem Team "Team Altstadt" zugeordnet und kann eigene Runden starten.</li>
                    </ul>
                </li>
                <li>
                    <span
                        class="cursor-pointer"
                        @click="doCopyTessa()"
                    >
                        <b>tessa.administrator</b>
                        <mdicon
                            v-if="!isCopiedTessa"
                            class="mr-1"
                            name="ContentCopy"

                        />
                        <mdicon
                            v-else
                            class="mr-1"
                            name="CheckCircleOutline"
                        />
                    </span>
                    <ul class="pl-5">
                        <li>
                            <mdicon
                                class="mr-1"
                                name="AccountCircleOutline"
                                size="22"
                                title="Benutzername oder E-Mail"
                            /><span :class="{'bg-info text-white': isCopiedTessa}">tessa.administrator</span>
                            <br>
                            <mdicon
                                class="mr-1"
                                name="LockOutline"
                                size="22"
                                title="Passwort"
                            /><span :class="{'bg-info text-white': isCopiedTessa}">tessa.administrator</span>
                        </li>
                        <li>tessa.administrator ist <b>Administratorin</b> und kann Teams definieren sowie die Altersgruppen definieren, die für Wegpunkte einer Runde zu erfassen sind.</li>
                        <li>tessa.administrator ist jedoch kein Mitglied eines Teams. Sie kann also keine eigene Runden starten.</li>
                    </ul>
                </li>
            </ul>
        </v-alert>
        <v-alert
            type="warning"
            class="mb-0 mt-5"
            prominent
            icon="mdi-alert-decagram-outline"
        >
            <p class="d-flex align-items-center">
                <b>BITTE NUR MIT UNSENSIBLEN DATEN TESTEN!</b>
            </p>
            <p>
                Auch andere Interessenten an Swapp bekommen diesen Demo-Zugang und sehen die gleichen Inhalte.
            </p>
        </v-alert>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, getCurrentInstance } from 'vue';
import { useAlertStore } from '@/js/stores';
import { useRoute } from 'vue-router';

interface CredentialsSelectEvent {
    username: string;
    password: string;
}

const emit = defineEmits<{
    'credentials-select': [payload: CredentialsSelectEvent];
}>();

const route = useRoute();
const alertStore = useAlertStore();
const instance = getCurrentInstance();

const isCopiedAdelheid = ref(false);
const isCopiedBenno = ref(false);
const isCopiedTessa = ref(false);

const isOnDemoPage = computed(() => {
    return window.location.host.includes('swapp.demo') || route.query.demo;
});

const doCopyAdelheid = () => {
    isCopiedAdelheid.value = true;
    isCopiedBenno.value = false;
    isCopiedTessa.value = false;
    doCopy('adelheid.administrator');
    window.setTimeout(() => isCopiedAdelheid.value = false, 3000);
};

const doCopyBenno = () => {
    isCopiedBenno.value = true;
    isCopiedAdelheid.value = false;
    isCopiedTessa.value = false;
    doCopy('benno.benutzer');
    window.setTimeout(() => isCopiedBenno.value = false, 3000);
};

const doCopyTessa = () => {
    isCopiedTessa.value = true;
    isCopiedAdelheid.value = false;
    isCopiedBenno.value = false;
    doCopy('tessa.administrator');
    window.setTimeout(() => isCopiedTessa.value = false, 3000);
};

const doCopy = (copyText: string) => {
    emit('credentials-select', { username: copyText, password: copyText });

    const copyTextPlugin = instance?.appContext.config.globalProperties.$copyText;
    if (copyTextPlugin) {
        copyTextPlugin(copyText).then(() => {
            alertStore.info(
                `"${copyText}" ist nun in deiner Zwischenablage sowie in den Anmeldefeldern.`,
                'Zugangsdaten in die Zwischenablage kopiert'
            );
        }, (e: Error) => {
            console.log(e);
        });
    }
};
</script>

<style scoped>

</style>
