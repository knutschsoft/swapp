<template>
    <div class="px-2 pt-2">
        <v-row density="compact">
            <v-col v-if="tags.length" cols="12">
                <div class="d-flex flex-row align-items-center">
                    <div>Tags</div>
                    <v-btn
                        title="Filterung nach Tags entfernen"
                        class="ml-auto"
                        :color="filter.wayPointTags.length ? 'primary-lighten-2' : 'secondary-lighten-4'"
                        size="x-small"
                        @click="unsetFilterWayPointTags"
                        icon
                    >
                        <v-icon
                            color="white"
                            icon="mdi-filter-remove-outline"
                        />
                    </v-btn>
                    <v-menu v-if="hasDisabledTag" :close-on-content-click="false">
                        <template #activator="{ props }">
                            <v-btn class="ml-2" size="x-small" v-bind="props" aria-label="Weitere Tag-Optionen" icon="mdi-dots-vertical" />
                        </template>
                        <v-list dense>
                            <v-list-item>
                                <v-list-item-title>Optionen</v-list-item-title>
                            </v-list-item>
                            <v-list-item>
                                <v-switch
                                    v-model="filter.showDisabledTags"
                                    color="primary"
                                    label="Deaktivierte Tags anzeigen"
                                    hide-details
                                />
                            </v-list-item>
                                <v-expansion-panels flat density="compact" class="mt-1">
                                    <v-expansion-panel>
                                        <v-expansion-panel-title class="text-body-2">
                                            <v-icon size="16" class="mr-1 text-muted">mdi-information-outline</v-icon>
                                            Erklärung zu Tags
                                        </v-expansion-panel-title>
                                        <v-expansion-panel-text class="text-body-2 py-1 mx-4">
                                            <ul class="pl-5 mb-1">
                                                <li>Aktivierte Tags, die mindestens einer Runde zugeordnet sind, werden <strong>immer</strong> angezeigt.</li>
                                                <li>Deaktivierte Tags werden nur angezeigt, wenn die Option oben aktiviert ist.</li>
                                            </ul>
                                        </v-expansion-panel-text>
                                    </v-expansion-panel>
                                </v-expansion-panels>
                        </v-list>
                    </v-menu>
                </div>
                <v-chip-group
                    v-model="filter.wayPointTags"
                    multiple
                    column
                >
                    <template
                        v-for="tag in tags"
                        :key="tag['id']"
                    >
                        <v-chip
                            v-if="tag.isEnabled"
                            :value="tag.tagId"
                            class="mr-1 mb-1"
                            density="compact"
                            filter
                            color="primary"
                            variant="outlined"
                        >
                            {{ tag.name }}
                        </v-chip>
                    </template>
                    <hr
                        v-if="hasDisabledTag && filter.showDisabledTags"
                        class="d-block w-100 my-1 mr-2"
                    >
                    <template
                        v-if="hasDisabledTag && filter.showDisabledTags"
                        v-for="tag in tags"
                        :key="tag['id']"
                    >
                        <v-chip
                            v-if="!tag.isEnabled"
                            :value="tag.tagId"
                            class="mr-1 mb-1"
                            density="compact"
                            filter
                            color="primary"
                            variant="outlined"
                        >
                            {{ tag.name }}
                            <mdicon name="TagOff" class="text-muted ml-1" size="16" />
                        </v-chip>
                    </template>
                </v-chip-group>
            </v-col>
            <v-col
                cols="12"
                sm="6"
                md="6"
                xl="4"
            >
                <filter-text-field
                    v-model="filter.note"
                    label="Beobachtung"
                    data-test="filter-note-way-point"
                    :isLoading="isLoading"
                />
            </v-col>
            <v-col
                cols="12"
                sm="6"
                md="6"
                xl="4"
            >
                <filter-text-field
                    v-model="filter.oneOnOneInterview"
                    label="Einzelgespräch"
                    data-test="filter-oneOnOneInterview-way-point"
                    :isLoading="isLoading"
                />
            </v-col>
            <v-col
                cols="12"
                sm="6"
                md="6"
                xl="2"
            >
                <filter-text-field
                    v-model="filter.locationName"
                    label="Ort"
                    data-test="filter-locationName-way-point"
                    :isLoading="isLoading"
                />
            </v-col>
            <v-col
                cols="12"
                sm="6"
                md="6"
                xl="2"
            >
                <filter-combobox-field
                    v-model="filter.teamName"
                    label="Teamname"
                    data-test="filter-team-walk"
                    :is-loading="isLoading"
                    :suggestions="teamNames"
                    :hide-no-data="true"
                />
            </v-col>
            <v-col
                cols="12"
                sm="6"
                md="6"
                xl="2"
            >
                <filter-combobox-field
                    v-model="filter.conceptOfDay"
                    label="Tageskonzept"
                    data-test="filter-conceptOfDay-walk"
                    :is-loading="isLoading"
                    :suggestions="conceptOfDaySuggestions"
                />
            </v-col>
            <v-col
                cols="12"
                sm="6"
                md="6"
                xl="2"
            >
                <filter-combobox-field
                    v-model="filter.walkName"
                    label="Runde"
                    data-test="filter-name-walk"
                    :is-loading="isLoading"
                    :suggestions="walkNames"
                    :hide-no-data="true"
                />
            </v-col>
            <v-col
                cols="12"
                xs="12"
                sm="12"
                md="12"
                xl="12"
            >
                <date-range-picker
                    v-model="filter.visitedAt"
                    :is-loading="isLoading"
                    data-test="visited-at-filter"
                    placeholder="Ankunft"
                    name="Ankunft"
                    @cleared="unsetFilterVisitedAt"
                />
            </v-col>
            <v-col
                class="my-1"
                cols="12"
                xs="12"
                sm="12"
                md="12"
                xl="12"
            >
                <v-btn
                    color="secondary"
                    block
                    :disabled="(isLoading || isExportLoading || !hasFilter) && currentPage === 1"
                    @click="unsetAllFilter"
                    data-test="reset-way-point-filter"
                    density="comfortable"
                    class="text-transform-none"
                    :append-icon="hasFilter ? 'mdi-filter-remove-outline' : 'mdi-filter-outline'"
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
                    density="comfortable"
                    class="text-transform-none"
                    append-icon="mdi-download"
                    :loading="isExportLoading"
                    @click="exportWayPoints"
                >
                    {{ totalItems > 5000 ? 5000 : totalItems }} Wegpunkt{{ totalItems !== 1 ? 'e' : '' }} als .csv-Datei exportieren
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
            :search="search"
            item-value="name"
            :no-data-text="noItemsText"
            :loading-text="loadingText"
            :sort-by="sortBy"
            multi-sort
            mobile-breakpoint="lg"
            density="compact"
            show-current-page
            @update:options="loadItems"
            :no-results-text="noItemsText"
            @update:items-per-page="handlePerPageChange"
            @update:page="handleCurrentPageChange"
        >
            <template v-slot:item.malesCount="{item}">
                {{ getWalkByIri(item.walk)?.isWithAgeRanges ? item.malesCount : '-' }}
            </template>
            <template v-slot:item.femalesCount="{item}">
                {{ getWalkByIri(item.walk)?.isWithAgeRanges ? item.femalesCount : '-' }}
            </template>
            <template v-slot:item.queerCount="{item}">
                {{ getWalkByIri(item.walk)?.isWithAgeRanges ? item.queerCount : '-' }}
            </template>
            <template v-slot:item.peopleCount="{item}">
                {{ getWalkByIri(item.walk)?.isWithPeopleCount ? item.peopleCount : '-' }}
            </template>
            <template v-slot:item.note="{item}">
                <tooltip :text="item.note?.trim()" :nl2br="true" />
            </template>
            <template v-slot:item.oneOnOneInterview="{item}">
                <tooltip :text="item.oneOnOneInterview?.trim()" :nl2br="true" />
            </template>
            <template v-slot:item.wayPointTags="{item}">
                {{ formatTags(item.wayPointTags) }}
            </template>
            <template v-slot:item.walk.teamName="{item}">
                {{ getWalkByIri(item.walk)?.teamName }}
            </template>
            <template v-slot:item.walk.name="{item}">
                {{ getWalkByIri(item.walk)?.name }}
            </template>
            <template v-slot:item.visitedAt="{item}">
                {{ formatDateTimeNoSecondsWithDayOfWeek(item.visitedAt) }}
            </template>
            <template v-slot:item.actions="{item}">
                <div class="d-flex justify-content-around">
                    <v-btn
                        density="compact"
                        :disabled="isLoading"
                        color="secondary"
                        :to="{name: 'WayPointDetail', params: { wayPointId: item.wayPointId, walkId: getWalkByIri(item.walk)?.walkId }}"
                        :data-test="`button-wegpunkt-ansehen-${ item.locationName }`"
                        class="text-transform-none"
                    >
                        Wegpunkt ansehen
                        <v-icon icon="mdi-routes" class="ml-1"></v-icon>
                        <v-icon icon="mdi-eye" class="ml-1"></v-icon>
                    </v-btn>
                </div>
            </template>
        </v-data-table-server>
    </div>
