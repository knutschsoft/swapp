import type { App } from "vue";
import { useAuthStore } from '@/js/stores';
import {CanceledError} from "axios";

export function registerErrorHandler(app: App) {
    // app.config.errorHandler = (err, instance, info) => {
    //     if (err instanceof CanceledError) {
    //         return
    //     }
    //
    //
    //     const authStore = useAuthStore();
    //     const user = authStore.currentUser;
    //     const username = user ? user.email : "anonymous";
    //     console.log(username)
    //     console.log('err')
    //     console.log(err)
    //     const message = err instanceof Error ? err.message : JSON.stringify(err);
    //
    //     nelmioLog("error", message, {
    //         info,
    //         location: window.location,
    //         user: username,
    //     });
    //
    //     console.error(err);
    // };
}
