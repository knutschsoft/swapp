'use strict';
import apiClient from '../api';
import dayjs from 'dayjs';

const updateFilterParams = function (params) {
    let sort = '';
    if (params.sortBy) {
        sort = `&order[${params.sortBy}]=${params.sortDesc ? 'desc' : 'asc'}`;
    }
    for (const [key, value] of Object.entries(params.filter)) {
        if (value === null || value === undefined) {
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

        return apiClient.get(`/api/way_points?page=${params.currentPage}&itemsPerPage=${params.perPage}` + sort);
    },
    export(params) {
        const sort = updateFilterParams(params);

        return apiClient.get(
            '/api/way_points/export?page=1&itemsPerPage=5000' + sort,
            {
                headers: { accept: 'text/csv' },
                responseType: 'arraybuffer',
            },
        );
    },
};
