<template>
    <div class="px-2 pt-2">
        <v-row dense class="">
            <v-col
                v-if="effectiveWalkTableFiltersPreferences['isResubmission']"
                cols="12"
                sm="6"
                md="6"
                lg="4"
                xl="2"
            >
                <filter-boolean-field
                    v-model="filter.isResubmission"
                    label="Wiedervorlage zur Dienstberatung?"
                    :is-loading="isLoading"
                    @cleared="filter.isResubmission = 'null'"
                />
            </v-col>
            <v-col
                v-if="effectiveWalkTableFiltersPreferences['isUnfinished']"
                cols="12"
                sm="6"
                md="6"
                lg="4"
                xl="2"
            >
                <filter-boolean-field
                    v-model="filter.isUnfinished"
                    label="Beendet?"
                    :is-loading="isLoading"
                    @cleared="filter.isUnfinished = 'null'"
                />
            </v-col>
            <v-col
                v-if="effectiveWalkTableFiltersPreferences['name']"
                cols="12"
                sm="6"
                md="6"
                lg="4"
                xl="2"
                no-gutters
            >
                <filter-combobox-field
                    v-model="filter.name"
                    label="Rundenname"
                    data-test="filter-name-walk"
                    :is-loading="isLoading"
                    :suggestions="walkNames"
                    :hide-no-data="true"
                />
            </v-col>
            <v-col
                v-if="effectiveWalkTableFiltersPreferences['conceptOfDay']"
                cols="12"
                sm="6"
                md="6"
                lg="4"
                xl="2"
                no-gutters
            >
                <filter-combobox-field
                    v-model="filter.conceptOfDay"
                    label="Tageskonzept"
                    data-test="filter-conceptOfDay-walk"
                    :is-loading="isLoading"
                    :suggestions="conceptOfDaySuggestions"
                    :hide-no-data="true"
                />
            </v-col>
            <v-col
                v-if="effectiveWalkTableFiltersPreferences['teamName']"
                cols="12"
                sm="6"
                md="6"
                lg="4"
                xl="2"
            >
                <filter-combobox-field
                    v-model="filter.teamName"
                    label="Team"
                    data-test="fil<ter-team-walk"
                    :is-loading="isLoading"
                    :suggestions="teamNames"
                    :hide-no-data="true"
                />
            </v-col>
            <v-col
                v-if="effectiveWalkTableFiltersPreferences['guestNames']"
                cols="12"
                sm="6"
                md="6"
                lg="4"
                xl="2"
            >
                <filter-combobox-field
                    v-model="filter.guestNames"
                    label="weitere Teilnehmende"
                    data-test="filter-team-guest-names"
                    :is-loading="isLoading"
                    :suggestions="guestNames"
                    :hide-no-data="true"
                />
            </v-col>
            <v-col
                v-if="effectiveWalkTableFiltersPreferences['startTime']"
                cols="12"
                sm="6"
                md="6"
                lg="4"
                xl="2"
            >
                <date-range-picker
                    v-model="filter.startTime"
                    :is-loading="isLoading"
                    data-test="start-time-filter"
                    placeholder="Rundenbeginn"
                    @cleared="unsetFilterStartTime"
                />
            </v-col>
            <v-col
                cols="12"
                class="my-1"
                xs="12"
                sm="12"
                md="12"
                xl="12"
            >
                <v-btn
                    color="secondary"
                    block
                    :disabled="(isLoading || isExportLoading || !hasFilter) && currentPage === 1"
                    data-test="reset-walk-filter"
                    class="text-transform-none"
                    density="comfortable"
                    :append-icon="hasFilter ? 'mdi-filter-remove-outline' : 'mdi-filter-outline'"
                    @click="unsetAllFilter"
                >
                    Alle Filter zurücksetzen
                </v-btn>
            </v-col>
            <v-col
                cols="12"
                xs="12"
                sm="12"
                md="12"
                xl="12"
            >
                <v-btn
                    color="secondary"
                    block
                    :disabled="isLoading || isExportLoading || totalItems === 0"
                    class="text-transform-none"
                    density="comfortable"
                    append-icon="mdi-download"
                    :loading="isExportLoading"
                    @click="exportWalks"
                >
                    {{ totalItems > 5000 ? 5000 : totalItems }} Rund{{ totalItems === 1 ? 'e' : 'en' }} als .csv-Datei exportieren
                </v-btn>
            </v-col>
        </v-row>
        <v-data-table-server
            class="mb-0"
            :items-per-page="itemsPerPage"
            :page="currentPage"
            :headers="headers"
            :items="serverItems"
            :items-length="totalItems"
            :items-per-page-options="itemsPerPageOptions"
            :items-per-page-text="itemsPerPageText"
            :loading="isLoading"
            item-value="@id"
            :no-data-text="noItemsText"
            :loading-text="loadingText"
            multi-sort
            :sort-by="sortBy"
            mobile-breakpoint="md"
            density="compact"
            show-current-page
            @update:options="handleOptionsUpdate"
            :no-results-text="noItemsText"
            @update:items-per-page="handlePerPageChange"
            @update:page="handleCurrentPageChange"
        >
            <template v-slot:item.startTime="{item}">
                {{ formatDateTimeNoSecondsWithDayOfWeek(item.startTime) }}
            </template>
            <template #item.conceptOfDay="{item}">
                {{ item.conceptOfDay.join(', ') }}
            </template>
            <template v-slot:item.rating="{item}">
                <walk-rating
                    v-if="!item.isUnfinished && getClientByIri(item.client)"
                    :rating="item.rating"
                    :client="getClientByIri(item.client)"
                    :item-size="30"
                    :show-rating="false"
                    read-only
                />
                <template v-else>-</template>
            </template>
            <template v-slot:item.endTime="{item}">
                <template v-if="item.isUnfinished">-</template>
                <template v-else> {{ formatEndDate(item.endTime, item.startTime) }}</template>
            </template>
            <template v-slot:item.isResubmission="{item}">
                {{ item.isResubmission ? 'ja' : 'nein' }}
            </template>
            <template v-slot:item.actions="{item}">
                <div class="d-flex flex-wrap align-center align-content-center justify-center">
                    <v-btn
                        :to="{name: 'WalkDetail', params: { walkId: item.walkId}}"
                        :data-test="`button-runde-ansehen-${ item.name }`"
                        color="secondary"
                        :disabled="isLoading"
                        density="compact"
                        class="my-1 text-transform-none"
                    >
                        Runde ansehen
                        <v-icon icon="mdi-walk" class="ml-1"></v-icon>
                        <v-icon icon="mdi-eye" class="ml-1"></v-icon>
                    </v-btn>
                    <v-btn
                        v-if="item.isUnfinished"
                        :to="{name: 'WalkAddWayPoint', params: { walkId: item.walkId}}"
                        density="compact"
                        color="secondary"
                        class="ml-1 my-1 text-transform-none"
                        :disabled="isLoading"
                    >
                        Runde fortsetzen
                        <v-icon icon="mdi-walk" class="ml-1"></v-icon>
                        <v-icon icon="mdi-shoe-print" class="ml-1"></v-icon>
                    </v-btn>
                </div>
            </template>
        </v-data-table-server>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted, defineEmits } from 'vue';
