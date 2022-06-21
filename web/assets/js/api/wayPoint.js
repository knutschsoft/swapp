"use strict";

import axios from 'axios';
import dayjs from 'dayjs';

export default {
    find(params) {
        let sort = '';
        if (params.sortBy) {
            sort = `&order[${params.sortBy}]=${params.sortDesc ? 'desc' : 'asc'}`;
        }
        for (const [key, value] of Object.entries(params.filter)) {
            if (value === null) {
            } else if ('wayPointTags' === key) {
                value.forEach(iri => {
                    sort += `&${key}[]=${iri}`;
                });
            } else if ('teamName' === key) {
                sort += `&walk.${key}=${value}`;
            } else if ('visitedAt' === key && value.startDate && value.endDate) {
                sort += `&${key}[after]=${dayjs(value.startDate).toISOString()}&${key}[before]=${dayjs(value.endDate).toISOString()}`;
            } else {
                sort += `&${key}=${value}`;
            }
        }

        return axios.get(`/api/way_points?page=${params.currentPage}&itemsPerPage=${params.perPage}` + sort);
    },
    create(payload) {
        return axios.post('/api/way_points/create', payload);
    },
    change(payload) {
        return axios.post(`/api/way_points/change`, payload);
    },
    findById(id) {
        return axios.get(`/api/way_points/${id}`);
    },
    findAll() {
        return axios.get("/api/way_points");
    },
};
