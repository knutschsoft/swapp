"use strict";

import axios from 'axios';

export default {
    findAll() {
        return axios.get("/api/tags");
    },
    create(payload) {
        return axios.post("/api/tags/create", payload);
    },
};
