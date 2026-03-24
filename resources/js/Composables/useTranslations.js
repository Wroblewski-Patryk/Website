
import { usePage } from '@inertiajs/vue3';

export function useTranslations() {
    const page = usePage();
    const resolveLocale = (forceLocale = null) => {
        return (
            forceLocale
            || page.props.locale
            || page.props.default_locale
            || page.props.languages?.find?.(lang => lang?.is_default)?.code
            || page.props.languages?.[0]?.code
            || 'en'
        );
    };

    /**
     * Translates a given object based on current locale.
     * @param {Object|string} obj - The object to translate (e.g. {pl: "...", en: "..."})
     * @returns {string} - The translated string
     */
    const translate = (obj, fallbackOrParams = null, paramsOrFallback = null, forceLocale = null) => {
        if (!obj) return typeof fallbackOrParams === 'string' ? fallbackOrParams : '';

        // Backward-compatible argument normalization:
        // - t(key, 'Fallback')
        // - t(key, 'Fallback', { name: '...' })
        // - t(key, { name: '...' })
        let fallback = null;
        let params = {};

        if (typeof fallbackOrParams === 'string') {
            fallback = fallbackOrParams;
            if (paramsOrFallback && typeof paramsOrFallback === 'object' && !Array.isArray(paramsOrFallback)) {
                params = paramsOrFallback;
            }
        } else if (fallbackOrParams && typeof fallbackOrParams === 'object' && !Array.isArray(fallbackOrParams)) {
            params = fallbackOrParams;
            if (typeof paramsOrFallback === 'string') {
                fallback = paramsOrFallback;
            }
        } else if (typeof paramsOrFallback === 'string') {
            fallback = paramsOrFallback;
        }

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
            const locale = resolveLocale(forceLocale);

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
