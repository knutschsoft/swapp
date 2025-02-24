import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useAlertStore = defineStore('alert', () => {
    const alerts = ref<{ message: string; title?: string, type: 'success' | 'info' | 'error' | null, show: boolean }[]>([])

    const addAlert = (message: string, title: string = '', type: 'success' | 'info' | 'error' = 'info') => {
        alerts.value.push({ message, title, type, show: true })
    }

    const success = (message: string, title: string = '') => addAlert(message, title, 'success')
    const info = (message: string, title: string = '') => addAlert(message, title, 'info')
    const error = (message: string, title: string = '') => addAlert(message, title, 'error')

    const remove = (index: number) => {
        alerts.value.splice(index, 1)
    }

    return { alerts, success, info, error, remove }
})
