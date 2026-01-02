<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/js/stores/auth'
import { useUserPreferencesStore } from '@/js/stores/userPreferences'

type KeyLabel = { key: string; label: string }

const authStore = useAuthStore()
const prefsStore = useUserPreferencesStore()

// Load preferences if not already loaded
onMounted(() => {
    if (!prefsStore.isLoaded) {
        prefsStore.load()
    }
})

/**
 * Available options - these define ALL possible columns/filters.
 * Must match UserPreferencesSchema::defaults() in PHP.
 */
const wpFilters: KeyLabel[] = [
    { key: 'wayPointTags', label: 'Tags' },
    { key: 'note', label: 'Beobachtung' },
    { key: 'oneOnOneInterview', label: 'Einzelgespräch' },
    { key: 'locationName', label: 'Ort' },
    { key: 'teamName', label: 'Teamname' },
    { key: 'conceptOfDay', label: 'Tageskonzept' },
    { key: 'walkName', label: 'Runde' },
    { key: 'visitedAt', label: 'Ankunft' },
]

const wpColumns: KeyLabel[] = [
    { key: 'locationName', label: 'Ort' },
    { key: 'malesCount', label: 'Männer' },
    { key: 'femalesCount', label: 'Frauen' },
    { key: 'queerCount', label: 'Andere' },
    { key: 'peopleCount', label: 'Anzahl Personen' },
    { key: 'note', label: 'Beobachtung' },
    { key: 'oneOnOneInterview', label: 'Einzelgespräch' },
    { key: 'wayPointTags', label: 'Tags' },
    { key: 'walkTeamName', label: 'Teamname' },
    { key: 'visitedAt', label: 'Ankunft' },
    { key: 'walkName', label: 'Runde' },
    { key: 'actions', label: 'Aktionen' },
]

const walkFilters: KeyLabel[] = [
    { key: 'isResubmission', label: 'Wiedervorlage' },
    { key: 'isUnfinished', label: 'Beendet' },
    { key: 'name', label: 'Name' },
    { key: 'teamName', label: 'Teamname' },
    { key: 'guestNames', label: 'Gäste' },
    { key: 'startTime', label: 'Rundenbeginn' },
]

const walkColumns: KeyLabel[] = [
    { key: 'name', label: 'Name' },
    { key: 'teamName', label: 'Teamname' },
    { key: 'startTime', label: 'Start' },
    { key: 'endTime', label: 'Ende' },
    { key: 'walkCreator', label: 'Ersteller' },
    { key: 'rating', label: 'Bewertung' },
    { key: 'systemicQuestion', label: 'Systemische Frage' },
    { key: 'systemicAnswer', label: 'Systemische Antwort' },
    { key: 'commitments', label: 'Vereinbarungen' },
    { key: 'insights', label: 'Erkenntnisse' },
    { key: 'isResubmission', label: 'Wiedervorlage' },
    { key: 'weather', label: 'Wetter' },
    { key: 'conceptOfDay', label: 'Tageskonzept' },
    { key: 'holidays', label: 'Feiertage' },
    { key: 'guestNames', label: 'Gäste' },
    { key: 'walkTeamMembers', label: 'Team-Mitglieder' },
    { key: 'isUnfinished', label: 'Unbeendet' },
    { key: 'actions', label: 'Aktionen' },
]

const tab = ref<'walks' | 'wayPoints'>('wayPoints')
const saving = ref(false)
const saveMessage = ref('')
const showSaveMessage = ref(false)

/**
 * Get effective preferences (merged with defaults).
 */
const effectivePreferences = computed(() => prefsStore.effective)

/**
 * Computed arrays for v-chip-group (selected keys).
 * v-chip-group expects an array of selected keys.
 */
const waypointVisibleFilters = computed({
    get: () => {
        const filters = effectivePreferences.value.tables.wayPoints.filters
        return Object.entries(filters)
            .filter(([, visible]) => visible)
            .map(([key]) => key)
    },
    set: async (selected: string[]) => {
        const currentFilters = effectivePreferences.value.tables.wayPoints.filters
        const changes: Record<string, boolean> = {}

        // Nur Änderungen finden (PATCH-Semantik)
        wpFilters.forEach(f => {
            const isNowSelected = selected.includes(f.key)
            const wasSelected = currentFilters[f.key] ?? true

            if (isNowSelected !== wasSelected) {
                changes[f.key] = isNowSelected
            }
        })

        if (Object.keys(changes).length === 0) {
            return // Keine Änderungen
        }

        await savePreferences(
            { tables: { wayPoints: { filters: changes } } },
            'Filter (Wegpunkte) aktualisiert'
        )
    }
})

