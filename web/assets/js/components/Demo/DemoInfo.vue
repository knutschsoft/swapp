<template>
    <div
        v-if="isOnDemoPage"
        class="text-muted"
    >
        <hr class="mt-3 mb-4" />
        <b-alert
            variant="info"
            show
        >
            <p>
                <mdicon name="EmoticonHappyOutline" />
                <b>Willkommen auf der Demo-Seite von Swapp</b>
            </p>
            <p>
                Folgende Benutzer können sich anmelden:
            </p>
            <ul>
                <li>
                    <b>waldi_beta</b>
                    <ul>
                        <li>
                            Benutzer: waldi_beta
                            <br>
                            Passwort: waldi_beta
                        </li>
                        <li>waldi_beta ist der <b>Teamleiter</b> und kann Teams definieren sowie die Altersgruppen definieren, die für Wegpunkte einer Runde zu erfassen sind.</li>
                        <li>Zugleich ist waldi_beta auch Mitglied des Teams "Team Nord". Er kann also auch eigene Runden starten.</li>
                    </ul>
                </li>
                <li>
                    <b>jens_beta</b>
                    <ul>
                        <li>
                            Benutzer: jens_beta
                            <br>
                            Passwort: jens_beta
                        </li>
                        <li>jens_beta ist ein normaler Nutzer. Er ist dem Team "Team Altstadt" zugeordnet und kann eigene Runden starten.</li>
                    </ul>
                </li>
                <li>
                    <b>patrick_beta</b>
                    <ul>
                        <li>
                            Benutzer: patrick_beta
                            <br>
                            Passwort: patrick_beta
                        </li>
                        <li>patrick_beta ist der <b>Teamleiter</b> und kann Teams definieren sowie die Altersgruppen definieren, die für Wegpunkte einer Runde zu erfassen sind.</li>
                        <li>patrick_beta ist jedoch kein Mitglied eines Teams. Er kann also keine eigene Runden starten.</li>
                    </ul>
                </li>
            </ul>
        </b-alert>
        <b-alert
            variant="warning"
            show
            class="mb-0"
        >
            <p>
                <mdicon
                    name="AlertCircle"
                    class="mr-2"
                /><b>BITTE NUR MIT UNSENSIBLEN DATEN TESTEN!</b>
            </p>
            <p>
                Auch andere Interessenten an Swapp bekommen diesen Demo-Zugang und sehen die gleichen Inhalte.
            </p>
        </b-alert>
    </div>
</template>

<script>
"use strict";

export default {
    name: "DemoInfo",
    data: () => ({
    }),
    computed: {
        isOnDemoPage() {
            return window.location.host.includes('swapp.demo') || this.$route.query.demo;
        },
    },
    created() {
    },
    methods: {
    },
}
</script>

<style scoped>

</style>
