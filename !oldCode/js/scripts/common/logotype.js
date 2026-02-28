var logotype = {
    animation: null,

    init: function () {
        var element = $('#logo'),
            elementImg = element.find('img');
        var borderLeft = $('#border-left');

        TweenMax.set(element, {opacity: 0});

        this.animation = new TimelineMax({
            paused:true
        });
        this.animation
            .set( borderLeft, {width: border.margin+'px'})
            .set(element, {width: border.margin +'px'})
            .set(element, {width: border.margin*2 +'px'})
            .to(elementImg, 0.5 , {
                left: '+='+border.margin/2+'px',
                scale:2
            })
            .to( borderLeft, 0.5, {
                width: 2*border.margin+'px'
            }, 0);
    },
    show: function(){
        var element = $('#logo');
        TweenMax.to(element, 2.5, {opacity: 1});

        this.handle();
    },
    handle: function(){
        var element = $('#logo');

        element.hover(function () {
            if ( nav.opened === false && helper.getOrientation() == "landscape"){
                logotype.animation.timeScale(1);
                logotype.animation.play();
            }
        },function () {
            if ( nav.opened === false && helper.getOrientation() == "landscape" ){
                logotype.animation.timeScale(3);
                logotype.animation.reverse();
            }
        });
    },
    resize: function () {

    }
};
