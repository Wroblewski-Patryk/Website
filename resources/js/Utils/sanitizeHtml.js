const BLOCKED_TAGS = new Set(['script', 'iframe', 'object', 'embed', 'style', 'link', 'meta']);

export function sanitizeHtml(input) {
    if (typeof input !== 'string' || input.trim() === '') {
        return '';
    }

    if (typeof window === 'undefined' || typeof DOMParser === 'undefined') {
        return input;
    }

    const parser = new DOMParser();
    const doc = parser.parseFromString(input, 'text/html');

    doc.querySelectorAll('*').forEach((node) => {
        const tagName = node.tagName?.toLowerCase();
        if (tagName && BLOCKED_TAGS.has(tagName)) {
            node.remove();
            return;
        }

        [...node.attributes].forEach((attr) => {
            const name = attr.name.toLowerCase();
            const value = (attr.value || '').trim().toLowerCase();

            if (name.startsWith('on')) {
                node.removeAttribute(attr.name);
                return;
            }

            if ((name === 'href' || name === 'src') && value.startsWith('javascript:')) {
                node.removeAttribute(attr.name);
            }
        });
    });

    return doc.body.innerHTML;
}
