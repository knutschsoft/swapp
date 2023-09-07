import axios from 'axios';
import apiClient from '../api';

export default {
    findUserWithToken(id, token) {
        return axios.get(id, { headers: { Authorization: 'Bearer ' + token } });
    },
    isConfirmationTokenValid(user, confirmationToken) {
        return apiClient.post('/api/users/is-confirmation-token-valid', {
            user,
            confirmationToken: {
                token: confirmationToken,
            },
        }, { headers: { Authorization: '' } });
    },
    userEmailConfirm(user, confirmationToken) {
        return apiClient.post('/api/users/user-email-confirm', {
            user,
            confirmationToken: {
                token: confirmationToken,
            },
        }, { headers: { Authorization: '' } });
    },
};
