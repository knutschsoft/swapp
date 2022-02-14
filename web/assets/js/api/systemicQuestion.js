"use strict";

import axios from 'axios';

export default {
    findAll(params) {
        if (params === undefined) {
            params = {
                itemsPerPage: 1000,
                page: 1,
            };
        }
        let sort = '';
        if (params.sortBy) {
            sort = `&order[${params.sortBy}]=${params.sortDesc ? 'desc' : 'asc'}`;
        }
        if (params.filter) {
            for (const [key, value] of Object.entries(params.filter)) {
                if (value !== null) {
                    sort += `&${key}=${value}`;
                }
            }
        }

        return axios.get(`/api/systemic_questions?page=${params.page}&itemsPerPage=${params.itemsPerPage}` + sort);
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