import dayjs from 'dayjs';
import WalkAPI from '../../api/walk.js';
import WalkRating from '../Walk/WalkRating.vue';
import {useClientStore, useGeneralStore, useTeamStore, useUserPreferencesStore} from '@/js/stores';
import { formatDateTimeNoSecondsWithDayOfWeek, formatTime, itemsPerPageOptions, itemsPerPageText, loadingText, noItemsText } from "@/js/utils";
import { FilterBooleanField, FilterComboboxField, FilterTextField, type SuggestionItem} from "@/js/components/Common";
import { DateRangePicker } from "@/js/components/Common";
import axios, {AxiosError} from "axios";
import {Team} from "@/js/model.ts";

const emits = defineEmits(['refresh-total-walks']);

const clientStore = useClientStore();
const generalStore = useGeneralStore();
const teamStore = useTeamStore();
const userPreferencesStore = useUserPreferencesStore();

const isLoading = ref(false);
const isExportLoading = ref(false);
let abortController: AbortController | null = null;
let exportCtx: any = null;

const allTeamNames = ref<any[]>([]);
const allWalkNames = ref<any[]>([]);
const allGuestNames = ref<any[]>([]);
const allConceptOfDaySuggestions = ref<Array<{ conceptOfDay?: Record<string, string> }>>(
    []
);

