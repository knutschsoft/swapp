"use strict";

import axios from 'axios';

export default {
    findAll() {
        return axios.get("/api/way_points");
    },
};
