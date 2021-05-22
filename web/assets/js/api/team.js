"use strict";

import axios from 'axios';

export default {
    findAll() {
        return axios.get("/api/teams");
    },
    change(payload) {
        return axios.post("/api/teams/change", payload);
    },
};
