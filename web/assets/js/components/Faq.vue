<template>
    <div>
        <content-collapse
            title="FAQ - Häufig gestellte Fragen"
            collapse-key="faq-swapp"
            is-visible-by-default
        >
            <b-list-group>
                <template v-for="item in items">
                    <b-list-group-item
                        class="d-flex justify-content-between align-items-center"
                        variant="dark"
                    >
                        <div>
                            <span class="font-weight-bold">{{ item.header }}</span>
                        </div>
                    </b-list-group-item>
                    <b-list-group-item>
                        <ul class="pl-3 mb-0">
                            <li
                                v-for="entry in item.entries"
                            >
                                <template v-if="Array.isArray(entry.text)">
                                    <span
                                        v-html="entry.text[0]"
                                    />
                                    <ul>
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
                    </b-list-group-item>
                </template>
            </b-list-group>
        </content-collapse>
    </div>
</template>

<script>
'use strict';
import ContentCollapse from './ContentCollapse.vue';
import dayjs from 'dayjs';

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
                            text: [
                                'Jedes Team kann für sich selber definieren, was direkte Kontakte sind. Bspw. können direkte Kontakte als Personen, mit denen gesprochen wurde, definiert werden.',
                            ],
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
