"use strict";
import TagAPI from '../../api/tag';

const
    FETCHING_TAGS = "FETCHING_TAGS",
    FETCHING_TAGS_SUCCESS = "FETCHING_TAGS_SUCCESS",
    FETCHING_TAGS_ERROR = "FETCHING_TAGS_ERROR"
;


// initial state
const state = {
    tags: [],
    error: null,
    isLoading: false,
};

// getters
const getters = {
    tags(state) {
        return state.tags;
    },
    hasTags(state) {
        return state.tags.length > 0;
    },
    error(state) {
        return state.error;
    },
    isLoading(state) {
        return state.isLoading;
    }
};

// mutations
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
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
