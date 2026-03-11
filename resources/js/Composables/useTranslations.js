
import { usePage } from '@inertiajs/vue3';

export function useTranslations() {
    const page = usePage();

    /**
     * Translates a given object based on current locale.
     * @param {Object|string} obj - The object to translate (e.g. {pl: "...", en: "..."})
     * @returns {string} - The translated string
     */
    const translate = (obj, fallback = null) => {
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

        const locale = page.props.locale || 'pl';

        // Try the exact locale
        if (obj[locale]) return obj[locale];

        // Fallback to whatever first key is available if no translation exists for the exact locale
        const keys = Object.keys(obj);
        if (keys.length > 0) return obj[keys[0]];

        return fallback || String(obj);
    };

    return { translate, t: translate };
}
