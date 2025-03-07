<template>
    <div>
        <content-collapse
            title="FAQ - Häufig gestellte Fragen"
            collapse-key="faq-swapp"
            is-visible-by-default
        >
            <v-list class="pt-0">
                <template v-for="item in items">
                    <v-list-item
                        class="d-flex justify-content-between align-items-center bg-grey grey lighten-2"
                    >
                        <div>
                            <span class="font-weight-bold">{{ item.header }}</span>
                        </div>
                    </v-list-item>
                    <v-list-item>
                        <ul class="pl-5 mb-0">
                            <li
                                v-for="entry in item.entries"
                            >
                                <template v-if="Array.isArray(entry.text)">
                                    <span
                                        v-html="entry.text[0]"
                                    />
                                    <ul class="pl-5">
                                        <li
                                            v-for="(textItem, i) in entry.text"
                                            v-if="i !== 0"
                                            v-html="textItem"
                                        />
                                    </ul>
                                </template>
                                <span
                                    v-else
                                    v-html="entry.text"
                                />
                            </li>
                        </ul>
                    </v-list-item>
                </template>
            </v-list>
        </content-collapse>
    </div>
</template>

<script>
'use strict';
import ContentCollapse from './ContentCollapse.vue';

export default {
    name: 'Faq',
    components: {
        ContentCollapse,
    },
    data: () => {
        return {
        };
    },
    computed: {
        items() {
            return [
                {
                    'header': 'Werden bestehende Runden geändert, wenn ich ein Team ändere?',
                    'entries': [
                        {
                            text: 'Nein, die Änderungen an einem Team wirken sich nur auf zukünftig erstellte Runden aus.',
                        },
                    ],
                },
                {
                    'header': 'Ist es möglich als Altersgruppe zusätzlich zu 0-99 auch nur die bis 18-jährigen zu erfassen?',
                    'entries': [
                        {
                            text: 'Ja, in diesem Fall können bspw. zwei Altersgruppen erstellt werden - eine für 0-18 und eine für 19-99.',
                        },
                    ],
                },
                {
                    'header': 'Was sind "Direkte Kontakte" an einem Wegpunkt?',
                    'entries': [
                        {
                            text: 'Jedes Team kann für sich selber definieren, was direkte Kontakte sind. Bspw. können direkte Kontakte als Personen, mit denen gesprochen wurde, definiert werden.',
                        },
                    ],
                },
                {
                    'header': 'Was ist der Unterschied zwischen der Benutzerrolle "Administrator" und einem Nutzer ohne diese Rolle?',
                    'entries': [
                        {
                            text:  'Ein Nutzer kann nur Runden sowie Wegpunkte bearbeiten und löschen bei denen er der Rundenersteller ist.',
                        },
                        {
                            text:  'Ein Administrator kann weitere Benutzer erstellen sowie deren Rolle, Nutzername und E-Mail-Adresse ändern.',
                        },
                        {
                            text:  'Ein Administrator kann ein Team erstellen sowie Teams bearbeiten.',
                        },
                        {
                            text:  'Ein Administrator kann Systemische Fragen erstellen sowie Systemische Fragen bearbeiten.',
                        },
                        {
                            text:  'Ein Administrator kann Tags erstellen sowie Tags bearbeiten.',
                        },
                    ],
                },
            ];
        },
    },
    created() {
    },
    methods: {
    },
};
</script>

<style>
</style>
