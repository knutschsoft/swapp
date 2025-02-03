import dayjs from 'dayjs'

export const formatDate = (dateString: Date | string | null) => {
    return dateString ? dayjs(dateString).format('DD.MM.YYYY') : ''
}
export const formatDateMonth = (dateString: Date | string | null) => {
    return dateString ? dayjs(dateString).format('MM/YY') : ''
}
export const formatDateTime = (dateString: Date | string | null) => {
    return dateString ? dayjs(dateString).format('DD.MM.YYYY HH:mm:ss') : ''
}
export const formatDateTimeNoSeconds = (dateString: Date | string | null) => {
    return dateString ? dayjs(dateString).format('DD.MM.YYYY HH:mm') : ''
}
export const formatDateTimeNoSecondsWithDayOfWeek = (dateString: Date | string | null) => {
    return dateString ? dayjs(dateString).format('dd, DD.MM.YYYY HH:mm') : ''
}
export const formatTime = (dateString: Date | string | null) => {
    return dateString ? dayjs(dateString).format('HH:mm') : ''
}
