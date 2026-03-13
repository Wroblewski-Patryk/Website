
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

        // Try to find any locale if the requested one is empty/missing
        // BUT only if we didn't explicitly force a locale that should be empty
        const availableKeys = Object.keys(obj).filter(k => obj[k]);
        if (availableKeys.length > 0) {
            // If requested locale exists but is empty, and we have other languages, maybe fallback?
            // For the builder, we might want to show empty if it's empty.
            if (obj[locale] === '') return ''; 
            
            if (!obj[locale]) return obj[availableKeys[0]];
        }

        return fallback || String(obj);
    };

    return { translate, t: translate };
}
