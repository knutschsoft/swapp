"use strict";

import axios from 'axios';

const updateFilterParams = function (params) {
    let sort = '';
    if (params.sortBy) {
        sort = `&order[${params.sortBy}]=${params.sortDesc ? 'desc' : 'asc'}`;
    }
    for (const [key, value] of Object.entries(params.filter)) {
        if (value === null) {
        } else if ('exists' === key) {
            for (const [existsKey, existsValue] of Object.entries(value)) {
                sort += `&${key}[${existsKey}]=${existsValue}`;
            }
        } else {
            sort += `&${key}=${value}`;
        }
    }

    return sort;
};

export default {
    findAllWithWayPoints() {
        const params = {
            perPage: 1000,
            currentPage: 1,
            filter: {
                exists: {
                    wayPoints: 1,
                },
            },
            sortBy: 'name',
        };

        const sort = updateFilterParams(params);

        return axios.get(`/api/tags?page=${params.currentPage}&itemsPerPage=${params.perPage}` + sort);
    },
};
