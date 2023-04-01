"use strict";

import axios from 'axios';

const updateFilterParams = function (params) {
    let sort = '';
    if (params.sortBy) {
        sort = `&order[${params.sortBy}]=${params.sortDesc ? 'desc' : 'asc'}`;
    }
    for (const [key, value] of Object.entries(params.filter)) {
        if (value === null) {
        } else if ('exists' === key) {
            for (const [existsKey, existsValue] of Object.entries(value)) {
                sort += `&${key}[${existsKey}]=${existsValue}`;
            }
        } else {
            sort += `&${key}=${value}`;
        }
    }

    return sort;
};

export default {
    find(params) {
        let sort = updateFilterParams(params);

        return axios.get(`/api/tags?page=${params.page}&itemsPerPage=${params.itemsPerPage}` + sort);
    },
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

        return axios.get(`/api/tags?page=${params.page}&itemsPerPage=${params.itemsPerPage}` + sort);
    },
    findAllWithWayPoints() {
        const params = {
            perPage: 1000,
            currentPage: 1,
            filter: {
                exists: {
                    wayPoints: 1,
                },
            },
            sortBy: 'name',
        };

        const sort = updateFilterParams(params);

        return axios.get(`/api/tags?page=${params.currentPage}&itemsPerPage=${params.perPage}` + sort);
    },
    create(payload) {
        return axios.post("/api/tags/create", payload);
    },
    enable(payload) {
        return axios.post("/api/tags/enable", payload);
    },
    disable(payload) {
        return axios.post("/api/tags/disable", payload);
    },
};
