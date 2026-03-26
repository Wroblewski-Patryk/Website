export const ICON_SETS = [
    { id: 'phosphor', label: 'Phosphor' },
    { id: 'fontawesome-solid', label: 'Font Awesome Solid' },
    { id: 'fontawesome-regular', label: 'Font Awesome Regular' },
    { id: 'fontawesome-brands', label: 'Font Awesome Brands' },
];

const phosphorIcons = [
    ['PhHouse', 'House home'],
    ['PhUser', 'User person profile'],
    ['PhUsers', 'Users team group'],
    ['PhEnvelope', 'Envelope mail message'],
    ['PhPhone', 'Phone call'],
    ['PhGlobe', 'Globe world language'],
    ['PhGear', 'Gear settings'],
    ['PhCheckCircle', 'Check success'],
    ['PhWarningCircle', 'Warning alert'],
    ['PhInfo', 'Info details'],
    ['PhStar', 'Star favorite'],
    ['PhHeart', 'Heart like'],
    ['PhMagnifyingGlass', 'Search find'],
    ['PhCalendarBlank', 'Calendar date'],
    ['PhClock', 'Clock time'],
    ['PhImage', 'Image media photo'],
    ['PhVideoCamera', 'Video media'],
    ['PhMusicNote', 'Music audio'],
    ['PhShoppingCart', 'Cart shop'],
    ['PhCreditCard', 'Credit card payment'],
];

const fontAwesomeSolid = [
    ['fas fa-house', 'House home'],
    ['fas fa-user', 'User person profile'],
    ['fas fa-users', 'Users group team'],
    ['fas fa-envelope', 'Envelope mail'],
    ['fas fa-phone', 'Phone call'],
    ['fas fa-globe', 'Globe world'],
    ['fas fa-gear', 'Gear settings'],
    ['fas fa-circle-check', 'Check success'],
    ['fas fa-circle-info', 'Info details'],
    ['fas fa-triangle-exclamation', 'Warning alert'],
    ['fas fa-star', 'Star favorite'],
    ['fas fa-heart', 'Heart like'],
    ['fas fa-magnifying-glass', 'Search'],
    ['fas fa-cart-shopping', 'Shopping cart'],
    ['fas fa-credit-card', 'Credit card'],
];

const fontAwesomeRegular = [
    ['far fa-user', 'User regular'],
    ['far fa-envelope', 'Envelope regular'],
    ['far fa-star', 'Star regular'],
    ['far fa-heart', 'Heart regular'],
    ['far fa-calendar', 'Calendar regular'],
];

const fontAwesomeBrands = [
    ['fab fa-facebook', 'Facebook brand social'],
    ['fab fa-instagram', 'Instagram brand social'],
    ['fab fa-linkedin', 'LinkedIn brand social'],
    ['fab fa-x-twitter', 'X Twitter brand social'],
    ['fab fa-youtube', 'YouTube brand video'],
];

const mapIcons = (items, setId, prefix = '') => items.map(([value, keywords]) => ({
    id: `${setId}:${value}`,
    set: setId,
    value: prefix ? `${prefix}${value}` : value,
    label: value,
    keywords,
}));

export const ICON_LIBRARY = [
    ...mapIcons(phosphorIcons, 'phosphor', 'ph:'),
    ...mapIcons(fontAwesomeSolid, 'fontawesome-solid', 'fa:'),
    ...mapIcons(fontAwesomeRegular, 'fontawesome-regular', 'fa:'),
    ...mapIcons(fontAwesomeBrands, 'fontawesome-brands', 'fa:'),
];

export const searchIconLibrary = (query = '', setId = 'all') => {
    const normalizedQuery = String(query).trim().toLowerCase();
    return ICON_LIBRARY.filter((item) => {
        if (setId !== 'all' && item.set !== setId) return false;
        if (!normalizedQuery) return true;
        return (
            item.label.toLowerCase().includes(normalizedQuery) ||
            item.keywords.toLowerCase().includes(normalizedQuery) ||
            item.value.toLowerCase().includes(normalizedQuery)
        );
    });
};
