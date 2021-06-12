<template>
    <b-form
        @submit.prevent.stop="handleSubmit"
        class="p-1 p-sm-2 p-lg-3"
    >
        <b-form-group
            content-cols="12"
            label-cols="12"
            content-cols-lg="8"
            label-cols-lg="2"
            label="Name"
        >
            <b-input
                v-model="name"
                required
                minlength="3"
                maxlength="100"
                placeholder="Name des Tags"
                :state="nameState"
                data-test="name"
            />
        </b-form-group>
        <b-form-group
            content-cols="12"
            label-cols="12"
            content-cols-lg="8"
            label-cols-lg="2"
        >
            <template slot="label" slot-scope="{ }">
                Farbe
                <color-badge
                    v-if="color"
                    :color="color"
                />
            </template>
            <b-form-radio-group
                v-model="color"
                class="check-boxes d-flex flex-row flex-wrap justify-content-start"
                buttons
                placeholder="Farbe des Tags"
                data-test="farbe"
                :state="colorState"
                button-variant="secondary rounded-0 mt-1 mr-1 px-4"
                required
            >
                <b-form-radio
                    v-for="htmlColor in availableColors"
                    :key="htmlColor.name"
                    :value="htmlColor.name"
                    :style="`background-color: ${ htmlColor.name }`"
                >
                    <div
                        style="height:inherit; background: inherit; -webkit-background-clip: text; background-clip: text; color: transparent; text-align: center; filter: invert(1) grayscale(1) contrast(999);"
                    >
                        {{ htmlColor.name }}
                    </div>
                </b-form-radio>
            </b-form-radio-group>
        </b-form-group>
        <b-form-group
            v-if="isSuperAdmin"
            content-cols="12"
            label-cols="12"
            content-cols-lg="8"
            label-cols-lg="2"
        >
            <template slot="label" slot-scope="{ }">
                Klient
            </template>
            <b-form-select
                v-model="client"
                data-test="client"
                placeholder="Für welchen Klienten?"
                :options="availableClients"
                value-field="@id"
                text-field="name"
            />
        </b-form-group>
        <div :id="createButtonId">
            <b-button
                type="submit"
                variant="secondary"
                data-test="button-tag-create"
                block
                class="col-12"
                :tabindex="isFormInvalid ? '-1' : ''"
            >
                Neuen Tag erstellen
            </b-button>
        </div>
        <b-popover
            v-if="isFormInvalid"
            :target="createButtonId"
            triggers="hover"
            placement="top"
        >
            <template #title>Name und Farbe</template>
            Bitte erst Name und Farbe wählen bevor ein neuer Tag erstellt werden kann.
        </b-popover>
        <form-error
            :error="error"
        />
    </b-form>
</template>

<script>
'use strict';
import ColorBadge from './ColorBadge.vue';
import { html } from 'color_library';
import FormError from '../Common/FormError.vue';

