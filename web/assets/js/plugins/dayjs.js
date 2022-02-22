import dayjs from 'dayjs';

require('dayjs/locale/de');
dayjs.locale('de');

let customParseFormat = require('dayjs/plugin/customParseFormat')
dayjs.extend(customParseFormat)
