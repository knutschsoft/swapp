import {acceptHMRUpdate, defineStore} from 'pinia';
import dayjs, {Dayjs} from 'dayjs';
import {type RemovableRef, useLocalStorage } from "@vueuse/core";

type State = {
    changelogs: Array<any>,
    lastVisitedAt: RemovableRef<Dayjs>;
}

export const useChangelogStore = defineStore("changelog", {
    state: (): State => ({
        changelogs: [
            {
                header: '20.09.2023',
                avatarText: '&#x1F6B6;',
                avatarTitle: 'Rundenersteller',
                entries: [
                    {
                        text: [
                            'Feature: Der Rundenersteller einer Runde muss explizit festgehalten werden.',
                            'Für bereits existierenden Runden ist ein leerer Wert gesetzt.',
                            'Bei bereits existierenden Teams sind die systemischen Fragen aktiviert.',
                            'Als voreingestellter Wert ist immer der aktuell eingeloggte Nutzende gesetzt, so dass kein weiterer Klick notwendig ist.',
                            'Der voreingestellte Wert kann auch geändert werden, da zum Beispiel während einer Runde das Handy einem anderen Teilnehmenden gegeben wird, damit dieser dokumentiert.',
                            'Use Case: Die Teamleitung hat einige Tage nach Abschluss einer Runde Rückfragen und wendet sich an den Rundenersteller als Hauptansprechpartner für eine Runde.',
                        ],
                        gallery: [
                            {
                                src: '../changelog/20230920_Rundenersteller_Runde_beginnen_Formular_vorher_nachher.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung des Rundenerstellers auf dem "Runde beginnen"-Formular (links: vorher - rechts: neu)',
                            },
                            {
                                src: '../changelog/20230920_Rundenersteller_Runde_bearbeiten_Formular.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung des Rundenerstellers auf dem "Runde bearbeiten"-Formular',
                            },
                            {
                                src: '../changelog/20230920_Rundenersteller_Rundendetailseite_vorher_nachher.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung des Rundenerstellers auf der Rundendetailseite (links: vorher - rechts: neu)',
                            },
                            {
                                src: '../changelog/20230920_Rundenersteller_Runde_bearbeiten-Formular.png',
                                thumbnailHeight: '80px',
                                description: 'Darstellung des Rundenerstellers auf der Rundendetailseite (links: vorher - rechts: neu)',
                            },
                        ],
                    },
                    {
                        text: [
                            'UX-Feature: Ein Nutzender sieht, ob sein Gerät gerade keine Internetverbindung hat.',
                        ],
                        gallery: [
                            {
                                src: '../changelog/20230920_Keine_Internetverbindung_vorher_nachher.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung von "Keine Internetverbindung" auf der Anmeldungs-Seite (links: vorher - rechts: neu)',
                            },
                        ],
                    },
                    {
                        text: [
                            'UX-Feature: Das Team-Editieren-Modal nimmt nun auf verschiedenen Bildschirmgrößen mehr vom verfügbaren Platz ein.',
                        ],
                    },
                    {
                        text: [
                            'Misc: Größere Softwarebibliothek-Änderung // Vuex-Store durch Pinia-Store ersetzt.',
                        ],
                    },
                ],
            },
            {
                header: '28.07.2023',
                avatarText: '&#x1F6E0;',
                avatarTitle: 'Bug-Fix',
                entries: [
                    {
                        text: [
                            'Fix: Das Setzen der Bewertung speicherte im "Runde beenden"-Formular immer den Wert "1".',
                            'Als Workaround musste man die Bewertung nochmal im "Runde ändern"-Formular setzen und speichern.',
                        ],
                    },
                ],
            },
            {
                header: '14.07.2023',
                avatarText: '&#129533;',
                avatarTitle: 'Robert "Spongebob" Schwammkopfs 37. Geburtstag',
                entries: [
                    {
                        text: [
                            'Feature: Die systemischen Fragen am Ende einer Runde können deaktiviert werden.',
                            'Dazu kann ein Administrator pro Team die systemischen Fragen und Antworten aktivieren bzw. deaktivieren.',
                            'Bei bereits existierenden Teams sind die systemischen Fragen aktiviert.',
                            'Use Case: Einige Teams benötigen die systemischen Fragen nicht. Dadurch werden beim Rundenabschluss weniger Klicks und Eingaben benötigt.',
                        ],
                        gallery: [
                            {
                                src: '../changelog/20230712_Systemische_Frage_Teamformular_deaktiviert.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung von deaktivierten Systemischen Fragen auf dem "Team"-Formular (links: vorher - rechts: neu)',
                            },
                            {
                                src: '../changelog/20230712_Systemische_Frage_Teamformular_aktiviert.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung von aktivierten Systemischen Fragen auf dem "Team"-Formular (links: vorher - rechts: neu)',
                            },
                            {
                                src: '../changelog/20230712_Systemische_Frage_Runde_beenden_Formular.png',
                                thumbnailHeight: '95px',
                                description: 'Darstellung im "Runde beenden"-Formular (links: vorher - rechts: neu mit deaktiverter Erfassung der systemischen Frage)',
                            },
                            {
                                src: '../changelog/20230712_Systemische_Frage_Systemische_Fragen_Formular.png',
                                thumbnailHeight: '100px',
                                description: 'Neuer Hinweistext im "Systemische Fragen"-Formular (links: vorher - rechts: neu)',
                            },
                        ],
                    },
                    {
                        text: [
                            'UX-Feature: Die Bewertungssterne können durch ein selbstgewähltes Bild ersetzt und somit (pro Träger) personalisiert werden.',
                            'Damit wird ein weiteres Stück Gamification ermöglicht.',
                            'Für App-Nutzer wird die Bewertung der Runden attraktiver, wenn sie das Bewertungsbild selbst auswählen dürfen.',
                            'Use Case: Der Träger identifiziert sich aufgrund seiner Zielgruppe/inhaltlichen Ausrichtung eher mit anderen Symbolen als mit Sternchen.',
                        ],
                        gallery: [
                            {
                                src: '../changelog/20230712_Dashboard_Rundenbewertung_mit_eigenem_Bild_nachher.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung des eigenen Bewertungsbildes auf dem Dashboard',
                            },
                            {
                                src: '../changelog/20230712_Dashboard_Rundenbewertung_mit_eigenem_Bild_vorher.png',
                                thumbnailHeight: '100px',
                                description: 'Bisherige Darstellung ohne eigenes Bewertungsbild auf dem Dashboard',
                            },
                        ],
                    },
                    {
                        text: [
                            'UX-Feature: Bei allen Datumsfiltern gibt es zusätzlich Schnellauswahlmöglichkeiten für Quartale und Halbjahre.',
                            'Damit können schneller die Zeitspannen für Quartals- und Halbjahresbericht erstellt werden.',
                        ],
                        gallery: [
                            {
                                src: '../changelog/20230712_Dashboard_Datumsauswahl_Beginn_mobil_vorher_nachher.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung des Datumsauswahl für den Beginn einer Runde mit neuen Schnellauswahlfeldern (links: vorher - rechts: neu)',
                            },
                            {
                                src: '../changelog/20230712_Dashboard_Datumsauswahl_Ankunft_vorher_nachher.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung des Datumsauswahl für die Ankunft eines Wegpunktes mit neuen Schnellauswahlfeldern (links: vorher - rechts: neu)',
                            },
                        ],
                    },
                    {
                        text: [
                            'Fix: Auf dem Dashboard in der Rundenliste wird keine Bewertung angezeigt, wenn die Runde noch nicht abgeschlossen ist.',
                            'Bisher wurde immer die Bewertung von 1 angezeigt. Nun wird ein "-" angezeigt.',
                        ],
                    },
                ],
            },
            {
                header: '01.05.2023',
                avatarText: '&#128119;',
                avatarTitle: 'Tag der Arbeit',
                entries: [
                    {
                        text: [
                            'UX-Feature: Der Name einer Runde kann mittels Autocomplete-Feld ausgewählt werden. Freitexteingaben sind weiterhin möglich.',
                            'Dazu kann ein Administrator pro Team die Autocomplete-Vorschläge für den Namen einer Runde festlegen.',
                            'Use Case: Beim Erstellen und Verändern einer Runde braucht ein Benutzender weniger Klicks und Eingaben zu machen. Zudem sind die gemachten Eingaben über alle Runden hinweg einheitlicher und verschiedene Schreibweisen werden vermieden.',
                        ],
                        gallery: [
                            {
                                src: '../changelog/20230501_Namen_einer_Runde_Runde_beginnen-Formular.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung auf dem "Runde erstellen"-Formular',
                            },
                            {
                                src: '../changelog/20230501_Namen_einer_Runde_Teamformular.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung auf dem Teamformular',
                            },
                            {
                                src: '../changelog/20230501_Namen_einer_Runde_Teamliste.png',
                                thumbnailHeight: '75px',
                                description: 'Darstellung in der Teamliste',
                            },
                        ],
                    },
                    {
                        text: [
                            'UX-Feature: Die Tageskonzepte einer Runde können mittels Autocomplete-Feld ausgewählt werden. Freitexteingaben sind weiterhin möglich.',
                            'Dazu kann ein Administrator pro Team die Autocomplete-Vorschläge für das Tageskonzept einer Runde festlegen.',
                            'Use Case: Beim Erstellen und Verändern einer Runde braucht ein Benutzender weniger Klicks und Eingaben zu machen. Zudem sind die gemachten Eingaben über alle Runden hinweg einheitlicher und verschiedene Schreibweisen werden vermieden.',
                        ],
                        gallery: [
                            {
                                src: '../changelog/20230501_Tageskonzept_einer_Runde_Runde_beginnen-Formular.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung auf dem "Runde erstellen"-Formular',
                            },
                            {
                                src: '../changelog/20230501_Tageskonzept_einer_Runde_Teamformular.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung auf dem Teamformular',
                            },
                            {
                                src: '../changelog/20230501_Tageskonzept_einer_Runde_Teamliste.png',
                                thumbnailHeight: '80px',
                                description: 'Darstellung in der Teamliste',
                            },
                        ],
                    },
                    {
                        text: [
                            'UX-Feature: Die Auswahl der Bewertung erfolgt nun über eine Sternenauswahl anstatt über eine einfache Dropdown-Box.',
                            'Dies verringert für die Auswahl der Bewertung die Anzahl der Klicks von 2 auf 1.',
                            'Zusätzlich wird damit ein Stück Gamification bereitgestellt.',
                        ],
                        gallery: [
                            {
                                src: '../changelog/20230501_Rundenbewertung_als_Stern_vorher_nachher.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung auf der Rundenbewertung (links: vorher - rechts: neu)',
                            },
                        ],
                    },
                    {
                        text: [
                            'Einführung der "FAQ - Häufig gestellte Fragen" um (neuen) Nutzern häufig auftretende Fragen inkl. deren Antworten zu geben.',
                            'Der Aufruf der FAQ erfolgt über das Hauptmenü.',
                        ],
                        gallery: [
                            {
                                src: '../changelog/20230501_FAQ_Einführung.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung der FAQ mit den ersten Fragen und Antworten',
                            },
                        ],
                    },
                    {
                        text: [
                            'Die Größe des Vorschaubildes eines Wegpunktes ist reduziert damit es vor allem auf kleinen Devices nicht zu viel Platz einnimmt und das Scrollen erleichtert.',
                        ],
                    },
                    {
                        text: [
                            'Fix: Ein Admin kann nun ein existierendes Bild an einem Wegpunkt löschen. Dies funktionierte zuvor nicht.',
                        ],
                    },
                ],
            },
            {
                header: '09.04.2023',
                avatarText: '&#x1F423;',
                avatarTitle: 'Frohe Ostern!',
                entries: [
                    {
                        text: [
                            'UX-Feature: Der Rundenbeginn kann vom Wegpunkthinzufügen-Formular aus auf die aktuell eingestellte Ankunftszeit gesetzt werden.',
                            'Der Button zum Rundenbeginn ändern wird nur beim ersten Wegpunkt angezeigt und wenn die Ankunftszeit des Wegpunktes for dem Rundenbeginn liegt.',
                            'Use Case: Ein Bearbeiter möchte nachträglich eine Runde von gestern protokollieren. Dabei startet er die Runde und vergisst den Rundenbeginn abzuändern. Nun möchte er den ersten Wegpunkt hinzufügen und stellt den gestrigen Tag ein. Der erste Wegpunkt liegt nun vor dem Rundenbeginn, was zu einem Fehler führen würde.',
                        ],
                        gallery: [
                            {
                                src: '../changelog/20230409_Wegpunkte_Rundenstartzeit_vorher_nachher.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung auf dem Wegpunkt hinzufügen-Formular',
                            },
                            {
                                src: '../changelog/20230409_Wegpunkte_Rundenstartzeit_nach_klick.png',
                                thumbnailHeight: '100px',
                                description: 'Nach Klick auf Rundenbeginn auf "Dienstag 28.03.2023 um 08:22" setzen',
                            },
                        ],
                    },
                    {
                        text: [
                            'Feature: Ein Admin kann Tags deaktivieren und aktivieren.',
                            'Nur aktivierte Tags werden einem Benutzer beim Erstellen eines Wegpunktes angezeigt.',
                            'Neben aktivierten Tags werden beim Bearbeiten eines Wegpunktes auch die deaktivierten Tags mit angezeigt, die dem Wegpunkt zugeordnet sind.',
                            'In der Wegpunkte-Tabelle auf der Startseite sind als Filtermöglichkeit alle Tags dargestellt, die mindestens einem Wegpunkt zugeordnet sind.',
                        ],
                        gallery: [],
                    },
                    {
                        text: [
                            'Fix: Ein Admin kann eine noch nicht abgeschlossene Runde ändern. Dies war bisher nur für abgeschlossene Runden möglich und führte für nichtabgeschlossene Runden bisher zu einem Fehler.',
                        ],
                    },
                ],
            },
            {
                header: '25.03.2023',
                avatarText: '&#129498;',
                entries: [
                    {
                        text: [
                            'Feature: Die Filterung der Runden wirkt sich auf den Runden-CSV-Export aus.',
                            'Alle Filtereinstellungen der Runden-Tabelle werden dabei berücksichtigt statt bisher nur der Rundenbeginn.',
                            'Der Dateiname setzt sich aus den gewählten Filtern zusammen: "streetworkrunden_export.csv" bzw. bei mehreren gesetzten Filtern "20220101-20221231&shy;_BEENDET_ja&shy;_TEAM_Zugehende Sozialarbeit 1&shy;_streetworkrunden_export.csv"',
                        ],
                        gallery: [
                            {
                                src: '../changelog/20230325_Streetworkrunden_Export_vorher_small.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung auf dem Dashboard vor der Umstellung',
                            },
                            {
                                src: '../changelog/20230325_Streetworkrunden_Export_nachher_small.png',
                                thumbnailHeight: '100px',
                                description: 'zusammengefasste Darstellung auf dem Dashboard nach der Umstellung',
                            },
                        ],
                    },
                    {
                        text: [
                            'Feature: Beim Runden-CSV-Export werden weitere Spalten mit ausgegeben.',
                            'Die Personenanzahl von Nutzergruppen wird je Nutzergruppe als eigene Spalte mit ausgegeben.',
                            'Die Teilnehmenden einer Runde werden in einer eigenen Spalte mit ausgegeben.',
                            'Der Wochentag des Beginns und des Endes einer Runde wird als eigene Spalte mit ausgegeben.',
                            'Das Datum des Beginns und des Endes einer Runde wird als eigene Spalte mit ausgegeben.',
                            'Die Uhrzeit des Beginns und des Endes einer Runde wird als eigene Spalte mit ausgegeben.',
                        ],
                    },
                    {
                        text: [
                            'Feature: Beim Wegpunkte-CSV-Export werden weitere Spalten mit ausgegeben.',
                            'Der Wochentag der Ankunft eines Wegpunktes wird als eigene Spalte mit ausgegeben.',
                            'Das Datum der Ankunft eines Wegpunktes wird als eigene Spalte mit ausgegeben.',
                            'Die Uhrzeit der Ankunft eines Wegpunktes wird als eigene Spalte mit ausgegeben.',
                        ],
                    },
                    {
                        text: [
                            'UX-Feature: Alle Filterungen heben sich nun expliziter im von anderen Elementen Formular vom Rest ab.',
                            'Dies verbessert das Erkennen und Zurücksetzen von gesetzten Filtern.',
                            'Zusätzlich gibt es einen Button um alle gesetzten Filter mit einem Mal zurückzusetzen.',
                        ],
                        gallery: [
                            {
                                src: '../changelog/20230325_Streetworkrunden_Filter_vorher.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung der gesetzten Filterung der Wegpunktetabelle vor der Umstellung',
                            },
                            {
                                src: '../changelog/20230325_Streetworkrunden_Filter_nachher.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung der gesetzten Filterung der Wegpunktetabelle nach der Umstellung',
                            },
                        ],
                    },
                ],
            },
            {
                header: '01.02.2023',
                avatarText: '&#129528;',
                entries: [
                    {
                        text: [
                            'Feature: Ein Admin kann einen Wegpunkt löschen.',
                            'Dies ist auf der Wegpunkt-Detailseite möglich.',
                            'Um ein versehentliches Löschen zu vermeiden kommt eine Sicherheitsabfrage und der Ort des Wegpunktes muss manuell eingegeben werden.',
                        ],
                        gallery: [
                            {
                                src: '../changelog/20230201_Wegpunkt_loeschen_vorher_nachher.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung auf der Wegpunkt-Detailseite (links: vorher - rechts: neu)',
                            },
                            {
                                src: '../changelog/20230201_Wegpunkt_loeschen_Overlay.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung der Sicherheitsabfrage',
                            },
                            {
                                src: '../changelog/20230201_Wegpunkt_loeschen_Overlay_ausgefuellt.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung der ausgefüllten Sicherheitsabfrage',
                            },
                        ],
                    },
                    {
                        text: [
                            'Feature: Ein Admin kann eine Runde löschen. Dabei werden auch alle zugehörigen Wegpunkte gelöscht.',
                            'Dies ist auf der Runden-Detailseite möglich.',
                            'Um ein versehentliches Löschen zu vermeiden kommt eine Sicherheitsabfrage und der Name der Runde muss manuell eingegeben werden.',
                        ],
                        gallery: [
                            {
                                src: '../changelog/20230201_Runde_loeschen_vorher_nachher.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung auf der Runden-Detailseite (links: vorher - rechts: neu)',
                            },
                            {
                                src: '../changelog/20230201_Runde_loeschen_Overlay.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung der Sicherheitsabfrage',
                            },
                            {
                                src: '../changelog/20230201_Runde_loeschen_Overlay_ausgefuellt.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung der ausgefüllten Sicherheitsabfrage',
                            },
                        ],
                    },
                    {
                        text: [
                            'UX-Feature: Ein Bearbeiter bekommt einen Hinweis angezeigt, wenn die Ankunftszeit eines Wegpunktes mehr als 4 Stunden her ist gegenüber dem letzten Wegpunkt bzw. der Rundenstartzeit.',
                            'Use Case 1: Der Ankunftszeit eines Wegpunktes ist beim nachträglichen Protokollieren bspw. am nächsten Tag initial auf die aktuelle Uhrzeit gesetzt und wurde bisher vom Nutzenden übersehen.',
                            'Use Case 2: Der Endzeitpunkt einer Runde ist beim nachträglichen Protokollieren nach bspw. 2 Tagen auf einem falschen Wert und zeigt einen Validierungfehler, wenn ein Wegpunkt vorher eine zu späte Ankunftszeit gesetzt bekommen hatte.',
                            'Beispiel zu Use Case 3: Eine Runde wurde am Montag gestartet und zwei Wegpunkte wurden angelegt. Am Mittwoch wurden weitere Wegpunkte hinzugefügt. Dabei wurde die Ankunftszeit nicht abgeändert, da es für den Nutzenden nicht ersichtlich war und bspw. durch Flüchtigkeitsfehler vergessen/übersehen wurude. Beim Abschluss der Runde mit Rundenendzeit auf Montag wurde dann ein Validierungsfehler angezeigt.',
                        ],
                        gallery: [
                            {
                                src: '../changelog/20230201_Wegpunkte_Formular_Hinweis_Ankunftszeit.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung des Hinweises im Wegpunkte-Formular',
                            },
                            {
                                src: '../changelog/20230201_Wegpunkte_Hinzufuegen_Formular_Hinweis_Ankunftszeit.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung des Hinweises im Wegpunkt hinzufügen-Formular in Stunden bezogen auf die Rundenstartzeit',
                            },
                        ],
                    },
                    {
                        text: [
                            'UX-Feature: Ein Bearbeiter bekommt einen Hinweis angezeigt, wenn die Rundenendzeit einer Runde mehr als 4 Stunden her ist gegenüber dem letzten Wegpunkt.',
                            'Use Case: Eine Runde wurde am Montag gestartet und zwei Wegpunkte wurden angelegt. Am Mittwoch wird die Runde beendet und die Rundenendzeit enthält initial die aktuelle Uhrzeit für Mittwoch. Das wurde vom Nutzenden bisher leicht übersehen und die Rundenendzeit nicht auf Montag gesetzt.',
                        ],
                        gallery: [
                            {
                                src: '../changelog/20230201_Runden_Formular_Hinweis_Rundenendzeit.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung des Hinweises im Runden-Formular in Tagen',
                            },
                            {
                                src: '../changelog/20230201_Runden_Formular_Hinweis_Rundenendzeit_Stunden.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung des Hinweises im Runden-Formular in Stunden',
                            },
                        ],
                    },
                    {
                        text: [
                            'UX-Feature: Ein Bearbeiter kann die Rundenendzeit einer Runde sowie die Ankunftszeit eines Wegpunktes mittels Schnellauswahlbuttons setzen.',
                            'Dies reduziert die Anzahl der Klicks.',
                            'Die Schnellauswahlbuttons sind auf allen Wepunkt- und Runden-Formulare verfügbar.',
                            'Es gibt einen Schnellauswahl für den aktuelle Zeitpunkt und einen für 5 Minuten nach dem letzten Wegpunkt (bzw. der Rundenstartzeit, falls es keinen letzten Wegpunkt gibt).',
                            'Use Case 1: Eine Runde wurde am Montag morgen gestartet und zwei Wegpunkte wurden angelegt. Am Montag Abend wird die Protokollierung fortgesetzt und die Uhrzeit muss geändert werden. Mittels klick auf "Schnellauswahl: 5 Minuten nach dem letzten Wegpunkt" setzt der Nutzende schnell die Ankunftszeit.',
                            'Use Case 2: Eine Runde wurde am Montag gestartet und keine Wegpunkte wurden angelegt. Am Mittwoch wird die Protokollierung fortgesetzt und die Uhrzeit muss geändert werden. Mittels klick auf "Schnellauswahl: 5 Minuten nach dem Rundenbeginn" setzt der Nutzende schnell die Ankunftszeit.',
                        ],
                        gallery: [
                            {
                                src: '../changelog/20230201_Schnellauswahlbuttons_Wegpunkte_Formular_vorher_nachher.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung der Schnellauswahlbuttons im Wegpunkte-Formular (links: vorher - rechts: neu)',
                            },
                        ],
                    },
                    {
                        text: [
                            'UX-Feature: Die Darstellung der Rundenstartzeit und Rundenendzeit im Runde-Bearbeiten-Formular ist nun 1-zeilig anstatt 2-zeilig.',
                            'Dies erlaubt mehr Content auf dem Bildschirm und spart scrollen.',
                        ],
                        gallery: [
                            {
                                src: '../changelog/20230201_Runden_Formular_Einzeilig.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung der Zeitauswahl im Runden-Formular (links: vorher - rechts: neu)',
                            },
                        ],
                    },
                    {
                        text: [
                            'Fix: Bei der Datumsänderung konnte es ggfs. zu falschen Datumswerten kommen, die erst nach dem Speichern sichtbar wurden.',
                            'Dies passierte bspw. beim Wechsel von "01.02.2023" auf "31.01.2023".',
                        ]
                    },
                    {
                        text: 'Fix: Bei Aufruf eines nichtexistierenden Wegpunktes wurde ggfs. ein Fehler angezeigt, der die Weiterleitung auf das Dashboard verhinderte.',
                    },
                ]
            },
            {
                header: '17.01.2023',
                avatarText: '&#x1F3D7;',
                entries: [
                    {
                        text: 'Misc: Softwarebibliotheken geupdated. Unter anderem von Symfony 5.4 auf 6.x und von Api Platform 2.7 auf 3.x.',
                    },
                ]
            },
            {
                header: '09.11.2022',
                avatarText: '&#129303;',
                avatarTitle: 'Zu Ehren des \'Falls der Berliner Mauer\'',
                entries: [
                    {
                        text: [
                            'Feature: Ein Admin kann pro Team festlegen, ob zu einem Wegpunkt die Altersgruppen oder die Anzahl der Personen vor Ort oder keines von beiden festgehalten werden sollen.',
                            'Bisher war die Erfassung der Altersgruppen ein Pflichtfeld.',
                            'Use Case 1: Bei einer Gruppe von 40 Leuten ist es schwierig bzw. zeitaufwändig zu sagen, wie das Verhältnis von m/w/x ist.',
                            'Use Case 2: Bei manchen Teams werden nur in bestimmten Fällen Altersgruppen mit erfasst.',
                            'Use Case 3: Bei manchen Teams spielt die Anzahl der Personen keine Rolle und braucht nicht mit erfasst werden.',
                        ],
                        gallery: [
                            {
                                src: '../changelog/20220927_Team_Formular_vorher_nachher.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung im Team-Formular (links: vorher - rechts: neu)',
                            },
                            {
                                src: '../changelog/20220927_Wegpunkte_Formular_vorher_nachher.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung im Wegpunkte-Formular (links: vorher - rechts: neu ohne Altersgruppen)',
                            },
                            {
                                src: '../changelog/20220927_Wegpunkte_Formular_vorher_nachher_nur_mit_personenzahl.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung im Wegpunkte-Formular (links: vorher - rechts: neu nur mit Anzahl der Personen)',
                            },
                            {
                                src: '../changelog/20220927_Altersgruppen_Wegpunkttabelle_Dashboard_vorher_nachher.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung in der Wegpunkttabelle auf dem Dashboard (links: vorher - rechts: neu)',
                            },
                            {
                                src: '../changelog/20220927_Altersgruppen_Wegpunkttabelle_Rundendetailseite_vorher_nachher.png',
                                thumbnailHeight: '75px',
                                description: 'Darstellung in der Wegpunkttabelle auf der Runden-Detailseite (links: vorher - rechts: neu)',
                            },
                        ],
                    },
                    {
                        text: [
                            'Feature: Ein Admin kann pro Team festlegen, ob zu einer Runde weitere Teilnehmende festgehalten werden sollen.',
                            'Für die weiteren Teilnehmenden können optional Autocomplete-Vorschläge erstellt werden.',
                            'Die weiteren Teilnehmenden werden im csv-Export der Runden in einer Spalte aufgelistet mit ausgegeben.',
                            'Use Case 1: Ab und zu nehmen Personen der Diakonie an der Runde des Teams teil.',
                            'Use Case 2: Ab und zu nimmt ein/-e Praktikant/-in an der Runde des Teams teil.',
                        ],
                        gallery: [
                            {
                                src: '../changelog/20221026_Team_Formular_vorher_nachher.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung im Team-Formular (neu: Einstellungen für die Dokumentation einer Runde)',
                            },
                            {
                                src: '../changelog/20221026_Runden_Formular_vorher_nachher.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung im Runden-Formular (links: vorher - rechts: neu mit weiteren Teilnehmenden und Autocomplete)',
                            },
                            {
                                src: '../changelog/20221026_Runden_Formular_eigener_Eintrag.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung im Runden-Formular: eigene Einträge sind auch während der Rundenerstellung möglich',
                            },
                        ],
                    },
                    {
                        text: [
                            'Feature: Erhöhung der möglichen Werte für die Altersgruppen.',
                            'Bisher war nur die maximale Anzahl von 20 möglich.',
                            'Nun kann - in verschiedenen Abstufungen - bis zu 500 ausgewählt werden.',
                        ],
                        gallery: [
                            {
                                src: '../changelog/20220927_Werte_Altersgruppen_Wegpunkte_Formular_vorher_nachher.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung im Wegpunkte-Formular (links: vorher - rechts: neu)',
                            },
                        ],
                    },
                    {
                        text: [
                            'Feature: Ein Nutzender muss nicht alle Felder einer Runde eingeben .',
                            'Dies betrifft die Felder: "Systemische Antwort", "Reflexion", "Termine, Besorgungen, Verabredungen" sowie "Erkenntnisse, Überlegungen, Zielsetzungen".',
                            'Die Nichteingabe muss jedoch bewusst per Klick bestätigt werden.',
                            'Der Vorteil ist, dass keine Dummywerte mehr eingetragen werden brauchen und ein Nutzender sich somit die Klicks dafür spart.',
                        ],
                        gallery: [
                            {
                                src: '../changelog/20221026_optionale_Felder_Wegpunkt_abschliessen_Formular_vorher_nachher.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung bei Runde abschließen und bei Runde ändern (links: vorher - rechts: neu)',
                            },
                        ],
                    },
                    {
                        text: [
                            'Feature: Ein Nutzender kann die Rundenliste auf dem Dashboard nach nicht beendeten Runden filtern.',
                        ],
                        gallery: [
                            {
                                src: '../changelog/20220927_Filter_Rundenende_Dashboard_vorher_nachher.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung der Filterung auf dem Dashboard (links: vorher - rechts: neu)',
                            },
                        ],
                    },
                    {
                        text: [
                            'Feature: Wenn ein Nutzer mit einem veralteten und nicht unterstützten Browser Swapp versucht zu nutzen, bekommt er einen Hinweis diesbezüglich angezeigt.',
                            'Dies ist bspw. beim Internet Explorer 11 der Fall.',
                        ],
                        gallery: [
                            {
                                src: '../changelog/20221026_Deprecated_Browser_hint.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung des Hinweises auf jeder Seite',
                            },
                        ],
                    },
                    {text: 'Fix: Beim Klick auf "Wegpunkt speichern und Runde abschließen" im Firefox wurden 2 Wegpunkte anstatt einer gespeichert. Dies passiert nun nicht mehr.'},
                ],
            },
            {
                header: '24.06.2022',
                avatarText: '😺️',
                entries: [
                    {
                        text: [
                            'Feature: Ein Bearbeiter kann die Wegpunkte auf dem Dashboard nach dem Zeitpunkt der Ankunft filtern. ',
                            'Bei der Standardeinstellung ist kein Zeitraum ausgewählt.',
                            'Beim Seitenwechsel wird sich die Filtereinstellung gemerkt.',
                        ],
                        gallery: [
                            {
                                src: '../changelog/20220624_Ankunft_Wegpunkt_Filterung_vorher_nachher.png',
                                thumbnailHeight: '100px',
                                description: 'Der neue Filter nach Ankunft auf dem Dashboard (links: vorher - rechts: neu)',
                            },
                        ],
                    },
                    {
                        text: [
                            'Feature: Ein Bearbeiter kann die Wegpunkte als csv-Datei exportieren.',
                            'Die Filtereinstellungen der Wegpunkte-Tabelle werden dabei berücksichtigt.',
                            'Der Dateiname setzt sich aus den gewählten Filtern zusammen: "streetworkwegpunkte_export.csv" bzw. bei Datumsfilterung "20220601-20220630&shy;_streetworkwegpunkte_export.csv"',
                        ],
                        gallery: [
                            {
                                src: '../changelog/20220624_Wegpunkte-csv-Export_vorher_nachher.png',
                                thumbnailHeight: '100px',
                                description: 'Der Button zum Export von Wegpunkten auf dem Dashboard (links: vorher - rechts: neu)',
                            },
                        ],
                    },
                    {
                        text: 'Feature: Ein Admin kann pro Team festlegen, ob zu einem Wegpunkt die Personenanzahl von Nutzergruppen festgehalten werden soll und um welche Nutzergruppen es sich handelt.',
                        gallery: [
                            {
                                src: '../changelog/20220624_Nutzergruppen_Teamformular_vorher_nachher.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung im Team-Formular (links: vorher - rechts: neu). Der Admin kann die Namen der Nutzergruppen frei bestimmen.',
                            },
                        ],
                    },
                    {
                        text: [
                            'Feature: Ein Bearbeiter kann bei einem Wegpunkt die Personenanzahl von Nutzergruppen festhalten.',
                            'Die Darstellung erfolgt auf der Wegpunkt-Detailseite sowie beim csv-Export der Wegpunkte.',
                        ],
                        gallery: [
                            {
                                src: '../changelog/20220624_Nutzergruppen_Formular_vorher_nachher.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung im Wegpunkte-Formular (links: vorher - rechts: neu)',
                            },
                            {
                                src: '../changelog/20220624_Nutzergruppen_Detailseite_vorher_nachher.png',
                                thumbnailHeight: '50px',
                                description: 'Darstellung auf der Wegpunkte-Detailseite (links: vorher - rechts: neu)',
                            },
                        ],
                    },
                    {
                        text: [
                            'Feature: Zu einem Wegpunkt muss die Ankunft als Zeitpunkt festgehalten werden.',
                            'Sie muss nach der Rundenstartzeit und - falls die Runde schon abgeschlossen ist - vor der Rundenendzeit liegen.',
                            'Die Darstellung erfolgt in der Wegpunkttabelle auf der Runden-Detailseite, auf der Wegpunkt-Detailseite sowie in der Wegpunkttabelle auf dem Dashboard anstatt der Spalte Runden-Beginn.',
                        ],
                        gallery: [
                            {
                                src: '../changelog/20220624_Ankunft_Formular_vorher_nachher.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung im Formular (links: vorher - rechts: neu)',
                            },
                            {
                                src: '../changelog/20220624_Ankunft_Detailseite_vorher_nachher.png',
                                thumbnailHeight: '75px',
                                description: 'Darstellung auf der Detailseite (links: vorher - rechts: neu)',
                            },
                            {
                                src: '../changelog/20220624_Ankunft_Wegpunkttabelle_Rundendetailseite_vorher_nachher.png',
                                thumbnailHeight: '75px',
                                description: 'Darstellung in der Wegpunkttabelle auf der Runden-Detailseite (links: vorher - rechts: neu)',
                            },
                            {
                                src: '../changelog/20220624_Ankunft_Wegpunkttabelle_Dashboard_vorher_nachher.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung in der Wegpunkttabelle auf dem Dashboard (links: vorher - rechts: neu)',
                            },
                        ],
                    },
                    {
                        text: [
                            'Security: Die minimale Passwortlänge eines Benutzerpasswortes ist von 7 auf 12 Zeichen erhöht.',
                            'Dies betrifft nur das Neusetzen eines Passwortes.',
                        ],
                    },
                ],
            },
            {
                header: '01.06.2022',
                avatarText: '🙃️',
                entries: [
                    {
                        text: 'Feature: Swapp kann nun als App (Progressive Web App - PWA) installiert werden. Vorteile sind unter anderem ein schnellerer Zugriff auf Swapp und der Wegfall der Browserleiste. Die Installation ist auf fast allen Betriebssystemen möglich. Anbei die Installationsanleitung als Video auf einem iPhone:',
                        gallery: [
                            {
                                src: '../changelog/20220601_iPad_Splitscreen_PWA.jpeg',
                                thumbnailHeight: '100px',
                                description: 'Swapp im Splitscreen auf einem iPad (links: im Browser - rechts: als PWA)',
                            },
                            {
                                src: '../changelog/20220601_Swapp-Installation-iPhoneSE.mp4',
                                thumbnailHeight: '100px',
                                description: 'Installation von Swapp als PWA unter iOS.',
                                autoplay: false,
                                thumbnail: '../changelog/pexels-torsten-dettlaff-347734.jpg',
                            },
                        ],
                    },
                    {
                        text: 'Feature: Zu einem Wegpunkt wird die Anzahl der Personen vor Ort mit ausgegeben. Sie ergibt sich as der Summe der angetroffenen Personen aller Altersgruppen. Darstellung im Wegpunkt-Formular, auf der Wegpunkt-Detailseite sowie als eigene Spalte im Runden-CSV-Export.',
                        gallery: [
                            {
                                src: '../changelog/20220601_angetroffene_Personenzahl_Formular.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung im Formular (links: Nachher - rechts: Vorher)',
                            },
                            {
                                src: '../changelog/20220601_angetroffene_Personenzahl_Detailseite.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung auf der Detailseite (links: Nachher - rechts: Vorher)',
                            },
                        ],
                    },
                    {
                        text: 'Feature: Zu einem Wegpunkt kann die Anzahl direkter Kontakte mit aufgenommen werden. Ein direkter Kontakt ist eine Person mit der an diesem Wegpunkt gesprochen wurde. Die Erfassung ist optional und kann von einem Admin für jedes Team einzeln festgelegt werden. Zusätzlich wurde das Design für das Erstellen-Formular und Bearbeiten-Formular eines Teams überarbeitet. Darstellung im Wegpunkt-Formular, auf der Wegpunkt-Detailseite sowie als eigene Spalte im Runden-CSV-Export.',
                        gallery: [
                            {
                                src: '../changelog/20220601_Anzahl_direkter_Kontakte_eines_Wegpunktes_Teamseite.png',
                                thumbnailHeight: '100px',
                                description: 'Teamliste mit neuer Spalte "zusätzliche Wegpunkt-Felder" sowie überarbeitetes "Neues Team erstellen"-Formular',
                            },
                            {
                                src: '../changelog/20220601_Anzahl_direkter_Kontakte_eines_Wegpunktes_Team-Formular.png',
                                thumbnailHeight: '100px',
                                description: 'Überarbeitetes "Team bearbeiten"-Formular mit neuem Switch für "optionale Felder"',
                            },
                            {
                                src: '../changelog/20220601_Anzahl_direkter_Kontakte_eines_Wegpunktes_Formular.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung im Formular',
                            },
                            {
                                src: '../changelog/20220601_Anzahl_direkter_Kontakte_eines_Wegpunktes_Detailseite.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung auf der Detailseite',
                            },
                        ],
                    },
                    {
                        text: 'Feature: Ein Nutzer kann beim Runden-CSV-Export über einen Datumsfilter eine Spanne eingeben um nicht mehr alle Runden mit einmal zu exportieren.',
                        gallery: [
                            {
                                src: '../changelog/20220601_Datumsfilter_Runden-CSV-Export_vorher_nachher.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung in der Mobilansicht (links: vorher - rechts: neu)',
                            },
                            {
                                src: '../changelog/20220601_Datumsfilter_Runden-CSV-Export_Mobilansicht.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung in der Mobilansicht mit aufgeklapptem Datumsfilter',
                            },
                            {
                                src: '../changelog/20220601_Datumsfilter_Runden-CSV-Export_Tabletansicht.png',
                                thumbnailHeight: '100px',
                                description: 'Darstellung in der Tabletansicht mit aufgeklapptem Datumsfilter',
                            },
                        ],
                    },
                ],
            },
            {
                header: '24.02.2022',
                avatarText: '🪛',
                entries: [
                    {text: 'Fix: Beim Erstellen eines Wegpunktes wurden die Tags nicht mit gespeichert. Lediglich beim Ändern eines Wegpunktes funktionierte das Ändern der Tags.'},
                    {text: 'Fix: Für den "Name" (maximal 300 Zeichen), "Termine, Besorgungen, Verabredungen" (maximal 2500 Zeichen) und "Termine, Besorgungen, Verabredungen" (maximal 2500 Zeichen) einer Runde kann nun das jeweilige Limit an Zeichen auch gespeichert werden.'},
                ],
            },
            {
                header: '22.02.2022',
                avatarText: '🛠️',
                entries: [
                    {text: 'Feature: Ein Admin kann die Benutzerliste nach aktiven und inaktiven Benutzern filtern.'},
                    {text: 'Feature: Die Benutzer in der Benutzerliste sind nun nach aktiv/inaktiv und Benutzername sortiert.'},
                    {
                        text: 'Feature: Ein Nutzender wird nun mit einem Hinweis auf neue Änderungen in Swapp hingewiesen.',
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
                    {text: 'UX-Feature: Die Eingabefelder für die Altersgruppen beim Erstellen und Verändern eines Wegpunktes enthalten 3 Eingabefelder je Zeile anstatt 1 Eingabefeld je Zeile. Dies führt zu einer kompakteren und übersichtlicheren Darstellung.'},
                    {text: 'Fix: Wegpunkte konnten auf Safari- und iOS-Safari-Browser <=15.3 nicht verändert werden.'},
                ],
            },
            {
                header: '14.02.2022 - Valentinstags-Update',
                avatarText: '💘🎁',
                entries: [
                    {
                        text: 'Fix: Bei der Auswahl der systemischen Frage für eine Runde wurde der Träger nicht beachtet. Dies ist behoben worden, so dass beim Starten einer Runde eine zufällige Systemische Frage vom eigenen Träger verwendet wird.',
                    },
                    {text: 'Feature: Ein Admin kann ein Team erstellen.'},
                    {text: 'Misc: Softwarebibliotheken geupdated. Symfony-Update von 5.3 auf 5.4. PHP-Upgrade von 8.0 auf 8.1.'},
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
        lastVisitedAt: useLocalStorage('swapp-store-changelog-last-visited-at', dayjs().subtract(1, 'month')),
    }),
    getters: {
        getChangelogs({changelogs}): any[] {
            return changelogs;
        },
        getLatestCreatedAt({changelogs}): Dayjs {
            if (!changelogs.length) {
                return dayjs();
            }
            const header = changelogs[0].header;

            return dayjs(header.split(' ')[0], ['DD.MM.YYYY', 'YYYY']);
        },
        getLastVisitedAt({lastVisitedAt}): Dayjs | boolean {
            return lastVisitedAt;
        },
        hasNewChangelogItems({lastVisitedAt}: any): boolean {
            return this.getLatestCreatedAt.isAfter(lastVisitedAt);
        },
    },
    actions: {
        updateLastVisitedAt(lastVisitedAt: Dayjs): void {
            this.lastVisitedAt = lastVisitedAt;
        },
    },
})


// make sure to pass the right store definition, `useAuth` in this case.
// @ts-expect-error
if (import.meta.webpackHot) {
    // @ts-expect-error
    import.meta.webpackHot.accept(acceptHMRUpdate(useChangelogStore, import.meta.webpackHot))
}
