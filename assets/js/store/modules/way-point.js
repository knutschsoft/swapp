"use strict";
import WayPointAPI from '../../api/wayPoint';

const
    FETCHING_WAY_POINTS = "FETCHING_WAY_POINTS",
    FETCHING_WAY_POINTS_SUCCESS = "FETCHING_WAY_POINTS_SUCCESS",
    FETCHING_WAY_POINTS_ERROR = "FETCHING_WAY_POINTS_ERROR"
;

const state = {
    wayPoints: [],
    error: null,
    isLoading: false,
};

const getters = {
    wayPoints(state) {
        console.log(state.wayPoints);
        return state.wayPoints;
    },
    getWayPointById(state) {
        return wayPointId => {
            let foundWayPoint = false;
            state.wayPoints.forEach(
                (wayPoint) => {
                    if (String(wayPoint.id) === String(wayPointId)) {
                        foundWayPoint = wayPoint;
                    }
                }
            );

            return foundWayPoint;
        }
    },
    hasWayPoints(state) {
        return state.wayPoints.length > 0;
    },
    error(state) {
        return state.error;
    },
    isLoading(state) {
        return state.isLoading;
    }
};

const mutations = {
    [FETCHING_WAY_POINTS](state) {
        state.isLoading = true;
        state.error = null;
    },
    [FETCHING_WAY_POINTS_SUCCESS](state, wayPoints) {
        state.error = null;
        state.isLoading = false;
        state.wayPoints = wayPoints;
    },
    [FETCHING_WAY_POINTS_ERROR](state, error) {
        state.error = error;
        state.isLoading = false;
    },
};

const actions = {
    async findAll({commit}) {
        commit(FETCHING_WAY_POINTS);
        try {
            let response = await WayPointAPI.findAll();
            commit(FETCHING_WAY_POINTS_SUCCESS, response.data['hydra:member']);
        } catch (error) {
            commit(FETCHING_WAY_POINTS_ERROR, error);
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
