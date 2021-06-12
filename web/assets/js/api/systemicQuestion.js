"use strict";

import axios from 'axios';

export default {
    findAll() {
        return axios.get("/api/systemic_questions");
    },
    create(payload) {
        return axios.post("/api/systemic_questions/create", payload);
    },
    change(payload) {
        return axios.post("/api/systemic_questions/change", payload);
    },
    disable(payload) {
        return axios.post("/api/systemic_questions/disable", payload);
    },
    enable(payload) {
        return axios.post("/api/systemic_questions/enable", payload);
    },
};