</template>
<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue';
import dayjs from 'dayjs';
import WayPointAPI from '../../api/wayPoint';
import WalkAPI from '../../api/walk.js';
import TagAPI from '../../api/tag.js';
import {
    useGeneralStore,
    useTagStore,
    useUserPreferencesStore,
    useWalkStore,
    useWayPointStore,
} from '@/js/stores';
import {
    DateRangePicker,
    FilterComboboxField,
    FilterTextField,
} from '@/js/components/Common';
import Tooltip from '@/js/components/Common/Tooltip.vue';
import {
    formatDateTimeNoSecondsWithDayOfWeek,
    itemsPerPageOptions,
    itemsPerPageText,
    loadingText,
    noItemsText,
} from '@/js/utils';

interface Header {
    value: string;
    title: string;
    sortable?: boolean;
    align?: string;
}

interface SortItem {
    key: string;
    order: 'asc' | 'desc';
}

interface LoadItemsOptions {
    page: number;
    itemsPerPage: number;
    sortBy: SortItem[];
}

const emit = defineEmits<{
    (e: 'refresh-total-way-points', total: number): void;
}>();

const generalStore = useGeneralStore();
const tagStore = useTagStore();
const userPreferencesStore = useUserPreferencesStore();
const wayPointStore = useWayPointStore();
const walkStore = useWalkStore();

