import dayjs from 'dayjs';

require('dayjs/locale/de');
dayjs.locale('de');

let calendar = require('dayjs/plugin/calendar');
dayjs.extend(calendar);

let relativeTime = require('dayjs/plugin/relativeTime');
dayjs.extend(relativeTime);

let customParseFormat = require('dayjs/plugin/customParseFormat');
dayjs.extend(customParseFormat);

let updateLocale = require('dayjs/plugin/updateLocale');
dayjs.extend(updateLocale);

dayjs.updateLocale('de', {
    calendar: {
        lastDay: '[Gestern um] HH:mm',
        sameDay: '[Heute um] HH:mm',
        nextDay: '[Morgen um] HH:mm',
        lastWeek: '[letzten] dddd [um] HH:mm',
        nextWeek: 'dddd [um] HH:mm',
        sameElse: 'DD.MM.YYYY HH:mm',
    },
});
