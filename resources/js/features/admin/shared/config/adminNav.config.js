import { 
    PhFileText, PhFeather, PhCards, PhTextbox, PhPaintRoller, 
    PhCube, PhLayout, PhImageSquare, PhGlobe, PhGearSix, 
    PhUsers, PhTag, PhHash, PhList, PhPlus, PhPalette, PhTextT, PhTextH, PhSelectionAll, PhMagicWand, PhCardsThree, PhCalendarBlank
} from '@phosphor-icons/vue';

export const navigation = (t) => [
    {
        title: t('admin.menu.content', 'Content'),
        permission: 'can_manage_content',
        items: [
            {
                label: t('admin.menu.pages', 'Pages'),
                icon: PhFileText,
                route: 'admin.pages.index',
                createRoute: 'admin.pages.create',
                active: '/admin/pages',
                color: 'primary',
                children: [
                    { label: t('admin.common.all', 'All'), route: 'admin.pages.index', active: '/admin/pages', exact: true, icon: PhList },
                    { label: t('admin.pages.add', 'Add New'), route: 'admin.pages.create', active: '/admin/pages/create', icon: PhPlus },
                ]
            },
            {
                label: t('admin.menu.posts', 'Posts'),
                icon: PhFeather,
                route: 'admin.posts.index',
                createRoute: 'admin.posts.create',
                active: ['/admin/posts', '/admin/categories'],
                color: 'primary',
                children: [
                    { label: t('admin.common.all', 'All'), route: 'admin.posts.index', active: '/admin/posts', exact: true, icon: PhList },
                    { label: t('admin.common.add', 'Add New'), route: 'admin.posts.create', active: '/admin/posts/create', icon: PhPlus },
                    { label: t('admin.taxonomy.type_category', 'Categories'), route: 'admin.posts.categories.index', active: '/admin/posts/categories', icon: PhTag },
                    { label: t('admin.taxonomy.type_tag', 'Tags'), route: 'admin.posts.tags.index', active: '/admin/posts/tags', icon: PhHash }
                ]
            },
            {
                label: t('admin.menu.projects', 'Projects'),
                icon: PhCards,
                route: 'admin.projects.index',
                createRoute: 'admin.projects.create',
                active: '/admin/projects',
                color: 'primary',
                children: [
                    { label: t('admin.common.all', 'All'), route: 'admin.projects.index', active: '/admin/projects', exact: true, icon: PhList },
                    { label: t('admin.common.add', 'Add New'), route: 'admin.projects.create', active: '/admin/projects/create', icon: PhPlus },
                    { label: t('admin.menu.clients', 'Clients'), route: 'admin.projects.clients.index', active: '/admin/projects/clients', icon: PhUsers },
                    { label: t('admin.taxonomy.type_category', 'Categories'), route: 'admin.projects.categories.index', active: '/admin/projects/categories', icon: PhTag },
                    { label: t('admin.taxonomy.type_tag', 'Tags'), route: 'admin.projects.tags.index', active: '/admin/projects/tags', icon: PhHash }
                ]
            },
            {
                label: t('admin.menu.forms', 'Forms'),
                icon: PhTextbox,
                route: 'admin.forms.index',
                createRoute: 'admin.forms.create',
                active: '/admin/forms',
                color: 'primary',
                children: [
                    { label: t('admin.common.all', 'All'), route: 'admin.forms.index', active: '/admin/forms', exact: true, icon: PhList },
                    { label: t('admin.common.add', 'Add New'), route: 'admin.forms.create', active: '/admin/forms/create', icon: PhPlus }
                ]
            },
            {
                label: 'Publication Calendar',
                icon: PhCalendarBlank,
                route: 'admin.publication-calendar',
                active: '/admin/publication-calendar',
                color: 'primary',
            },
        ]
    },
    {
        title: t('admin.menu.library', 'Library'),
        permission: 'can_manage_content',
        items: [
            {
                label: t('admin.menu.media', 'Media'),
                icon: PhImageSquare,
                route: 'admin.media.index',
                active: '/admin/media',
                color: 'secondary'
            },
            {
                label: t('admin.menu.templates', 'Templates'),
                icon: PhLayout,
                route: 'admin.templates.index',
                createRoute: 'admin.templates.create',
                permission: 'can_manage_settings',
                active: '/admin/templates',
                color: 'secondary',
                children: [
                    { label: t('admin.common.all', 'All'), route: 'admin.templates.index', active: '/admin/templates', exact: true, icon: PhList },
                    { label: t('admin.common.add', 'Add New'), route: 'admin.templates.create', active: '/admin/templates/create', icon: PhPlus }
                ]
            },
            {
                label: t('admin.menu.animation_presets', 'Animation Presets'),
                icon: PhMagicWand,
                route: 'admin.animation-presets.index',
                createRoute: 'admin.animation-presets.create',
                permission: 'can_manage_settings',
                active: '/admin/animation-presets',
                color: 'secondary',
                children: [
                    { label: t('admin.common.all', 'All'), route: 'admin.animation-presets.index', active: '/admin/animation-presets', exact: true, icon: PhList },
                    { label: t('admin.common.add', 'Add New'), route: 'admin.animation-presets.create', active: '/admin/animation-presets/create', icon: PhPlus }
                ]
            }
        ]
    },
    {
        title: t('admin.menu.design', 'Design'),
        permission: 'can_manage_settings',
        items: [
            {
                label: t('admin.menu.theme', 'Theme'),
                icon: PhPaintRoller,
                route: 'admin.theme.index',
                active: '/admin/theme',
                color: 'accent',
                children: [
                    { label: t('admin.menu.colors', 'Colors'), route: 'admin.theme.colors', active: '/admin/theme/colors', icon: PhPalette },
                    { label: t('admin.menu.fonts', 'Fonts'), route: 'admin.theme.fonts', active: '/admin/theme/fonts', icon: PhTextT },
                    { label: t('admin.menu.typography', 'Typography'), route: 'admin.theme.typography', active: '/admin/theme/typography', icon: PhTextH },
                    { label: t('admin.menu.sizes', 'Sizes'), route: 'admin.theme.sizes', active: '/admin/theme/sizes', icon: PhSelectionAll },
                    { label: t('admin.menu.effects', 'Effects'), route: 'admin.theme.effects', active: '/admin/theme/effects', icon: PhMagicWand }
                ]
            },
            {
                label: t('admin.menu.blocks', 'Blocks'),
                icon: PhCube,
                route: 'admin.blocks.index',
                createRoute: 'admin.blocks.create',
                permission: 'can_manage_settings',
                active: '/admin/blocks',
                color: 'accent',
                children: [
                    { label: t('admin.common.all', 'All'), route: 'admin.blocks.index', active: '/admin/blocks', exact: true, icon: PhList },
                    { label: t('admin.common.add', 'Add New'), route: 'admin.blocks.create', active: '/admin/blocks/create', icon: PhPlus }
                ]
            }
        ]
    },
    {
        title: t('admin.menu.system', 'System Settings'),
        permission: 'can_manage_settings',
        items: [
            {
                label: t('admin.menu.languages', 'Languages'),
                icon: PhGlobe,
                route: 'admin.languages.index',
                createRoute: 'admin.languages.create',
                active: ['/admin/languages', '/admin/translations'],
                color: 'info',
                children: [
                    { label: t('admin.common.all', 'All'), route: 'admin.languages.index', active: '/admin/languages', exact: true, icon: PhList },
                    { label: t('admin.common.add', 'Add New'), route: 'admin.languages.create', active: '/admin/languages/create', icon: PhPlus },
                    { label: t('admin.menu.translations', 'Translations'), route: 'admin.translations.index', active: '/admin/translations', icon: PhGlobe }
                ]
            },
            {
                label: t('admin.menu.users', 'Users'),
                icon: PhUsers,
                route: 'admin.users.index',
                createRoute: 'admin.users.create',
                permission: 'can_manage_users',
                active: ['/admin/users', '/admin/roles'],
                color: 'info',
                children: [
                    { label: t('admin.common.all', 'All'), route: 'admin.users.index', active: '/admin/users', exact: true, icon: PhList },
                    { label: t('admin.common.add', 'Add New'), route: 'admin.users.create', active: '/admin/users/create', icon: PhPlus },
                    { label: t('admin.menu.roles', 'Roles'), route: 'admin.roles.index', active: '/admin/roles', icon: PhCardsThree }
                ]
            },
            {
                label: t('admin.menu.settings', 'Settings'),
                icon: PhGearSix,
                route: 'admin.settings.index',
                active: '/admin/settings',
                color: 'info'
            }
        ]
    }
];
