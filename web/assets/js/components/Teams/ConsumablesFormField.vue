<template>
    <v-card outlined class="mb-0 pt-3">
        <v-card-text>
            <v-switch
                v-model="team.isWithConsumables"
                :disabled="isDisabled"
                color="primary"
                label="Ausgabematerialien"
                density="compact"
            />
            <v-card-subtitle v-if="team.isWithConsumables" class="font-weight-bold pb-1">
                Ausgabematerialien definieren
            </v-card-subtitle>
            <v-divider v-if="team.isWithConsumables" class="mt-0 mb-0"></v-divider>
            <v-list v-if="team.isWithConsumables">
                <v-list-item
                    v-for="(consumableName, i) in team.consumableNames"
                    density="compact"
                    :key="i"
                >
                    <v-text-field
                        v-model="team.consumableNames[i].name"
                        :disabled="isDisabled"
                        type="text"
                        :state="team.consumableNames[i].name === '' ? null : (team.consumableNames[i].name.length > 1 && team.consumableNames[i].name.length <= 300)"
                        trim
                        required
                        variant="outlined"
                        density="compact"
                        clearable
                        hide-details
                        autocomplete="off"
                        placeholder="Name des Ausgabematerials eingeben..."
                    />
                    <template v-slot:append>
                        <v-col>
                            <v-btn density="compact" icon @click="removeConsumableName(i)">
                                <v-icon icon="mdi-trash-can" />
                            </v-btn>
                            <v-btn v-if="i !== 0" density="compact" icon class="ml-2" @click="moveConsumableUp(i)">
                                <v-icon icon="mdi-arrow-up-drop-circle-outline" />
                            </v-btn>
                            <v-btn v-if="i !== (team.consumableNames.length - 1)" density="compact" icon class="ml-2" @click="moveConsumableDown(i)">
                                <v-icon icon="mdi-arrow-down-drop-circle-outline" />
                            </v-btn>
                        </v-col>
                    </template>
                </v-list-item>
                <v-list-item>
                    <div class="cursor-pointer mt-1 mb-2" @click="addConsumableName()">
                        <mdicon name="PlusCircleOutline" />
                        neuen Autocomplete-Vorschlag hinzufügen
                    </div>
                </v-list-item>
            </v-list>
            <v-alert v-if="team.isWithConsumables" class="text-muted mb-0" variant="text">
                Beispiele für herausgegebene Materialien sind:
                <ul class="pl-5 mb-0">
                    <li>Carepakete</li>
                    <li>Nase</li>
                    <li>Folie</li>
                    <li>Sonstiges</li>
                    <li>Infos</li>
                    <li>Spritzenvergabe</li>
                    <li>Alkoholtupfer</li>
                    <li>Filteraufsätze</li>
                    <li>sterile Wasserampullen</li>
                    <li>Einmallöffel</li>
                    <li>Kondome</li>
                </ul>
                Hinweis: Die Werte werden beim Runden-CSV-Export zusammenaddiert.
            </v-alert>
        </v-card-text>
    </v-card>
</template>

<script setup>
import { defineProps } from "vue";

const props = defineProps({
    team: Object,
    isDisabled: Boolean
});

const addConsumableName = () => {
    props.team.consumableNames.push({ name: "" });
};

const removeConsumableName = (index) => {
    props.team.consumableNames.splice(index, 1);
};

const moveConsumableUp = (index) => {
    if (index > 0) {
        const temp = props.team.consumableNames[index];
        props.team.consumableNames.splice(index, 1);
        props.team.consumableNames.splice(index - 1, 0, temp);
    }
};

const moveConsumableDown = (index) => {
    if (index < props.team.consumableNames.length - 1) {
        const temp = props.team.consumableNames[index];
        props.team.consumableNames.splice(index, 1);
        props.team.consumableNames.splice(index + 1, 0, temp);
    }
};
</script>