const waypointVisibleColumns = computed({
    get: () => {
        const columns = effectivePreferences.value.tables.wayPoints.columns
        return Object.entries(columns)
            .filter(([, visible]) => visible)
            .map(([key]) => key)
    },
    set: async (selected: string[]) => {
        const currentColumns = effectivePreferences.value.tables.wayPoints.columns
        const changes: Record<string, boolean> = {}

        // Nur Änderungen finden (PATCH-Semantik)
        wpColumns.forEach(c => {
            const isNowSelected = selected.includes(c.key)
            const wasSelected = currentColumns[c.key] ?? true

            if (isNowSelected !== wasSelected) {
                changes[c.key] = isNowSelected
            }
        })

        if (Object.keys(changes).length === 0) {
            return // Keine Änderungen
        }

        await savePreferences(
            { tables: { wayPoints: { columns: changes } } },
            'Spalten (Wegpunkte) aktualisiert'
        )
    }
})

const walkVisibleFilters = computed({
    get: () => {
        const filters = effectivePreferences.value.tables.walks.filters
        return Object.entries(filters)
            .filter(([, visible]) => visible)
            .map(([key]) => key)
    },
    set: async (selected: string[]) => {
        const currentFilters = effectivePreferences.value.tables.walks.filters
        const changes: Record<string, boolean> = {}

        // Nur Änderungen finden (PATCH-Semantik)
        walkFilters.forEach(f => {
            const isNowSelected = selected.includes(f.key)
            const wasSelected = currentFilters[f.key] ?? true

            if (isNowSelected !== wasSelected) {
                changes[f.key] = isNowSelected
            }
        })

        if (Object.keys(changes).length === 0) {
            return // Keine Änderungen
        }

        await savePreferences(
            { tables: { walks: { filters: changes } } },
            'Filter (Runden) aktualisiert'
        )
    }
})

const walkVisibleColumns = computed({
    get: () => {
        const columns = effectivePreferences.value.tables.walks.columns
        return Object.entries(columns)
            .filter(([, visible]) => visible)
            .map(([key]) => key)
    },
    set: async (selected: string[]) => {
        const currentColumns = effectivePreferences.value.tables.walks.columns
        const changes: Record<string, boolean> = {}

        // Nur Änderungen finden (PATCH-Semantik)
        walkColumns.forEach(c => {
            const isNowSelected = selected.includes(c.key)
            const wasSelected = currentColumns[c.key] ?? true

            if (isNowSelected !== wasSelected) {
                changes[c.key] = isNowSelected
            }
        })

        if (Object.keys(changes).length === 0) {
            return // Keine Änderungen
        }

        await savePreferences(
            { tables: { walks: { columns: changes } } },
            'Spalten (Runden) aktualisiert'
        )
    }
})

/**
 * Save preferences with PATCH semantics.
 */
async function savePreferences(partial: any, msg = 'Einstellungen gespeichert') {
    saving.value = true
    try {
        await prefsStore.save(partial)
        saveMessage.value = msg
        showSaveMessage.value = true
    } catch (error) {
        console.error('Failed to save preferences:', error)
        saveMessage.value = 'Fehler beim Speichern'
        showSaveMessage.value = true
    } finally {
        saving.value = false
    }
}

/**
 * Select/deselect all for a given section.
 */
async function handleAll(table: 'wayPoints' | 'walks', type: 'filters' | 'columns') {
    const items = table === 'wayPoints'
        ? (type === 'filters' ? wpFilters : wpColumns)
        : (type === 'filters' ? walkFilters : walkColumns)

    const allSelected = items.reduce((acc, item) => {
        acc[item.key] = true
        return acc
    }, {} as Record<string, boolean>)

    const label = table === 'wayPoints' ? 'Wegpunkte' : 'Runden'
    const typeLabel = type === 'filters' ? 'Filter' : 'Spalten'

    await savePreferences(
        { tables: { [table]: { [type]: allSelected } } },
        `Alle ${typeLabel} (${label}) aktiviert`
    )
}

async function handleNone(table: 'wayPoints' | 'walks', type: 'filters' | 'columns') {
    const items = table === 'wayPoints'
        ? (type === 'filters' ? wpFilters : wpColumns)
        : (type === 'filters' ? walkFilters : walkColumns)

    const noneSelected = items.reduce((acc, item) => {
        acc[item.key] = false
        return acc
    }, {} as Record<string, boolean>)

    const label = table === 'wayPoints' ? 'Wegpunkte' : 'Runden'
    const typeLabel = type === 'filters' ? 'Filter' : 'Spalten'

    await savePreferences(
        { tables: { [table]: { [type]: noneSelected } } },
        `Alle ${typeLabel} (${label}) deaktiviert`
    )
}

