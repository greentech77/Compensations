import { format as formatDate } from 'date-fns'
import { usePage } from '@inertiajs/vue3'
import { sl } from 'date-fns/locale'

export function decimalFormat(value) {
    if (isNaN(value) || value == undefined) {
        return value
    }
    return ('' + value).replace('.', ',')
}

/*export function dateFormat(value, format = 'dd. MM. yyyy') {
    console.log(value);
    if (value === undefined) {
        return value
    }
    try {
        return formatDate(value, format)
    } catch (e) {
        return value
    }
}*/

export function dateFormat(value, format = 'dd. MM. yyyy') {
    console.log(value);
    if (value === undefined || value === null) {
        return null
    }
    
    try {
        // Ensure we're working with a Date object
        const date = value instanceof Date ? value : new Date(value)
        return formatDate(date, format)
    } catch (e) {
        console.error('Invalid date:', value)
        return null
    }
}

export function dateTimeFormat(value, format = 'dd. MM. yyyy \'ob\' H.mm') {
    if (value === undefined) {
        return value
    }
    const timezone = usePage().props.value.timezone || 'europe/ljubljana'
    try {
        return formatDate(parseISO(value), format)
    } catch (e) {
        return value
    }
}

export function booleanFormat(value) {
    if (typeof value == 'boolean') {
        return this.$t(`booleans.${value.toString()}`)
    } else {
        return  value
    }
}

/*export function percentFormat(value) {
    if (value == null || isNaN(value)) return ''; // Handle invalid or null values
    return `${value.toLocaleString('sl-SI', { 
        minimumFractionDigits: 2, 
        maximumFractionDigits: 2 
    })} %`
}*/

export function percentFormat(value) {
    if (value == null || value === '' || isNaN(Number(value))) return '';
    return `${Number(value).toLocaleString('sl-SI', { 
        minimumFractionDigits: 2, 
        maximumFractionDigits: 2 
    })} %`
}

export function currencyFormat(value) {
    // Convert to locale-specific format with `,` as the decimal separator and `.` as thousands separator
    return new Intl.NumberFormat('sl-SI', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(value) + ' EUR'; // Add the Euro symbol at the end
}

// Modify dateFormatSL to handle different input types
export function dateFormatSL(value) {
    // If already formatted, return as is
    if (typeof value === 'string' && value.includes('.')) {
        return value;
    }

    // If no value, return empty
    if (!value) return value;

    // Parse the input date string
    const date = new Date(value);
    
    // Check if date is valid
    if (isNaN(date.getTime())) return value;

    // Extract day, month, year
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();

    // Return formatted date
    return `${day}.${month}.${year}`;
}
