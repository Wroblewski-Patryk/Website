
import { usePage } from '@inertiajs/vue3';

export function useTranslations() {
    const page = usePage();

    /**
     * Translates a given object based on current locale.
     * @param {Object|string} obj - The object to translate (e.g. {pl: "...", en: "..."})
     * @returns {string} - The translated string
     */
    const translate = (obj, fallback = null, forceLocale = null) => {
        if (!obj) return fallback || '';
        
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
                return translations[obj];
            }
            return fallback || obj;
        }

        const locale = forceLocale || page.props.locale || 'pl';

        // Try the exact locale
        if (obj[locale]) return obj[locale];

        if (typeof obj !== 'object' || obj === null || Array.isArray(obj)) {
            return fallback || (obj !== null && typeof obj !== 'object' ? String(obj) : '');
        }

        const availableKeys = Object.keys(obj).filter(k => obj[k]);
        if (availableKeys.length > 0) {
            if (obj[locale] === '') return ''; 
            if (!obj[locale]) return obj[availableKeys[0]];
        }

        return fallback || '';
    };

    return { translate, t: translate };
}
