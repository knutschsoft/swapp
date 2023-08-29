import {defineStore} from 'pinia';

type State = {
    token: string,
}

export const useAuthStore = defineStore('auth', {
    state: (): State => ({
        token: ''
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
