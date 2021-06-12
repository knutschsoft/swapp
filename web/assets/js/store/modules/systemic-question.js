"use strict";
import SystemicQuestionAPI from '../../api/systemicQuestion';

const
    FETCHING_SYSTEMIC_QUESTIONS = "FETCHING_SYSTEMIC_QUESTIONS",
    FETCHING_SYSTEMIC_QUESTIONS_SUCCESS = "FETCHING_SYSTEMIC_QUESTIONS_SUCCESS",
    FETCHING_SYSTEMIC_QUESTIONS_ERROR = "FETCHING_SYSTEMIC_QUESTIONS_ERROR",
    CREATE_SYSTEMIC_QUESTION = "CREATE_SYSTEMIC_QUESTION",
    CREATE_SYSTEMIC_QUESTION_SUCCESS = "CREATE_SYSTEMIC_QUESTION_SUCCESS",
    CREATE_SYSTEMIC_QUESTION_ERROR = "CREATE_SYSTEMIC_QUESTION_ERROR",
    CHANGE_SYSTEMIC_QUESTION = "CHANGE_SYSTEMIC_QUESTION",
    CHANGE_SYSTEMIC_QUESTION_SUCCESS = "CHANGE_SYSTEMIC_QUESTION_SUCCESS",
    CHANGE_SYSTEMIC_QUESTION_ERROR = "CHANGE_SYSTEMIC_QUESTION_ERROR",
    ENABLE_SYSTEMIC_QUESTION = "ENABLE_SYSTEMIC_QUESTION",
    ENABLE_SYSTEMIC_QUESTION_SUCCESS = "ENABLE_SYSTEMIC_QUESTION_SUCCESS",
    ENABLE_SYSTEMIC_QUESTION_ERROR = "ENABLE_SYSTEMIC_QUESTION_ERROR",
    DISABLE_SYSTEMIC_QUESTION = "DISABLE_SYSTEMIC_QUESTION",
    DISABLE_SYSTEMIC_QUESTION_SUCCESS = "DISABLE_SYSTEMIC_QUESTION_SUCCESS",
    DISABLE_SYSTEMIC_QUESTION_ERROR = "DISABLE_SYSTEMIC_QUESTION_ERROR"
;

const state = {
    systemicQuestions: [],
    error: null,
    createSystemicQuestionError: null,
    changeSystemicQuestionError: null,
    enableSystemicQuestionError: null,
    disableSystemicQuestionError: null,
    isLoading: false,
    createSystemicQuestionIsLoading: false,
    changeSystemicQuestionIsLoading: false,
    enableSystemicQuestionIsLoading: false,
    disableSystemicQuestionIsLoading: false,
};

const getters = {
    systemicQuestions(state) {
        return state.systemicQuestions;
    },
    hasSystemicQuestions(state) {
        return state.systemicQuestions.length > 0;
    },
    error(state) {
        return state.error;
    },
    createSystemicQuestionError(state) {
        return state.createSystemicQuestionError;
    },
    enableSystemicQuestionError(state) {
        return state.createSystemicQuestionError;
    },
    disableSystemicQuestionError(state) {
        return state.createSystemicQuestionError;
    },
    isLoading(state) {
        return state.isLoading;
    },
    createSystemicQuestionIsLoading(state) {
        return state.createSystemicQuestionIsLoading;
    },
    changeSystemicQuestionIsLoading(state) {
        return state.changeSystemicQuestionIsLoading;
    },
    enableSystemicQuestionIsLoading(state) {
        return state.enableSystemicQuestionIsLoading;
    },
    disableSystemicQuestionIsLoading(state) {
        return state.disableSystemicQuestionIsLoading;
    },
};

