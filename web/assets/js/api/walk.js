'use strict';

import axios from 'axios';

export default {
    find(params) {
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

        return axios.get(`/api/walks?page=${params.currentPage}&itemsPerPage=${params.perPage}` + sort);
    },
    findLastWalkByTeam(team) {
        return this.find({
            sortBy: 'startTime',
            sortDesc: true,
            filter: {
                teamName: team.name,
            },
            currentPage: 1,
            perPage: 1,
        });
    },
    findOneById(walkId) {
        return axios.get('/api/walks/' + walkId);
    },
    create(payload) {
        return axios.post(`/api/walks/create`, payload);
    },
    change(payload) {
        return axios.post(`/api/walks/change`, payload);
    },
    export(payload) {
        return axios.post('/api/walks/export', payload);
    },
    findAllTeamNames() {
        return axios.get("/api/walks/team_names");
    },
};
