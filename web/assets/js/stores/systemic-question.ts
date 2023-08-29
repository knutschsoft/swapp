import {acceptHMRUpdate, defineStore} from 'pinia';
import apiClient from '../api'
import {AxiosRequestConfig, AxiosResponse} from "axios";

import {SystemicQuestion, SystemicQuestionChangeRequest, SystemicQuestionCreateRequest, SystemicQuestionEnableRequest, SystemicQuestionDisableRequest, SystemicQuestionsResponse} from '../model';

type State = {
    systemicQuestions: SystemicQuestion[],
    loadingArray: Array<string>,
    errorArray: Record<'fetch' | 'change' | 'create', any>,
}

function replaceObjectInState(state: State, object: SystemicQuestion) {
    let isReplaced = false;
    state.systemicQuestions.forEach(function (oldObject: SystemicQuestion, key: number) {
        if (oldObject['@id'] === object['@id']) {
            // see: https://vuejs.org/v2/guide/reactivity.html#For-Arrays
            state.systemicQuestions.splice(key, 1, object);
            isReplaced = true;
        }
    });
    if (!isReplaced) {
        state.systemicQuestions = [...state.systemicQuestions, object];
    }
}

export const useSystemicQuestionStore = defineStore("systemicQuestion", {
    state: (): State => ({
        systemicQuestions: [],
        loadingArray: [],
        errorArray: {'fetch': false, 'change': false, 'create': false},
    }),
    getters: {
        isLoading: (state) => state.loadingArray.length > 0,
        hasError: (state) => state.errorArray.fetch || state.errorArray.change || state.errorArray.create,
        getErrors: (state) => state.errorArray,
        getSystemicQuestions({systemicQuestions}): SystemicQuestion[] {
            return systemicQuestions;
        },
        getSystemicQuestionById({systemicQuestions}): (id: number) => SystemicQuestion | undefined {
            return (id: number): SystemicQuestion | undefined => {
                return systemicQuestions.find(systemicQuestion => systemicQuestion.systemicQuestionId === id);
            }
        },
        getSystemicQuestionByIri({systemicQuestions}): (iri: string) => SystemicQuestion | undefined {
            return (iri: string): SystemicQuestion | undefined => {
                return systemicQuestions.find(systemicQuestion => systemicQuestion['@id'] === iri);
            }
        },
    },
    actions: {
        async fetchByIri(iri: string): Promise<SystemicQuestion | void> {
            this.loadingArray.push('fetchByIri');
            this.errorArray.fetch = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.get(iri);
                const systemicQuestion: SystemicQuestion = response.data;
                replaceObjectInState(this, systemicQuestion);

                return systemicQuestion;
            } catch (error: any) {
                this.errorArray.fetch = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf('fetchByIri'), 1);
            }
        },
        async change(payload: AxiosRequestConfig<SystemicQuestionChangeRequest>): Promise<SystemicQuestion | void> {
            this.loadingArray.push('change');
            this.errorArray.change = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.post('/api/systemic_questions/change', payload);
                const systemicQuestion: SystemicQuestion = response.data;
                replaceObjectInState(this, systemicQuestion);

                return systemicQuestion;
            } catch (error: any) {
                this.errorArray.change = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf('change'), 1);
            }
        },
        async create(payload: AxiosRequestConfig<SystemicQuestionCreateRequest>): Promise<SystemicQuestion | void> {
            this.loadingArray.push('create');
            this.errorArray.create = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.post('/api/systemic_questions/create', payload);
                const systemicQuestion: SystemicQuestion = response.data;
                replaceObjectInState(this, systemicQuestion);

                return systemicQuestion;
            } catch (error: any) {
                this.errorArray.create = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf('create'), 1);
            }
        },
        async enable(payload: AxiosRequestConfig<SystemicQuestionEnableRequest>): Promise<SystemicQuestion | void> {
            this.loadingArray.push('change');
            this.errorArray.change = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.post('/api/systemic_questions/enable', payload);
                const systemicQuestion: SystemicQuestion = response.data;
                replaceObjectInState(this, systemicQuestion);

                return systemicQuestion;
            } catch (error: any) {
                this.errorArray.change = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf('change'), 1);
            }
        },
        async disable(payload: AxiosRequestConfig<SystemicQuestionDisableRequest>): Promise<SystemicQuestion | void> {
            this.loadingArray.push('change');
            this.errorArray.change = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.post('/api/systemic_questions/disable', payload);
                const systemicQuestion: SystemicQuestion = response.data;
                replaceObjectInState(this, systemicQuestion);

                return systemicQuestion;
            } catch (error: any) {
                this.errorArray.change = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf('change'), 1);
            }
        },
        async fetchSystemicQuestions(): Promise<void> {
            this.loadingArray.push('fetch');
            this.errorArray.fetch = false;
            try {
                const response: AxiosResponse<SystemicQuestionsResponse, any> = await apiClient.get('/api/systemic_questions?itemsPerPage=1000&page=1');
                this.systemicQuestions = response.data["hydra:member"];
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
    import.meta.webpackHot.accept(acceptHMRUpdate(useSystemicQuestionStore, import.meta.webpackHot))
}
