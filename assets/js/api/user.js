"use strict";
import axios from 'axios';

export default {
    find(id) {
        return axios.get(id);
    },
    findAll() {
        return axios.get("/api/users");
    },
    enable (userId) {
        return axios.put('/api/users/enable', {'userId': userId});
    },
    disable(userId) {
        return axios.put('/api/users/disable', {'userId': userId});
    },
};
