import {acceptHMRUpdate, defineStore} from 'pinia';
import apiClient from '../api'
import dayjs from 'dayjs';
import {AxiosResponse} from "axios";

import {
    Walk,
    WalkChangeRequest,
    WalkChangeStartTimeRequest,
    WalkChangeUnfinishedRequest,
    WalkCreateRequest,
    WalkEpilogueRequest,
    WalkRemoveRequest,
    WalksResponse
} from '../model';

type State = {
    walks: Walk[],
    totalWalks: Number,
    loadingArray: Array<string>,
    errorArray: Record<'fetch' | 'change' | 'create' | 'remove', any>,
}

const updateFilterParams = function (params: any) {
    let sort: string = '';
    if (params.sortBy) {
        sort = `&order[${params.sortBy}]=${params.sortDesc ? 'desc' : 'asc'}`;
    }
    if (typeof params.filter !== "object") {
        return sort;
    }
    for (const [key, value] of Object.entries(params.filter)) {
        if (value === null || value === undefined) {
        } else if ('walkTags' === key && Array.isArray(value)) {
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

function removeObjectFromState(state: State, object: Walk) {
    state.walks.forEach(function (oldObject, key) {
        if (oldObject['@id'] === object['@id']) {
            // see: https://vuejs.org/v2/guide/reactivity.html#For-Arrays
            state.walks.splice(key, 1);
        }
    });
}

function replaceObjectInState(state: State, object: Walk) {
    let isReplaced = false;
    state.walks.forEach(function (oldObject: Walk, key: number) {
        if (oldObject['@id'] === object['@id']) {
            // see: https://vuejs.org/v2/guide/reactivity.html#For-Arrays
            state.walks.splice(key, 1, object);
            isReplaced = true;
        }
    });
    if (!isReplaced) {
        state.walks = [...state.walks, object];
    }
}

export const useWalkStore = defineStore("walk", {
    state: (): State => ({
        walks: [],
        totalWalks: 0,
        loadingArray: [],
        errorArray: {'fetch': false, 'change': false, 'create': false, 'remove': false},
    }),
    getters: {
        isLoadingChange: (state) => (walkIri: string) => state.loadingArray.includes(`change-${walkIri}`),
        isLoadingCreate: (state) => state.loadingArray.includes(`create`),
        isLoading: (state) => state.loadingArray.length > 0,
        hasError: (state) => state.errorArray.fetch || state.errorArray.change || state.errorArray.create,
        getErrors: (state) => state.errorArray,
        getWalks({walks}): Walk[] {
            return walks;
        },
        hasWalks({walks}): boolean {
            return walks.length > 0;
        },
        getWalkById({walks}): (id: number | string) => Walk | undefined {
            return (id: number | string): Walk | undefined => {
                return walks.find(walk => String(walk.walkId) === String(id));
            }
        },
        getWalkByIri({walks}): (iri: string) => Walk | undefined {
            return (iri: string): Walk | undefined => {
                return walks.find(walk => walk['@id'] === iri);
            }
        },
    },
    actions: {
        async fetchById(id: number | string): Promise<Walk | void> {
            return this.fetchByIri(`/api/walks/${id}`)
        },
        async fetchByIri(iri: string): Promise<Walk | void> {
            this.loadingArray.push('fetchByIri');
            this.errorArray.fetch = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.get(iri);
                const walk: Walk = response.data;
                replaceObjectInState(this, walk);

                return walk;
            } catch (error: any) {
                this.errorArray.fetch = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf('fetchByIri'), 1);
            }
        },
        async create(payload: WalkCreateRequest): Promise<Walk | void> {
            this.loadingArray.push('create');
            this.errorArray.create = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.post('/api/walks/create', payload);
                const walk: Walk = response.data;
                replaceObjectInState(this, walk);

                return walk;
            } catch (error: any) {
                this.errorArray.create = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf('create'), 1);
            }
        },
        async change(payload: WalkChangeRequest): Promise<Walk | void> {
            this.loadingArray.push(`change-${payload.walk}`);
            this.errorArray.change = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.post('/api/walks/change', payload);
                const walk: Walk = response.data;
                replaceObjectInState(this, walk);

                return walk;
            } catch (error: any) {
                this.errorArray.change = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf(`change-${payload.walk}`), 1);
            }
        },
        async changeStartTime(payload: WalkChangeStartTimeRequest): Promise<Walk | void> {
            this.loadingArray.push(`change-${payload.walk}`);
            this.errorArray.change = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.post('/api/walks/change-start-time', payload);
                const walk: Walk = response.data;
                replaceObjectInState(this, walk);

                return walk;
            } catch (error: any) {
                this.errorArray.change = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf(`change-${payload.walk}`), 1);
            }
        },
        async changeUnfinished(payload: WalkChangeUnfinishedRequest): Promise<Walk | void> {
            this.loadingArray.push(`change-${payload.walk}`);
            this.errorArray.change = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.post('/api/walks/change-unfinished', payload);
                const walk: Walk = response.data;
                replaceObjectInState(this, walk);

                return walk;
            } catch (error: any) {
                this.errorArray.change = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf(`change-${payload.walk}`), 1);
            }
        },
        async epilogue(payload: WalkEpilogueRequest): Promise<Walk | void> {
            this.loadingArray.push(`change-${payload.walk}`);
            this.errorArray.change = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.post('/api/walks/epilogue', payload);
                const walk: Walk = response.data;
                replaceObjectInState(this, walk);

                return walk;
            } catch (error: any) {
                this.errorArray.change = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf(`change-${payload.walk}`), 1);
            }
        },
        resetChangeError(): void {
            this.errorArray.change = false;
        },
        resetCreateError(): void {
            this.errorArray.create = false;
        },
        async remove(payload: WalkRemoveRequest): Promise<void> {
            this.loadingArray.push(`remove-${payload.walk}`);
            this.errorArray.remove = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.post('/api/walks/remove', payload);
                const walk: Walk = response.data;
                removeObjectFromState(this, walk);
            } catch (error: any) {
                this.errorArray.remove = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf(`remove-${payload.walk}`), 1);
            }
        },
        async fetchWalks(params: any): Promise<void> {
            let sort = updateFilterParams(params);

            this.loadingArray.push('fetch');
            this.errorArray.fetch = false;
            try {
                const response: AxiosResponse<WalksResponse, any> = await apiClient.get(`/api/walks?page=${params.currentPage}&itemsPerPage=${params.perPage}` + sort);
                this.walks = response.data["hydra:member"];
                this.totalWalks = Number(response.data["hydra:totalItems"]);
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
    import.meta.webpackHot.accept(acceptHMRUpdate(useWalkStore, import.meta.webpackHot))
}