function allSelected(table: 'wayPoints' | 'walks', type: 'filters' | 'columns'): boolean {
    const prefs = effectivePreferences.value.tables[table][type]
    const items = table === 'wayPoints'
        ? (type === 'filters' ? wpFilters : wpColumns)
        : (type === 'filters' ? walkFilters : walkColumns)

    return items.every(item => prefs[item.key] === true)
}

function noneSelected(table: 'wayPoints' | 'walks', type: 'filters' | 'columns'): boolean {
    const prefs = effectivePreferences.value.tables[table][type]
    const items = table === 'wayPoints'
        ? (type === 'filters' ? wpFilters : wpColumns)
        : (type === 'filters' ? walkFilters : walkColumns)

    return items.every(item => prefs[item.key] === false)
}
</script>

<template>
    <v-card class="pa-0" elevation="1">
        <!-- Header -->
        <v-toolbar density="comfortable" flat>
            <v-toolbar-title>Einstellungen für Listen</v-toolbar-title>
            <v-spacer />
        </v-toolbar>

        <v-alert type="info" variant="tonal" class="mx-4 mt-3 mb-0">
            Du kannst hier steuern, <strong>welche Filter</strong> und <strong>welche Spalten</strong> in deinen Listen sichtbar sind.
            Änderungen werden <strong>sofort gespeichert</strong>, wenn du auswählst/abwählst.
        </v-alert>

        <!-- Tabs -->
        <v-tabs v-model="tab" class="mt-2 px-4" density="comfortable">
            <v-tab value="walks" prepend-icon="mdi-walk">Rundenliste</v-tab>
            <v-tab value="wayPoints" prepend-icon="mdi-map-marker-path">Wegpunkt-Liste</v-tab>
        </v-tabs>

        <v-divider class="mb-2" />

        <v-window v-model="tab">
            <!-- Wegpunkt-Liste -->
            <v-window-item value="wayPoints">
                <v-container fluid class="pt-2 pb-6">
                    <!-- Sektion: Filter -->
                    <v-card class="mx-4 my-3" elevation="0" variant="outlined">
                        <v-card-title class="text-subtitle-1">
                            Filtermöglichkeiten
                            <v-spacer />
                            <v-btn
                                size="small"
                                :color="allSelected('wayPoints', 'filters') ? 'primary' : undefined"
                                :variant="allSelected('wayPoints', 'filters') ? 'flat' : 'text'"
                                @click="handleAll('wayPoints', 'filters')"
                                :loading="saving"
                            >
                                Alle
                            </v-btn>
                            <v-btn
                                size="small"
                                :color="noneSelected('wayPoints', 'filters') ? 'primary' : undefined"
                                :variant="noneSelected('wayPoints', 'filters') ? 'flat' : 'text'"
                                @click="handleNone('wayPoints', 'filters')"
                                :loading="saving"
                            >
                                Keine
                            </v-btn>
                        </v-card-title>
                        <v-card-subtitle class="mb-1">
                            Wähle aus, welche Filter oberhalb der Wegpunkt-Tabelle angezeigt werden.
                        </v-card-subtitle>
                        <v-card-text>
                            <v-chip-group
                                v-model="waypointVisibleFilters"
                                multiple column class="mb-1"
                            >
                                <v-chip
                                    v-for="f in wpFilters"
                                    :key="f.key"
                                    :value="f.key"
                                    variant="outlined"
                                    color="primary"
                                    class="mr-2 mb-2"
                                    filter
                                    density="comfortable"
                                >
                                    {{ f.label }}
                                </v-chip>
                            </v-chip-group>
                        </v-card-text>
                    </v-card>

                    <!-- Sektion: Spalten -->
                    <v-card class="mx-4 my-3" elevation="0" variant="outlined">
                        <v-card-title class="text-subtitle-1">
                            Tabellenspalten
                            <v-spacer />
                            <v-btn
                                size="small"
                                :color="allSelected('wayPoints', 'columns') ? 'primary' : undefined"
                                :variant="allSelected('wayPoints', 'columns') ? 'flat' : 'text'"
                                @click="handleAll('wayPoints', 'columns')"
                                :loading="saving"
                            >
                                Alle
                            </v-btn>
                            <v-btn
                                size="small"
                                :color="noneSelected('wayPoints', 'columns') ? 'primary' : undefined"
                                :variant="noneSelected('wayPoints', 'columns') ? 'flat' : 'text'"
                                @click="handleNone('wayPoints', 'columns')"
                                :loading="saving"
                            >
                                Keine
                            </v-btn>
                        </v-card-title>
                        <v-card-subtitle class="mb-1">
                            Bestimme, welche Spalten in der Wegpunkt-Tabelle sichtbar sind.
                        </v-card-subtitle>
                        <v-card-text>
                            <v-chip-group
                                v-model="waypointVisibleColumns"
                                multiple column class="mb-1"
                            >
                                <v-chip
                                    v-for="c in wpColumns"
                                    :key="c.key"
                                    :value="c.key"
                                    variant="outlined"
                                    color="primary"
                                    class="mr-2 mb-2"
                                    filter
                                    density="comfortable"
                                >
                                    {{ c.label }}
                                </v-chip>
                            </v-chip-group>
                        </v-card-text>
                    </v-card>
                </v-container>
            </v-window-item>

            <!-- Rundenliste -->
            <v-window-item value="walks">
                <v-container fluid class="pt-2 pb-6">
                    <!-- Sektion: Filter -->
                    <v-card class="mx-4 my-3" elevation="0" variant="outlined">
                        <v-card-title class="text-subtitle-1">
                            Filtermöglichkeiten
                            <v-spacer />
                            <v-btn
                                size="small"
                                :color="allSelected('walks', 'filters') ? 'primary' : undefined"
                                :variant="allSelected('walks', 'filters') ? 'flat' : 'text'"
                                @click="handleAll('walks', 'filters')"
                                :loading="saving"
                            >
                                Alle
                            </v-btn>
                            <v-btn
                                size="small"
                                :color="noneSelected('walks', 'filters') ? 'primary' : undefined"
                                :variant="noneSelected('walks', 'filters') ? 'flat' : 'text'"
                                @click="handleNone('walks', 'filters')"
                                :loading="saving"
                            >
                                Keine
                            </v-btn>
                        </v-card-title>
                        <v-card-subtitle class="mb-1">
                            Wähle aus, welche Filter oberhalb der Rundenliste angezeigt werden.
                        </v-card-subtitle>
                        <v-card-text>
                            <v-chip-group
                                v-model="walkVisibleFilters"
                                multiple column class="mb-1"
                            >
                                <v-chip
                                    v-for="f in walkFilters"
                                    :key="f.key"
                                    :value="f.key"
                                    variant="outlined"
                                    color="primary"
                                    class="mr-2 mb-2"
                                    filter
                                    density="comfortable"
                                >
                                    {{ f.label }}
                                </v-chip>
                            </v-chip-group>
                        </v-card-text>
                    </v-card>

                    <!-- Sektion: Spalten -->
                    <v-card class="mx-4 my-3" elevation="0" variant="outlined">
                        <v-card-title class="text-subtitle-1">
                            Tabellenspalten
                            <v-spacer />
                            <v-btn
                                size="small"
                                :color="allSelected('walks', 'columns') ? 'primary' : undefined"
                                :variant="allSelected('walks', 'columns') ? 'flat' : 'text'"
                                @click="handleAll('walks', 'columns')"
                                :loading="saving"
                            >
                                Alle
                            </v-btn>
                            <v-btn
                                size="small"
                                :color="noneSelected('walks', 'columns') ? 'primary' : undefined"
                                :variant="noneSelected('walks', 'columns') ? 'flat' : 'text'"
                                @click="handleNone('walks', 'columns')"
                                :loading="saving"
                            >
                                Keine
                            </v-btn>
                        </v-card-title>
                        <v-card-subtitle class="mb-1">
                            Bestimme, welche Spalten in der Rundenliste sichtbar sind.
                        </v-card-subtitle>
                        <v-card-text>
                            <v-chip-group
                                v-model="walkVisibleColumns"
                                multiple column class="mb-1"
                            >
                                <v-chip
                                    v-for="c in walkColumns"
                                    :key="c.key"
                                    :value="c.key"
                                    variant="outlined"
                                    color="primary"
                                    class="mr-2 mb-2"
                                    filter
                                    density="comfortable"
                                >
                                    {{ c.label }}
                                </v-chip>
                            </v-chip-group>
                        </v-card-text>
                    </v-card>
                </v-container>
            </v-window-item>
        </v-window>

        <!-- dezentes Feedback -->
        <v-snackbar v-model="showSaveMessage" timeout="1500" location="bottom" variant="flat">
            {{ saveMessage }}
        </v-snackbar>
    </v-card>
</template>

<style scoped>
/* dezente Überschrift-Gewichtung für Sektionen */
.text-subtitle-1 {
    font-weight: 600;
}
</style>
