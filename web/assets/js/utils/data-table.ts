import type { VDataTable } from 'vuetify/components'

export type TableHeaders = VDataTable['$props']['headers']
export type SortItem = {
    key: string
    order?: boolean | 'asc' | 'desc'
}
export type TableItemsPerPageOptions = VDataTable['$props']['itemsPerPageOptions']

export const itemsPerPageOptions: TableItemsPerPageOptions = [
    { value: 5, title: '5' },
    { value: 10, title: '10' },
    { value: 25, title: '25' },
    { value: 50, title: '50' },
    { value: 100, title: '100' },
    { value: 250, title: '250' }
]
export const itemsPerPageText = 'Einträge pro Seite' as const
export const noItemsText = 'Keine Daten verfügbar' as const
export const loadingText = 'Daten werden geladen...' as const
