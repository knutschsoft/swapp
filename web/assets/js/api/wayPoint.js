'use strict';
import apiClient from '../api';
import dayjs from 'dayjs';
import {useParamTransformer} from "../utils";

const updateFilterParams = function (params) {
    let sort = '';
    if (params.sortBy) {
        sort = `&order[${params.sortBy}]=${params.sortDesc ? 'desc' : 'asc'}`;
    }
    for (const [key, value] of Object.entries(params.filter)) {
        if (value === null || value === undefined || '' === value) {
        } else if ('wayPointTags' === key) {
            value.forEach(iri => {
                sort += `&${key}[]=${iri}`;
            });
        } else if ('teamName' === key && '' !== value) {
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
        let transformedParams = useParamTransformer(params);

        return apiClient.get(`/api/way_points?${transformedParams}`);
    },
    export(params) {
        params.page = 1
        params.itemsPerPage = 5000
        const transformedParams = useParamTransformer(params);

        return apiClient.get(
            '/api/way_points/export?' + transformedParams,
            {
                headers: { accept: 'text/csv' },
                responseType: 'arraybuffer',
            },
        );
    },
};
