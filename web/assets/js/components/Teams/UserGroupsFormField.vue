<template>
    <v-card outlined class="mb-0 pt-3">
        <v-card-text>
            <v-switch
                v-model="team.isWithUserGroups"
                :disabled="isDisabled"
                color="primary"
                label="Personenanzahl von Nutzergruppen"
                density="compact"
            />
            <v-card-subtitle v-if="team.isWithUserGroups" class="font-weight-bold pb-1">
                Nutzergruppen definieren
            </v-card-subtitle>
            <v-divider v-if="team.isWithUserGroups" class="mt-0 mb-0"></v-divider>
            <v-list v-if="team.isWithUserGroups">
                <v-list-item
                    v-for="(userGroupName, i) in team.userGroupNames"
                    density="compact"
                    :key="i"
                >
                    <v-text-field
                        v-model="team.userGroupNames[i].name"
                        :disabled="isDisabled"
                        type="text"
                        :state="team.userGroupNames[i].name === '' ? null : (team.userGroupNames[i].name.length > 1 && team.userGroupNames[i].name.length <= 300)"
                        trim
                        required
                        variant="outlined"
                        density="compact"
                        clearable
                        hide-details
                        autocomplete="off"
                        placeholder="Name der Nutzergruppe eingeben..."
                    />
                    <template v-slot:append>
                        <v-col>
                            <v-btn density="compact" icon @click="removeUserGroupName(i)">
                                <v-icon icon="mdi-trash-can" />
                            </v-btn>
                            <v-btn v-if="i !== 0" density="compact" icon class="ml-2" @click="moveUserGroupUp(i)">
                                <v-icon icon="mdi-arrow-up-drop-circle-outline" />
                            </v-btn>
                            <v-btn v-if="i !== (team.userGroupNames.length - 1)" density="compact" icon class="ml-2" @click="moveUserGroupDown(i)">
                                <v-icon icon="mdi-arrow-down-drop-circle-outline" />
                            </v-btn>
                        </v-col>
                    </template>
                </v-list-item>
                <v-list-item>
                    <div class="cursor-pointer mt-1 mb-2" @click="addUserGroupName()">
                        <mdicon name="PlusCircleOutline" />
                        neuen Autocomplete-Vorschlag hinzufügen
                    </div>
                </v-list-item>
            </v-list>
            <v-alert v-if="team.isWithUserGroups" class="text-muted mb-0" variant="text">
                Beispiele für Nutzergruppen sind:
                <ul class="pl-5 mb-0">
                    <li>Aktuell Nutzende</li>
                    <li>jemals genutzt - nutzungsberechtigt</li>
                    <li>jemals genutzt - nicht nutzungsberechtigt</li>
                    <li>nie genutzt - nutzungsberechtigt</li>
                    <li>nie genutzt - nicht nutzungsberechtigt</li>
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

const addUserGroupName = () => {
    props.team.userGroupNames.push({ name: "" });
};

const removeUserGroupName = (index) => {
    props.team.userGroupNames.splice(index, 1);
};

const moveUserGroupUp = (index) => {
    if (index > 0) {
        const temp = props.team.userGroupNames[index];
        props.team.userGroupNames.splice(index, 1);
        props.team.userGroupNames.splice(index - 1, 0, temp);
    }
};

const moveUserGroupDown = (index) => {
    if (index < props.team.userGroupNames.length - 1) {
        const temp = props.team.userGroupNames[index];
        props.team.userGroupNames.splice(index, 1);
        props.team.userGroupNames.splice(index + 1, 0, temp);
    }
};
</script>
