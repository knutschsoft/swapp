import { computed, type ComputedRef } from 'vue';
import { useUserPreferencesStore } from '@/js/stores/userPreferences';
import type { TablePreferences, UserPreferences } from '@/js/types/preferences';

type TableType = 'wayPoints' | 'walks';

interface UseTablePreferencesReturn {
  /**
   * Current preferences for this table (merged with defaults).
   */
  preferences: ComputedRef<TablePreferences>;

  /**
   * Toggle a column's visibility.
   */
  toggleColumn: (columnName: string) => Promise<void>;

  /**
   * Toggle a filter's visibility.
   */
  toggleFilter: (filterName: string) => Promise<void>;

  /**
   * Set multiple columns at once.
   */
  setColumns: (columns: Record<string, boolean>) => Promise<void>;

  /**
   * Set multiple filters at once.
   */
  setFilters: (filters: Record<string, boolean>) => Promise<void>;

  /**
   * Check if a column is visible.
   */
  isColumnVisible: (columnName: string) => boolean;

  /**
   * Check if a filter is visible.
   */
  isFilterVisible: (filterName: string) => boolean;
}

/**
 * Composable for managing table preferences (columns and filters).
 *
 * Usage:
 * ```ts
 * const { preferences, toggleColumn, isColumnVisible } = useTablePreferences('wayPoints');
 *
 * // Check if column is visible
 * if (isColumnVisible('locationName')) { ... }
 *
 * // Toggle a column
 * await toggleColumn('locationName');
 * ```
 *
 * @param tableType - The table to manage preferences for ('wayPoints' or 'walks')
 */
export function useTablePreferences(tableType: TableType): UseTablePreferencesReturn {
  const prefsStore = useUserPreferencesStore();

  /**
   * Get effective preferences for this table (merged with defaults).
   */
  const preferences = computed<TablePreferences>(() => {
    return prefsStore.effective.tables[tableType];
  });

  /**
   * Check if a column is visible.
   */
  const isColumnVisible = (columnName: string): boolean => {
    return preferences.value.columns[columnName] ?? true; // Default to visible
  };

  /**
   * Check if a filter is visible.
   */
  const isFilterVisible = (filterName: string): boolean => {
    return preferences.value.filters[filterName] ?? true; // Default to visible
  };

  /**
   * Save preferences to backend (PATCH semantics).
   */
  const savePreferences = async (partial: Partial<UserPreferences>): Promise<void> => {
    await prefsStore.save(partial);
  };

  /**
   * Toggle a column's visibility.
   */
  const toggleColumn = async (columnName: string): Promise<void> => {
    const currentValue = preferences.value.columns[columnName] ?? true;

    await savePreferences({
      tables: {
        [tableType]: {
          columns: {
            [columnName]: !currentValue,
          },
        },
      },
    } as Partial<UserPreferences>);
  };

  /**
   * Toggle a filter's visibility.
   */
  const toggleFilter = async (filterName: string): Promise<void> => {
    const currentValue = preferences.value.filters[filterName] ?? true;

    await savePreferences({
      tables: {
        [tableType]: {
          filters: {
            [filterName]: !currentValue,
          },
        },
      },
    } as Partial<UserPreferences>);
  };

  /**
   * Set multiple columns at once.
   */
  const setColumns = async (columns: Record<string, boolean>): Promise<void> => {
    await savePreferences({
      tables: {
        [tableType]: {
          columns,
        },
      },
    } as Partial<UserPreferences>);
  };

  /**
   * Set multiple filters at once.
   */
  const setFilters = async (filters: Record<string, boolean>): Promise<void> => {
    await savePreferences({
      tables: {
        [tableType]: {
          filters,
        },
      },
    } as Partial<UserPreferences>);
  };

  return {
    preferences,
    toggleColumn,
    toggleFilter,
    setColumns,
    setFilters,
    isColumnVisible,
    isFilterVisible,
  };
}
