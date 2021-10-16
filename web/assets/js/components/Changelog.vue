<template>
    <div>
        <content-collapse
            title="Changelog - Was ist neu bei Swapp?"
            collapse-key="changelog-swapp"
            is-visible-by-default
        >
            <b-list-group>
                <template v-for="item in items">
                    <b-list-group-item
                        class="d-flex justify-content-between align-items-center"
                        variant="dark"
                    >
                        {{ item.header }}
                        <b-avatar
                            v-if="item.avatarText"
                            variant="light"
                            :text="item.avatarText"
                        />
                    </b-list-group-item>
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
                        header: 'xx.10.2021',
                        avatarText: '🆕️🛠️',
                        entries: [
                            'UX-Feature: Die Autocomplete-Vorschläge für den Ort eines Wegpunktes kann von einem Admin gruppenspezifisch definiert werden. Die vorherige Lösung mit allen Orten des Teams hatte zu viele Einträge und war zu unpraktikabel.',
                        ],
                    },
                    {
                        header: '15.10.2021',
                        avatarText: '🆕️🛠️',
                        entries: [
                            'Fix: Die Paginierung der Wegpunkttabelle auf dem Dashboard funktionierte nicht.',
                            'Fix: In der Spalte "Ende" in der Wegpunkttabelle soll nur die Uhrzeit stehen, wenn der Tag gleich dem Tag von "Beginn" ist. Dies war jedoch auch bei gleichem Wochentag der Fall.',
                            'UX-Feature: Die Gesamtanzahl der Wegpunkte auf dem Dashboard wird im Header angezeigt.',
                            'Misc: Softwarebibliotheken geupdated.',
                        ],
                    },
                    {
                        header: '2017 bis 2020',
                        avatarText: '🤓',
                        entries: [
                            'Misc: Es gab jede Menge Updates über die wir hier vielleicht noch berichten werden.',
                        ],
                    },
                    {
                        header: '21.06.2016',
                        avatarText: '🥳',
                        entries: [
                            'Misc: Beginn der produktiven Nutzung von Swapp.',
                        ],
                    },
                    {
                        header: '01.04.2016',
                        avatarText: '🧪',
                        entries: [
                            'Misc: Erster Beta-Test zusammen mit der <a target="_blank" href="https://www.treberhilfe-dresden.de">treberhilfe-dresden.de</a>.',
                        ],
                    },
                    {
                        header: '01.05.2015',
                        avatarText: '👷',
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
