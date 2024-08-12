const reloadSW = '__RELOAD_SW__'
// const reloadSW: any = '__RELOAD_SW__'

export default {
    name: 'useRegisterSW',
    data() {
        return {
            updateSW: undefined,
            offlineReady: false,
            isUpdateLoading: false,
            needRefresh: false
        }
    },
    async mounted() {
        try {
            const { registerSW } = await import('virtual:pwa-register')
            const vm = this
            this.updateSW = registerSW({
                immediate: true,
                onOfflineReady() {
                    console.log('onOfflineReady');
                    vm.offlineReady = true
                    vm.onOfflineReadyFn()
                },
                onNeedRefresh() {
                    console.log('onNeedRefresh');
                    vm.needRefresh = true
                    vm.onNeedRefreshFn()
                },
                onRegisteredSW(swUrl, r) {
                    console.log(`onRegisteredSW // Service Worker at: ${swUrl}`)
                    if (reloadSW === 'true') {
                        r && setInterval(async () => {
                            console.log('onRegisteredSW // Checking for sw update')
                            await r.update()
                        }, 20000 /* 20s for testing purposes */)
                    }
                    else {
                        console.log(`onRegisteredSW // SW Registered: ${r}`)
                    }
                },
                onRegisterError(e) {
                    vm.handleSWRegisterError(e)
                }
            })
        }
        catch {
            console.log('PWA disabled.')
        }
    },
    methods: {
        async closePromptUpdateSW() {
            this.offlineReady = false
            this.needRefresh = false
            this.isUpdateLoading = false
            console.log('closePromptUpdateSW closed')
        },
        onOfflineReadyFn() {
            console.log('onOfflineReady')
        },
        onNeedRefreshFn() {
            console.log('onNeedRefresh')
        },
        async updateServiceWorker() {
            console.log('updateServiceWorker');
            this.isUpdateLoading = true
            this.updateSW && this.updateSW(true)
            await this.closePromptUpdateSW
        },
        handleSWRegisterError(error) {}
    }
}
