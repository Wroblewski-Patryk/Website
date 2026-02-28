var welcome = {
    animation: null,

    init: function(){
        var section = $('#welcome');

        var header = section.find('h1');
        var headerText = helper.splitText(header);
        header.html(headerText);

        var spans = header.find('span');
        var amplitude = helper.vh2px(15);

        this.animation = new TimelineMax({
            paused: true,
            onComplete: function(){
                welcome.handle();
            }
        });

        this.animation
            .fromTo(section, 0.1, {autoAlpha:0}, {autoAlpha:1});

        spans.each(function(){
            var span = $(this);

            var random = Math.random(),
                random2 = Math.random();
            var left = Math.floor( random * amplitude ) - Math.floor( random2 * amplitude );
            var top = Math.floor( random2 * amplitude ) - Math.floor( random * amplitude );

            var delay = random;
            delay = parseFloat( delay.toFixed(2) );

            var aTime = random2 * 2.4;
            aTime = parseFloat( aTime.toFixed(2) );

            welcome.animation
                .set(span, {opacity: 0, left: left+'px', top: top+'px'}, 0)
                .to(span,  aTime/2,{ left: '0px', ease: Power2.easeOut }, delay)
                .to(span, aTime/2, { top: '0px', ease: Power2.easeOut }, delay + aTime/2)
                .to(span, aTime, { opacity: 1, ease: Power1.easeOut}, delay);
        });
    },
    show: function(){
        this.animation.timeScale(1);
        this.animation.play();
    },
    hide: function(){
        this.animation.timeScale(3);
        this.animation.reverse();
    },
    handled: false,
    handle: function(){
        if ( welcome.handled === false ){
            var elements = $('#welcome h1 span');
            var amplitude = helper.vh2px(10);
            var animations = [];

            elements.each(function(i){
                var element = $(this);

                var random = Math.random(),
                    random2 = Math.random();
                var left = Math.floor( random * amplitude ) - Math.floor( random2 * amplitude );
                var top = Math.floor( random2 * amplitude ) - Math.floor( random * amplitude );

                var aTime = random * 1.5;
                aTime = aTime.toFixed(2);

                animations[i] = new TimelineMax({
                    paused: true,
                    onComplete: function(){
                        setTimeout(function(){
                            animations[i].reverse();
                        }, aTime/2 * 1000);
                    }
                });
                animations[i]
                    .to(element,  aTime / 2, {
                        left: left + 'px',
                        ease: Power3.easeInOut
                    })
                    .to(element, aTime / 2, {
                        top: top + 'px',
                        ease: Power3.easeInOut
                    }, aTime / 2)
                    .to(element, aTime, {
                        opacity: 0.5
                    }, 0);

                element.hover(function(){
                    animations[i].play();
                });
            });
            this.handled = true;
        }
    },
    resize: function(){

    }

};