import axios from 'axios';

export default {
    findUserWithToken(id, token) {
        return axios.get(id, { headers: { Authorization: 'Bearer ' + token } });
    },
    find(id) {
        return axios.get(id);
    },
    login(username, password) {
        return axios.post('/api/getToken', {
            username: username,
            password: password,
        });
    },
    changePassword(userId, password, confirmationToken) {
        return axios.post('/api/security/change-password', {
            userId: userId,
            password: password,
            confirmationToken: confirmationToken,
        });
    },
    isConfirmationTokenValid(userId, confirmationToken) {
        return axios.post('/api/security/is-confirmation-token-valid', {
            userId: userId,
            confirmationToken: confirmationToken,
        }, { headers: { Authorization: '' } });
    },
    requestPasswordReset(username, honeypotEmail) {
        return axios.post('/api/security/request-password-reset', {
            username: username,
            email: honeypotEmail,
        }, { headers: { Authorization: '' } });
    },
};
