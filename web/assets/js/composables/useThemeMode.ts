import { computed,watchEffect } from 'vue'
import { useTheme } from 'vuetify'
import { usePreferredDark, useStorage } from '@vueuse/core'

export function useThemeMode() {
    const theme = useTheme()

    const preferredDark = usePreferredDark()

    const themeMode = useStorage<'light' | 'dark' | 'system'>(
        'theme-mode',
        'system'
    )

    watchEffect(() => {
        if (themeMode.value === 'system') {

            theme.global.name.value = preferredDark.value ? 'dark' : 'light'
        } else {
            theme.global.name.value = themeMode.value
        }
    })

    const isDark = computed(() => theme.global.current.value.dark)

    return {
        themeMode,
        isDark,
    }
}
