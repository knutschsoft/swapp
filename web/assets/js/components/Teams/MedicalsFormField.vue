<template>
    <v-card outlined class="mb-0 pt-3">
        <v-card-text>
            <v-switch
                v-model="team.isWithMedicals"
                :disabled="isDisabled"
                color="primary"
                label="Medizin"
                density="compact"
            />
            <v-card-subtitle v-if="team.isWithMedicals" class="font-weight-bold pb-1">
                Medizin definieren
            </v-card-subtitle>
            <v-divider v-if="team.isWithMedicals" class="mt-0 mb-0"></v-divider>
            <v-list v-if="team.isWithMedicals">
                <v-list-item
                    v-for="(medicalName, i) in team.medicalNames"
                    density="compact"
                    :key="i"
                >
                    <v-text-field
                        v-model="team.medicalNames[i].name"
                        :disabled="isDisabled"
                        type="text"
                        :state="team.medicalNames[i].name === '' ? null : (team.medicalNames[i].name.length > 1 && team.medicalNames[i].name.length <= 300)"
                        trim
                        required
                        variant="outlined"
                        density="compact"
                        clearable
                        hide-details
                        autocomplete="off"
                        placeholder="Name des Medizinischen eingeben..."
                    />
                    <template v-slot:append>
                        <v-col>
                            <v-btn density="compact" icon @click="removeMedicalName(i)">
                                <v-icon icon="mdi-trash-can" />
                            </v-btn>
                            <v-btn v-if="i !== 0" density="compact" icon class="ml-2" @click="moveMedicalUp(i)">
                                <v-icon icon="mdi-arrow-up-drop-circle-outline" />
                            </v-btn>
                            <v-btn v-if="i !== (team.medicalNames.length - 1)" density="compact" icon class="ml-2" @click="moveMedicalDown(i)">
                                <v-icon icon="mdi-arrow-down-drop-circle-outline" />
                            </v-btn>
                        </v-col>
                    </template>
                </v-list-item>
                <v-list-item>
                    <div class="cursor-pointer mt-1 mb-2" @click="addMedicalName()">
                        <mdicon name="PlusCircleOutline" />
                        neuen Autocomplete-Vorschlag hinzufügen
                    </div>
                </v-list-item>
            </v-list>
            <v-alert v-if="team.isWithMedicals" class="text-muted mb-0" variant="text">
                Beispiele für Medizinisches sind:
                <ul class="pl-5 mb-0">
                    <li>Verband/Verbandswechsel</li>
                    <li>Sichtung/Präventionsberatung</li>
                    <li>Medizinische Einrichtung begleitet</li>
                    <li>Notfallversorgung</li>
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

const addMedicalName = () => {
    props.team.medicalNames.push({ name: "" });
};

const removeMedicalName = (index) => {
    props.team.medicalNames.splice(index, 1);
};

const moveMedicalUp = (index) => {
    if (index > 0) {
        const temp = props.team.medicalNames[index];
        props.team.medicalNames.splice(index, 1);
        props.team.medicalNames.splice(index - 1, 0, temp);
    }
};

const moveMedicalDown = (index) => {
    if (index < props.team.medicalNames.length - 1) {
        const temp = props.team.medicalNames[index];
        props.team.medicalNames.splice(index, 1);
        props.team.medicalNames.splice(index + 1, 0, temp);
    }
};
</script>