const totalItems = ref(0);
const isInitializing = ref(true);
const currentPage = ref(1);
const itemsPerPage = ref(itemsPerPageOptions[1].value);
const serverItems = ref<any[]>([]);
const tableOptions = ref<any>({});
const sortBy = ref([{ key: 'startTime', order: 'desc' }]);

const effectiveWalkTableFiltersPreferences = computed(() => userPreferencesStore.effective.tables.walks.filters)
const effectiveWalkTableColumnsPreferences = computed(() => userPreferencesStore.effective.tables.walks.columns)
const headers = computed(() =>
    [
        { value: 'name', title: 'Name', sortable: true, align: 'center' },
        { value: 'conceptOfDay', title: 'Tageskonzept', sortable: false, align: 'center' },
        { value: 'rating', title: 'Bewertung', sortable: true, align: 'center' },
        { value: 'startTime', title: 'Rundenbeginn', sortable: true },
        { value: 'endTime', title: 'Ende', sortable: false },
        { value: 'peopleCount', title: 'Anzahl Personen', sortable: false, align: 'center' },
        { value: 'teamName', title: 'Team', sortable: true, align: 'center' },
        { value: 'isResubmission', sortable: true, title: 'WV DB?' },
        { value: 'actions', title: 'Aktionen', align: 'center', sortable: false },
    ].filter(header => {
        if (header.value === 'actions') {
            return true
        }

        return effectiveWalkTableColumnsPreferences.value[header.value];
    })
)
const filter = computed(() => generalStore.getWalkFilter);
const defaultFilter = computed(() => generalStore.defaultWalkFilter);
const defaultDateRange = computed(() => generalStore.defaultWalkFilter.startTime);

const teams = computed<Team[]>(() => teamStore.getTeams)
const guestNames = computed<SuggestionItem[]>(() => {
    const teamGuestNames = Array.from(
        new Set(
            teams.value.flatMap(team => team.guestNames ?? [])
        )
    ).sort()

    const otherNames = allGuestNames.value.map(g => g.name)

    const filteredOtherNames = otherNames.filter(
        name => !teamGuestNames.includes(name)
    )

    const items: SuggestionItem[] = []

    if (teamGuestNames.length) {
        items.push(
            { type: 'divider', text: 'Teilnehmende aus allen Teams' },
            ...teamGuestNames.map(name => ({ type: 'item', title: name })),
            { type: 'divider', text: 'Weitere verwendete Teilnehmende' },
        )
    }

    items.push(
        ...filteredOtherNames.map(name => ({ type: 'item', title: name }))
    )

    return items
})
const teamNames = computed<SuggestionItem[]>(() => allTeamNames.value.map(t => ({
    type: 'item',
    title: t.teamName
})));
const walkNames = computed<SuggestionItem[]>(() => {
    const teamWalkNames = Array.from(
        new Set(
            teams.value.flatMap(team => team.walkNames ?? [])
        )
    ).sort()

    const otherWalkNames = allWalkNames.value.map(w => w.name)

    const filteredOtherWalkNames = otherWalkNames.filter(
        name => !teamWalkNames.includes(name)
    )

    const items: SuggestionItem[] = []

    if (teamWalkNames.length) {
        items.push(
            { type: 'divider', text: 'Rundennamen aus Teams' },
            ...teamWalkNames.map(name => ({ type: 'item', title: name })),
            { type: 'divider', text: 'Weitere verwendete Rundennamen' },
        )
    }

    items.push(
        ...filteredOtherWalkNames.map(name => ({ type: 'item', title: name }))
    )

    return items
})
const conceptOfDaySuggestions = computed<SuggestionItem[]>(() => {
    const teamConcepts = Array.from(
        new Set(
            teams.value.flatMap(team => team.conceptOfDaySuggestions ?? [])
        )
    ).sort((a, b) => a.localeCompare(b))

    const teamConceptSet = new Set(teamConcepts)

    const otherConcepts = Array.from(
        new Set(
            allConceptOfDaySuggestions.value.flatMap(walk =>
                Object.values(walk.conceptOfDay ?? {})
            )
        )
    )
        .filter(title => !teamConceptSet.has(title))
        .sort((a, b) => a.localeCompare(b))

    const items: SuggestionItem[] = []

    if (teamConcepts.length) {
        items.push(
            { type: 'divider', text: 'Tageskonzepte aus allen Teams' },
            ...teamConcepts.map(title => ({ type: 'item', title })),
            { type: 'divider', text: 'Weitere verwendete Tageskonzepte' }
        )
    }

    items.push(
        ...otherConcepts.map(title => ({ type: 'item', title }))
    )

    return items
})
const hasFilter = computed(() => JSON.stringify(filter.value) !== JSON.stringify(defaultFilter.value));

