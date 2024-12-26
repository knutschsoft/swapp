import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useAlertStore = defineStore('alert', () => {
    const defaultValues = {
        message: '',
        title: '',
        type: null
    }

    const alert = ref<{ message: string; title?: string, type: 'success' | 'info' | 'error' | null }>(defaultValues)
    const showAlert = ref(false)

    const success = (message: string, title: string = '') => {
        alert.value = { message, title, type: 'success' }
        showAlert.value = true
    }

    const info = (message: string, title: string = '') => {
        alert.value = { message, title, type: 'info' }
        showAlert.value = true
    }

    const error = (message: string, title: string = '') => {
        alert.value = { message, title, type: 'error' }
        showAlert.value = true
    }

    const clear = () => {
        showAlert.value = false
        alert.value = defaultValues
    }

    return { alert, success, info, error, clear, showAlert }
})
