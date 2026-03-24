import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function useFormatter() {
    const page = usePage();
    const locale = computed(() => {
        return (
            page.props.locale
            || page.props.default_locale
            || page.props.languages?.find?.(lang => lang?.is_default)?.code
            || page.props.languages?.[0]?.code
            || 'en'
        );
    });

    /**
     * Formats a date string or object based on the current locale.
     * @param {string|Date} date 
     * @param {Object} options - Intl.DateTimeFormat options
     * @returns {string}
     */
    const formatDate = (dateString) => {
        if (!dateString) return '-';
        try {
            return new Intl.DateTimeFormat(locale.value, {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }).format(new Date(dateString));
        } catch (e) {
            return dateString;
        }
    };

    const formatDateTime = (dateString) => {
        if (!dateString) return '-';
        try {
            return new Intl.DateTimeFormat(locale.value, {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }).format(new Date(dateString));
        } catch (e) {
            return dateString;
        }
    };

    /**
     * Formats a number based on current locale.
     * @param {number} number 
     * @param {Object} options - Intl.NumberFormat options
     */
    const formatNumber = (number, options = {}) => {
        if (number === null || number === undefined) return '';
        try {
            return new Intl.NumberFormat(locale.value, options).format(number);
        } catch (e) {
            console.error('Error formatting number:', e);
            return String(number);
        }
    };

    /**
     * Formats currency.
     * @param {number} number 
     * @param {string} currency - ISO currency code (default: PLN)
     */
    const formatCurrency = (number, currency = 'PLN', options = {}) => {
        return formatNumber(number, {
            style: 'currency',
            currency: currency,
            ...options
        });
    };

    return {
        formatDate,
        formatDateTime,
        formatNumber,
        formatCurrency,
        locale
    };
}