const isLoading = ref(false);
const isExportLoading = ref(false);
const abortController = ref<AbortController | null>(null);
const exportCtx = ref<Record<string, any> | null>(null);

const allTeamNames = ref<Array<{ teamName: string }>>([]);
const allWalkNames = ref<Array<{ name: string }>>([]);
const allConceptOfDaySuggestions = ref<Array<{ conceptOfDay?: Record<string, string> }>>(
    []
);
const tags = ref<any[]>([]);

const totalItems = ref(0);
const search = ref('');
const currentPage = ref<Number>(1);
const itemsPerPage = ref<Number>(itemsPerPageOptions[1].value as number);
const serverItems = ref<any[]>([]);
const tableOptions = ref<{
    page: number;
    itemsPerPage: number;
    sortBy: SortItem[];
} | null>(null);

const sortBy = ref<SortItem[]>([{ key: 'visitedAt', order: 'desc' }]);

const effectivePreferences = computed(() => userPreferencesStore.effective)
const headers = computed<Header[]>(() => {
    const base: Header[] = [{ value: 'locationName', title: 'Ort', sortable: true }];

    base.push(
        { value: 'malesCount', title: 'Männer', sortable: true },
        { value: 'femalesCount', title: 'Frauen', sortable: true },
        { value: 'queerCount', title: 'Andere', sortable: true }
    );

    base.push({
        value: 'peopleCount',
        title: 'Anzahl Personen',
        sortable: true,
        align: 'center',
    });

    base.push(
        { value: 'note', title: 'Beobachtung', sortable: true },
        { value: 'oneOnOneInterview', title: 'Einzelgespräch', sortable: true },
        { value: 'wayPointTags', title: 'Tags', sortable: false },
        { value: 'walk.teamName', title: 'Team', sortable: true },
        { value: 'visitedAt', title: 'Ankunft' },
        { value: 'walk.name', title: 'Runde', sortable: true },
        { value: 'actions', title: 'Aktionen', sortable: false }
    );

    return base.filter(header => {
        if (header.value === 'actions') {
            return true
        }
        console.log(effectivePreferences.value.tables.wayPoints.columns[header.value])

        return effectivePreferences.value.tables.wayPoints.columns[header.value];
    })
});

