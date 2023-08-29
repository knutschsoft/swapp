import {acceptHMRUpdate, defineStore} from 'pinia';
import apiClient from '../api'
import {AxiosRequestConfig, AxiosResponse} from "axios";

import {Tag, TagCreateRequest, TagEnableRequest, TagDisableRequest, TagsResponse} from '../model';

type State = {
    tags: Tag[],
    loadingArray: Array<string>,
    errorArray: Record<'fetch' | 'change' | 'create', any>,
}

function replaceObjectInState(state: State, object: Tag) {
    let isReplaced = false;
    state.tags.forEach(function (oldObject: Tag, key: number) {
        if (oldObject['@id'] === object['@id']) {
            // see: https://vuejs.org/v2/guide/reactivity.html#For-Arrays
            state.tags.splice(key, 1, object);
            isReplaced = true;
        }
    });
    if (!isReplaced) {
        state.tags = [...state.tags, object];
    }
}

export const useTagStore = defineStore("tag", {
    state: (): State => ({
        tags: [],
        loadingArray: [],
        errorArray: {'fetch': false, 'change': false, 'create': false},
    }),
    getters: {
        isLoadingToggleTagState: (state) => (tagIri: string) => state.loadingArray.includes(`enable-${tagIri}`) || state.loadingArray.includes(`disable-${tagIri}`),
        isLoading: (state) => state.loadingArray.length > 0,
        hasError: (state) => state.errorArray.fetch || state.errorArray.change || state.errorArray.create,
        getErrors: (state) => state.errorArray,
        getTags({tags}): Tag[] {
            return tags;
        },
        getTagById({tags}): (id: number) => Tag | undefined {
            return (id: number): Tag | undefined => {
                return tags.find(tag => tag.tagId === id);
            }
        },
        getTagByIri({tags}): (iri: string) => Tag | undefined {
            return (iri: string): Tag | undefined => {
                return tags.find(tag => tag['@id'] === iri);
            }
        },
    },
    actions: {
        async fetchByIri(iri: string): Promise<Tag | void> {
            this.loadingArray.push('fetchByIri');
            this.errorArray.fetch = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.get(iri);
                const tag: Tag = response.data;
                replaceObjectInState(this, tag);

                return tag;
            } catch (error: any) {
                this.errorArray.fetch = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf('fetchByIri'), 1);
            }
        },
        async create(payload: TagCreateRequest): Promise<Tag | void> {
            this.loadingArray.push('create');
            this.errorArray.create = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.post('/api/tags/create', payload);
                const tag: Tag = response.data;
                replaceObjectInState(this, tag);

                return tag;
            } catch (error: any) {
                this.errorArray.create = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf('create'), 1);
            }
        },
        async enable(payload: TagEnableRequest): Promise<Tag | void> {
            this.loadingArray.push(`enable-${payload.tag}`);
            this.errorArray.change = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.post('/api/tags/enable', payload);
                const tag: Tag = response.data;
                replaceObjectInState(this, tag);

                return tag;
            } catch (error: any) {
                this.errorArray.change = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf(`enable-${payload.tag}`), 1);
            }
        },
        async disable(payload: TagDisableRequest): Promise<Tag | void> {
            this.loadingArray.push(`disable-${payload.tag}`);
            this.errorArray.change = false;
            try {
                const response: AxiosResponse<any, any> = await apiClient.post('/api/tags/disable', payload);
                const tag: Tag = response.data;
                replaceObjectInState(this, tag);

                return tag;
            } catch (error: any) {
                this.errorArray.change = error.response;
            } finally {
                this.loadingArray.splice(this.loadingArray.indexOf(`disable-${payload.tag}`), 1);
            }
        },
        async fetchTags(): Promise<void> {
            this.loadingArray.push('fetch');
            this.errorArray.fetch = false;
            try {
                const response: AxiosResponse<TagsResponse, any> = await apiClient.get('/api/tags?itemsPerPage=1000&page=1');
                this.tags = response.data["hydra:member"];
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
    import.meta.webpackHot.accept(acceptHMRUpdate(useTagStore, import.meta.webpackHot))
}