const mutations = {
    [FETCHING_SYSTEMIC_QUESTIONS](state) {
        state.isLoading = true;
        state.error = null;
    },
    [FETCHING_SYSTEMIC_QUESTIONS_SUCCESS](state, systemicQuestions) {
        state.error = null;
        state.isLoading = false;
        state.systemicQuestions = systemicQuestions;
    },
    [FETCHING_SYSTEMIC_QUESTIONS_ERROR](state, error) {
        state.error = error;
        state.isLoading = false;
    },
    [CREATE_SYSTEMIC_QUESTION](state) {
        state.createSystemicQuestionIsLoading = true;
        state.createSystemicQuestionError = null;
    },
    [CREATE_SYSTEMIC_QUESTION_SUCCESS](state, createdSystemicQuestion) {
        state.createSystemicQuestionError = null;
        state.createSystemicQuestionIsLoading = false;
        state.systemicQuestions = [ ...state.systemicQuestions, createdSystemicQuestion ];
    },
    [CREATE_SYSTEMIC_QUESTION_ERROR](state, error) {
        state.createSystemicQuestionError = error;
        state.createSystemicQuestionIsLoading = false;
    },
    [CHANGE_SYSTEMIC_QUESTION](state) {
        state.changeSystemicQuestionIsLoading = true;
        state.changeSystemicQuestionError = null;
    },
    [CHANGE_SYSTEMIC_QUESTION_SUCCESS](state, changedSystemicQuestion) {
        state.changeSystemicQuestionError = null;
        state.changeSystemicQuestionIsLoading = false;
        state.systemicQuestions.forEach((systemicQuestion, index) => {
            if (String(changedSystemicQuestion['@id']) === String(systemicQuestion['@id'])) {
                state.systemicQuestions.splice(index, 1, changedSystemicQuestion);
            }
        });    },
    [CHANGE_SYSTEMIC_QUESTION_ERROR](state, error) {
        state.changeSystemicQuestionError = error;
        state.changeSystemicQuestionIsLoading = false;
    },
    [ENABLE_SYSTEMIC_QUESTION](state) {
        state.enableSystemicQuestionIsLoading = true;
        state.enableSystemicQuestionError = null;
    },
    [ENABLE_SYSTEMIC_QUESTION_SUCCESS](state, enabledSystemicQuestion) {
        state.enableSystemicQuestionError = null;
        state.enableSystemicQuestionIsLoading = false;
        state.systemicQuestions.forEach((systemicQuestion, index) => {
            if (String(enabledSystemicQuestion['@id']) === String(systemicQuestion['@id'])) {
                state.systemicQuestions.splice(index, 1, enabledSystemicQuestion);
            }
        });
    },
    [ENABLE_SYSTEMIC_QUESTION_ERROR](state, error) {
        state.enableSystemicQuestionError = error;
        state.enableSystemicQuestionIsLoading = false;
    },
    [DISABLE_SYSTEMIC_QUESTION](state) {
        state.disableSystemicQuestionIsLoading = true;
        state.disableSystemicQuestionError = null;
    },
    [DISABLE_SYSTEMIC_QUESTION_SUCCESS](state, disabledSystemicQuestion) {
        state.disableSystemicQuestionError = null;
        state.disableSystemicQuestionIsLoading = false;
        state.systemicQuestions.forEach((systemicQuestion, index) => {
            if (String(disabledSystemicQuestion['@id']) === String(systemicQuestion['@id'])) {
                state.systemicQuestions.splice(index, 1, disabledSystemicQuestion);
            }
        });
    },
    [DISABLE_SYSTEMIC_QUESTION_ERROR](state, error) {
        state.disableSystemicQuestionError = error;
        state.disableSystemicQuestionIsLoading = false;
    },
};

const actions = {
    async findAll({commit}) {
        commit(FETCHING_SYSTEMIC_QUESTIONS);
        try {
            let response = await SystemicQuestionAPI.findAll();
            commit(FETCHING_SYSTEMIC_QUESTIONS_SUCCESS, response.data['hydra:member']);
        } catch (error) {
            commit(FETCHING_SYSTEMIC_QUESTIONS_ERROR, error);
        }
    },
    async create({commit}, payload) {
        commit(CREATE_SYSTEMIC_QUESTION);
        try {
            let response = await SystemicQuestionAPI.create(payload);
            commit(CREATE_SYSTEMIC_QUESTION_SUCCESS, response.data);

            return response.data;
        } catch (error) {
            commit(CREATE_SYSTEMIC_QUESTION_ERROR, error);
        }
    },
    async change({commit}, payload) {
        commit(CHANGE_SYSTEMIC_QUESTION);
        try {
            let response = await SystemicQuestionAPI.change(payload);
            commit(CHANGE_SYSTEMIC_QUESTION_SUCCESS, response.data);

            return response.data;
        } catch (error) {
            commit(CHANGE_SYSTEMIC_QUESTION_ERROR, error);
        }
    },
    async enable({commit}, payload) {
        commit(ENABLE_SYSTEMIC_QUESTION);
        try {
            let response = await SystemicQuestionAPI.enable(payload);
            commit(ENABLE_SYSTEMIC_QUESTION_SUCCESS, response.data);

            return response.data;
        } catch (error) {
            commit(ENABLE_SYSTEMIC_QUESTION_ERROR, error);
        }
    },
    async disable({commit}, payload) {
        commit(DISABLE_SYSTEMIC_QUESTION);
        try {
            let response = await SystemicQuestionAPI.disable(payload);
            commit(DISABLE_SYSTEMIC_QUESTION_SUCCESS, response.data);

            return response.data;
        } catch (error) {
            commit(DISABLE_SYSTEMIC_QUESTION_ERROR, error);
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
