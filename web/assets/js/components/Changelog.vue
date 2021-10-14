<template>
    <div>
        <content-collapse
            title="Changelog - Was ist neu bei Swapp?"
            collapse-key="changelog-swapp"
            is-visible-by-default
        >
            <b-list-group>
                <template v-for="item in items">
                    <b-list-group-item variant="dark">{{ item.header }}</b-list-group-item>
                    <b-list-group-item>
                        <ul>
                            <li
                                v-for="entry in item.entries"
                                v-html="entry"
                            />
                        </ul>
                    </b-list-group-item>
                </template>
            </b-list-group>
        </content-collapse>
    </div>
</template>

<script>
    "use strict";
    import ContentCollapse from './ContentCollapse.vue';

    export default {
        name: "Users",
        components: {
            ContentCollapse,
        },
        data: () => {
            return {
                items: [
                    {
                        header: '15.10.2021',
                        entries: [
                            'Fix: Paginierung der Wegpunkttabelle auf dem Dashboard funktionierte nicht',
                            'Feature: Die Gesamtanzahl der Wegpunkte auf dem Dashboard wird im Header angezeigt',
                            'Misc: Softwarebibliotheken geupdated',
                        ],
                    },
                    {
                        header: '2017-2020',
                        entries: [
                            'Misc: Jede Menge Updates über die wir hier vielleicht noch berichten werden',
                        ],
                    },
                    {
                        header: '21.06.2016',
                        entries: [
                            'Misc: Beginn der produktiven Nutzung von Swapp',
                        ],
                    },
                    {
                        header: '01.04.2016',
                        entries: [
                            'Misc: Erster Beta-Test zusammen mit der <a href="https://www.treberhilfe-dresden.de">treberhilfe-dresden.de</a>',
                        ],
                    },
                    {
                        header: '01.05.2015',
                        entries: [
                            'Misc: Start der Entwicklung von Swapp',
                        ],
                    },
                ]
            };
        },
    }
</script>

<style scoped>

</style>
