'use strict';
import WalkAPI from '../../api/walk';

const
    FETCHING_WALKS = 'FETCHING_WALKS',
    FETCHING_WALKS_SUCCESS = 'FETCHING_WALKS_SUCCESS',
    FETCHING_WALKS_ERROR = 'FETCHING_WALKS_ERROR',
    FETCHING_WALK = 'FETCHING_WALK',
    FETCHING_WALK_SUCCESS = 'FETCHING_WALK_SUCCESS',
    FETCHING_WALK_ERROR = 'FETCHING_WALK_ERROR',
    CHANGE_WALK = 'CHANGE_WALK',
    CHANGE_WALK_SUCCESS = 'CHANGE_WALK_SUCCESS',
    CHANGE_WALK_ERROR = 'CHANGE_WALK_ERROR',
    RESET_CHANGE_WALK_ERROR = "RESET_CHANGE_WALK_ERROR",
    REMOVE_WALK = "REMOVE_WALK",
    REMOVE_WALK_SUCCESS = "REMOVE_WALK_SUCCESS",
    REMOVE_WALK_ERROR = "REMOVE_WALK_ERROR",
    CREATE_WALK = 'CREATE_WALK',
    CREATE_WALK_SUCCESS = 'CREATE_WALK_SUCCESS',
    CREATE_WALK_ERROR = 'CREATE_WALK_ERROR'
;

function removeObjectFromState(state, object) {
    state.walks.forEach(function (oldObject, key) {
        if (oldObject['@id'] === object['@id']) {
            // see: https://vuejs.org/v2/guide/reactivity.html#For-Arrays
            state.walks.splice(key, 1);
        }
    });
}

const state = {
    walks: [],
    totalWalks: 0,
    error: null,
    errorChange: null,
    errorCreate: null,
    isLoading: false,
    isLoadingChange: false,
    isLoadingCreate: false,
};

const getters = {
    walks(state) {
        return state.walks;
    },
    getWalkById(state) {
        return walkId => {
            let foundWalk = false;
            state.walks.forEach(
                (walk) => {
                    if (String(walk.id) === String(walkId)) {
                        foundWalk = walk;
                    }
                },
            );

            return foundWalk;
        };
    },
    getWalkByIri(state, getters) {
        return iri => getters.getWalkById(iri.replace('/api/walks/', ''));

    },
    hasWalks(state) {
        return state.walks.length > 0;
    },
    totalWalks(state) {
        return state.totalWalks;
    },
    error(state) {
        return state.error;
    },
    errorChange(state) {
        return state.errorChange;
    },
    errorCreate(state) {
        return state.errorCreate;
    },
    isLoading(state) {
        return state.isLoading;
    },
    isLoadingChange(state) {
        return state.isLoadingChange;
    },
    isLoadingCreate(state) {
        return state.isLoadingCreate;
    },
};

