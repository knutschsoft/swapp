"use strict";
import WayPointAPI from '../../api/wayPoint';

const
    FETCHING_WAY_POINTS = "FETCHING_WAY_POINTS",
    FETCHING_WAY_POINTS_SUCCESS = "FETCHING_WAY_POINTS_SUCCESS",
    FETCHING_WAY_POINTS_ERROR = "FETCHING_WAY_POINTS_ERROR",
    FETCHING_WAY_POINT = "FETCHING_WAY_POINT",
    FETCHING_WAY_POINT_SUCCESS = "FETCHING_WAY_POINT_SUCCESS",
    FETCHING_WAY_POINT_ERROR = "FETCHING_WAY_POINT_ERROR"
;

const state = {
    wayPoints: [],
    error: null,
    isLoading: false,
    totalWayPoints: 0,
};

const getters = {
    wayPoints(state) {
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
    [FETCHING_WAY_POINT](state) {
        state.isLoading = true;
        state.error = null;
    },
    [FETCHING_WAY_POINT_SUCCESS](state, wayPoint) {
        state.error = null;
        state.isLoading = false;
        let fetchedWayPoint = wayPoint;
        state.wayPoints.forEach((wayPoint, index) => {
            if (String(fetchedWayPoint.id) === String(wayPoint.id)) {
                state.wayPoints.splice(index, 1);
            }
        });

        state.wayPoints = [...state.wayPoints, fetchedWayPoint];
    },
    [FETCHING_WAY_POINT_ERROR](state, error) {
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
    async find({commit}, payload) {
        commit(FETCHING_WAY_POINTS);
        try {
            let response = await WayPointAPI.find(payload);
            commit(FETCHING_WAY_POINTS_SUCCESS, response.data);
            return response.data['hydra:member'];
        } catch (error) {
            commit(FETCHING_WAY_POINTS_ERROR, error);
        }
    },
    async findById({commit}, id) {
        commit(FETCHING_WAY_POINT);
        try {
            let response = await WayPointAPI.findById(id);
            commit(FETCHING_WAY_POINT_SUCCESS, response.data);
            return response.data;
        } catch (error) {
            commit(FETCHING_WAY_POINT_ERROR, error);
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
