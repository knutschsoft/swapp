'use strict';
import apiClient from '../api';
import dayjs from 'dayjs';

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
    find(params) {
        let sort = updateFilterParams(params);

        return apiClient.get(`/api/clients?page=${params.page}&itemsPerPage=${params.itemsPerPage}` + sort);
    },
};
