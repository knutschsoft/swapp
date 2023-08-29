import {defineStore} from 'pinia';
import { useLocalStorage, type RemovableRef } from "@vueuse/core";

type State = {
    token: RemovableRef<string>,
}

export const useAuthStore = defineStore('auth', {
    state: (): State => ({
        token: useLocalStorage('token', '0'),
    }),
    getters: {
        getToken({token}): string {
            return token;
        },
    },
    actions: {
        setToken(token: string): void {
            this.token = token;
        },
    }
});
