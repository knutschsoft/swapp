import axios from 'axios';

export default {
    findUserWithToken(id, token) {
        return axios.get(id, { headers: { Authorization: 'Bearer ' + token } });
    },
    find(id) {
        return axios.get(id);
    },
    login(username, password) {
        return axios.post('/api/users/getToken', {
            username: username,
            password: password,
        });
    },
    changePassword(user, password, confirmationToken) {
        return axios.post('/api/users/change-password', {
            user,
            password: password,
            confirmationToken: {
                token: confirmationToken,
            },
        }, { headers: { Authorization: '' } });
    },
    isConfirmationTokenValid(user, confirmationToken) {
        return axios.post('/api/users/is-confirmation-token-valid', {
            user,
            confirmationToken: {
                token: confirmationToken,
            },
        }, { headers: { Authorization: '' } });
    },
    userEmailConfirm(user, confirmationToken) {
        return axios.post('/api/users/user-email-confirm', {
            user,
            confirmationToken: {
                token: confirmationToken,
            },
        }, { headers: { Authorization: '' } });
    },
    requestPasswordReset(username, honeypotEmail) {
        return axios.post('/api/users/request-password-reset', {
            username: username,
            email: honeypotEmail,
        }, { headers: { Authorization: '' } });
    },
};
