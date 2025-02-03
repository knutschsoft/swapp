export const isEmpty = (obj?: Record<any, any> | any[] | null | string) =>
    [Object, Array].includes(((obj as any) || {}).constructor) && !Object.entries(obj || {}).length
