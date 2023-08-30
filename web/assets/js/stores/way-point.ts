import {acceptHMRUpdate, defineStore} from 'pinia';
import apiClient from '../api'
import dayjs from 'dayjs';
import {AxiosResponse} from "axios";

import {WayPoint, WayPointCreateRequest, WayPointChangeRequest, WayPointRemoveRequest, WayPointsResponse} from '../model';

type State = {
    wayPoints: WayPoint[],
    loadingArray: Array<string>,
    errorArray: Record<'fetch' | 'change' | 'create' | 'remove', any>,
}

const updateFilterParams = function (params: any) {
    let sort: string = '';
    if (params.sortBy) {
        sort = `&order[${params.sortBy}]=${params.sortDesc ? 'desc' : 'asc'}`;
    }
    for (const [key, value] of Object.entries(params.filter)) {
        if (value === null || value === undefined) {
        } else if ('wayPointTags' === key && Array.isArray(value)) {
            value.forEach((iri: String) => {
                sort += `&${key}[]=${iri}`;
            });
        } else if ('teamName' === key) {
            sort += `&walk.${key}=${value}`;
        } else if ('visitedAt' === key) {
            // @ts-ignore
            if (value.startDate && value.endDate) {
                // @ts-ignore
                sort += `&${key}[after]=${dayjs(value.startDate).startOf('day').toISOString()}&${key}[before]=${dayjs(value.endDate).endOf('day').toISOString()}`;
            }
        } else {
            sort += `&${key}=${value}`;
        }
    }

    return sort;
};

function removeObjectFromState(state: State, object: WayPoint) {
    state.wayPoints.forEach(function (oldObject, key) {
        if (oldObject['@id'] === object['@id']) {
            // see: https://vuejs.org/v2/guide/reactivity.html#For-Arrays
            state.wayPoints.splice(key, 1);
        }
    });
}
function replaceObjectInState(state: State, object: WayPoint) {
    let isReplaced = false;
    state.wayPoints.forEach(function (oldObject: WayPoint, key: number) {
        if (oldObject['@id'] === object['@id']) {
            // see: https://vuejs.org/v2/guide/reactivity.html#For-Arrays
            state.wayPoints.splice(key, 1, object);
            isReplaced = true;
        }
    });
    if (!isReplaced) {
        state.wayPoints = [...state.wayPoints, object];
    }
}

export const useWayPointStore = defineStore("wayPoint", {
    state: (): State => ({
        wayPoints: [],
        loadingArray: [],
        errorArray: {'fetch': false, 'change': false, 'create': false, 'remove': false},
    }),
    getters: {
        isLoadingChange: (state) => (wayPointIri: string) => state.loadingArray.includes(`change-${wayPointIri}`),
        isLoadingCreate: (state) => state.loadingArray.includes(`create`),
        isLoading: (state) => state.loadingArray.length > 0,
        hasError: (state) => state.errorArray.fetch || state.errorArray.change || state.errorArray.create,
        getErrors: (state) => state.errorArray,
        getWayPoints({wayPoints}): WayPoint[] {
            return wayPoints;
        },
        hasWayPoints({wayPoints}): boolean {
            return wayPoints.length > 0;
        },
        getWayPointById({wayPoints}): (id: number) => WayPoint | undefined {
            return (id: number): WayPoint | undefined => {
                return wayPoints.find(wayPoint => wayPoint.wayPointId === id);
            }
        },
        getWayPointByIri({wayPoints}): (iri: string) => WayPoint | undefined {
            return (iri: string): WayPoint | undefined => {
                return wayPoints.find(wayPoint => wayPoint['@id'] === iri);
            }
        },
        getWayPointByWayPointName({wayPoints}): (wayPointName: string) => WayPoint | undefined {
            return (wayPointName: string): WayPoint | undefined => {
                return wayPoints.find((wayPoint: WayPoint) => wayPoint.locationName === wayPointName);
            }
        },
    },
    actions: {
        async fetchById(id: string): Promise<WayPoint | void> {
            return this.fetchByIri(`/api/way_points/${id}`)
        },
        async fetchByIri(iri: string): Promise<WayPoint | void> {
            this.loadingArray.push('fetchByIri');
            this.errorArray.fetch = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.get(iri);
                const wayPoint: WayPoint = response.data;
                replaceObjectInState(this, wayPoint);

                return wayPoint;
            } catch (error: any) {
                this.errorArray.fetch = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf('fetchByIri'), 1);
            }
        },
        async create(payload: WayPointCreateRequest): Promise<WayPoint | void> {
            this.loadingArray.push('create');
            this.errorArray.create = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.post('/api/way_points/create', payload);
                const wayPoint: WayPoint = response.data;
                replaceObjectInState(this, wayPoint);

                return wayPoint;
            } catch (error: any) {
                this.errorArray.create = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf('create'), 1);
            }
        },
        async change(payload: WayPointChangeRequest): Promise<WayPoint | void> {
            this.loadingArray.push(`change-${payload.wayPoint}`);
            this.errorArray.change = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.post('/api/way_points/change', payload);
                const wayPoint: WayPoint = response.data;
                replaceObjectInState(this, wayPoint);

                return wayPoint;
            } catch (error: any) {
                this.errorArray.change = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf(`change-${payload.wayPoint}`), 1);
            }
        },
        resetChangeError(): void {
            this.errorArray.change = false;
        },
        async remove(payload: WayPointRemoveRequest): Promise<void> {
            this.loadingArray.push(`remove-${payload.wayPoint}`);
            this.errorArray.remove = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.post('/api/way_points/remove', payload);
                const wayPoint: WayPoint = response.data;
                removeObjectFromState(this, wayPoint);
            } catch (error: any) {
                this.errorArray.remove = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf(`remove-${payload.wayPoint}`), 1);
            }
        },
        async fetchWayPoints(params: any): Promise<void> {
            let sort = updateFilterParams(params);

            this.loadingArray.push('fetch');
            this.errorArray.fetch = false;
            try {
                const response: AxiosResponse<WayPointsResponse, any> = await apiClient.get(`/api/way_points?page=${params.currentPage}&itemsPerPage=${params.perPage}` + sort);
                this.wayPoints = response.data["hydra:member"];
            } catch (error: any) {
                this.errorArray.fetch = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf('fetch'), 1);
            }
        }
    },
})


// make sure to pass the right store definition, `useAuth` in this case.
// @ts-expect-error
if (import.meta.webpackHot) {
    // @ts-expect-error
    import.meta.webpackHot.accept(acceptHMRUpdate(useWayPointStore, import.meta.webpackHot))
}
