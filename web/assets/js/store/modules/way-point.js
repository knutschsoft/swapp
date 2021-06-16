"use strict";
import WayPointAPI from '../../api/wayPoint';

const
    FETCHING_WAY_POINTS = "FETCHING_WAY_POINTS",
    FETCHING_WAY_POINTS_SUCCESS = "FETCHING_WAY_POINTS_SUCCESS",
    FETCHING_WAY_POINTS_ERROR = "FETCHING_WAY_POINTS_ERROR",
    FETCHING_WAY_POINT = "FETCHING_WAY_POINT",
    FETCHING_WAY_POINT_SUCCESS = "FETCHING_WAY_POINT_SUCCESS",
    FETCHING_WAY_POINT_ERROR = "FETCHING_WAY_POINT_ERROR",
    CHANGE_WAY_POINT = "CHANGE_WAY_POINT",
    CHANGE_WAY_POINT_SUCCESS = "CHANGE_WAY_POINT_SUCCESS",
    CHANGE_WAY_POINT_ERROR = "FETCHING_WAY_POINT_ERROR",
    CREATE_WAY_POINT = 'CREATE_WAY_POINT',
    CREATE_WAY_POINT_SUCCESS = 'CREATE_WAY_POINT_SUCCESS',
    CREATE_WAY_POINT_ERROR = 'CREATE_WAY_POINT_ERROR'
;

const state = {
    wayPoints: [],
    error: null,
    errorChange: null,
    isLoading: false,
    isLoadingChange: false,
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
    getWayPointByIri(state, getters) {
        return iri => getters.getWayPointById(iri.replace('/api/way_points/', ''));

    },
    hasWayPoints(state) {
        return state.wayPoints.length > 0;
    },
    error(state) {
        return state.error;
    },
    errorChange(state) {
        return state.errorChange;
    },
    isLoading(state) {
        return state.isLoading;
    },
    isLoadingChange(state) {
        return state.isLoadingChange;
    },
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
    [CHANGE_WAY_POINT](state) {
        state.isLoadingChange = true;
        state.errorChange = null;
    },
    [CHANGE_WAY_POINT_SUCCESS](state, wayPoint) {
        state.errorChange = null;
        state.isLoadingChange = false;
        let fetchedWayPoint = wayPoint;
        state.wayPoints.forEach((wayPoint, index) => {
            if (String(fetchedWayPoint.id) === String(wayPoint.id)) {
                state.wayPoints.splice(index, 1);
            }
        });

        state.wayPoints = [...state.wayPoints, fetchedWayPoint];
    },
    [CHANGE_WAY_POINT_ERROR](state, error) {
        state.errorChange = error;
        state.isLoadingChange = false;
    },
    [CREATE_WAY_POINT](state) {
        state.isLoadingChange = true;
        state.errorChange = null;
    },
    [CREATE_WAY_POINT_SUCCESS](state, wayPoint) {
        state.errorChange = null;
        state.isLoadingChange = false;
        state.wayPoints = [...state.wayPoints, wayPoint];
    },
    [CREATE_WAY_POINT_ERROR](state, error) {
        state.errorChange = error;
        state.isLoadingChange = false;
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
    async change({commit, dispatch}, payload) {
        commit(CHANGE_WAY_POINT);
        try {
            let response = await WayPointAPI.change(payload);
            commit(CHANGE_WAY_POINT_SUCCESS, response.data);
            dispatch('walk/findByIri', response.data.walk, { root: true });

            return response.data;
        } catch (error) {
            commit(CHANGE_WAY_POINT_ERROR, error);
        }
    },
    async create({commit, dispatch}, payload) {
        commit(CREATE_WAY_POINT);
        try {
            let response = await WayPointAPI.create(payload);
            commit(CREATE_WAY_POINT_SUCCESS, response.data);
            dispatch('walk/findByIri', response.data.walk, { root: true });

            return response.data;
        } catch (error) {
            commit(CREATE_WAY_POINT_ERROR, error);
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
