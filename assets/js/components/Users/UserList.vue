<template>
    <div>
        <div
            v-if="isLoading"
            class="d-flex justify-content-center my-3"
        >
            <b-spinner
                v-show="isLoading"
                style="width: 3rem; height: 3rem;"
                label="is Loading Spinner"
            />
        </div>
        <b-table
            v-show="!isLoading && users.length"
            :items="users"
            :fields="fields"
            class="mb-0"
        >
            <template v-slot:cell(actions)="row">
                <b-button
                    size="sm"
                    @click="toggleEnabled(row.item.userId, row.item.enabled)"
                >
                    Account {{ row.item.enabled ? 'de' : '' }}aktivieren
                </b-button>
            </template>
        </b-table>
    </div>
</template>

<script>
    "use strict";
    export default {
        name: "UserList",
        data: function () {
            return {
                fields: [
                    {
                        key: 'id',
                        label: 'ID',
                        class: 'text-center',
                    },
                    {
                        key: 'username',
                        sortable: true,
                        class: 'text-center',
                    },
                    {
                        key: 'teams',
                        sortable: true,
                        formatter: (value, key, item) => {
                            let teamNames = [];
                            value.forEach(team => {
                                teamNames.push(team.name)
                            })

                            if (teamNames.length) {
                                return teamNames.join(', ')
                            }

                            return '-'
                        },
                        sortByFormatted: true,
                        class: 'text-center',
                    },
                    {
                        key: 'email',
                        label: 'E-Mail',
                        sortable: true,
                        class: 'text-center',
                    },
                    {
                        key: 'roles',
                        formatter: (value, key, item) => {
                            return value.join(', ')
                        },
                        class: 'text-center',
                    },
                    { key: 'actions', label: 'Aktionen', class: 'text-center', }
                ],
                editModalRolle: {
                    id: 'edit-modal-rolle',
                    title: '',
                },
            }
        },
        computed: {
            users() {
                return this.$store.getters['user/users'];
            },
            isLoading() {
                return this.$store.getters["user/isLoading"];
            },
            error() {
                return this.$store.getters["user/error"];
            },
        },
        created() {
            this.$store.dispatch('user/findAll');
        },
        methods: {
            editRolle(rolle, user) {
                this.$root.$emit('bv::show::modal', this.editModalRolle.id)
            },
            resetEditModalRolle() {
                this.editModalRolle.title = ''
            },
            getFormattedFakultaet: function (id) {
                let fakultaet = this.$store.getters["fakultaet/getFakultaetById"](id);

                return `${fakultaet.kurzName} - ${fakultaet.name}`;
            },
            getFormattedStudiengang: function (id) {
                let studiengang = this.$store.getters["studiengang/getStudiengangById"](id);

                return `${studiengang.kurzName} - ${studiengang.name}`;
            },
            toggleEnabled: function (userId, isEnabled) {
                if (isEnabled) {
                    this.$store.dispatch("user/disable", userId);
                } else {
                    this.$store.dispatch("user/enable", userId);
                }
            }
        }
    }
</script>

<style scoped lang="scss">
</style>
