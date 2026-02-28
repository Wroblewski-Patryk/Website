var nav = {
    init: function(){
        var element = $('nav');
        TweenMax.set(element, { opacity: 0 });

        var positions = element.find('ul li a');
        positions.each(function(){
            var position = $(this);
            var text = helper.splitText(position);
            position.html(text);
        });
    },
    show: function(){
        var main = $('nav');
        TweenMax.to(main, 2.5, { opacity : 1 });

        this.handle();
    },
    resize: function(){

    },
    opened: false,
    handle: function(){
        var element = $('nav');
        var hamburger = $('#hamburger');
        var bg = $('#nav-bg');

        var ul = element.find('ul');
        var anchors = ul.find('li a');
        var amplitude = helper.vh2px(20);

        var animation = new TimelineMax({
            paused: true
        });

        animation
            .set(element, {width: border.margin+'vh'})
            .set(element, {width: '100%'})
            .to(bg, 0.5, {width: '100%'});

        animation
            .set(ul, {autoAlpha: 1});

        anchors.each(function () {
            var anchor = $(this);
            var spans = anchor.find('span');

            var i = 0;
            var delay = 0;
            spans.each(function(){
                var span = $(this);
                var left = Math.floor( Math.random() * amplitude ) - Math.floor( Math.random() * amplitude );
                var top = Math.floor( Math.random() * amplitude ) - Math.floor( Math.random() * amplitude );
                var aTime = Math.floor( ( Math.random() * 0.5 ) * 100 ) / 100;
                delay = 0.4 + i * 0.1;

                animation
                    .set(span, {left: left+'px', top: top+'px'}, delay)
                    .to(span,  aTime,{ left: '0px', ease: Power4.easeOut }, delay)
                    .to(span, aTime, { top: '0px', ease: Power4.easeOut }, delay + aTime)
                    .to(span, aTime*2, { opacity: 1, ease: Power4.easeOut }, delay);

                i += 1;
            });
        });

        var hs1 = hamburger.find('span').eq(0);
        var hs2 = hamburger.find('span').eq(1);
        var hs3 = hamburger.find('span').eq(2);
        var hamburgerSpansAnimation = new TimelineMax({paused:true});
        hamburgerSpansAnimation
            .to(hs1, 0.15, {top: '50%' }, 0)
            .to(hs2, 0.15, {top: '50%' }, 0)
            .to(hs3, 0.15, {top: '50%' }, 0)
            .to(hs1, 0.15, {transform: 'rotate(45deg)'}, 0.15)
            .to(hs2, 0.15, {transform: 'rotate(45deg)'}, 0.15)
            .to(hs3, 0.15, {transform: 'rotate(-45deg)'}, 0.15);

        element.click(function(){
            if ( nav.opened === false ){
                animation.timeScale(1);
                animation.play();

                hamburgerSpansAnimation.timeScale(1);
                hamburgerSpansAnimation.play();

                logotype.animation.timeScale(1);
                logotype.animation.play();

                nav.activeAnimation.timeScale(1);
                nav.activeAnimation.play();

                nav.opened = true;
            } else {
                animation.timeScale(3);
                animation.reverse();
                hamburgerSpansAnimation.timeScale(3);
                hamburgerSpansAnimation.reverse();

                logotype.animation.timeScale(3);
                logotype.animation.reverse();

                nav.activeAnimation.timeScale(3);
                nav.activeAnimation.reverse();

                nav.opened = false;
            }
        });


        //hover animation
        var borderRight = $('#border-right');
        var hamburgerAnimation = new TimelineMax({paused:true});
        hamburgerAnimation
            .set(borderRight, {width: border.margin+'px'})
            .set(element, {width: border.margin +'px'})
            .set(element, {width: border.margin*2 +'px'})
            .to(hamburger, 0.5 , {
                right: '+='+border.margin/2+'px',
                scale:2
            })
            .to(borderRight, 0.5, {width: 2*border.margin+'px'}, 0);

        element.hover(function(){
            if ( nav.opened === false ) {
                hamburgerAnimation.timeScale(1);
                hamburgerAnimation.play();
            }
        }, function(){
            if ( nav.opened === false ) {
                hamburgerAnimation.timeScale(3);
                hamburgerAnimation.reverse();
            }
        });
        //end of hover animation

        //hiding menu by clicking position
        anchors.click(function(e){
            e.preventDefault();
            var section = $(this).attr('href');
            if ( section !== route.active ){
                section = section.replace('/','');
                sections.goto(section);
            }
        });
        //end of hiding menu by clicking position
    },
    activeAnimation: null,
    setActive: function(section){
        var element = $('nav');
        var ul = element.find('ul');
        var li = ul.find('li');

        if ( li.find('.left').length ){
            li.find('.left').remove();
            li.find('.right').remove();
        }

        li.removeClass('active');

        li.each(function(){
            var elem = $(this);
            var anchor = elem.find('a');
            var href = anchor.attr('href');
            href = href.replace('/','');

            if ( href === section ){
                elem.addClass('active');

                if ( elem.find('.left').length === 0 ){
                    var div = '<div class="left"></div>';
                    elem.prepend(div);
                    div = '<div class="right"></div>';
                    elem.append(div);
                }
                var left = elem.find('.left');
                var right = elem.find('.right');

                if( nav.activeAnimation === null ){
                    nav.activeAnimation = new TimelineMax({paused:true});
                } else {
                    nav.activeAnimation.clear();
                }
                nav.activeAnimation.fromTo( left, 0.25, {left: '-2vh', opacity: 0}, {left: '0', opacity: 1}, 1.5 );
                nav.activeAnimation.fromTo( right, 0.25, {right: '-2vh', opacity: 0}, {right: '0', opacity: 1}, 1.5 );
            }
        });

    }
};
