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
        } else if ('startTime' === key) {
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
    remove(payload) {
        return axios.post(`/api/walks/remove`, payload);
    },
    epilogue(payload) {
        return axios.post(`/api/walks/epilogue`, payload);
    },
    export(params) {
        const sort = updateFilterParams(params);

        return axios.get(
            '/api/walks/export?page=1&itemsPerPage=5000' + sort,
            {
                headers: { accept: 'text/csv' },
                responseType: 'arraybuffer',
            },
        );
    },
    findAllTeamNames() {
        return axios.get("/api/walks/team_names");
    },
};
