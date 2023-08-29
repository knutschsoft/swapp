import {acceptHMRUpdate, defineStore} from 'pinia';
import apiClient from '../api'
import {AxiosError, AxiosRequestConfig, AxiosResponse} from "axios";

// coming from generated code of api client generator
import {
    ApiClientsGetCollection200Response as ClientsResponse,
    ClientClientChangeRequestJsonld as ClientChangeRequest,
    ClientClientCreateRequestJsonld as ClientCreateRequest,
    ClientJsonldClientRead as Client
} from '../model';

type State = {
    clients: Client[],
    loadingArray: Array<string>,
    errorArray: Record<'fetch'|'change'|'create', any>,
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
        errorArray: {'fetch': false, 'change': false, 'create': false},
    }),
    getters: {
        isLoading: (state) => state.loadingArray.length > 0,
        hasError: (state) => state.errorArray.fetch || state.errorArray.change || state.errorArray.create,
        getErrors: (state) => state.errorArray,
        getClients({clients}): Client[] {
            return clients;
        },
        getClientById({clients}): (id: number) => Client | undefined {
            return (id: number): Client | undefined => {
                return clients.find(client => client.clientId === id);
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
        async changeClient(payload: AxiosRequestConfig<ClientChangeRequest>): Promise<Client | void> {
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
        async createClient(payload: AxiosRequestConfig<ClientCreateRequest>): Promise<Client | void> {
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
        async fetchClients(): Promise<void> {
            this.loadingArray.push('fetch');
            this.errorArray.fetch = false;
            try {
                const response: AxiosResponse<ClientsResponse, any> = await apiClient.get('/api/clients?itemsPerPage=1000&page=1');
                this.clients = response.data["hydra:member"];
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
