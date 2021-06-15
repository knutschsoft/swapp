'use strict';
import axios from 'axios';

export default {
    find(id) {
        return axios.get(id);
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

        return axios.get(`/api/users?page=${params.page}&itemsPerPage=${params.itemsPerPage}` + sort);
    },
    create(user) {
        return axios.post('/api/users/create', user);
    },
    change(user) {
        return axios.post('/api/users/change', user);
    },
    enable(userId) {
        return axios.put('/api/users/enable', { 'userId': userId });
    },
    disable(userId) {
        return axios.put('/api/users/disable', { 'userId': userId });
    },
};
