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
        addBlock(type, parentId = null) {
            const defaults = {
                heading: { text: 'New Heading', level: 'h2', align: 'left' },
                text: { text: 'Enter your content here...', align: 'left' },
                hero: { headline: 'Premium Headline', subheadline: 'Elegant subtext for your high-end website.', primaryLabel: 'Get Started', secondaryLabel: 'Learn More' },
                image: { url: '', alt: '' },
                button: { label: 'Click Me', url: '#', style: 'primary', align: 'left', newTab: false },
                section: { width: 'boxed', bgColor: 'transparent', align: 'left' },
                columns: { columns: 2 },
                contact_form: { title: 'Contact Us', successMessage: 'Message sent successfully!', button_text: 'Send Message' }
            };

            const newBlock = {
                id: 'block_' + Math.random().toString(36).substr(2, 9),
                type,
                parent_id: parentId || null,
                content: defaults[type] || {},
                children: [],
                appearance: {
                    marginTop: '0px', marginBottom: '0px',
                    paddingTop: '0px', paddingBottom: '0px',
                    backgroundColor: 'transparent',
                    textColor: 'inherit',
                    textAlign: 'left',
                    borderRadius: '0px',
                    borderWidth: '0px',
                    borderColor: 'transparent',
                    boxShadow: 'none',
                    customClasses: '',
                    animations: {
                        enabled: false,
                        preset: 'fadeUp',
                        duration: 1,
                        delay: 0,
                        trigger: 'in-view'
                    }
                }
            };

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
