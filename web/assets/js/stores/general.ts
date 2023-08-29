import {defineStore} from 'pinia';

type State = {
    apiUrl: string,
}

export const useGeneralStore = defineStore("general", {
    state: (): State => ({
        apiUrl: '',
    }),
    getters: {
        getApiUrl({apiUrl}): string {
            return apiUrl;
        },
    },
    actions: {
        setApiUrl(apiUrl: string): void {
            this.apiUrl = apiUrl;
        }
    },
})
