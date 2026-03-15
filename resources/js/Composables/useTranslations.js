
import { usePage } from '@inertiajs/vue3';

export function useTranslations() {
    const page = usePage();

    /**
     * Translates a given object based on current locale.
     * @param {Object|string} obj - The object to translate (e.g. {pl: "...", en: "..."})
     * @returns {string} - The translated string
     */
    const translate = (obj, params = {}, fallback = null, forceLocale = null) => {
        if (!obj) return fallback || '';
        
        let result = '';

        // Handle JSON strings if they slip through
        if (typeof obj === 'string' && obj.startsWith('{') && obj.endsWith('}')) {
            try {
                const decoded = JSON.parse(obj);
                if (decoded && typeof decoded === 'object') {
                    obj = decoded;
                }
            } catch (e) {}
        }

        if (typeof obj === 'string') {
            const translations = page.props.translations || {};
            if (translations[obj]) {
                result = translations[obj];
            } else {
                result = fallback || obj;
            }
        } else {
            const locale = forceLocale || page.props.locale || 'pl';

            // Try the exact locale
            if (obj[locale]) {
                result = obj[locale];
            } else if (typeof obj !== 'object' || obj === null || Array.isArray(obj)) {
                result = fallback || (obj !== null && typeof obj !== 'object' ? String(obj) : '');
            } else {
                const availableKeys = Object.keys(obj).filter(k => obj[k]);
                if (availableKeys.length > 0) {
                    if (obj[locale] === '') {
                        result = ''; 
                    } else {
                        result = obj[availableKeys[0]];
                    }
                } else {
                    result = fallback || '';
                }
            }
        }

        // Apply interpolation
        if (result && typeof result === 'string' && params && typeof params === 'object') {
            Object.keys(params).forEach(key => {
                result = result.replace(new RegExp(`:${key}`, 'g'), params[key]);
            });
        }

        return result;
    };

    return { translate, t: translate };
}