watch([filter, effectiveWalkTableFiltersPreferences], () => {
    if (isInitializing.value) return;

    currentPage.value = 1;
    generalStore.updateWalkCurrentPage(1);

    loadItems({
        page: currentPage.value,
        itemsPerPage: itemsPerPage.value,
        sortBy: sortBy.value,
    });
}, { deep: true });

onMounted(async () => {
    const allTeams = await WalkAPI.findAllTeamNames();
    allTeamNames.value = allTeams.data['member'];
    await teamStore.fetchTeams();
    const allGuests = await WalkAPI.findAllGuestNames();
    allGuestNames.value = allGuests.data['member'];
    const allNames = await WalkAPI.findAllWalkNames();
    allWalkNames.value = allNames.data['member'];
    const allConceptOfDaySuggestionsResult = await WalkAPI.findAllConceptOfDayWithWayPoints();
    allConceptOfDaySuggestions.value = allConceptOfDaySuggestionsResult.data['member'];
});

async function getClientByIri(clientIri: string) {
    if (!clientStore.getClientByIri(clientIri)) {
        await clientStore.fetchByIri(clientIri)
    }

    return clientStore.getClientByIri(clientIri);
}

function formatEndDate(dateString: string, startDateString: string) {
    const date = new Date(dateString);
    if (dayjs(dateString).isSame(dayjs(startDateString), 'day')) {
        return formatTime(date);
    }
    return formatDateTimeNoSecondsWithDayOfWeek(dateString);
}

async function loadItems(options: { page: number; itemsPerPage: number; sortBy: any[] }) {
    tableOptions.value = options;

    if (abortController) abortController.abort();
    abortController = new AbortController();
    const signal = abortController.signal;

    const data: any = {
        page: options.page,
        itemsPerPage: options.itemsPerPage,
    };
    const filterMapping: Record<string, keyof typeof filter.value> = {
        'name': 'name',
        'teamName': 'teamName',
        'guestNames': 'guestNames',
        'conceptOfDay': 'conceptOfDay',
    }
    for (const [requestKey, filterKey] of Object.entries(filterMapping)) {
        if (effectiveWalkTableFiltersPreferences.value[requestKey]) {
            data[requestKey] = filter.value[filterKey];
        }
    }
    if (effectiveWalkTableFiltersPreferences.value['isResubmission']) {
        data['isResubmission'] = filter.value.isResubmission !== 'null' ? filter.value.isResubmission : undefined;
    }
    if (effectiveWalkTableFiltersPreferences.value['isUnfinished']) {
        data['isUnfinished'] = filter.value.isUnfinished !== 'null' ? !filter.value.isUnfinished : undefined;
    }

    options.sortBy.forEach(val => {
        data[`order[${val.key}]`] = val.order;
    });

    if (effectiveWalkTableFiltersPreferences.value['startTime'] && filter.value.startTime && filter.value.startTime[0] && filter.value.startTime[1]) {
        data['startTime[after]'] = dayjs(filter.value.startTime[0]).startOf('day').toISOString();
        data['startTime[before]'] = dayjs(filter.value.startTime[1]).endOf('day').toISOString();
    }

    exportCtx = data;

    isLoading.value = true;
    let result = null;
    try {
        result = await WalkAPI.find(data, signal);
    } catch (error: unknown) {
        if (axios.isCancel(error)) {
            return;
        }

        if (error instanceof AxiosError) {
            console.error(error.code, error.message);
        }

        throw error;
    }

    isLoading.value = false;

    const items = result.data['member'];
    const total = result.data['totalItems'] ?? 0;
    generalStore.updateWalkFilterResult(items);
    serverItems.value = items;
    totalItems.value = total;
    emits('refresh-total-walks', totalItems.value);
}

