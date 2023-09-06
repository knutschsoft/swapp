import {defineStore} from 'pinia';
import apiClient from '../api'
import {type RemovableRef, useLocalStorage} from "@vueuse/core";
import {AxiosResponse} from "axios";
// @ts-ignore
import SecurityAPI from '../api/security.js';

import {User, LoginCheckRequest } from "../model";
import {useUserStore} from "./user";

type State = {
    token: RemovableRef<string>,
    refreshToken: RemovableRef<string>,
    loadingArray: Array<string>,
    currentUser: RemovableRef<User | void>,
    switchUsername: RemovableRef<string>,
    originUser: RemovableRef<User | void>,
    isAuthenticated: RemovableRef<Boolean>,
    isUserSwitched: RemovableRef<Boolean>,
    errorArray: Record<'login' | 'logout', any>,
}

export const useAuthStore = defineStore('auth', {
    state: (): State => ({
        token: useLocalStorage('token', '0'),
        refreshToken: useLocalStorage('refreshToken', '0'),
        loadingArray: [],
        currentUser: useLocalStorage('currentUser', undefined),
        switchUsername: useLocalStorage('switchUsername', ''),
        originUser: useLocalStorage('originUser', undefined),
        isAuthenticated: useLocalStorage('isAuthenticated', false),
        isUserSwitched: useLocalStorage('isUserSwitched', false),
        errorArray: {'login': false, 'logout': false,},
    }),
    getters: {
        hasRole: ({currentUser}) => (role: string): Boolean => {
            if (!currentUser || !currentUser.roles) {
                return false;
            }

            return -1 !== currentUser.roles.indexOf(role);
        },
        isLoading: ({loadingArray}): Boolean => loadingArray.length > 0,
        isAuthenticated: ({isAuthenticated}): Boolean => isAuthenticated,
        // isAdmin: (): Boolean => this.hasRole('ROLE_ADMIN') || this.isSuperAdmin,
        isAdmin(): Boolean { return this.hasRole('ROLE_ADMIN') || this.isSuperAdmin; },
        isSuperAdmin(): Boolean { return this.hasRole('ROLE_SUPER_ADMIN') },
        // isAdmin: ({currentUser}): Boolean => this.hasRole({currentUser})('ROLE_ADMIN') || this.isSuperAdmin,
        // isSuperAdmin: ({currentUser}): Boolean => this.hasRole({currentUser})('ROLE_SUPER_ADMIN'),
        isUserSwitched: ({isUserSwitched}): Boolean => isUserSwitched,
        getToken: ({token}): string => token,
        // currentUser: ({currentUser}): User | void => currentUser,
        hasError: ({errorArray}) => errorArray.login || errorArray.logout,
        getErrors: ({errorArray}) => errorArray,
    },
    actions: {
        setToken(token: string): void {
            this.token = token;
        },
        async switchUser(user: User): Promise<void> {
            const switchUser = await SecurityAPI.find(user['@id']);
            this.switchUsername = String(switchUser.username);
            this.originUser = this.currentUser;
            this.currentUser = switchUser;
            window.location.reload();
        },
        exitSwitchUser(): void {
            this.currentUser = this.originUser;
            this.switchUsername = '';
            this.originUser = undefined;
            // localStorage.setItem('swapp-store-user', localStorage.getItem('origin-user'));
            // localStorage.removeItem('switch-user');
            // localStorage.removeItem('origin-user');
            // window.location.reload()
        },
        logout(): void {
            if (this.isUserSwitched) {
                this.exitSwitchUser();
            }

            this.token = '';
            this.isAuthenticated = false;
            this.currentUser = undefined;
        },
        async login(payload: LoginCheckRequest): Promise<User | void> {
            this.loadingArray.push(`login`);
            this.errorArray.login = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.post('/api/users/getToken', payload);
                console.log(response);
                console.log(response.data);
                const loginResult: any = response.data; // LoginCheckPost200Response
                this.setToken(loginResult.token);
                this.isAuthenticated = true;
                this.currentUser = await useUserStore().fetchByIri(loginResult['@id']);

                return this.currentUser;
            } catch (error: any) {
                this.errorArray.login = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf(`login`), 1);
            }
        },
    }
});
