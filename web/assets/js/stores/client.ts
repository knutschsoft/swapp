import {acceptHMRUpdate, defineStore} from 'pinia';
import apiClient from '../api'
import type {AxiosResponse} from "axios";

import type {Client, ClientChangeRequest, ClientCreateRequest, ClientRemoveRequest, ClientsResponse} from '../model';

type State = {
    clients: Client[],
    loadingArray: Array<string>,
    errorArray: Record<'fetch' | 'change' | 'create' | 'remove', any>,
}

function replaceObjectInState(state: State, object: Client) {
    let isReplaced = false;
    state.clients.forEach(function (oldObject: Client, key: number) {
        if (oldObject['@id'] === object['@id']) {
            // see: https://vuejs.org/v2/guide/reactivity.html#For-Arrays
            state.clients.splice(key, 1, object);
            isReplaced = true;
        }
    });
    if (!isReplaced) {
        state.clients = [...state.clients, object];
    }
}

export const useClientStore = defineStore("client", {
    state: (): State => ({
        clients: [],
        loadingArray: [],
        errorArray: {'fetch': false, 'change': false, 'create': false, 'remove': false},
    }),
    getters: {
        isLoadingChange: (state) => (clientIri: string) => state.loadingArray.includes(`change-${clientIri}`),
        isLoadingCreate: (state) => state.loadingArray.includes(`create`),
        isLoadingFetch: (state) => state.loadingArray.includes(`fetch`) || state.loadingArray.includes(`fetchByIri`),
        isLoadingRemove: (state) => (clientIri: string) => state.loadingArray.includes(`remove-${clientIri}`),
        isLoading: (state) => state.loadingArray.length > 0,
        hasError: (state) => state.errorArray.fetch || state.errorArray.change || state.errorArray.create,
        getErrors: (state) => state.errorArray,
        getClients({clients}): Client[] {
            return clients;
        },
        getClientById({clients}): (id: number | string) => Client | undefined {
            return (id: number | string): Client | undefined => {
                return clients.find(client => String(client.clientId) === String(id));
            }
        },
        getClientByIri({clients}): (iri: string) => Client | undefined {
            return (iri: string): Client | undefined => {
                return clients.find(client => client['@id'] === iri);
            }
        },
    },
    actions: {
        async fetchByIri(iri: string): Promise<Client | void> {
            this.loadingArray.push('fetchByIri');
            this.errorArray.fetch = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.get(iri);
                const client: Client = response.data;
                replaceObjectInState(this, client);

                return client;
            } catch (error: any) {
                this.errorArray.fetch = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf('fetchByIri'), 1);
            }
        },
        async changeClient(payload: ClientChangeRequest): Promise<Client | void> {
            this.loadingArray.push('change');
            this.errorArray.change = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.post('/api/clients/change', payload);
                const client: Client = response.data;
                replaceObjectInState(this, client);

                return client;
            } catch (error: any) {
                this.errorArray.change = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf('change'), 1);
            }
        },
        async createClient(payload: ClientCreateRequest): Promise<Client | void> {
            this.loadingArray.push('create');
            this.errorArray.create = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.post('/api/clients/create', payload);
                const client: Client = response.data;
                replaceObjectInState(this, client);

                return client;
            } catch (error: any) {
                this.errorArray.create = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf('create'), 1);
            }
        },
        async removeClient(payload: ClientRemoveRequest): Promise<boolean> {
            this.loadingArray.push(`remove-${payload.client}`);
            this.errorArray.remove = false;
            try {
                await apiClient.post('/api/clients/remove', payload);
                this.clients = this.clients.filter(c => c['@id'] !== payload.client);

                return true;
            } catch (error: any) {
                this.errorArray.remove = error.response;

                return false;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf(`remove-${payload.client}`), 1);
            }
        },
        resetRemoveError(): void {
            this.errorArray.remove = false;
        },
        async fetchClients(): Promise<void> {
            this.loadingArray.push('fetch');
            this.errorArray.fetch = false;
            try {
                const response: AxiosResponse<ClientsResponse, any> = await apiClient.get('/api/clients?itemsPerPage=1000&page=1');
                this.clients = response.data["member"];
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
    import.meta.webpackHot.accept(acceptHMRUpdate(useClientStore, import.meta.webpackHot))
}
