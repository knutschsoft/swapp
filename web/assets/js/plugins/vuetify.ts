import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'
import {createVuetify} from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import {de} from 'vuetify/locale'
import 'typeface-roboto/index.css';
import colors from 'vuetify/util/colors'

const vuetify = createVuetify({
    // Check https://pictogrammers.com/library/mdi/ for possible icons & their names
    icons: {
        defaultSet: 'mdi'
    },
    locale: {
        locale: 'de',
        messages: {de}
    },
    theme: {
        defaultTheme: 'light',
        themes: {
            light: {
                dark: false,
                colors: {
                    primary: '#1867C0',
                    'primary-darken-1': '#1558A0',
                    'primary-darken-2': '#124880',
                    'primary-darken-3': '#0E3760',
                    'primary-darken-4': '#0B2740',
                    'primary-darken-5': '#081820',
                    'primary-lighten-1': '#3B7ECC',
                    'primary-lighten-2': '#5E95D8',
                    'primary-lighten-3': '#81ACE4',
                    'primary-lighten-4': '#A4C3F0',
                    'primary-lighten-5': '#C7DCF9',
                    'primary-accent-1': '#64B5F6',
                    'primary-accent-2': '#42A5F5',
                    'primary-accent-3': '#1E88E5',
                    'primary-accent-4': '#1565C0',
                    'primary-accent-5': '#0D47A1',

                    secondary: colors.grey.darken3, // #424242
                    'secondary-darken-1': '#3A3A3A',
                    'secondary-darken-2': '#323232',
                    'secondary-darken-3': '#2A2A2A',
                    'secondary-darken-4': '#222222',
                    'secondary-darken-5': '#1A1A1A',
                    'secondary-lighten-1': '#5A5A5A',
                    'secondary-lighten-2': '#727272',
                    'secondary-lighten-3': '#8A8A8A',
                    'secondary-lighten-4': '#A2A2A2',
                    'secondary-lighten-5': '#BABABA',
                    'secondary-accent-1': '#616161',
                    'secondary-accent-2': '#757575',
                    'secondary-accent-3': '#9E9E9E',
                    'secondary-accent-4': '#BDBDBD',
                    'secondary-accent-5': '#E0E0E0',
                }
            },
            dark: {
                dark: true,
                colors: {

                    background: '#0F172A',
                    surface: '#1F2A3A',
                    'surface-variant': '#273449',

                    primary: '#6FA7E8',
                    'primary-lighten-5': '#EBF5FC', // sehr hell
                    'primary-lighten-4': '#D7EBFA',
                    'primary-lighten-3': '#C3E1F8',
                    'primary-lighten-2': '#AED7F6',
                    'primary-lighten-1': '#99CDF4',
                    'primary-darken-1': '#5B96DB',
                    'primary-darken-2': '#4A85CD',
                    'primary-darken-3': '#3973BF',
                    'primary-darken-4': '#2B62B0',
                    'primary-darken-5': '#1C509F', // sehr dunkel

                    secondary: '#64748B',          // Basis
                    'secondary-lighten-5': '#F0F3F6', // sehr hell
                    'secondary-lighten-4': '#D9E0EB',
                    'secondary-lighten-3': '#C2CEDF',
                    'secondary-lighten-2': '#ABBBD3',
                    'secondary-lighten-1': '#94A9C7',
                    'secondary-darken-1': '#576678',
                    'secondary-darken-2': '#485569',
                    'secondary-darken-3': '#39445B',
                    'secondary-darken-4': '#2A334D',
                    'secondary-darken-5': '#1C223F', // sehr dunkel

                    success: '#22C55E',
                    warning: '#F59E0B',
                    error: '#EF4444',
                    info: '#38BDF8',

                    outline: '#334155',
                    'outline-variant': '#475569',

                    'on-surface': '#E2E8F0',
                    'on-background': '#E2E8F0',
                },

                variables: {
                    'border-color': '#334155',
                    'border-opacity': 1,
                    'hover-opacity': 0.08,
                    'focus-opacity': 0.12,
                    'selected-opacity': 0.18,

                }
            }
        },
    },
    components,
    directives
})

export default vuetify;
