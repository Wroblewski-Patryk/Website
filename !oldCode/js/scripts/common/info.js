var info = {
    animation: null,
    init: function(){
        var element = $('#info');

        TweenMax.set(element, {autoAlpha: 0});

        this.animation = new TimelineMax({
            repeat: -1,
            paused: true
        });

    },
    show: function(){
        var element = $('#info');

        this.animation
            .to(element, 2, {autoAlpha:0.5})
            .to(element, 1, {autoAlpha:1});

        TweenMax.to(element, 3, {autoAlpha: 1, onComplete: function(){
            info.animation.play();
        }});
    },
    hide: function(){
        this.animation.clear();
        this.animation.repeat(0);

        var element = $('#info');
        this.animation.to(element, 1, {autoAlpha: 0});
    },
    resize: function(){}
};