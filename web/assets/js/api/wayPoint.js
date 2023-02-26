'use strict';

import axios from 'axios';
import dayjs from 'dayjs';

const updateFilterParams = function (params) {
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
        } else if ('visitedAt' === key) {
            if (value.startDate && value.endDate) {
                sort += `&${key}[after]=${dayjs(value.startDate).startOf('day').toISOString()}&${key}[before]=${dayjs(value.endDate).endOf('day').toISOString()}`;
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

        return axios.get(`/api/way_points?page=${params.currentPage}&itemsPerPage=${params.perPage}` + sort);
    },
    create(payload) {
        return axios.post('/api/way_points/create', payload);
    },
    change(payload) {
        return axios.post(`/api/way_points/change`, payload);
    },
    remove(payload) {
        return axios.post(`/api/way_points/remove`, payload);
    },
    findById(id) {
        return axios.get(`/api/way_points/${id}`);
    },
    findAll() {
        return axios.get('/api/way_points');
    },
    export(params) {
        const sort = updateFilterParams(params);

        return axios.get(
            '/api/way_points/export?page=1&itemsPerPage=5000' + sort,
            {
                headers: { accept: 'text/csv' },
                responseType: 'arraybuffer',
            },
        );
    },
};