function handleOptionsUpdate(options: { page: number; itemsPerPage: number; sortBy: any[] }) {
    if (isInitializing.value) return;
    loadItems(options);
}

function handleCurrentPageChange(value: number) {
    if (isInitializing.value) return;
    currentPage.value = Number(value);
    generalStore.updateWalkCurrentPage(Number(value));

    loadItems({
        page: value,
        itemsPerPage: itemsPerPage.value,
        sortBy: sortBy.value,
    });
}

function handlePerPageChange(value: number) {
    itemsPerPage.value = Number(value);
    generalStore.updateWalkPerPage(Number(value));

    loadItems({
        page: 1,
        itemsPerPage: value,
        sortBy: sortBy.value,
    });
}

function unsetFilterStartTime() {
    filter.value.startTime = defaultDateRange.value;
}

function unsetAllFilter() {
    generalStore.updateWalkFilter(defaultFilter.value);
    handleCurrentPageChange(1);
}

function forceFileDownload(response: any, title: string) {
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', title);
    document.body.appendChild(link);
    link.click();
}

async function exportWalks() {
    isExportLoading.value = true;
    const response = await WalkAPI.export(exportCtx);
    forceFileDownload(response, getFileName());
    isExportLoading.value = false;
}

function getFileName() {
    // Nur Filter im Dateinamen aufnehmen, die per UserPreferences sichtbar/aktiv sind -
    // das deckt sich mit dem, was loadItems an die API schickt. Sonst entsteht eine
    // irreführende Datei wie "TEAM_X_…csv", obwohl der Export gar nicht nach Team gefiltert wurde.
    let title = `streetworkrunden_export.csv`;
    const prefs = effectiveWalkTableFiltersPreferences.value;

    if (prefs['teamName'] && filter.value.teamName.length) title = `TEAM_${filter.value.teamName.join('_')}_${title}`;
    if (prefs['guestNames'] && filter.value.guestNames.length) title = `WEITERE_TEILNEHMENDE_${filter.value.guestNames.join('_')}_${title}`;
    if (prefs['isResubmission'] && filter.value.isResubmission !== 'null') title = `WV_DB_${filter.value.isResubmission ? 'ja' : 'nein'}_${title}`;
    if (prefs['isUnfinished'] && filter.value.isUnfinished !== 'null') title = `BEENDET_${!filter.value.isUnfinished ? 'nein' : 'ja'}_${title}`;
    if (prefs['name'] && filter.value.name.length) title = `NAME_${filter.value.name.join('_')}_${title}`;
    if (prefs['conceptOfDay'] && filter.value.conceptOfDay.length) title = `TAGESKONZEPT_${filter.value.conceptOfDay.join('_')}_${title}`;

    if (prefs['startTime']) {
        const startDate = dayjs(filter.value?.startTime?.[0]);
        const endDate = dayjs(filter.value?.startTime?.[1]);
        if (startDate.isValid() && endDate.isValid()) {
            const formattedStartDate = startDate.format('YYYYMMDD');
            const formattedEndDate = endDate.format('YYYYMMDD');
            if (formattedStartDate === formattedEndDate) title = `${formattedStartDate}_${title}`;
            else title = `${formattedStartDate}-${formattedEndDate}_${title}`;
        }
    }

    return title;
}
const load = async () => {
    itemsPerPage.value = generalStore.walkPerPage;
    currentPage.value = generalStore.walkCurrentPage;
    await userPreferencesStore.load();

    await loadItems({
        page: currentPage.value,
        itemsPerPage: itemsPerPage.value,
        sortBy: sortBy.value,
    });

    isInitializing.value = false;
}
load()
</script>

<style scoped>
</style>
