import {isEmpty} from "@/js/utils/is-empty";

export interface FetchParams extends Record<string, any> {
    page?: number
    itemsPerPage?: number
    order?: string | null
    'email.email'?: string | null
}

export const createUrlAppend = (options: { url?: string; fetchParams?: FetchParams }): string => {
    return `${options.url ?? ''}${isEmpty(options.fetchParams) ? '' : `?${useParamTransformer(options.fetchParams ?? {})}`}`
}

export const useParamTransformer = (params: FetchParams): string => {
    const query = new URLSearchParams()

    Object.keys(params).forEach((key) => {
        const value = params[key]

        if (Array.isArray(value)) {
            value.forEach((val: any) => query.append(`${key}[]`, val))
        } else if (value !== null && value !== undefined) {
            query.append(key, String(value))
        }
    })

    return query.toString()
}