const mutations = {
    [FETCHING_WALKS](state) {
        state.isLoading = true;
        state.error = null;
    },
    [FETCHING_WALKS_SUCCESS](state, walks) {
        state.error = null;
        state.isLoading = false;
        state.walks = walks['hydra:member'];
        state.totalWalks = walks['hydra:totalItems'];
    },
    [FETCHING_WALKS_ERROR](state, error) {
        state.error = error;
        state.isLoading = false;
    },
    [FETCHING_WALK](state) {
        state.isLoading = true;
        state.error = null;
    },
    [FETCHING_WALK_SUCCESS](state, walk) {
        state.error = null;
        state.isLoading = false;
        let fetchedWalk = walk;
        state.walks.forEach((walk, index) => {
            if (String(fetchedWalk.id) === String(walk.id)) {
                state.walks.splice(index, 1);
            }
        });

        state.walks = [...state.walks, fetchedWalk];
    },
    [FETCHING_WALK_ERROR](state, error) {
        state.error = error;
        state.isLoading = false;
    },
    [CHANGE_WALK](state) {
        state.isLoadingChange = true;
        state.errorChange = null;
    },
    [CHANGE_WALK_SUCCESS](state, walk) {
        state.errorChange = null;
        state.isLoadingChange = false;
        let changedWalk = walk;
        state.walks.forEach((walk, index) => {if (String(changedWalk.id) === String(walk.id)) {
                state.walks.splice(index, 1);
            }
        });

        state.walks = [...state.walks, changedWalk];
    },
    [CHANGE_WALK_ERROR](state, error) {
        state.errorChange = error;
        state.isLoadingChange = false;
    },
    [RESET_CHANGE_WALK_ERROR](state) {
        state.errorChange = null;
    },
    [REMOVE_WALK](state) {
        state.isLoadingChange = true;
        state.errorChange = null;
    },
    [REMOVE_WALK_SUCCESS](state, walk) {
        state.errorChange = null;
        state.isLoadingChange = false;
        removeObjectFromState(state, walk);
    },
    [REMOVE_WALK_ERROR](state, error) {
        state.errorChange = error;
        state.isLoadingChange = false;
    },
    [CREATE_WALK](state) {
        state.isLoadingCreate = true;
        state.errorCreate = null;
    },
    [CREATE_WALK_SUCCESS](state, walk) {
        state.errorCreate = null;
        state.isLoadingCreate = false;

        state.walks = [...state.walks, walk];
    },
    [CREATE_WALK_ERROR](state, error) {
        state.errorCreate = error;
        state.isLoadingCreate = false;
    },
};

const actions = {
    async find({ commit }, payload) {
        commit(FETCHING_WALKS);
        try {
            let response = await WalkAPI.find(payload);
            commit(FETCHING_WALKS_SUCCESS, response.data);
            return response.data['hydra:member'];
        } catch (error) {
            commit(FETCHING_WALKS_ERROR, error);
            return [];
        }
    },
    async findById({ commit }, walkId) {
        commit(FETCHING_WALK);
        try {
            let response = await WalkAPI.findOneById(walkId);
            commit(FETCHING_WALK_SUCCESS, response.data);
        } catch (error) {
            commit(FETCHING_WALK_ERROR, error);
        }
    },
    async findByIri({ commit, dispatch }, walkIri) {
        dispatch('findById', walkIri.replace('/api/walks/', ''));
    },
    async change({ commit }, payload) {
        commit(CHANGE_WALK);
        try {
            let response = await WalkAPI.change(payload);
            commit(CHANGE_WALK_SUCCESS, response.data);

            return response.data;
        } catch (error) {
            commit(CHANGE_WALK_ERROR, error);
        }
    },
    async changeStartTime({ commit }, payload) {
        commit(CHANGE_WALK);
        try {
            let response = await WalkAPI.changeStartTime(payload);
            commit(CHANGE_WALK_SUCCESS, response.data);

            return response.data;
        } catch (error) {
            commit(CHANGE_WALK_ERROR, error);
        }
    },
    async epilogue({ commit }, payload) {
        commit(CHANGE_WALK);
        try {
            let response = await WalkAPI.epilogue(payload);
            commit(CHANGE_WALK_SUCCESS, response.data);

            return response.data;
        } catch (error) {
            commit(CHANGE_WALK_ERROR, error);
        }
    },
    async create({ commit }, payload) {
        commit(CREATE_WALK);
        try {
            let response = await WalkAPI.create(payload);
            commit(CREATE_WALK_SUCCESS, response.data);

            return response.data;
        } catch (error) {
            commit(CREATE_WALK_ERROR, error);
        }
    },
    async changeUnfinished({ commit }, payload) {
        commit(CHANGE_WALK);
        try {
            let response = await WalkAPI.changeUnfinished(payload);
            commit(CHANGE_WALK_SUCCESS, response.data);

            return response.data;
        } catch (error) {
            commit(CHANGE_WALK_ERROR, error);
        }
    },
    async remove({commit, dispatch}, walk) {
        commit(REMOVE_WALK);
        try {
            let response = await WalkAPI.remove({walk: walk['@id']});
            commit(REMOVE_WALK_SUCCESS, walk);

            return response.data;
        } catch (error) {
            commit(REMOVE_WALK_ERROR, error);
        }
    },
    resetChangeError({ commit }) {
        commit(RESET_CHANGE_WALK_ERROR);
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};
