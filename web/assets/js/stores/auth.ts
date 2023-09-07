import {defineStore} from 'pinia';
import apiClient from '../api'
import {type RemovableRef, useLocalStorage } from "@vueuse/core";
import {AxiosResponse} from "axios";
import {User, LoginCheckRequest } from "../model";
import {useUserStore} from "./user";

type EmptyObj = Record<PropertyKey, never>;

type State = {
    token: RemovableRef<string>,
    refreshToken: RemovableRef<string>,
    loadingArray: Array<string>,
    currentUser: RemovableRef<User | EmptyObj>,
    switchUsername: RemovableRef<string>,
    originUser: RemovableRef<User | EmptyObj>,
    isAuthenticated: RemovableRef<Boolean>,
    isUserSwitched: RemovableRef<Boolean>,
    errorArray: Record<'login' | 'logout', any>,
}

export const useAuthStore = defineStore('auth', {
    state: (): State => ({
        token: useLocalStorage('token', '0'),
        refreshToken: useLocalStorage('refreshToken', '0'),
        loadingArray: [],
        currentUser: useLocalStorage('currentUser', {}),
        switchUsername: useLocalStorage('switchUsername', ''),
        originUser: useLocalStorage('originUser', {}),
        isAuthenticated: useLocalStorage('isAuthenticated', false),
        isUserSwitched: useLocalStorage('isUserSwitched', false),
        errorArray: {'login': false, 'logout': false},
    }),
    getters: {
        hasRole: ({currentUser}) => (role: string): Boolean => {
            if (!currentUser || !currentUser.roles) {
                return false;
            }

            return -1 !== currentUser.roles.indexOf(role);
        },
        isLoading: ({loadingArray}): Boolean => loadingArray.length > 0,
        isAdmin(): Boolean { return this.hasRole('ROLE_ADMIN') || this.isSuperAdmin; },
        isSuperAdmin(): Boolean { return this.hasRole('ROLE_SUPER_ADMIN') },
        getToken: ({token}): string => token,
        hasError: ({errorArray}) => errorArray.login || errorArray.logout,
        getErrors: ({errorArray}) => errorArray,
    },
    actions: {
        setToken(token: string): void {
            this.token = token;
        },
        async switchUser(user: User): Promise<void> {
            const switchUser = await useUserStore().fetchByIri(String(user['@id']));
            if (!switchUser) {
                return;
            }
            this.switchUsername = String(switchUser.username);
            this.originUser = this.currentUser;
            this.currentUser = switchUser;
            this.isUserSwitched = true;
            window.location.reload();
        },
        exitSwitchUser(): void {
            this.currentUser = this.originUser;
            this.switchUsername = '';
            this.originUser = {};
            this.isUserSwitched = false;
            window.location.reload()
        },
        logout(): void {
            if (this.isUserSwitched) {
                this.exitSwitchUser();
            }

            this.token = '';
            this.isAuthenticated = false;
            this.currentUser = {};
        },
        async login(payload: LoginCheckRequest): Promise<User | void> {
            this.loadingArray.push(`login`);
            this.errorArray.login = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.post('/api/users/getToken', payload);
                const loginResult: any = response.data; // LoginCheckPost200Response
                this.setToken(loginResult.token);
                this.isAuthenticated = true;
                const currentUser = await useUserStore().fetchByIri(loginResult['@id']);
                if (!currentUser) {
                    return;
                }
                this.currentUser = currentUser;

                return this.currentUser;
            } catch (error: any) {
                this.errorArray.login = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf(`login`), 1);
            }
        },
    }
});
