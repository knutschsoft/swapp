import axios from 'axios'
import type {AxiosInstance, AxiosError, InternalAxiosRequestConfig} from 'axios'
import { useAuthStore, useGeneralStore } from '@/js/stores/';
import router from '@/js/router'

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

// apiClient.interceptors.response.use(
//     response => response,
//     (error: AxiosError) => {
//         console.log(error);
//         console.log(error.response);
//         if (error.response?.status === 403) {
//             // const authStore = useAuthStore();
//
//             // optional: Nachricht an Nutzer oder Loggen
//             console.warn('403 erhalten – Benutzer wird ausgeloggt');
//
//             // ausloggen
//             // authStore.logout(); // oder wie deine Logout-Methode heißt
//
//             // optional: Weiterleitung zur Login-Seite
//             router.push({ name: 'Logout' }); // falls du vue-router verwendest
//         }
//
//         // Fehler weiterreichen, damit Komponente damit umgehen kann, falls nötig
//         return Promise.reject(error);
//     }
// );

export default apiClient
