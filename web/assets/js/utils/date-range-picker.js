import dayjs from 'dayjs';

const locale = {
    direction: 'ltr',
    format: 'dd.mm.yyyy',
    separator: ' - ',
    applyLabel: 'Übernehmen',
    cancelLabel: 'Abbrechen',
    weekLabel: 'W',
    customRangeLabel: 'Custom Range',
    daysOfWeek: ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'],
    monthNames: ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'],
    firstDay: 1,
};

const now = dayjs();
let ranges = {
    'Dieser Monat': [now.startOf('month').toDate(), now.endOf('month').toDate()],
    'Letzter Monat': [now.subtract(1, 'month').startOf('month').toDate(), now.subtract(1, 'month').endOf('month').toDate()],
    'Letzte 6 Monate': [now.subtract(5, 'month').startOf('month').toDate(), now.endOf('month').toDate()],
    'Dieses Jahr': [now.startOf('year').toDate(), now.endOf('year').toDate()],
    'Letztes Jahr': [now.subtract(1, 'year').startOf('year').toDate(), now.subtract(1, 'year').endOf('year').toDate()],
    'Vorletztes Jahr': [now.subtract(2, 'year').startOf('year').toDate(), now.subtract(2, 'year').endOf('year').toDate()],
};
for (let i = 1; i <= 4; i++) {
    ranges[`${now.subtract(i, 'quarter').year()} ${now.subtract(i, 'quarter').quarter()}. Quartal`] = [
        now.subtract(i, 'quarter').startOf('quarter').toDate(),
        now.subtract(i, 'quarter').endOf('quarter').toDate()
    ];
}

export default {
    locale,
    ranges,
};
