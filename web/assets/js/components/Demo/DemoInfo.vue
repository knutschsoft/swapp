<template>
    <div
        v-if="isOnDemoPage"
        class="text-muted"
    >
        <hr class="mt-3 mb-4" />
        <b-alert
            ref="wurst"
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
                    <ul>
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
                    <ul>
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
                    <ul>
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
        isCopiedAdelheid: false,
        isCopiedBenno: false,
        isCopiedTessa: false,
    }),
    computed: {
        isOnDemoPage() {
            return window.location.host.includes('swapp.demo') || this.$route.query.demo;
        },
    },
    created() {
    },
    methods: {
        doCopyAdelheid() {
            this.isCopiedAdelheid = true;
            this.isCopiedBenno = false;
            this.isCopiedTessa = false;
            this.doCopy('adelheid.administrator');
            window.setTimeout(() => this.isCopiedAdelheid = false, 3000);
        },
        doCopyBenno() {
            this.isCopiedBenno = true;
            this.isCopiedAdelheid = false;
            this.isCopiedTessa = false;
            this.doCopy('benno.benutzer');
            window.setTimeout(() => this.isCopiedBenno = false, 3000);
        },
        doCopyTessa() {
            this.isCopiedTessa = true;
            this.isCopiedAdelheid = false;
            this.isCopiedBenno = false;
            this.doCopy('tessa.administrator');
            window.setTimeout(() => this.isCopiedTessa = false, 3000);
        },
        doCopy(copyText) {
            this.$copyText(copyText).then(() => {
                this.$bvToast.toast(`"${copyText}" ist nun in deiner Zwischenablage.`, {
                    title: 'Zugangsdaten in die Zwischenablage kopiert',
                    toaster: 'b-toaster-top-right',
                    autoHideDelay: 10000,
                    appendToast: true,
                    variant: 'info',
                    solid: true,
                });
            }, function (e) {
                console.log(e);
            });
        },
    },
}
</script>

<style scoped>

</style>
