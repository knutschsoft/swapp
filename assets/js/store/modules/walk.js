"use strict";
import WalkAPI from '../../api/walk';

const
    FETCHING_WALKS = "FETCHING_WALKS",
    FETCHING_WALKS_SUCCESS = "FETCHING_WALKS_SUCCESS",
    FETCHING_WALKS_ERROR = "FETCHING_WALKS_ERROR",
    FETCHING_WALK = "FETCHING_WALK",
    FETCHING_WALK_SUCCESS = "FETCHING_WALK_SUCCESS",
    FETCHING_WALK_ERROR = "FETCHING_WALK_ERROR"
;

const state = {
    walks: [],
    totalWalks: 0,
    error: null,
    isLoading: false,
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
                }
            );

            return foundWalk;
        }
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
    isLoading(state) {
        return state.isLoading;
    }
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

        state.walks = [ ...state.walks, fetchedWalk ];
    },
    [FETCHING_WALK_ERROR](state, error) {
        state.error = error;
        state.isLoading = false;
    },
};

const actions = {
    async find({commit}, payload) {
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
    async findById({commit}, walkId) {
        commit(FETCHING_WALK);
        try {
            let response = await WalkAPI.findOneById(walkId);
            commit(FETCHING_WALK_SUCCESS, response.data);
        } catch (error) {
            commit(FETCHING_WALK_ERROR, error);
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
