import dayjs, { type Dayjs } from 'dayjs'

import 'dayjs/locale/de';
import calendar from 'dayjs/plugin/calendar'
import relativeTime from 'dayjs/plugin/relativeTime'
import customParseFormat from 'dayjs/plugin/customParseFormat'
import updateLocale from 'dayjs/plugin/updateLocale'
import quarterOfYear from 'dayjs/plugin/quarterOfYear'
import localizedFormat from 'dayjs/plugin/localizedFormat';

dayjs.locale('de');
dayjs.extend(calendar);
dayjs.extend(relativeTime);
dayjs.extend(customParseFormat);
dayjs.extend(updateLocale);
dayjs.extend(quarterOfYear);
dayjs.extend(localizedFormat);

dayjs.updateLocale('de', {
    calendar: {
        lastDay: '[Gestern um] HH:mm',
        sameDay: '[Heute um] HH:mm',
        nextDay: '[Morgen um] HH:mm',
        lastWeek: '[importzten] dddd [um] HH:mm',
        nextWeek: 'dddd [um] HH:mm',
        sameElse: 'DD.MM.YYYY HH:mm',
    },
});
