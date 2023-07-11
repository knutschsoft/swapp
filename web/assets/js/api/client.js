'use strict';

import axios from 'axios';

export default {
    find(params) {
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

        return axios.get(`/api/clients?page=${params.page}&itemsPerPage=${params.itemsPerPage}` + sort);
    },
    findOneById(clientId) {
        return this.findOneByIri('/api/clients/' + clientId);
    },
    findOneByIri(clientIri) {
        return axios.get(clientIri);
    },
    create(payload) {
        return axios.post('/api/clients/create', payload);
    },
    change(payload) {
        return axios.post('/api/clients/change', payload);
    },
};
