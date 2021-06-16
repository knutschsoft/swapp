"use strict";
import TagAPI from '../../api/tag';

const
    FETCHING_TAGS = "FETCHING_TAGS",
    FETCHING_TAGS_SUCCESS = "FETCHING_TAGS_SUCCESS",
    FETCHING_TAGS_ERROR = "FETCHING_TAGS_ERROR",
    CREATE_TAG = "CREATE_TAG",
    CREATE_TAG_SUCCESS = "CREATE_TAG_SUCCESS",
    CREATE_TAG_ERROR = "CREATE_TAG_ERROR"
;

const state = {
    tags: [],
    error: null,
    createTagError: null,
    isLoading: false,
    createTagIsLoading: false,
};

const getters = {
    tags(state) {
        return state.tags.slice(0).sort((a, b) => a.name > b.name ? 1 : -1);
    },
    getTagById(state) {
        return tagId => {
            let foundTag = false;
            state.tags.forEach(
                (tag) => {
                    if (String(tag.id) === String(tagId)) {
                        foundTag = tag;
                    }
                },
            );

            return foundTag;
        };
    },
    getTagByIri(state, getters) {
        return tagIri => getters.getTagById(tagIri.replace('/api/tags/', ''));
    },
    hasTags(state) {
        return state.tags.length > 0;
    },
    error(state) {
        return state.error;
    },
    createTagError(state) {
        return state.createTagError;
    },
    isLoading(state) {
        return state.isLoading;
    },
    createTagIsLoading(state) {
        return state.createTagIsLoading;
    },
};

const mutations = {
    [FETCHING_TAGS](state) {
        state.isLoading = true;
        state.error = null;
    },
    [FETCHING_TAGS_SUCCESS](state, tags) {
        state.error = null;
        state.isLoading = false;
        state.tags = tags;
    },
    [FETCHING_TAGS_ERROR](state, error) {
        state.error = error;
        state.isLoading = false;
    },
    [CREATE_TAG](state) {
        state.createTagIsLoading = true;
        state.createTagError = null;
    },
    [CREATE_TAG_SUCCESS](state, createdTag) {
        state.createTagError = null;
        state.createTagIsLoading = false;
        state.tags = [ ...state.tags, createdTag ];
    },
    [CREATE_TAG_ERROR](state, error) {
        state.createTagError = error;
        state.createTagIsLoading = false;
    },
};

const actions = {
    async findAll({commit}) {
        commit(FETCHING_TAGS);
        try {
            let response = await TagAPI.findAll();
            commit(FETCHING_TAGS_SUCCESS, response.data['hydra:member']);
        } catch (error) {
            commit(FETCHING_TAGS_ERROR, error);
        }
    },
    async create({commit}, payload) {
        commit(CREATE_TAG);
        try {
            let response = await TagAPI.create(payload);
            commit(CREATE_TAG_SUCCESS, response.data);

            return response.data;
        } catch (error) {
            commit(CREATE_TAG_ERROR, error);
        }
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
