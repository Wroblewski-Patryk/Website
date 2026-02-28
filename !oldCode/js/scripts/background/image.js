var image = {
    animation: null,
    init: function(){
        var element = $('#image');

        if ( this.animation === null ){
            this.animation = new TimelineMax();
        } else {
            this.animation.clear();
        }
        this.animation
            .fromTo(element, 1.5, {width: '0%'}, {width: '100%'});
    },
    resize: function(){
        this.init();
    }
};