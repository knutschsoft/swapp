<template>
    <b-alert
        show
        class="w-100 text-muted mb-0"
        variant="debug"
    >
        <b>Hinweis:</b>
        <ul class="mb-0">
            <li>
                Beim Abschluss jeder Runde gibt es ein Reflexionsprotokoll mit
                systemischen Reflexionsfragen, welche u.a. einen
                "psychohygienischen" Beitrag zum Abschluss der Streetwork leisten.
                Dazu wird bei Abschluss einer Runde zufällig eine dieser Fragen gestellt und muss beantwortet werden.
            </li>
            <li>
                Die systemischen Fragen sind nur für
                <router-link :to="{name: 'Teams'}">Teams</router-link>
                relevant, für die systemische Fragen aktiviert sind.
                <br>
                <template
                    v-if="teamsWithSystemicQuestions"
                >
                    Deine Teams mit systemischen Fragen sind: {{ teamsWithSystemicQuestions }}
                </template>
                <template
                    v-else
                >
                    Du hast bisher noch keine Teams mit systemischen Fragen.
                </template>
            </li>
            <li>
                Eine Änderung der Fragestellung ändert lediglich die Fragestellung für neue Runden.
            </li>
            <li>
                Bereits bestehende Runden bleiben von Änderungen an einer Frage unberührt.
            </li>
        </ul>
    </b-alert>
</template>

<script>
'use strict';

export default {
    name: 'SystemicQuestionHint',
    props: {},
    components: {},
    data: function () {
        return {};
    },
    computed: {
        teamsWithSystemicQuestions() {
            return this.teams.filter(team => team.isWithSystemicQuestion).map(team => '"' + team.name + '"').join(', ');
        },
        teams() {
            return this.$store.getters['team/teams'];
        },
    },
    async created() {
    },
    methods: {},
};
</script>
