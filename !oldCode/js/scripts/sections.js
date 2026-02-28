var sections = {
    animation: null,

    init: function(){
        var active = route.active;

        this.animation = new TimelineMax({
            paused: false,
            onComplete: function(){
                switch (active){
                    case 'welcome':
                        welcome.show();
                        break;
                    case 'about':
                        about.show();
                        break;
                    case 'work':
                        work.show();
                        break;
                    case 'contact':
                        contact.show();
                        break;
                    default:
                        welcome.show();
                        break;
                }
            },
            onReverseComplete:function(){
                sections.animationComplete();
            }
        });
        title.init(active);
        color.init(active);
    },
    show: function(  ){
        this.animation.timeScale(1);
        this.animation.play();
    },
    hide: function(){
        var active = route.active;

        switch (active){
            case 'welcome':
                welcome.hide();
                break;
            case 'about':
                about.hide();
                break;
            case 'work':
                work.hide();
                break;
            case 'contact':
                contact.hide();
                break;
            default:
                welcome.hide();
                break;
        }

        this.animation.timeScale(3);
        this.animation.reverse();
    },

    animationCompleteTask: null,
    animationCompleteTaskSite: null,
    animationComplete: function(){
        var task = this.animationCompleteTask;
        switch(task){
            case 'prev':
                var prev = route.getPrev();
                route.setActive(prev);
                break;
            case 'next':
                var next = route.getNext();
                route.setActive(next);
                break;
            case 'goto':
                var section = sections.animationCompleteTaskSite;
                route.setActive(section);
                break;
            default:
                break;
        }

        if ( task !== null ){
            this.init();
            this.show();
        }
    },
    prev: function(){
        this.animationCompleteTask = 'prev';
        this.hide();
    },
    next: function(){
        this.animationCompleteTask = 'next';
        this.hide();
    },
    goto: function (section) {
        this.animationCompleteTask = 'goto';
        this.animationCompleteTaskSite = section;
        this.hide();
    },
    resize: function(){
        var active = route.active;

        switch (active){
            case 'welcome':
                welcome.resize();
                break;
            case 'about':
                about.resize();
                break;
            case 'work':
                work.resize();
                break;
            case 'contact':
                contact.resize();
                break;
            default:
                welcome.resize();
                break;
        }
    }
};
