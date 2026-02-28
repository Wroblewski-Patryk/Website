var title = {
    delay: 0.5,
    animation: null,
    titles: ['welcome', 'about', 'work', 'contact'],

    init: function(section){
        var id = route.getId(section);
        var title = this.titles[id];
        var element = $('#title');

        element.text(title);
        title = helper.splitText(element);
        element.html(title);

        var spans = element.find('span');
        var amplitude = helper.vh2px(50);
        var i = 0;

        spans.each(function(){
            var span = $(this);
            var random1 = Math.random();
            var random2 = Math.random();
            var left = Math.floor( random1 * amplitude ) - Math.floor( random2 * amplitude );
            var top = Math.floor( random2 * amplitude ) - Math.floor( random1 * amplitude );
            var delay = i * 0.15;
            var aTime = Math.floor( ( random1 ) * 100 ) / 100;

            sections.animation
                .set(span, {
                    opacity: 0,
                    left: left+'px',
                    top: top+'px'
                }, 0)
                .to(span,  aTime,{
                    left: '0px',
                    ease: Power4.easeOut
                }, delay)
                .to(span, aTime, {
                    top: '0px',
                    ease: Power4.easeOut
                }, delay + aTime / 2)
                .to(span, aTime*2, {
                    opacity: 1,
                    ease: Power4.easeOut
                }, delay);

            i += 1;
        });
    },
    show: function(){
        this.animation.timeScale(1);
        this.animation.play();
    },
    hide: function(){
        this.animation.timeScale(1);
        this.animation.reverse();
    }
};
