import axios, {AxiosInstance, InternalAxiosRequestConfig} from 'axios'
// import axios, { AxiosRequestConfig, AxiosPromise, AxiosResponse } from 'axios';
import {useGeneralStore} from '../stores/general';
import {useAuthStore} from '../stores/auth';

const apiClient: AxiosInstance = axios.create({
    headers: {
        'Content-type': 'application/ld+json',
    },
})
apiClient.interceptors.request.use((config: InternalAxiosRequestConfig): InternalAxiosRequestConfig<any> | Promise<InternalAxiosRequestConfig<any>> => {
    const generalStore = useGeneralStore();
    const authStore = useAuthStore();
    config.baseURL = generalStore.getApiUrl;
    const token = authStore.getToken;
    if (token) {
        config.headers['Authorization'] = `Bearer ${token}`;
        if (authStore.switchUsername) {
            config.headers['-SWITCH-USER'] = authStore.switchUsername;
        }
    } else {
        config.headers['Authorization'] = ``;
    }

    return config;
})

export default apiClient
