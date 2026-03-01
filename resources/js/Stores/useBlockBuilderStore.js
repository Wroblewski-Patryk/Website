import { defineStore } from 'pinia';

export const useBlockBuilderStore = defineStore('blockBuilder', {
    state: () => ({
        blocks: [],
        activeBlockId: null,
        isDirty: false,
    }),
    actions: {
        init(initialBlocks) {
            this.blocks = Array.isArray(initialBlocks) ? initialBlocks : [];
            this.isDirty = false;
        },
        createBlockObject(type, parentId = null) {
            const defaults = {
                heading: { text: 'New Heading', level: 'h2', align: 'left' },
                text: { text: 'Enter your content here...', align: 'left' },
                hero: { headline: 'Premium Headline', subheadline: 'Elegant subtext for your high-end website.', primaryLabel: 'Get Started', secondaryLabel: 'Learn More' },
                image: { url: '', alt: '' },
                button: { label: 'Click Me', url: '#', style: 'primary', align: 'left', newTab: false },
                section: { width: 'boxed', bgColor: 'transparent', align: 'left' },
                columns: { columns: 2 },
                contact_form: { title: 'Contact Us', successMessage: 'Message sent successfully!', button_text: 'Send Message' },
                form_input: { label: 'Label', placeholder: 'Enter text...', name: 'field_' + Date.now(), type: 'text', required: false },
                form_textarea: { label: 'Label', placeholder: 'Enter message...', name: 'field_' + Date.now(), required: false },
                form_select: { label: 'Label', name: 'field_' + Date.now(), options: 'Option 1\nOption 2', required: false },
                form_submit: { label: 'Submit', style: 'primary', fullWidth: false },
                language_switcher: { style: 'pill' },
                form_input: { label: 'Name', name: 'name', type: 'text', placeholder: '' },
                form_textarea: { label: 'Message', name: 'message', placeholder: '' },
                form_select: { label: 'Subject', name: 'subject', options: [] },
                form_submit: { text: 'Send Message' },
                language_switcher: { style: 'dropdown' },
                menu: { menu_id: null },
                portfolio: {
                    projects: [
                        { title: 'Project 1', date: '2024', description: '', desktop_image: '', mobile_image: '', url: '' }
                    ]
                },
                custom_code: { html: '<div>Custom HTML</div>', js: 'console.log("Hello");' }
            };

            const block = {
                id: crypto.randomUUID(),
                type,
                parent_id: parentId, // Changed from parentId to parent_id to match existing structure
                content: defaults[type] || {},
                children: [], // Ensure children array is always present
                settings: {
                    id: '',
                    class: '',
                    animations: {
                        enabled: false,
                        type: 'fade-up', // fade-up, slide-left, reveal-text
                        duration: 1,
                        delay: 0,
                        ease: 'power2.out'
                    },
                    layout: {
                        fullHeight: false,
                        fixedBg: false,
                        padding: 'py-20'
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
