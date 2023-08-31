import {acceptHMRUpdate, defineStore} from 'pinia';
import apiClient from '../api'
import {AxiosResponse} from "axios";

import {Team, TeamCreateRequest, TeamChangeRequest, TeamsResponse} from '../model';

type State = {
    teams: Team[],
    loadingArray: Array<string>,
    errorArray: Record<'fetch' | 'change' | 'create', any>,
}

function replaceObjectInState(state: State, object: Team) {
    let isReplaced = false;
    state.teams.forEach(function (oldObject: Team, key: number) {
        if (oldObject['@id'] === object['@id']) {
            // see: https://vuejs.org/v2/guide/reactivity.html#For-Arrays
            state.teams.splice(key, 1, object);
            isReplaced = true;
        }
    });
    if (!isReplaced) {
        state.teams = [...state.teams, object];
    }
}

export const useTeamStore = defineStore("team", {
    state: (): State => ({
        teams: [],
        loadingArray: [],
        errorArray: {'fetch': false, 'change': false, 'create': false},
    }),
    getters: {
        isLoadingChange: (state) => (teamIri: string) => state.loadingArray.includes(`change-${teamIri}`),
        isLoadingCreate: (state) => state.loadingArray.includes(`create`),
        isLoading: (state) => state.loadingArray.length > 0,
        hasError: (state) => state.errorArray.fetch || state.errorArray.change || state.errorArray.create,
        getErrors: (state) => state.errorArray,
        getTeams({teams}): Team[] {
            return teams;
        },
        hasTeams({teams}): boolean {
            return teams.length > 0;
        },
        getTeamById({teams}): (id: number | string) => Team | undefined {
            return (id: number | string): Team | undefined => {
                return teams.find(team => String(team.teamId) === String(id));
            }
        },
        getTeamByIri({teams}): (iri: string) => Team | undefined {
            return (iri: string): Team | undefined => {
                return teams.find(team => team['@id'] === iri);
            }
        },
        getTeamByTeamName({teams}): (teamName: string) => Team | undefined {
            return (teamName: string): Team | undefined => {
                return teams.find((team: Team) => team.name === teamName);
            }
        },
    },
    actions: {
        async fetchByIri(iri: string): Promise<Team | void> {
            this.loadingArray.push('fetchByIri');
            this.errorArray.fetch = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.get(iri);
                const team: Team = response.data;
                replaceObjectInState(this, team);

                return team;
            } catch (error: any) {
                this.errorArray.fetch = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf('fetchByIri'), 1);
            }
        },
        async create(payload: TeamCreateRequest): Promise<Team | void> {
            this.loadingArray.push('create');
            this.errorArray.create = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.post('/api/teams/create', payload);
                const team: Team = response.data;
                replaceObjectInState(this, team);

                return team;
            } catch (error: any) {
                this.errorArray.create = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf('create'), 1);
            }
        },
        async change(payload: TeamChangeRequest): Promise<Team | void> {
            this.loadingArray.push(`change-${payload.team}`);
            this.errorArray.change = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.post('/api/teams/change', payload);
                const team: Team = response.data;
                replaceObjectInState(this, team);

                return team;
            } catch (error: any) {
                this.errorArray.change = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf(`change-${payload.team}`), 1);
            }
        },
        async fetchTeams(): Promise<void> {
            this.loadingArray.push('fetch');
            this.errorArray.fetch = false;
            try {
                const response: AxiosResponse<TeamsResponse, any> = await apiClient.get('/api/teams?itemsPerPage=1000&page=1');
                this.teams = response.data["hydra:member"];
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
    import.meta.webpackHot.accept(acceptHMRUpdate(useTeamStore, import.meta.webpackHot))
}
