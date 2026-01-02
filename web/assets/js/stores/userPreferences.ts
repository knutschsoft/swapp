import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import {
    defaultPreferences,
    mergePreferences,
    type UserPreferences
} from '@/js/types/preferences'
import apiClient from '@/js/api'
import { useAuthStore } from './auth'

export const useUserPreferencesStore = defineStore('userPreferences', () => {

    const preferences = ref<UserPreferences | null>(null)
    const loading = ref(false)
    const error = ref<Error | null>(null)

    /**
     * Returns effective preferences (merged with defaults).
     * Always returns a value, even if not loaded yet.
     */
    const effective = computed<UserPreferences>(() => {
        if (!preferences.value) {
            return defaultPreferences
        }
        return mergePreferences(defaultPreferences, preferences.value)
    })

    /**
     * Whether preferences have been loaded at least once.
     */
    const isLoaded = computed<boolean>(() => {
        return preferences.value !== null
    })

    /**
     * Whether preferences are currently loading.
     */
    const isLoading = computed<boolean>(() => {
        return loading.value
    })

    /**
     * Last error (if any).
     */
    const getError = computed<Error | null>(() => {
        return error.value
    })

    /**
     * Loads preferences for the current user.
     * Uses caching unless forced.
     */
    const load = async (force = false): Promise<void> => {
        if (isLoaded.value && !force) {
            return
        }

        const authStore = useAuthStore()
        if (!authStore.user) {
            console.warn('Cannot load preferences: no authenticated user')
            return
        }

        loading.value = true
        error.value = null

        try {
            const response = await apiClient.get(
                `${authStore.user.preferences}`
            )

            // Store only preference deltas
            preferences.value = response.data.preferences || {}
        } catch (e) {
            error.value = e as Error
            console.error('Failed to load preferences:', e)

            // Fallback to defaults
            preferences.value = {}
        } finally {
            loading.value = false
        }
    }

    /**
     * Saves partial preferences (PATCH semantics).
     */
    const save = async (
        partial: Partial<UserPreferences>
    ): Promise<void> => {
        const authStore = useAuthStore()
        if (!authStore.user) {
            throw new Error('Cannot save preferences: no authenticated user')
        }

        loading.value = true
        error.value = null

        try {
            const response = await apiClient.post(
                '/api/user_preferences/change',
                {
                    user: authStore.user['@id'],
                    preferences: partial
                }
            )

            // Backend returns merged preferences (source of truth)
            preferences.value = response.data.preferences || {}
        } catch (e) {
            error.value = e as Error
            console.error('Failed to save preferences:', e)
            throw e
        } finally {
            loading.value = false
        }
    }

    const clear = (): void => {
        preferences.value = null
        loading.value = false
        error.value = null
    }

    const reset = async (): Promise<void> => {
        await save({})
        preferences.value = {}
    }

    return {
        preferences,
        loading,
        error,

        effective,
        isLoaded,
        isLoading,
        getError,

        load,
        save,
        clear,
        reset
    }
})
