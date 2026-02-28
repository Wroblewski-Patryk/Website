var route = {
    sections: ['welcome','about','work','contact'],
    active: 'welcome',
    lang: 'pl',

    init: function(){
        var pathname = window.location.pathname;
        var section = pathname.substr(1, pathname.length);
        if ( section === '' )
            this.active = this.sections[0];
        else
            this.active = section;

        nav.setActive(this.active);
    },
    getId: function(section){
        var sections = this.sections;
        var id = 0;
        for ( var i = 0; i < sections.length; i++ ){
            if ( sections[i] === section )
                id = i;
        }
        return id;
    },
    setActive: function(section){
        this.active = section;

        var sId = this.getId(section);
        var sTitle = title.titles[sId];
        window.history.replaceState({}, sTitle, '/'+section);

        nav.setActive(section);
    },
    getNext: function(){
        var sections = this.sections;
        var active = this.active;
        var sectionId = this.getId(active);
        var sectionIdNext = 0;

        if ( sectionId < sections.length - 1 )
            sectionIdNext = sectionId + 1;

        return sections[sectionIdNext];
    },
    getPrev: function(){
        var sections = this.sections;
        var active = this.active;
        var sectionId = this.getId(active);
        var sectionIdPrev = sections.length - 1;

        if ( sectionId > 0 )
            sectionIdPrev = sectionId - 1;

        return sections[sectionIdPrev];
    }
};