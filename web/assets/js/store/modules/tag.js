"use strict";
import TagAPI from '../../api/tag';

const
    FETCHING_TAGS = "FETCHING_TAGS",
    FETCHING_TAGS_SUCCESS = "FETCHING_TAGS_SUCCESS",
    FETCHING_TAGS_ERROR = "FETCHING_TAGS_ERROR",
    CREATE_TAG = "CREATE_TAG",
    CREATE_TAG_SUCCESS = "CREATE_TAG_SUCCESS",
    CREATE_TAG_ERROR = "CREATE_TAG_ERROR",
    ENABLE_TAG = "ENABLE_TAG",
    ENABLE_TAG_SUCCESS = "ENABLE_TAG_SUCCESS",
    ENABLE_TAG_ERROR = "ENABLE_TAG_ERROR",
    DISABLE_TAG = "DISABLE_TAG",
    DISABLE_TAG_SUCCESS = "DISABLE_TAG_SUCCESS",
    DISABLE_TAG_ERROR = "DISABLE_TAG_ERROR"
;

const state = {
    tags: [],
    error: null,
    createTagError: null,
    toggleTagStateError: false,
    isLoading: false,
    createTagIsLoading: false,
    isLoadingToggleTagState: [],
};

function replaceObjectInState(state, object) {
    let isReplaced = false;
    state.tags.forEach(function (oldObject, key) {
        if (oldObject['@id'] === object['@id']) {
            // see: https://vuejs.org/v2/guide/reactivity.html#For-Arrays
            state.tags.splice(key, 1, object);
            isReplaced = true;
        }
    });
    if (!isReplaced) {
        state.tags = [ ...state.tags, object ];
    }
}

function removeStringValueFromStateProperty(stateProperty, stringValue) {
    stateProperty.forEach(function (oldStringValue, key) {
        if (oldStringValue === stringValue) {
            // see: https://vuejs.org/v2/guide/reactivity.html#For-Arrays
            stateProperty.splice(key, 1);
        }
    });
}

const getters = {
    tags(state) {
        return state.tags.slice(0).sort((a, b) => a.name.toLowerCase() > b.name.toLowerCase() ? 1 : -1);
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
        return iri => getters.getTagById(iri.replace('/api/tags/', ''));
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
    isLoadingToggleTagState(state) {
        return iri => state.isLoadingToggleTagState.includes(iri);
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
    [ENABLE_TAG](state, tagIri) {
        state.isLoadingToggleTagState = [ ...state.isLoadingToggleTagState, tagIri ];
        state.toggleTagStateError = null;
    },
    [ENABLE_TAG_SUCCESS](state, tag) {
        state.toggleTagStateError = null;
        removeStringValueFromStateProperty(state.isLoadingToggleTagState, tag['@id']);
        replaceObjectInState(state, tag);
    },
    [ENABLE_TAG_ERROR](state, error, tagIri) {
        state.toggleTagStateError = error;
        removeStringValueFromStateProperty(state.isLoadingToggleTagState, tagIri);
    },
    [DISABLE_TAG](state, tagIri) {
        state.isLoadingToggleTagState = [ ...state.isLoadingToggleTagState, tagIri ];
        state.toggleTagStateError = null;
    },
    [DISABLE_TAG_SUCCESS](state, tag) {
        state.toggleTagStateError = null;
        removeStringValueFromStateProperty(state.isLoadingToggleTagState, tag['@id']);
        replaceObjectInState(state, tag);
    },
    [DISABLE_TAG_ERROR](state, error, tagIri) {
        state.toggleTagStateError = error;
        removeStringValueFromStateProperty(state.isLoadingToggleTagState, tagIri);
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
    async enable({commit}, payload) {
        commit(ENABLE_TAG, payload.tag);
        try {
            let response = await TagAPI.enable(payload);
            commit(ENABLE_TAG_SUCCESS, response.data);

            return response.data;
        } catch (error) {
            commit(ENABLE_TAG_ERROR, error, payload.tag);

            return null;
        }
    },
    async disable({commit}, payload) {
        commit(DISABLE_TAG, payload.tag);
        try {
            let response = await TagAPI.disable(payload);
            commit(DISABLE_TAG_SUCCESS, response.data);

            return response.data;
        } catch (error) {
            commit(DISABLE_TAG_ERROR, error, payload.tag);
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
