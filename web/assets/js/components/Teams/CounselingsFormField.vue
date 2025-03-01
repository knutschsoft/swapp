<template>
    <v-card outlined class="mb-0 pt-3">
        <v-card-text>
            <v-switch
                v-model="team.isWithCounselings"
                :disabled="isDisabled"
                color="primary"
                label="Beratungen"
                density="compact"
            />
            <v-card-subtitle v-if="team.isWithCounselings" class="font-weight-bold pb-1">
                Beratungen definieren
            </v-card-subtitle>
            <v-divider v-if="team.isWithCounselings" class="mt-0 mb-0"></v-divider>
            <v-list v-if="team.isWithCounselings">
                <v-list-item
                    v-for="(counselingName, i) in team.counselingNames"
                    density="compact"
                    :key="i"
                >
                    <v-text-field
                        v-model="team.counselingNames[i].name"
                        :disabled="isDisabled"
                        type="text"
                        :state="team.counselingNames[i].name === '' ? null : (team.counselingNames[i].name.length > 1 && team.counselingNames[i].name.length <= 300)"
                        trim
                        required
                        variant="outlined"
                        density="compact"
                        clearable
                        hide-details
                        autocomplete="off"
                        placeholder="Name der Beratung eingeben..."
                    />
                    <template v-slot:append>
                        <v-col>
                            <v-btn density="compact" icon @click="removeCounselingName(i)">
                                <v-icon icon="mdi-trash-can" />
                            </v-btn>
                            <v-btn v-if="i !== 0" density="compact" icon class="ml-2" @click="moveCounselingUp(i)">
                                <v-icon icon="mdi-arrow-up-drop-circle-outline" />
                            </v-btn>
                            <v-btn v-if="i !== (team.counselingNames.length - 1)" density="compact" icon class="ml-2" @click="moveCounselingDown(i)">
                                <v-icon icon="mdi-arrow-down-drop-circle-outline" />
                            </v-btn>
                        </v-col>
                    </template>
                </v-list-item>
                <v-list-item>
                    <div class="cursor-pointer mt-1 mb-2" @click="addCounselingName()">
                        <mdicon name="PlusCircleOutline" />
                        neuen Autocomplete-Vorschlag hinzufügen
                    </div>
                </v-list-item>
            </v-list>
            <v-alert v-if="team.isWithCounselings" class="text-muted mb-0" variant="text">
                Beispiele für Beratungen sind:
                <ul class="pl-5 mb-0">
                    <li>Klassische Beratung</li>
                    <li>Krisenintervention</li>
                    <li>Kurzberatung</li>
                    <li>Vernetzende Beratung</li>
                    <li>Begleitung</li>
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

const addCounselingName = () => {
    props.team.counselingNames.push({ name: "" });
};

const removeCounselingName = (index) => {
    props.team.counselingNames.splice(index, 1);
};

const moveCounselingUp = (index) => {
    if (index > 0) {
        const temp = props.team.counselingNames[index];
        props.team.counselingNames.splice(index, 1);
        props.team.counselingNames.splice(index - 1, 0, temp);
    }
};

const moveCounselingDown = (index) => {
    if (index < props.team.counselingNames.length - 1) {
        const temp = props.team.counselingNames[index];
        props.team.counselingNames.splice(index, 1);
        props.team.counselingNames.splice(index + 1, 0, temp);
    }
};
</script>