const filter = computed(() => generalStore.getWayPointFilter);
const defaultFilter = computed(() => generalStore.defaultWayPointFilter);
const defaultDateRange = computed(() => generalStore.defaultWayPointFilter.visitedAt);

const teamNames = computed<string[]>(() =>
    allTeamNames.value.map((teamName) => teamName.teamName)
);

const walkNames = computed<string[]>(() =>
    allWalkNames.value.map((walk) => walk.name)
);

const conceptOfDaySuggestions = computed<string[]>(() => {
    const all = allConceptOfDaySuggestions.value.flatMap((walk) =>
        Object.values(walk.conceptOfDay ?? {})
    );
    return [...new Set(all)].sort((a, b) => a.localeCompare(b));
});

const hasDisabledTag = computed<boolean>(() =>
    Boolean(tags.value.find((tag) => !tag.isEnabled))
);

const wayPoints = computed(() => wayPointStore.getWayPoints);

const hasFilter = computed<boolean>(() => {
    return JSON.stringify(filter.value) !== JSON.stringify(defaultFilter.value);
});

watch(
    filter,
    () => {
        search.value = String(Date.now());
    },
    { deep: true }
);

function getTagByIri(iri: string) {
    return tagStore.getTagByIri(iri);
}

function getWalkByIri(iri: string) {
    return walkStore.getWalkByIri(iri);
}

function formatTags(iriList: string[]): string {
    let formattedTags = '';
    let tagList: any[] = [];

    iriList.forEach((iri) => {
        const tagByIri = getTagByIri(iri);
        if (!tagByIri) {
            return;
        }
        tagList.push(tagByIri);
    });

    tagList = tagList.sort((tagA, tagB) => (tagA.name > tagB.name ? 1 : -1));
    tagList.forEach((tag, key) => {
        if (key) {
            formattedTags += ', ';
        }
        formattedTags += ` ${tag.name}`;
    });

    return formattedTags;
}

async function loadItems({ page, itemsPerPage, sortBy }: LoadItemsOptions) {
    tableOptions.value = { page, itemsPerPage, sortBy };

    if (abortController.value) {
        abortController.value.abort();
    }
    abortController.value = new AbortController();
    const signal = abortController.value.signal;

    const data: Record<string, any> = {
        page,
        itemsPerPage,
        wayPointTags: filter.value.wayPointTags,
        note: filter.value.note,
        oneOnOneInterview: filter.value.oneOnOneInterview,
        locationName: filter.value.locationName,
        'walk.teamName': filter.value.teamName,
        'walk.name': filter.value.walkName,
        'walk.conceptOfDay': filter.value.conceptOfDay,
    };

    sortBy.forEach((val) => {
        data[`order[${val.key}]`] = val.order;
    });

    if (filter.value.visitedAt[0] && filter.value.visitedAt[1]) {
        data['visitedAt[after]'] = dayjs(filter.value.visitedAt[0])
            .startOf('day')
            .toISOString();
        data['visitedAt[before]'] = dayjs(filter.value.visitedAt[1])
            .endOf('day')
            .toISOString();
    }

    exportCtx.value = data;

    isLoading.value = true;
    let result;
    try {
        result = await WayPointAPI.find(data, signal);
    } catch (e) {
        return
    }

    isLoading.value = false;

    const items = result.data['member'];
    const total = result.data['totalItems'] ?? 0;

    const walkPromises: Promise<unknown>[] = [];
    const walkPromiseIds: string[] = [];

    items.forEach((wayPoint: any) => {
        if (!getWalkByIri(wayPoint.walk)) {
            const id = wayPoint.walk.replace('/api/walks/', '');
            if (!walkPromiseIds.includes(id)) {
                walkPromises.push(walkStore.fetchById(id));
                walkPromiseIds.push(id);
            }
        }
    });

    await Promise.all(walkPromises);

    generalStore.updateWayPointFilterResult(items);
    serverItems.value = items;
    totalItems.value = total;

    emit('refresh-total-way-points', totalItems.value);
}

