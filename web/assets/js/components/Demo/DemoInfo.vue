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
            <ul>
                <li>
                    <b>adelheid.administrator</b>
                    <ul>
                        <li>
                            <mdicon
                                class="mr-1"
                                name="AccountCircleOutline"
                                size="22"
                                title="Benutzername oder E-Mail"
                            />adelheid.administrator
                            <br>
                            <mdicon
                                class="mr-1"
                                name="LockOutline"
                                size="22"
                                title="Passwort"
                            />adelheid.administrator
                        </li>
                        <li>adelheid.administrator ist <b>Administratorin</b> und kann Teams definieren sowie die Altersgruppen definieren, die für Wegpunkte einer Runde zu erfassen sind.</li>
                        <li>Zugleich ist adelheid.administrator auch Mitglied des Teams "Team Nord". Sie kann damit auch eigene Runden starten.</li>
                    </ul>
                </li>
                <li>
                    <b>benno.benutzer</b>
                    <ul>
                        <li>
                            <mdicon
                                class="mr-1"
                                name="AccountCircleOutline"
                                size="22"
                                title="Benutzername oder E-Mail"
                            />benno.benutzer
                            <br>
                            <mdicon
                                class="mr-1"
                                name="LockOutline"
                                size="22"
                                title="Passwort"
                            />benno.benutzer
                        </li>
                        <li>benno.benutzer ist ein normaler Nutzender. Er ist dem Team "Team Altstadt" zugeordnet und kann eigene Runden starten.</li>
                    </ul>
                </li>
                <li>
                    <b>tessa.administrator</b>
                    <ul>
                        <li>
                            <mdicon
                                class="mr-1"
                                name="AccountCircleOutline"
                                size="22"
                                title="Benutzername oder E-Mail"
                            />tessa.administrator
                            <br>
                            <mdicon
                                class="mr-1"
                                name="LockOutline"
                                size="22"
                                title="Passwort"
                            />tessa.administrator
                        </li>
                        <li>tessa.administrator ist <b>Administratorin</b> und kann Teams definieren sowie die Altersgruppen definieren, die für Wegpunkte einer Runde zu erfassen sind.</li>
                        <li>tessa.administrator ist jedoch kein Mitglied eines Teams. Sie kann also keine eigene Runden starten.</li>
                    </ul>
                </li>
            </ul>
        </b-alert>
        <b-alert
            variant="warning"
            show
            class="mb-0"
        >
            <p class="d-flex align-items-center">
                <mdicon
                    name="AlertDecagramOutline"
                    class="mr-1"
                />
                <b>BITTE NUR MIT UNSENSIBLEN DATEN TESTEN!</b>
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
