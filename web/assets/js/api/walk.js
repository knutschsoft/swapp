'use strict';

import axios from 'axios';

export default {
    find(params) {
        let sort = '';
        if (params.sortBy) {
            sort = `&order[${params.sortBy}]=${params.sortDesc ? 'desc' : 'asc'}`;
        }
        return axios.get(`/api/walks?page=${params.currentPage}&itemsPerPage=${params.perPage}` + sort);
    },
    findOneById(walkId) {
        return axios.get('/api/walks/' + walkId);
    },
    addWayPoint(payload) {
        return axios.post('/api/walks/add-way-point', payload);
    },
};
