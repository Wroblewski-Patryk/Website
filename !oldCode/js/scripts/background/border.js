var border = {
    delay: 0.5,

    animation: null,
    margin: 0,

    init:function(){
        var border = $('#border');
        var docWidth = $(window).width(),
            docHeight = $(window).height();

        this.setMargin();

        this.animation = new TimelineMax({
            paused: true,
            delay: title.delay,
            onComplete:function(){
                logotype.show();
                nav.show();
                info.show();
            }
        });

        this.animation
            .set(border, {
                width: '0px',
                height: '0px'
            })
            .to(border, 0.01, { opacity: 1 })
            .to(border, 0.5, { height: docHeight+'px' })
            .to(border, 1.2, { width: docWidth+'px' });

        this.slideAnimation = new TimelineMax({
            paused:true
        });
    },
    setMargin: function(){
        this.margin = helper.vh2px(10);
        var toReturn = this.margin;
        return toReturn;
    },
    setSize: function(){
        var border = $('#border');
        var borderLeft = $('#border-left');
        var borderRight = $('#border-right');

        var margin = this.setMargin();
        var width = $(window).width(),
            height = $(window).height();

        TweenMax.set(border, {
            width: width+'px',
            height: height+'px'
        });
        TweenMax.set(borderLeft, {width: margin+'px'});
        TweenMax.set(borderRight, {width: margin+'px'});
    },
    show: function(){
        this.animation.play();
    },
    hide: function(){
        this.animation.reverse();
    },
    slideAnimation: null,
    slide: function(direction){
        var border = $('#border');

        var margin = this.margin;
        var docWidth = $(window).width(),
            docHeight = $(window).height();
        var width = docWidth - 2 * margin,
            height = docHeight - 2 * margin;

        if ( this.slideAnimation.isActive() === false ){
            this.slideAnimation.clear();

            var sets = {
                width: width + 'px',
                height: height + 'px',
                left: '',
                right: margin + 'px'
            };
            if ( direction === 'left' )
                sets = {
                    width: width + 'px',
                    height: height + 'px',
                    left: margin + 'px',
                    right: ''
                };

            this.slideAnimation.set(border, sets);

            width = docWidth - 3 * margin;
            this.slideAnimation
                .to(border, 0.5, {
                    width: width+'px'
                });

            this.slideAnimation.play();
        }
    }
};