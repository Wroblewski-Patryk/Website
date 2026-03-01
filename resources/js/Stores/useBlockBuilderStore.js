import { defineStore } from 'pinia';

export const useBlockBuilderStore = defineStore('blockBuilder', {
    state: () => ({
        blocks: [],
        activeBlockId: null,
        isDirty: false,
        categories: [
            {
                id: 'content',
                label: 'Content',
                icon: 'fas fa-font',
                blocks: [
                    { type: 'paragraph', label: 'Paragraph', icon: 'fas fa-paragraph' },
                    { type: 'heading', label: 'Heading', icon: 'fas fa-heading' },
                    { type: 'list', label: 'List', icon: 'fas fa-list-ul' },
                    { type: 'quote', label: 'Quote', icon: 'fas fa-quote-left' },
                    { type: 'button', label: 'Button', icon: 'fas fa-mouse-pointer' },
                    { type: 'divider', label: 'Divider', icon: 'fas fa-minus' },
                    { type: 'spacer', label: 'Spacer', icon: 'fas fa-arrows-alt-v' },
                    { type: 'table', label: 'Table', icon: 'fas fa-table' },
                    { type: 'custom_code', label: 'HTML/Code', icon: 'fas fa-code' },
                ]
            },
            {
                id: 'media',
                label: 'Media',
                icon: 'fas fa-images',
                blocks: [
                    { type: 'image', label: 'Image', icon: 'fas fa-image' },
                    { type: 'gallery', label: 'Gallery', icon: 'fas fa-th' },
                    { type: 'video', label: 'Video', icon: 'fas fa-video' },
                    { type: 'audio', label: 'Audio', icon: 'fas fa-volume-up' },
                    { type: 'file', label: 'File', icon: 'fas fa-file-download' },
                    { type: 'media_text', label: 'Media & Text', icon: 'fas fa-columns' },
                    { type: 'carousel', label: 'Carousel', icon: 'fas fa-sliders-h' },
                ]
            },
            {
                id: 'layout',
                label: 'Layout',
                icon: 'fas fa-layer-group',
                blocks: [
                    { type: 'section', label: 'Section', icon: 'fas fa-vector-square' },
                    { type: 'columns', label: 'Columns', icon: 'fas fa-columns' },
                    { type: 'group', label: 'Group', icon: 'fas fa-object-group' },
                    { type: 'stack', label: 'Stack/Flex', icon: 'fas fa-layer-group' },
                ]
            },
            {
                id: 'marketing',
                label: 'Marketing',
                icon: 'fas fa-bullhorn',
                blocks: [
                    { type: 'hero', label: 'Hero', icon: 'fas fa-star' },
                    { type: 'cta_box', label: 'CTA Box', icon: 'fas fa-comment-alt' },
                    { type: 'card', label: 'Card', icon: 'fas fa-id-card' },
                    { type: 'testimonial', label: 'Testimonial', icon: 'fas fa-user-check' },
                    { type: 'faq', label: 'FAQ', icon: 'fas fa-question-circle' },
                    { type: 'pricing', label: 'Pricing', icon: 'fas fa-tags' },
                    { type: 'counter', label: 'Counter', icon: 'fas fa-sort-numeric-up' },
                ]
            },
            {
                id: 'theme',
                label: 'Theme (FSE)',
                icon: 'fas fa-palette',
                blocks: [
                    { type: 'site_logo', label: 'Site Logo', icon: 'fas fa-fingerprint' },
                    { type: 'navigation', label: 'Navigation', icon: 'fas fa-bars' },
                    { type: 'breadcrumbs', label: 'Breadcrumbs', icon: 'fas fa-ellipsis-h' },
                    { type: 'posts_list', label: 'Post List', icon: 'fas fa-th-list' },
                ]
            },
            {
                id: 'embed',
                label: 'Embeds',
                icon: 'fas fa-external-link-alt',
                blocks: [
                    { type: 'google_maps', label: 'Maps', icon: 'fas fa-map-marked-alt' },
                    { type: 'language_switcher', label: 'Lang', icon: 'fas fa-globe' },
                ]
            }
        ],
    }),
    actions: {
        init(initialBlocks) {
            this.blocks = Array.isArray(initialBlocks) ? initialBlocks : [];
            this.isDirty = false;
        },
        createBlockObject(type, parentId = null) {
            const defaults = {
                // 1. Content
                paragraph: { text: 'This is a paragraph block. You can edit this text in the sidebar.' },
                heading: { text: 'New Heading', level: 2, align: 'left' },
                list: { items: ['Item 1', 'Item 2'], type: 'bullets' },
                quote: { text: 'Quote text', author: '', type: 'standard' },
                button: { label: 'Click Me', url: '#', style: 'primary', align: 'left', newTab: false },
                divider: { style: 'solid', weight: '1px' },
                spacer: { height: 'py-10' },
                table: { rows: [['Cell 1', 'Cell 2']] },
                custom_code: { html: '<div>Custom HTML</div>', js: '' },

                // 2. Media
                image: { url: '', alt: '', caption: '', link: '' },
                gallery: { images: [] },
                video: { source: 'youtube', url: '', autoplay: false },
                audio: { url: '' },
                file: { url: '', label: 'Download File' },
                media_text: { media_url: '', text: '', media_position: 'left' },
                carousel: { items: [] },

                // 3. Layout
                section: { width: 'boxed', bgColor: 'transparent', align: 'left' },
                columns: { count: 2, stackOnMobile: true },
                group: {},
                stack: { direction: 'col', gap: '4' },

                // 4. Marketing
                hero: { headline: 'Premium Headline', subheadline: 'Elegant subtext.', primaryLabel: 'Get Started', secondaryLabel: 'Learn More', bg_image: '' },
                cta_box: { title: 'Ready to start?', button_label: 'Contact' },
                card: { title: 'Feature', description: '', icon: 'fas fa-star' },
                testimonial: { text: '', author: '', company: '' },
                faq: { items: [{ q: 'Question?', a: 'Answer.' }] },
                pricing: { plans: [] },
                counter: { number: 100, suffix: '+', label: 'Clients' },

                // 5. Theme
                site_logo: { width: '150px' },
                navigation: { menu_id: null },
                breadcrumbs: {},
                posts_list: { count: 3, layout: 'grid' },

                // 6. Embed & Forms
                google_maps: { address: '', zoom: 14 },
                form_input: { label: 'Name', name: 'name', type: 'text', placeholder: 'Enter your name', required: true },
                form_textarea: { label: 'Message', name: 'message', placeholder: 'How can we help?', required: true },
                form_select: { label: 'Select Option', name: 'category', options: 'Option 1\nOption 2', required: false },

                // Legacy / Compatibility
                portfolio: { projects: [] },
                language_switcher: { style: 'dropdown' },
                menu: { menu_id: null },
            };

            const block = {
                id: crypto.randomUUID(),
                type,
                parent_id: parentId,
                content: defaults[type] || {},
                children: [],
                settings: {
                    id: '',
                    class: '',
                    animations: {
                        enabled: false,
                        trigger: 'onEnter', // onEnter, onScroll, onLoad, onHover
                        preset: 'fade-up', // fade-up, slide-left, reveal-text, zoom-in, clip-reveal
                        duration: 0.8,
                        delay: 0,
                        ease: 'power2.out',
                        timelineId: '', // For sequencing
                        once: true,
                    },
                    layout: {
                        fullHeight: false,
                        fixedBg: false,
                        padding: 'py-20',
                        zIndex: 1
                    },
                    style: {
                        textColor: '',
                        bgColor: '',
                    }
                }
            };
            return block;
        },
        addBlock(type, parentId = null) {
            const newBlock = this.createBlockObject(type, parentId);

            if (parentId) {
                const parent = this.blocks.find(b => b.id === parentId);
                if (parent) {
                    if (!parent.children) parent.children = [];
                    parent.children.push(newBlock);
                }
            } else {
                this.blocks.push(newBlock);
            }
            this.activeBlockId = newBlock.id;
            this.isDirty = true;
        },

        removeBlock(id) {
            this.blocks = this.blocks.filter(b => b.id !== id);
            if (this.activeBlockId === id) this.activeBlockId = null;
            this.isDirty = true;
        },
        updateBlock(id, payload) {
            const findAndUpdate = (list) => {
                for (let i = 0; i < list.length; i++) {
                    if (list[i].id === id) {
                        list[i] = { ...list[i], ...payload };
                        return true;
                    }
                    if (list[i].children && findAndUpdate(list[i].children)) return true;
                }
                return false;
            };
            findAndUpdate(this.blocks);
            this.isDirty = true;
        },
        setActiveBlock(id) {
            this.activeBlockId = id;
        },
        moveBlock(index, delta) {
            const newIndex = index + delta;
            if (newIndex < 0 || newIndex >= this.blocks.length) return;

            const element = this.blocks.splice(index, 1)[0];
            this.blocks.splice(newIndex, 0, element);
            this.isDirty = true;
        }
    },
    getters: {
        activeBlock: (state) => state.blocks.find(b => b.id === state.activeBlockId),
    }
});
