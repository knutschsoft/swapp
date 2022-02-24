'use strict';

import dayjs from 'dayjs';
const swappStoreChangelogLastVisitedAt = 'swapp-store-changelog-last-visited-at';

const
    UPDATE_LAST_VISITED_AT = 'FETCHING_CLIENTS'
;

const state = {
    changelogs: [
        {
            header: '24.02.2022',
            avatarText: '🪛',
            entries: [
                { text: 'Fix: Beim Erstellen eines Wegpunktes wurden die Tags nicht mit gespeichert. Lediglich beim Ändern eines Wegpunktes funktionierte das Ändern der Tags.' },
            ],
        },
        {
            header: '22.02.2022',
            avatarText: '🛠️',
            entries: [
                { text: 'Feature: Ein Admin kann die Benutzerliste nach aktiven und inaktiven Benutzern filtern.' },
                { text: 'Feature: Die Benutzer in der Benutzerliste sind nun nach aktiv/inaktiv und Benutzername sortiert.' },
                {
                    text: 'Feature: Ein Benutzer wird nun mit einem Hinweis auf neue Änderungen in Swapp hingewiesen.',
                    gallery: [
                        {
                            src: '../changelog/20220222_changelog_aktiviert_icon.png',
                            thumbnailHeight: '100px',
                            description: 'dezenter Hinweis mit blauer Alarmglocke',
                        },
                        {
                            src: '../changelog/20220222_changelog_aktiviert_menu.png',
                            thumbnailHeight: '100px',
                            description: 'ein weiterer Hinweis findet sich auch im Menü wieder',
                        },
                        {
                            src: '../changelog/20220222_changelog_deaktiviert_icon.png',
                            thumbnailHeight: '100px',
                            description: 'Noch nicht gesehene Änderungen an Swapp sind mit "Neu" markiert.',
                        },
                        {
                            src: '../changelog/20220222_changelog_alt.png',
                            thumbnailHeight: '100px',
                            description: 'Alt: So sah es vorher aus.',
                        },
                    ],
                },
                { text: 'UX-Feature: Die Eingabefelder für die Altersgruppen beim Erstellen und Verändern eines Wegpunktes enthalten 3 Eingabefelder je Zeile anstatt 1 Eingabefeld je Zeile. Dies führt zu einer kompakteren und übersichtlicheren Darstellung.' },
                { text: 'Fix: Wegpunkte konnten auf Safari- und iOS-Safari-Browser <=15.3 nicht verändert werden.' },
            ],
        },
        {
            header: '14.02.2022 - Valentinstags-Update',
            avatarText: '💘🎁',
            entries: [
                {
                    text: 'Fix: Bei der Auswahl der systemischen Frage für eine Runde wurde der Klient nicht beachtet. Dies ist behoben worden, so dass beim Starten einer Runde eine zufällige Systemische Frage vom eigenen Klienten verwendet wird.',
                },
                { text: 'Feature: Ein Admin kann ein Team erstellen.' },
                { text: 'Misc: Softwarebibliotheken geupdated. Symfony-Update von 5.3 auf 5.4. PHP-Upgrade von 8.0 auf 8.1.' },
            ],
        },
        {
            header: '30.10.2021',
            avatarText: '🛠️',
            entries: [
                {
                    text: 'UX-Feature: Die Autocomplete-Vorschläge für den Ort eines Wegpunktes sind nun aufsteigend alphabetisch sortiert.',
                },
                {
                    text: 'UX-Feature: Bei Zeiteingaben sind die Schritte bei Auswahl einer Minute von 1 auf 5 erhöht, damit der Nutzer weniger klicken muss.',
                },
                {
                    text: 'UX-Feature [iOS]: Das automatische Zoomen bei Klick auf ein Eingabefeld wird verhindert.',
                },
                {
                    text: 'Fix [iOS]: Das Wetter einer Runde hat sich nicht mit einem Klick auf den Wert "Sonne" setzen lassen. Es musste vorher ein anderer Wert ausgewählt werden.',
                },
                {
                    text: 'Fix: Beim automatischen Logout nach abgelaufener Anmeldesession erscheint nun eine Benachrichtigung anstatt einer Fehlermeldung und der Nutzer wird automatisch abgemeldet.',
                    gallery: [
                        {
                            src: '../changelog/20211030_abmeldung.png',
                            thumbnailHeight: '100px',
                            description: 'Benachrichtigung bei automatischer Abmeldung',
                            alt: 'Benachrichtigung bei automatischer Abmeldung auf Swapp der Streetworkapp.',
                        },
                    ],
                },
            ],
        },
        {
            header: '18.10.2021',
            avatarText: '🆕️🛠️',
            entries: [
                {
                    text: 'Feature: Zu einem Wegpunkt kann nun zusätzlich das Feld "Einzelgespräch" ausgefüllt werden. Position ist unterhalb des Feldes "Beobachtung".',
                },
                {
                    text: 'Feature: Wegpunkte können nach dem Feld "Einzelgespräch" gefiltert werden.',
                },
                {
                    text: 'Feature: Ein Nutzer kann nun den Zurücksetzen-Button für die Filterung "Wiedervorlage Dienstberatung?" nutzen um den Wert auf "egal" zurückzusetzen.',
                },
                {
                    text: 'Feature: Ein Admin sieht den Erstellungszeitpunkt eines Nutzers. Für bereits bestehende Nutzer ist dies der Zeitpunkt des Updates.',
                },
                {
                    text: 'UX-Feature: Die Autocomplete-Vorschläge für den Ort eines Wegpunktes kann von einem Admin gruppenspezifisch definiert werden. Die vorherige Lösung mit allen Orten des Teams hatte zu viele Einträge und war zu unpraktikabel.',
                },
                {
                    text: 'UX-Feature: Das Erkennen von gesetzten Filtern ist erleichtert. Buttons zum Zurücksetzen des Filters für die Runden- und Wegpunkttabelle sind standardmäßig deaktiviert und werden erst aktiv, wenn die Filterung einen Wert enthält. Der Feldname ist zusätzlich fett hinterlegt, wenn die Filterung aktiv ist.',
                },
                {
                    text: 'Fix: Datumsangaben sind nun alle im deutschen Format. Dies war u.a. beim Wochentag in der Wegpunktetabelle auf dem Dashboard nicht der Fall.',
                },
                {
                    text: 'Misc: Softwarebibliotheken geupdated.',
                },
            ],
        },
        {
            header: '15.10.2021',
            avatarText: '🆕️🛠️',
            entries: [
                {
                    text: 'Fix: Die Paginierung der Wegpunkttabelle auf dem Dashboard funktionierte nicht.',
                },
                {
                    text: 'Fix: In der Spalte "Ende" in der Wegpunkttabelle soll nur die Uhrzeit stehen, wenn der Tag gleich dem Tag von "Beginn" ist. Dies war jedoch auch bei gleichem Wochentag der Fall.',
                },
                {
                    text: 'UX-Feature: Die Gesamtanzahl der Wegpunkte auf dem Dashboard wird im Header angezeigt.',
                },
                {
                    text: 'Misc: Softwarebibliotheken geupdated.',
                },
            ],
        },
        {
            header: '2017 bis 2020',
            avatarText: '🤓',
            entries: [
                {
                    text: 'Misc: Es gab jede Menge Updates über die wir hier vielleicht noch berichten werden.',
                },
            ],
        },
        {
            header: '21.06.2016',
            avatarText: '🥳',
            entries: [
                {
                    text: 'Misc: Beginn der produktiven Nutzung von Swapp.',
                },
            ],
        },
        {
            header: '01.04.2016',
            avatarText: '🧪',
            entries: [
                {
                    text: 'Misc: Erster Beta-Test zusammen mit der <a target="_blank" href="https://www.treberhilfe-dresden.de">treberhilfe-dresden.de</a>.',
                },
            ],
        },
        {
            header: '01.05.2015',
            avatarText: '👷',
            entries: [
                {
                    text: 'Misc: Start der Entwicklung von Swapp',
                },
            ],
        },
    ],
    lastVisitedAt: false,
};

const getters = {
    changelogs(state) {
        return state.changelogs;
    },
    latestCreatedAt(state) {
        if (!state.changelogs.length) {
            return dayjs();
        }
        const header = state.changelogs[0].header;

        return dayjs(header.split(' ')[0], ["DD.MM.YYYY", "YYYY"]);
    },
    lastVisitedAt(state) {
        return state.lastVisitedAt;
    },
    hasNewChangelogItems(state, getters) {
        return getters.latestCreatedAt.isAfter(getters.lastVisitedAt)
    },
};

const mutations = {
    [UPDATE_LAST_VISITED_AT](state, payload) {
        state.lastVisitedAt = payload;
        localStorage.setItem(swappStoreChangelogLastVisitedAt, JSON.stringify(payload));
    },
};

const actions = {
    updateLastVisitedAt({ commit }, payload) {
        commit(UPDATE_LAST_VISITED_AT, payload);
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};