export default {
    name: 'TagCreate',
    components: {
        FormError,
        ColorBadge,
    },
    data: function () {
        return {
            name: null,
            color: null,
            client: null,
            createButtonId: 'tag-create-submit',
        };
    },
    computed: {
        colors() {
            return this.$store.getters['tag/tags'].map(tag => tag.color);
        },
        names() {
            return this.$store.getters['tag/tags'].map(tag => tag.name);
        },
        colorState() {
            if (null === this.color) {
                return;
            }

            return -1 === this.colors.indexOf(this.color);
        },
        nameState() {
            if (null === this.name) {
                return;
            }

            return -1 === this.names.indexOf(this.name);
        },
        isLoading() {
            return this.$store.getters['tag/isLoading'];
        },
        currentUser() {
            return this.$store.getters['security/currentUser'];
        },
        isSuperAdmin() {
            return this.$store.getters['security/isSuperAdmin'];
        },
        isFormInvalid() {
            return !this.name || !this.color || !this.colorState || !this.nameState || this.isLoading;
        },
        error() {
            return this.$store.getters['tag/createTagError'];
        },
        availableClients() {
          return this.$store.getters['client/clients']  ;
        },
        availableColors() {
            return html.filter(htmlColor => (-1 === this.colors.indexOf(htmlColor.name)
                && !htmlColor.name.toLowerCase().includes('grey')
                && !htmlColor.name.toLowerCase().includes('gray')
                && !htmlColor.name.toLowerCase().includes('black')
                && !htmlColor.name.toLowerCase().includes('dodgerblue')
                && !htmlColor.name.toLowerCase().includes('white')
                && !htmlColor.name.toLowerCase().includes('oldlace')
                && !htmlColor.name.toLowerCase().includes('mitcream')
                && !htmlColor.name.toLowerCase().includes('lawngreen')
                && !htmlColor.name.toLowerCase().includes('greenyellow')
                && !htmlColor.name.toLowerCase().includes('red')
                && !htmlColor.name.toLowerCase().includes('darkblue')
                && !htmlColor.name.toLowerCase().includes('blanchedalmond')
                && !htmlColor.name.toLowerCase().includes('paleturquoise')
                && !htmlColor.name.toLowerCase().includes('peachpuff')
                && !htmlColor.name.toLowerCase().includes('moccasin')
                && !htmlColor.name.toLowerCase().includes('lightgoldenrodyellow')
                && !htmlColor.name.toLowerCase().includes('rebeccapurple')
                && !htmlColor.name.toLowerCase().includes('magenta')
                && !htmlColor.name.toLowerCase().includes('seashell')
                && !htmlColor.name.toLowerCase().includes('lightskyblue')
                && !htmlColor.name.toLowerCase().includes('darkgoldenrod')
                && !htmlColor.name.toLowerCase().includes('green')
                && !htmlColor.name.toLowerCase().includes('aliceblue')
                && !htmlColor.name.toLowerCase().includes('lightyellow')
                && !htmlColor.name.toLowerCase().includes('lightcyan')
                && !htmlColor.name.toLowerCase().includes('snow')
                && !htmlColor.name.toLowerCase().includes('cyan')
                && !htmlColor.name.toLowerCase().includes('lightblue')
                && !htmlColor.name.toLowerCase().includes('mincream')
                && !htmlColor.name.toLowerCase().includes('lightcoral')
                && !htmlColor.name.toLowerCase().includes('darkviolet')
                && !htmlColor.name.toLowerCase().includes('violet')
                && !htmlColor.name.toLowerCase().includes('mediumturquoise')
                && !htmlColor.name.toLowerCase().includes('mintcream')
                && !htmlColor.name.toLowerCase().includes('burlywood')
                && !htmlColor.name.toLowerCase().includes('cornsilk')
                && !htmlColor.name.toLowerCase().includes('honeydew')
            )).sort(function(a, b) {
                return 3 * a.rgb.r - 3 * b.rgb.r + 2 * a.rgb.g - 2 * b.rgb.g + a.rgb.b - b.rgb.b > 0 ? 1 : -1;
            });
        },
    },
    async created() {
        await this.$store.dispatch('client/findAll');
        this.client = this.currentUser.client;
    },
    methods: {
        async handleSubmit() {
            if (this.isFormInvalid) {
                this.$root.$emit('bv::show::popover', this.createButtonId)
                window.setTimeout(() => { this.$root.$emit('bv::hide::popover', this.createButtonId) }, 2000);
                return false;
            }
            let payload = {
                name: this.name,
                color: this.color,
                client: this.client,
            };

            const tag = await this.$store.dispatch('tag/create', payload);
            if (tag) {
                const message = `Der Tag ${tag.name} (${tag.color}) wurde erfolgreich erstellt.`;
                this.$bvToast.toast(message, {
                    title: 'Tag erstellt',
                    toaster: 'b-toaster-top-right',
                    autoHideDelay: 10000,
                    appendToast: true,
                });

                this.$store.dispatch('tag/findAll');

                this.name = null;
                this.color = null;
            } else {
                this.$bvToast.toast('Upps! :-(', {
                    title: 'Tag erstellen fehlgeschlagen',
                    toaster: 'b-toaster-top-right',
                    autoHideDelay: 10000,
                    variant: 'danger',
                    appendToast: true,
                });
            }
        },
    },
};
</script>

<style scoped lang="scss">
</style>
