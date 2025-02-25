'use strict';
import apiClient from '../api';
import dayjs from 'dayjs';
import {useParamTransformer} from "@/js/utils";

const updateFilterParams = function (params) {
    let sort = '';
    if (params.sortBy) {
        sort = `&order[${params.sortBy}]=${params.sortDesc ? 'desc' : 'asc'}`;
    }
    if (typeof params?.filter !== "object") {
        return sort;
    }
    for (const [key, value] of Object.entries(params.filter)) {
        if (value === null || value === undefined || '' === value) {
        }  else if (Array.isArray(value)) {
            value.forEach((iri) => {
                sort += `&${key}[]=${iri}`;
            });
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
    find(params, signal) {
        let transformedParams = useParamTransformer(params);

        return apiClient.get(`/api/walks?${transformedParams}`, { signal });
    },
    findOld(params) {
        let sort = updateFilterParams(params);

        return apiClient.get(`/api/walks?page=${params.currentPage}&itemsPerPage=${params.perPage}` + sort);
    },
    findLastWalkByTeam(team) {
        return this.findOld({
            sortBy: 'startTime',
            sortDesc: true,
            filter: {
                teamName: team.name,
            },
            currentPage: 1,
            perPage: 1,
        });
    },
    export(params) {
        params.page = 1;
        params.itemsPerPage = 5000;
        let transformedParams = useParamTransformer(params);

        return apiClient.get(
            '/api/walks/export?' + transformedParams,
            {
                headers: { accept: 'text/csv' },
                responseType: 'arraybuffer',
            },
        );
    },
    findAllTeamNames() {
        return apiClient.get("/api/walks/team_names");
    },
    findAllUnfinishedWalks(teams) {
        return this.findOld({
            sortBy: 'startTime',
            sortDesc: true,
            filter: {
                teamName: teams.map(team => team.name),
                isUnfinished: true,
            },
            currentPage: 1,
            perPage: 1000,
        });
    },
};
