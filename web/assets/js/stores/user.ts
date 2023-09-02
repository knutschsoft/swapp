import {acceptHMRUpdate, defineStore} from 'pinia';
import apiClient from '../api'
import dayjs from 'dayjs';
import {AxiosResponse} from "axios";

import {
    User,
    UserChangeRequest,
    UserChangePasswordRequest,
    UserEnableRequest,
    UserDisableRequest,
    UserRequestPasswordResetRequest,
    UserIsConfirmationTokenValidRequest,
    UserCreateRequest,
    UsersResponse
} from '../model';

type State = {
    users: User[],
    totalUsers: Number,
    loadingArray: Array<string>,
    errorArray: Record<'fetch' | 'change' | 'create' | 'remove', any>,
}

const updateFilterParams = function (params: any) {
    let sort: string = '';
    if (params?.sortBy) {
        sort = `&order[${params.sortBy}]=${params.sortDesc ? 'desc' : 'asc'}`;
    }
    if (typeof params?.filter !== "object") {
        return sort;
    }
    for (const [key, value] of Object.entries(params.filter)) {
        if (value === null || value === undefined) {
        } else if ('userUsers' === key && Array.isArray(value)) {
            value.forEach((iri: String) => {
                sort += `&${key}[]=${iri}`;
            });
        } else if ('teamName' === key) {
            sort += `&user.${key}=${value}`;
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

function removeObjectFromState(state: State, object: User) {
    state.users.forEach(function (oldObject, key) {
        if (oldObject['@id'] === object['@id']) {
            // see: https://vuejs.org/v2/guide/reactivity.html#For-Arrays
            state.users.splice(key, 1);
        }
    });
}

function replaceObjectInState(state: State, object: User) {
    let isReplaced = false;
    state.users.forEach(function (oldObject: User, key: number) {
        if (oldObject['@id'] === object['@id']) {
            // see: https://vuejs.org/v2/guide/reactivity.html#For-Arrays
            state.users.splice(key, 1, object);
            isReplaced = true;
        }
    });
    if (!isReplaced) {
        state.users = [...state.users, object];
    }
}

export const useUserStore = defineStore("user", {
    state: (): State => ({
        users: [],
        totalUsers: 0,
        loadingArray: [],
        errorArray: {'fetch': false, 'change': false, 'create': false, 'remove': false},
    }),
    getters: {
        isLoadingChange: (state) => (userIri: string) => state.loadingArray.includes(`change-${userIri}`),
        isLoadingCreate: (state) => state.loadingArray.includes(`create`),
        isLoading: (state) => state.loadingArray.length > 0,
        hasError: (state) => state.errorArray.fetch || state.errorArray.change || state.errorArray.create,
        getErrors: (state) => state.errorArray,
        getUsers({users}): User[] {
            return users;
        },
        hasUsers({users}): boolean {
            return users.length > 0;
        },
        getUserById({users}): (id: number | string) => User | undefined {
            return (id: number | string): User | undefined => {
                return users.find(user => String(user.userId) === String(id));
            }
        },
        getUserByIri({users}): (iri: string) => User | undefined {
            return (iri: string): User | undefined => {
                return users.find(user => user['@id'] === iri);
            }
        },
    },
    actions: {
        async fetchById(id: number | string): Promise<User | void> {
            return this.fetchByIri(`/api/users/${id}`)
        },
        async fetchByIri(iri: string): Promise<User | void> {
            this.loadingArray.push('fetchByIri');
            this.errorArray.fetch = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.get(iri);
                const user: User = response.data;
                replaceObjectInState(this, user);

                return user;
            } catch (error: any) {
                this.errorArray.fetch = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf('fetchByIri'), 1);
            }
        },
        async create(payload: UserCreateRequest): Promise<User | void> {
            this.loadingArray.push('create');
            this.errorArray.create = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.post('/api/users/create', payload);
                const user: User = response.data;
                replaceObjectInState(this, user);

                return user;
            } catch (error: any) {
                this.errorArray.create = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf('create'), 1);
            }
        },
        async change(payload: UserChangeRequest): Promise<User | void> {
            this.loadingArray.push(`change-${payload.user}`);
            this.errorArray.change = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.post('/api/users/change', payload);
                const user: User = response.data;
                replaceObjectInState(this, user);

                return user;
            } catch (error: any) {
                this.errorArray.change = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf(`change-${payload.user}`), 1);
            }
        },
        async enable(payload: UserEnableRequest): Promise<User | void> {
            this.loadingArray.push(`change-${payload.user}`);
            this.errorArray.change = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.post('/api/users/enable', payload);
                const user: User = response.data;
                replaceObjectInState(this, user);

                return user;
            } catch (error: any) {
                this.errorArray.change = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf(`change-${payload.user}`), 1);
            }
        },
        async disable(payload: UserDisableRequest): Promise<User | void> {
            this.loadingArray.push(`change-${payload.user}`);
            this.errorArray.change = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.post('/api/users/disable', payload);
                const user: User = response.data;
                replaceObjectInState(this, user);

                return user;
            } catch (error: any) {
                this.errorArray.change = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf(`change-${payload.user}`), 1);
            }
        },
        async requestPasswordReset(payload: UserRequestPasswordResetRequest): Promise<User | void> {
            this.loadingArray.push(`change-${payload.username}`);
            this.errorArray.change = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.post('/api/users/request-password-reset', payload);
                const user: User = response.data;
                replaceObjectInState(this, user);

                return user;
            } catch (error: any) {
                this.errorArray.change = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf(`change-${payload.username}`), 1);
            }
        },
        async isConfirmationTokenValid(payload: UserIsConfirmationTokenValidRequest): Promise<User | void> {
            this.loadingArray.push(`change-${payload.user}`);
            this.errorArray.change = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.post('/api/users/is-confirmation-token-valid', payload);
                const user: User = response.data;
                replaceObjectInState(this, user);

                return user;
            } catch (error: any) {
                this.errorArray.change = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf(`change-${payload.user}`), 1);
            }
        },
        async changePassword(payload: UserChangePasswordRequest): Promise<User | void> {
            this.loadingArray.push(`change-${payload.user}`);
            this.errorArray.change = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.post('/api/users/change-password', payload);
                const user: User = response.data;
                replaceObjectInState(this, user);

                return user;
            } catch (error: any) {
                this.errorArray.change = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf(`change-${payload.user}`), 1);
            }
        },
        resetChangeError(): void {
            this.errorArray.change = false;
        },
        resetCreateError(): void {
            this.errorArray.create = false;
        },
        async fetchUsers(params: any): Promise<User[] | void> {
            if (!params) {
                params = {};
            }
            let sort = updateFilterParams(params);
            if (!params.perPage && !params.currentPage) {
                params.perPage = 1000;
                params.currentPage = 1;
            }

            this.loadingArray.push('fetch');
            this.errorArray.fetch = false;
            try {
                const response: AxiosResponse<UsersResponse, any> = await apiClient.get(`/api/users?page=${params.currentPage}&itemsPerPage=${params.perPage}` + sort);
                this.users = response.data["hydra:member"];
                this.totalUsers = Number(response.data["hydra:totalItems"]);

                return response.data["hydra:member"];
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
    import.meta.webpackHot.accept(acceptHMRUpdate(useUserStore, import.meta.webpackHot))
}