function handleCurrentPageChange(value: number | string) {
    const newVal = Number(value);
    currentPage.value = newVal;
    generalStore.updateWayPointCurrentPage(newVal);
}

function handlePerPageChange(value: number | string) {
    const newVal = Number(value);
    itemsPerPage.value = newVal;
    generalStore.updateWayPointPerPage(newVal);
}

function unsetFilterWayPointTags() {
    filter.value.wayPointTags = [];
}

function unsetFilterVisitedAt() {
    filter.value.visitedAt = defaultDateRange.value;
}

function unsetAllFilter() {
    generalStore.updateWayPointFilter(defaultFilter.value);
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

async function exportWayPoints() {
    if (!exportCtx.value) {
        return;
    }
    isExportLoading.value = true;
    const response = await WayPointAPI.export(exportCtx.value);
    forceFileDownload(response, getFileName());
    isExportLoading.value = false;
}

function getFileName(): string {
    let title = 'streetworkwegpunkte_export.csv';

    if (filter.value.wayPointTags.length) {
        const tagNames: string[] = [];
        filter.value.wayPointTags.forEach((tagIri: string) => {
            tagNames.push(getTagByIri(tagIri)?.name);
        });
        title = `TAGS_${tagNames.join('_')}_${title}`;
    }

    if (filter.value.oneOnOneInterview) {
        title = `EINZELGESPRAECH_${filter.value.oneOnOneInterview}_${title}`;
    }

    if (filter.value.note) {
        title = `BEOBACHTUNG_${filter.value.note}_${title}`;
    }

    if (filter.value.teamName.length) {
        title = `TEAM_${filter.value.teamName.join('_')}_${title}`;
    }

    if (filter.value.locationName) {
        title = `ORT_${filter.value.locationName}_${title}`;
    }

    if (filter.value?.visitedAt[0] && filter.value?.visitedAt[1]) {
        const formattedStartDate = dayjs(filter.value.visitedAt[0]).format('YYYYMMDD');
        const formattedEndDate = dayjs(filter.value.visitedAt[1]).format('YYYYMMDD');
        if (formattedStartDate === formattedEndDate) {
            title = `${formattedStartDate}_${title}`;
        } else {
            title = `${formattedStartDate}-${formattedEndDate}_${title}`;
        }
    }

    return title;
}

onMounted(async () => {
    const tagResult = await TagAPI.findAllWithWayPoints();
    tags.value = tagResult.data['member'];
    tagStore.fetchTags();

    const allTeamNamesResult = await WalkAPI.findAllTeamNames();
    allTeamNames.value = allTeamNamesResult.data['member'];

    const allWalkNamesResult = await WalkAPI.findAllWalkNames();
    allWalkNames.value = allWalkNamesResult.data['member'];

    const allConceptOfDaySuggestionsResult = await WalkAPI.findAllConceptOfDay();
    allConceptOfDaySuggestions.value = allConceptOfDaySuggestionsResult.data['member'];

    if (!userPreferencesStore.isLoaded) {
        await userPreferencesStore.load()
    }
})

const load = () => {
    itemsPerPage.value = generalStore.wayPointPerPage;
    currentPage.value = generalStore.wayPointCurrentPage;
}
load()
</script>


<style>
</style>
