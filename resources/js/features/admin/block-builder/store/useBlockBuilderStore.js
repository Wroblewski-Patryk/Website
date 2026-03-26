import { defineStore } from 'pinia';

export const useBlockBuilderStore = defineStore('blockBuilder', {
    state: () => ({
        blocks: [],
        activeBlockId: null,
        hoveredBlockId: null,
        isDirty: false,
        isDepthView: false,
        isDragging: false,
        showRightSidebar: true,
        expandedNodes: {}, // Persist expanded/collapsed state of layers
        sidebarCollapses: {}, // Persist expanded/collapsed state of sidebar sections
        isEditingBlock: false,
        lastSavedBlocks: [],
        autosaveConflict: null,
        editingLocale: null,
        availableLocales: [],
        categories: [
            {
                id: 'typography',
                label: 'Typography',
                icon: 'PhTextT',
                blocks: [
                    { type: 'paragraph', label: 'Paragraph', icon: 'PhTextAa' },
                    { type: 'heading', label: 'Heading', icon: 'PhTextHOne' },
                    { type: 'list', label: 'List', icon: 'PhListBullets' },
                    { type: 'quote', label: 'Quote', icon: 'PhQuotes' },
                    { type: 'custom_code', label: 'Code / HTML', icon: 'PhCode' },
                ]
            },
            {
                id: 'actions',
                label: 'Actions',
                icon: 'PhCursorClick',
                blocks: [
                    { type: 'button', label: 'Button', icon: 'PhHandPointing' },
                    { type: 'dropdown', label: 'Dropdown', icon: 'PhCaretDown' },
                    { type: 'modal', label: 'Modal', icon: 'PhBrowsers' },
                    { type: 'swap', label: 'Swap', icon: 'PhArrowsLeftRight' },
                ]
            },
            {
                id: 'data_display',
                label: 'Data Display',
                icon: 'PhDesktop',
                blocks: [
                    { type: 'accordion', label: 'Accordion', icon: 'PhListDashes' },
                    { type: 'avatar', label: 'Avatar', icon: 'PhUserCircle' },
                    { type: 'badge', label: 'Badge', icon: 'PhCertificate' },
                    { type: 'card', label: 'Card', icon: 'PhIdentificationCard' },
                    { type: 'carousel', label: 'Carousel', icon: 'PhSlidersHorizontal' },
                    { type: 'chat', label: 'Chat', icon: 'PhChats' },
                    { type: 'countdown', label: 'Countdown', icon: 'PhTimer' },
                    { type: 'diff', label: 'Diff', icon: 'PhCircleHalf' },
                    { type: 'stat', label: 'Stat', icon: 'PhChartLineUp' },
                    { type: 'table', label: 'Table', icon: 'PhTable' },
                    { type: 'timeline', label: 'Timeline', icon: 'PhListDashes' },
                ]
            },
            {
                id: 'feedback',
                label: 'Feedback',
                icon: 'PhWarningCircle',
                blocks: [
                    { type: 'alert', label: 'Alert', icon: 'PhWarning' },
                    { type: 'progress', label: 'Progress', icon: 'PhListChecks' },
                    { type: 'radial_progress', label: 'Radial', icon: 'PhCircleNotch' },
                ]
            },
            {
                id: 'data_input',
                label: 'Data Input',
                icon: 'PhPencilSimple',
                blocks: [
                    { type: 'text_input', label: 'Text Input', icon: 'PhTextT' },
                    { type: 'textarea', label: 'Textarea', icon: 'PhTextAlignLeft' },
                    { type: 'select', label: 'Select', icon: 'PhListNumbers' },
                    { type: 'checkbox', label: 'Checkbox', icon: 'PhCheckSquare' },
                    { type: 'radio', label: 'Radio', icon: 'PhRadioButton' },
                    { type: 'toggle', label: 'Toggle', icon: 'PhToggleRight' },
                    { type: 'range', label: 'Range', icon: 'PhSlidersHorizontal' },
                    { type: 'rating', label: 'Rating', icon: 'PhStarHalf' },
                    { type: 'file_input', label: 'File Input', icon: 'PhUploadSimple' },
                    { type: 'form', label: 'Form Container', icon: 'PhFiles' },
                ]
            },
            {
                id: 'layout',
                label: 'Layout',
                icon: 'PhColumns',
                blocks: [
                    { type: 'container', label: 'Container', icon: 'PhBoundingBox' },
                    { type: 'divider', label: 'Divider', icon: 'PhMinus' },
                ]
            },
            {
                id: 'media',
                label: 'Media',
                icon: 'PhImage',
                blocks: [
                    { type: 'icon', label: 'Icon', icon: 'PhStar' },
                    { type: 'image', label: 'Image', icon: 'PhImage' },
                    { type: 'video', label: 'Video', icon: 'PhVideoCamera' },
                ]
            },
            {
                id: 'navigation',
                label: 'Navigation',
                icon: 'PhNavigationArrow',
                blocks: [
                    { type: 'breadcrumbs', label: 'Breadcrumbs', icon: 'PhDotsThree' },
                    { type: 'menu', label: 'Menu', icon: 'PhList' },
                    { type: 'navbar', label: 'Navbar', icon: 'PhBrowser' },
                    { type: 'steps', label: 'Steps', icon: 'PhFootprints' },
                    { type: 'tabs', label: 'Tabs', icon: 'PhFolder' },
                ]
            },
            {
                id: 'mockup',
                label: 'Mockup',
                icon: 'PhDesktop',
                blocks: [
                    { type: 'mockup_browser', label: 'Browser', icon: 'PhBrowser' },
                    { type: 'mockup_code', label: 'Code', icon: 'PhTerminal' },
                    { type: 'mockup_phone', label: 'Phone', icon: 'PhDeviceMobile' },
                    { type: 'mockup_window', label: 'Window', icon: 'PhAppWindow' },
                ]
            },
            {
                id: 'building',
                label: 'Building',
                icon: 'PhCube',
                blocks: [
                    { type: 'template_reference', label: 'Template Part', icon: 'PhLayout' },
                    { type: 'content_slot', label: 'Content Slot', icon: 'PhSquare' },
                    { type: 'header_slot', label: 'Header Slot', icon: 'PhArrowUp' },
                    { type: 'footer_slot', label: 'Footer Slot', icon: 'PhArrowDown' },
                    { type: 'sidebar_slot', label: 'Sidebar Slot', icon: 'PhSidebars' },
                ]
            },
            {
                id: 'extended',
                label: 'Extended',
                icon: 'PhPlusCircle',
                blocks: [
                    { type: 'composed_block', label: 'Composed Block', icon: 'PhBracketsCurly' },
                    { type: 'posts_list', label: 'Posts', icon: 'PhArticle' },
                    { type: 'projects_list', label: 'Projects', icon: 'PhBriefcase' },
                    { type: 'text_rotate', label: 'Text Rotate', icon: 'PhArrowsClockwise' },
                ]
            }
        ],
    }),
    actions: {
        init(initialContent) {
            if (initialContent && !Array.isArray(initialContent) && typeof initialContent === 'object') {
                // Migration logic: merge { pl: [], en: [] } into a single structure
                const locales = Object.keys(initialContent);
                const firstLocale = locales[0] || 'en';
                const baseBlocks = JSON.parse(JSON.stringify(initialContent[firstLocale] || []));

                // Helper to merge content from other locales
                const mergeContent = (targetBlocks, sourceBlocks, locale) => {
                    targetBlocks.forEach(target => {
                        const source = sourceBlocks.find(s => s.id === target.id);
                        if (source) {
                            // Merge content - only translation keys
                            const translatableKeys = ['text', 'label', 'url', 'buttonLabel', 'title', 'description', 'img1', 'img2', 'html', 'js', 'placeholder', 'error_message', 'success_message', 'submit_label', 'actionButton', 'prefix', 'words', 'suffix', 'alt', 'caption', 'desc', 'value', 'unit'];
                            
                            Object.keys(source.content).forEach(key => {
                                const val = source.content[key];
                                if (translatableKeys.includes(key) && typeof val === 'string') {
                                    if (typeof target.content[key] !== 'object' || target.content[key] === null) {
                                        const oldVal = target.content[key];
                                        target.content[key] = {};
                                        target.content[key][firstLocale] = oldVal;
                                    }
                                    target.content[key][locale] = val;
                                } else if (key === 'items' && Array.isArray(val) && val.length > 0 && typeof val[0] === 'object') {
                                  // specially handle accordion/tabs/timeline items which might have nested title/content
                                    if (Array.isArray(target.content[key])) {
                                        val.forEach((item, idx) => {
                                            if (target.content[key][idx]) {
                                               Object.keys(item).forEach(subKey => {
                                                   if (translatableKeys.includes(subKey) && typeof item[subKey] === 'string') {
                                                        if (typeof target.content[key][idx][subKey] !== 'object' || target.content[key][idx][subKey] === null) {
                                                            const oldVal = target.content[key][idx][subKey];
                                                            target.content[key][idx][subKey] = {};
                                                            target.content[key][idx][subKey][firstLocale] = oldVal;
                                                        }
                                                        target.content[key][idx][subKey][locale] = item[subKey];
                                                   }
                                               });
                                            }
                                        });
                                    }
                                } else if (key === 'items' && Array.isArray(val) && val.length > 0 && typeof val[0] === 'string') {
                                  // specially handle string arrays (like lists, menu)
                                    if (Array.isArray(target.content[key])) {
                                        val.forEach((item, idx) => {
                                            if (target.content[key][idx] !== undefined) {
                                                if (typeof target.content[key][idx] !== 'object' || target.content[key][idx] === null) {
                                                    const oldVal = target.content[key][idx];
                                                    target.content[key][idx] = {};
                                                    target.content[key][idx][firstLocale] = oldVal;
                                                }
                                                target.content[key][idx][locale] = item;
                                            }
                                        });
                                    }
                                }
                            });
                            // Recurse children
                            if (target.children && source.children) {
                                mergeContent(target.children, source.children, locale);
                            }
                        }
                    });
                };

                locales.slice(1).forEach(locale => {
                    mergeContent(baseBlocks, initialContent[locale], locale);
                });

                this.blocks = baseBlocks;
            } else {
                // Flat format or new unified format
                this.blocks = Array.isArray(initialContent) ? initialContent : [];
            }
            this.isDirty = false;
            this.lastSavedBlocks = JSON.parse(JSON.stringify(this.blocks || []));
            this.autosaveConflict = null;
        },
        initLocales(languages, currentLocale) {
            this.availableLocales = languages || [];
            if (currentLocale) {
                this.editingLocale = currentLocale;
            }
        },
        setEditingLocale(locale) {
            this.editingLocale = locale;
        },
        createBlockObject(type, parentId = null) {
            const defaults = {
                // 1. Typography
                paragraph: { text: 'This is a paragraph block. You can edit this text in the sidebar.' },
                heading: { text: 'New Heading', level: 2, align: 'left' },
                list: { items: ['Item 1', 'Item 2'], type: 'bullets' },
                quote: { text: 'Quote text', author: '' },
                custom_code: { html: '<div>Custom HTML</div>', js: '' },

                // 2. Actions
                button: { label: 'Click Me', url: '#', style: 'primary', align: 'left' },
                dropdown: { label: 'Dropdown', items: ['Item 1', 'Item 2'] },
                modal: { id: 'modal-1', buttonLabel: 'Open Modal', title: 'Hello', text: 'Modal content here.' },
                swap: { onIcon: 'PhSun', offIcon: 'PhMoon', active: false },

                // 3. Data Display
                accordion: { items: [{ title: 'Click to open', content: 'hello' }] },
                avatar: { url: 'https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.webp', online: true },
                badge: { text: 'New', style: 'primary' },
                card: { title: 'Card Title', description: 'A sophisticated card component.', image: 'https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp', buttonText: 'Buy Now' },
                carousel: { image_ids: [], images: ['https://img.daisyui.com/images/stock/photo-1559703248-dcaaec9fab78.webp'] },
                chat: { messages: [{ side: 'start', text: 'Hi!' }, { side: 'end', text: 'Hello!' }] },
                countdown: { value: 60, unit: 'min' },
                diff: { img1: 'https://img.daisyui.com/images/stock/photo-1560717789-0ac7c58acfa6.webp', img2: 'https://img.daisyui.com/images/stock/photo-1560717789-0ac7c58acfa6-blur.webp' },
                stat: { title: 'Total Views', value: '89,400', desc: '21% more than last month', icon: 'PhLightning' },
                table: { rows: [['Cell 1', 'Cell 2']] },
                timeline: { items: [{ year: '1984', title: 'First Macintosh computer', content: 'bla bla' }] },

                // 3.5 Feedback
                alert: { type: 'alert-info', text: 'Important information here.' },
                progress: { value: 50, max: 100, color: 'progress-primary' },
                radial_progress: { value: 70 },


                // 4. Data Input
                text_input: { label: 'Name', placeholder: 'Enter name', required: false },
                textarea: { label: 'Message', placeholder: 'Enter message...', required: false },
                select: { label: 'Option', options: 'Option 1\nOption 2' },
                checkbox: { label: 'Remember me', checked: false },
                radio: { label: 'Option 1', group: 'group1' },
                toggle: { label: 'Enable notifications', checked: true },
                range: { min: 0, max: 100, val: 50 },
                rating: { max: 5, val: 3 },
                file_input: { label: 'Upload document', placeholder: 'Choose file' },
                form: {
                    form_id: null,
                    submit_url: '',
                    success_message: 'Thank you! Your message has been sent.',
                    error_message: 'Something went wrong.',
                    submit_label: 'Send Message'
                },

                // 5. Layout & Media
                container: {
                    htmlTag: 'section',
                    isBoxed: true,
                    layoutType: 'default',
                    flexConfig: { direction: 'col', align: 'stretch', justify: 'start', wrap: 'nowrap', gap: '4' },
                    gridConfig: { cols: '1', gap: '4' }
                },
                divider: { text: 'OR' },
                image: { media_id: null, url: 'https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp', alt: '', caption: '' },
                video: { url: '', autoplay: false, controls: true },
                icon: { icon: 'ph:PhStar', size: '2rem', color: '' },

                // 6. Navigation
                breadcrumbs: { items: ['Home', 'Documents', 'Add Document'] },
                menu: { items: ['Home', 'About', 'Contact'] },
                navbar: { title: 'daisyUI', links: ['Home', 'About'], actionButton: 'Get Started' },
                steps: { items: ['Register', 'Choose plan', 'Purchase', 'Receive Product'] },
                tabs: { tabs: [{ title: 'Tab 1', content: 'Tab 1 content' }, { title: 'Tab 2', content: 'Tab 2 content' }] },

                // 7. Mockup
                mockup_browser: { url: 'https://daisyui.com', content: 'Hello!' },
                mockup_code: { code: 'npm i daisyui' },
                mockup_phone: { url: 'https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp' },
                mockup_window: { content: 'Window content...' },

                // 8. Extended
                composed_block: { composed_block_id: null, snapshot_title: '' },
                posts_list: { count: 3, layout: 'grid' },
                projects_list: { count: 3, layout: 'grid' },
                text_rotate: { prefix: 'We are ', words: 'Creative\nAwesome\nInnovators', suffix: '!', interval: 2000 },

                // 9. Building
                template_reference: { template_id: null },
                content_slot: { label: 'Page Content Placeholder' },
                header_slot: { label: 'Header Injection Point' },
                footer_slot: { label: 'Footer Injection Point' },
                sidebar_slot: { label: 'Sidebar Injection Point' },

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
                        padding: 'py-0',
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
                const findAndAdd = (list) => {
                    for (let i = 0; i < list.length; i++) {
                        if (list[i].id === parentId) {
                            if (!list[i].children) list[i].children = [];
                            list[i].children.push(newBlock);
                            return true;
                        }
                        if (list[i].children && findAndAdd(list[i].children)) return true;
                    }
                    return false;
                };
                findAndAdd(this.blocks);
            } else {
                this.blocks.push(newBlock);
            }
            this.activeBlockId = newBlock.id;
            this.isDirty = true;
        },
        duplicateBlock(id) {
            const findAndDuplicate = (list) => {
                for (let i = 0; i < list.length; i++) {
                    if (list[i].id === id) {
                        // Deep clone the block, but keep a new ID
                        const clone = JSON.parse(JSON.stringify(list[i]));

                        // Recursive function to assign new IDs to the cloned block and its children
                        const assignNewIds = (block) => {
                            block.id = crypto.randomUUID();
                            if (block.children && block.children.length > 0) {
                                block.children.forEach(assignNewIds);
                            }
                        };

                        assignNewIds(clone);

                        // Insert the clone right after the original item
                        list.splice(i + 1, 0, clone);
                        this.activeBlockId = clone.id; // Optional: select the new block
                        return true;
                    }
                    if (list[i].children && findAndDuplicate(list[i].children)) return true;
                }
                return false;
            };
            findAndDuplicate(this.blocks);
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
            if (id) this.isEditingBlock = true;
        },
        selectBlock(id) {
            this.setActiveBlock(id);
        },
        removeBlock(id) {
            const findAndRemove = (list) => {
                for (let i = 0; i < list.length; i++) {
                    if (list[i].id === id) {
                        list.splice(i, 1);
                        return true;
                    }
                    if (list[i].children && findAndRemove(list[i].children)) return true;
                }
                return false;
            };
            findAndRemove(this.blocks);

            if (this.activeBlockId === id) {
                this.activeBlockId = null;
                this.isEditingBlock = false;
            }
            this.isDirty = true;
        },
        deleteBlock(id) {
            this.removeBlock(id);
        },
        toggleBlockVisibility(id) {
            const findAndToggle = (list) => {
                for (let i = 0; i < list.length; i++) {
                    if (list[i].id === id) {
                        if (!list[i].settings) list[i].settings = {};
                        list[i].settings.hidden = !list[i].settings.hidden;
                        return true;
                    }
                    if (list[i].children && findAndToggle(list[i].children)) return true;
                }
                return false;
            };
            findAndToggle(this.blocks);
            this.isDirty = true;
        },
        toggleBlockLock(id) {
            const findAndToggle = (list) => {
                for (let i = 0; i < list.length; i++) {
                    if (list[i].id === id) {
                        if (!list[i].settings) list[i].settings = {};
                        list[i].settings.locked = !list[i].settings.locked;
                        return true;
                    }
                    if (list[i].children && findAndToggle(list[i].children)) return true;
                }
                return false;
            };
            findAndToggle(this.blocks);
            this.isDirty = true;
        },
        moveBlock(index, delta) {
            const newIndex = index + delta;
            if (newIndex < 0 || newIndex >= this.blocks.length) return;

            const element = this.blocks.splice(index, 1)[0];
            this.blocks.splice(newIndex, 0, element);
            this.isDirty = true;
        },
        markSavedSnapshot(blocks = null) {
            const source = Array.isArray(blocks) ? blocks : this.blocks;
            this.lastSavedBlocks = JSON.parse(JSON.stringify(source || []));
            this.autosaveConflict = null;
        },
        setAutosaveConflict(payload = {}) {
            this.autosaveConflict = {
                message: payload.message || 'Autosave conflict detected.',
                detectedAt: new Date().toISOString(),
                localBlocks: JSON.parse(JSON.stringify(this.blocks || [])),
                lastSavedBlocks: JSON.parse(JSON.stringify(this.lastSavedBlocks || [])),
            };
        },
        clearAutosaveConflict() {
            this.autosaveConflict = null;
        }
    },
    getters: {
        activeBlock: (state) => {
            let found = null;
            const findBlock = (list) => {
                for (const b of list) {
                    if (b.id === state.activeBlockId) {
                        found = b;
                        return;
                    }
                    if (!found && b.children) findBlock(b.children);
                }
            };
            findBlock(state.blocks);
            return found;
        },
    }
});
