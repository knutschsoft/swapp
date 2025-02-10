export default {
    name: 'useRegisterSW',
    data() {
        return {
            updateSW: undefined,
            offlineReady: false,
            isUpdateLoading: false,
            showErrorSnackbar: false,
            needRefresh: false
        }
    },
    async mounted() {
        try {
            // const {registerSW} = await import('virtual:pwa-register')
            // const vm = this
            // this.updateSW = registerSW({
            //     immediate: true,
            //     onOfflineReady() {
            //         // alert('onOfflineReady');
            //         vm.offlineReady = true
            //         vm.onOfflineReadyFn()
            //     },
            //     onNeedRefresh() {
            //         // alert('onNeedRefresh');
            //         vm.needRefresh = true
            //         vm.onNeedRefreshFn()
            //     },
            //     onRegisteredSW(swUrl, r) {
            //         // alert(`onRegisteredSW // Service Worker at: ${swUrl}`)
            //         // if (r) {
            //         //     alert('Service Worker Registrierung erfolgreich.')
            //         // } else {
            //         //     alert(`Service Worker Registrierung NICHT erfolgreich`)
            //         // }
            //     },
            //     onRegisterError(e) {
            //         // alert(`Service Worker onRegisterError`)
            //         vm.handleSWRegisterError(e)
            //     }
            // })
        } catch {
            // alert('PWA ist deaktiviert oder wird nicht unterstützt')
        }
    },
    methods: {
        async closePromptUpdateSW() {
            this.offlineReady = false
            this.needRefresh = false
            this.isUpdateLoading = false
            // alert('closePromptUpdateSW closed')
        },
        onOfflineReadyFn() {
            // alert('onOfflineReadyFn')
        },
        onNeedRefreshFn() {
            // alert('onNeedRefreshFn')
        },
        async updateServiceWorker() {
            try {
                // alert('updateServiceWorker // Start updating Service Worker');

                // Ladeanimation aktivieren
                this.isUpdateLoading = true;

                // Service Worker-Update anstoßen
                if (this.updateSW) {
                    await this.updateSW(true);
                    // alert('updateServiceWorker // Service Worker updated successfully');
                    // } else {
                    //     alert('updateServiceWorker // No updateSW function available');
                }
            } catch (error) {
                // alert('updateServiceWorker // Failed to update Service Worker:', error);

                // Snackbar anzeigen
                this.showErrorSnackbar = true;
            } finally {
                // Ladeanimation deaktivieren und Prompt schließen
                this.isUpdateLoading = false;
                await this.closePromptUpdateSW();
            }
        },
        handleSWRegisterError(error) {
            // Snackbar anzeigen
            // alert('Fehler beim Registrieren des Service Workers:', error)
            this.showErrorSnackbar = true;
        }
    }
}
