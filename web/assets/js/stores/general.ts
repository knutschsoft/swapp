import {defineStore} from 'pinia';
import {type RemovableRef, useLocalStorage } from "@vueuse/core";
import dayjs, {Dayjs} from 'dayjs';

import {
    Walk, WayPoint,
} from '../model';

type DateRange = {
    startDate: Dayjs | null,
    endDate: Dayjs | null,
}

type WalkFilter = {
    isResubmission: Boolean | null,
    isUnfinished: Boolean | null,
    name: String,
    teamName: String,
    startTime: DateRange,
}

type WayPointFilter = {
    wayPointTags: String[],
    locationName: '',
    note: '',
    teamName: String,
    oneOnOneInterview: String,
    visitedAt: DateRange,
}

type State = {
    apiUrl: string,
    navUserFilter: RemovableRef<string>,
    defaultActiveUsersDateRange: DateRange,
    activeUsersDateRange: RemovableRef<DateRange>,
    defaultWalkFilter: WalkFilter,
    walkFilter: RemovableRef<WalkFilter>,
    walkPerPage: RemovableRef<Number>,
    walkCurrentPage: RemovableRef<Number>,
    walkFilterResult: RemovableRef<Walk[]>
    defaultWayPointFilter: WayPointFilter,
    wayPointFilter: RemovableRef<WayPointFilter>,
    wayPointPerPage: RemovableRef<Number>,
    wayPointCurrentPage: RemovableRef<Number>,
    wayPointFilterResult: RemovableRef<WayPoint[]>
}

const startTime: DateRange = {
    startDate: null,
    endDate: null,
};
let now = dayjs();
const defaultActiveUsersDateRange: DateRange = {
    startDate: now.subtract(5, 'month').startOf('month'),
    endDate: now.endOf('month'),
};

const defaultWalkFilter: WalkFilter = {
    isResubmission: null,
    isUnfinished: null,
    name: '',
    teamName: '',
    startTime: startTime,
};
const defaultWayPointFilter: WayPointFilter = {
    wayPointTags: [],
    locationName: '',
    note: '',
    teamName: '',
    oneOnOneInterview: '',
    visitedAt: startTime,
};

export const useGeneralStore = defineStore("general", {
    state: (): State => ({
        apiUrl: '',
        navUserFilter: useLocalStorage('nav-user-filter', ''),
        defaultWalkFilter: defaultWalkFilter,
        defaultActiveUsersDateRange: defaultActiveUsersDateRange,
        activeUsersDateRange: useLocalStorage('aktive-benutzer-date-range', defaultActiveUsersDateRange),
        walkFilter: useLocalStorage('swapp-store-abgeschlossene-runden-filter', defaultWalkFilter),
        walkPerPage: useLocalStorage('swapp-store-abgeschlossene-runden-per-page', 5),
        walkCurrentPage: useLocalStorage('swapp-store-abgeschlossene-runden-current-page', 1),
        walkFilterResult: useLocalStorage('swapp-store-abgeschlossene-runden-walks', []),
        defaultWayPointFilter: defaultWayPointFilter,
        wayPointFilter: useLocalStorage('swapp-store-alle-wegpunkte-filter', defaultWayPointFilter),
        wayPointPerPage: useLocalStorage('swapp-store-alle-wegpunkte-per-page', 5),
        wayPointCurrentPage: useLocalStorage('swapp-store-alle-wegpunkte-current-page', 1),
        wayPointFilterResult: useLocalStorage('swapp-store-alle-wegpunkte-walks', []),
    }),
    getters: {
        getApiUrl({apiUrl}): string {
            return apiUrl;
        },
        getWayPointFilter({wayPointFilter}): WayPointFilter {
            if (!wayPointFilter.visitedAt || !dayjs(wayPointFilter.visitedAt.startDate).isValid() || !dayjs(wayPointFilter.visitedAt.endDate).isValid()) {
                wayPointFilter.visitedAt = defaultWayPointFilter.visitedAt;
            }

            return wayPointFilter;
        },
        getWalkFilter({walkFilter}): WalkFilter {
            if (!walkFilter.startTime || !dayjs(walkFilter.startTime.startDate).isValid() || !dayjs(walkFilter.startTime.endDate).isValid()) {
                walkFilter.startTime = defaultWalkFilter.startTime;
            }

            return walkFilter;
        },
    },
    actions: {
        setApiUrl(apiUrl: string): void {
            this.apiUrl = apiUrl;
        },
        updateWalkFilter(walkFilter: WalkFilter): void {
            this.walkFilter = walkFilter;
        },
        updateWalkCurrentPage(currentPage: Number): void {
            this.walkCurrentPage = currentPage;
        },
        updateWalkPerPage(perPage: Number): void {
            this.walkPerPage = perPage;
        },
        updateWalkFilterResult(walks: Walk[]): void {
            this.walkFilterResult = walks;
        },
        updateWayPointFilter(wayPointFilter: WayPointFilter): void {
            this.wayPointFilter = wayPointFilter;
        },
        updateWayPointCurrentPage(currentPage: Number): void {
            this.wayPointCurrentPage = currentPage;
        },
        updateWayPointPerPage(perPage: Number): void {
            this.wayPointPerPage = perPage;
        },
        updateWayPointFilterResult(wayPoints: WayPoint[]): void {
            this.wayPointFilterResult = wayPoints;
        },
        updateActiveUsersDateRange(dateRange: any): void {
            this.activeUsersDateRange.startDate = dayjs(dateRange.endDate)
            this.activeUsersDateRange.endDate = dayjs(dateRange.endDate)
        },
    },
})
