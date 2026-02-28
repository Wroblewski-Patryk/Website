var app = {
    init: function () {
        route.init();

        //bg init
        border.init();
        image.init();

        //common init
        logotype.init();
        nav.init();
        info.init();

        //sections init
        sections.init();

        welcome.init();
        about.init();
        work.init();
        contact.init();

        // when loaded
        this.show();

        // when resized
        this.resize();
    },
    show: function () {
        border.show();

        this.handleParallax();
        this.handleScroll();
        this.handleArrows();
    },
    resize: function () {
        $(window).resize(function() {
            border.setSize();
            image.setSize();
            sections.resize();
        });
    },
    handleScroll: function () {
        $(window).bind('wheel', function(event) {
            if ( sections.animation.isActive() === false ){
                if (event.originalEvent.deltaY >= 0)
                    sections.next();
                else
                    sections.prev();
            }

            if ( info.animation.isActive() )
                info.hide();
        });
    },
    handleArrows: function(){
        $(window).keydown(function (e) {
            if ( sections.animation.isActive() === false ){
                var button = e.which;
                switch (button) {
                    case 38:
                        sections.prev();
                        break;
                    case 40:
                        sections.next();
                        break;
                }
            }
            if ( info.animation.isActive() )
                info.hide();
        });
    },
    handleParallax: function(){
        var mouse = {
            x: 0,
            y: 0
        };
        var cx = window.innerWidth / 2;
        var cy = window.innerHeight / 2;

        var animation = new TimelineMax();

        $('body').on('mousemove', function(event) {
            var sectionImage = $('#image');
            var sectionTitle = $('#title');

            mouse.x = event.pageX;
            mouse.y = event.pageY;

            animation.clear();

            var dx = mouse.x - cx;
            var dy = mouse.y - cy;

            var tiltx = (dy / cy);
            var tilty = -(dx / cx);
            var radius = Math.sqrt(Math.pow(tiltx, 2) + Math.pow(tilty, 2));
            var degree = radius * 10;

            animation
                // .to(sectionImage, 1.5, {
                //     transform: 'scale(1.2) rotate3d(' + tiltx + ', ' + tilty + ', 0, ' + degree + 'deg)',
                //     ease: Power2.easeOut
                // }, 0)
                .to(sectionTitle, 1.5, {
                    transform: 'rotate3d(' + tiltx + ', ' + tilty + ', 0, -' + degree*2 + 'deg)',
                    ease: Power2.easeOut
                }, 0);
        });

        $(window).resize(function() {
            cx = window.innerWidth / 2;
            cy = window.innerHeight / 2;
        });
    }
};

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
var helper = {
    orientation: null,
    getOrientation: function(){
        var width = $(window).width(),
            height = $(window).height();

        if ( width > height ){
            this.orientation = 'landscape';
        } else {
            this.orientation = 'portrait';
        }

        return this.orientation;
    },
    splitText: function(element){
        var newText = '';
        var lines = element.html().split('<br>');
        var linesLength = lines.length;

        for(var i = 0; i < linesLength; i++){
            var indexOfStrongOpen = lines[i].indexOf('<strong>');
            lines[i] = lines[i].replace('<strong>','');
            lines[i] = lines[i].replace('</strong>','');

            var lineLength = lines[i].length;
            for(var j = 0; j < lineLength; j++ ){
                var addClass = '';
                if (j >= indexOfStrongOpen)
                    addClass = ' class="strong"';

                newText += '<span'+addClass+'>'+lines[i][j]+'</span>';
            }
            if ( i < linesLength-1 )
                newText += '<br/>';
        }
        return newText;
    },
    vh2px: function(vh){
        var docHeight = $(window).height();
        return Math.ceil(vh/100*docHeight);
    },
    vw2px: function(vw){
        var docWidth = $(window).width();
        return Math.ceil(vw/100*docWidth);
    }
};

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
var color = {
    colors: ['#1180AE', '#5F4B8B', '#D74061', '#F37A29'],
    init: function(section){


        this.setColor(section);

    },
    setColor: function(section){
        var element = $('#color');
        var sc = this.colors[0];

        switch (section){
            case 'welcome':
                sc = color.colors[0];
                break;
            case 'about':
                sc = color.colors[1];
                break;
            case 'work':
                sc = color.colors[2];
                break;
            case 'contact':
                sc = color.colors[3];
                break;

        }
        TweenMax.to(element, 0.5, {backgroundColor: sc});
    }
};
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
var about = {
    animation: null,
    init: function(){
        var section = $('#about');
        var allDelay = 0;
        this.animation = new TimelineMax({
            paused: true,
            delay: allDelay
        });
        this.animation
            .fromTo(section, 0.1, {autoAlpha: 0}, {autoAlpha: 1});

        var image = section.find('img');
        // this.animation.fromTo(image, 1, {right: '10vh', opacity:0}, {right: '0vh', opacity:1});

        section.find('p').each(function(i){
            var p = $(this);
            about.animation.fromTo(p, 0.5, {opacity:0, left: '10vh'}, { opacity:1, left: '0vh'}, i * 0.25);
        });

        // image.on('load',function () {
        //     aboutImage.init();
        // });
    },
    show: function(){
        this.animation.timeScale(1);
        this.animation.play();

        // aboutImage.show();
        aboutImageCanvas.init();
    },
    hide: function(){
        this.animation.timeScale(3);
        this.animation.reverse();

        // aboutImage.hide();
    },
    resize: function(){

    }
};
var aboutImageCanvas = {
    initialized: false,
    animation: null,
    init: function () {
        var container = $('#about-photo-container');
        var img = container.find('img');
        img.css('opacity',0);
        var imgWidth = img.innerWidth();
        var imgHeight = img.innerHeight();

        var canvas = container.find('canvas'),
            canvasElem = canvas[0];
        var contex = canvasElem.getContext("2d");

        var amplitude = helper.vh2px(20);

        canvasElem.width = imgWidth + (amplitude * 2);
        canvasElem.height = imgHeight + (amplitude * 2);
        canvas.css({
            bottom: '-'+(amplitude-2)+'px',
            right: '-'+amplitude+'px'
        });


        var image = new Image();
        image.src = img.attr('src');

        var scale = image.width / imgWidth;
        var tileSize = helper.vh2px(2.5),
            imgSize = tileSize*scale;

        var cols = Math.ceil(imgWidth/tileSize);
        var rows = Math.ceil(imgHeight/tileSize);

        var i = 0;
        image.onload = function(){
            TweenLite.ticker.addEventListener("tick", aboutImageShow);

            var imgs = [];
            for(var x = 0; x < cols; x++) {
                imgs[x] = [];
                for (var y = 0; y < rows; y++) {
                    var random1 = Math.random(),
                        random2 = Math.random();
                    var tileX = x * tileSize + amplitude,
                        tileY = y * tileSize + amplitude,
                        tileRandomX = tileX + Math.floor( random1 * amplitude ) - Math.floor( random2 * amplitude ),
                        tileRandomY = tileY + Math.floor( random2 * amplitude ) - Math.floor( random1 * amplitude ),
                        imgX = tileX * scale,
                        imgY = tileY * scale;

                    imgs[x][y] = new Image();
                    imgs[x][y].src = img.attr('src');
                    imgs[x][y].globalAlpha = 0;
                    imgs[x][y].xpos = tileRandomX;
                    imgs[x][y].ypos = tileRandomY;

                    // var animationTime = random1 * 1.25;
                    var animationTime = random1 * 1;
                    // var delay = random2 * 1.25;
                    var delay = i*0.1;

                    TweenMax
                        .to(imgs[x][y], animationTime/2, {
                            xpos: tileX,
                            ypos: tileRandomY,
                            delay: delay
                        });
                    TweenMax
                        .to(imgs[x][y], animationTime/2, {
                            xpos: tileX,
                            ypos: tileY,
                            delay: delay+animationTime/2
                        });
                    TweenMax
                        .to(imgs[x][y], animationTime*1.5, {
                            globalAlpha: 1,
                            delay: delay
                        });
                }

                i++;
            }

            function aboutImageShow(){
                contex.clearRect(0, 0, imgWidth+amplitude*2, imgHeight+amplitude*2);

                for(var x = 0; x < cols; x++){
                    for(var y = 0; y < rows; y++){
                        var tileX = x * tileSize,
                            tileY = y * tileSize,
                            imgX = tileX*scale,
                            imgY = tileY*scale;

                        contex.globalAlpha = imgs[x][y].globalAlpha;
                        contex.imageSmoothingEnabled = false;
                        contex.drawImage(imgs[x][y], imgX, imgY, imgSize, imgSize, imgs[x][y].xpos, imgs[x][y].ypos, tileSize, tileSize);
                    }

                }
            }
        };
    },
    show: function () {

    },
    hide: function () {

    }
};
var aboutImage = {
    initialized: false,
    animation: null,
    init: function () {
        var imageContainer = $('#about-photo-container'),
            image = imageContainer.find('img');

        imageContainer.find('.photo-particle').remove();

        var i = 0,
            j = 0;
        var cols = 10,
            rows = 0;

        var elements = [];

        var imageWidth = image.width(),
            imageHeight = image.height(),
            divWidth = imageWidth / cols,
            ratio = imageHeight/imageWidth;

        imageWidth = imageWidth.toFixed(2);
        imageHeight = imageHeight.toFixed(2);

        rows = Math.ceil( cols * ratio );

        var backgroundSize = imageWidth + 'px ' + imageHeight + 'px';
        for( i = 0; i < cols; i++){
            for ( j = 0; j < rows; j++){
                var backgroundLeft = ( i * divWidth );
                var backgroundTop = ( j * divWidth );

                var divHeight = 0;
                if  ( j === rows - 1 ){
                    divHeight = imageHeight - ( j * divWidth );
                } else {
                    divHeight = divWidth;
                }

                var div = '<div class="photo-particle"></div>';
                imageContainer.append(div);

                elements[i+'.'+j] = imageContainer.find('div.photo-particle:last-of-type');
                elements[i+'.'+j].css({
                    width: divWidth+1 + 'px',
                    height:  divHeight+1 + 'px',
                    left: backgroundLeft + 'px',
                    top: backgroundTop + 'px',
                    backgroundPosition: (imageWidth - backgroundLeft ) + 'px ' + (imageHeight - backgroundTop) + 'px',
                    backgroundSize: backgroundSize
                });
            }
        }


        if( this.animation === null ){
            this.animation = new TimelineMax({paused:true});
        } else {
            this.animation.clear();
        }

        this.animation.set(image, {opacity: 0});

        var amplitude = helper.vh2px(25);

        for( i = 0; i < cols; i++) {
            for ( j = 0; j < rows; j++) {
                var element = elements[i+'.'+j];

                var oldLeft = element.css('left');
                oldLeft = oldLeft.replace('px','');
                oldLeft = parseFloat(oldLeft);
                var oldTop = element.css('top');
                oldTop = oldTop.replace('px','');
                oldTop = parseFloat(oldTop);

                var random1 = Math.random();
                var random2 = Math.random();

                var left = Math.floor( random1 * amplitude ) - Math.floor( random2 * amplitude );
                var top = Math.floor( random1 * amplitude ) - Math.floor( random2 * amplitude );

                var delay = (cols - i) * 0.05 + j * 0.025 + random1;
                delay = parseFloat( delay.toFixed(2) );

                var aTime = 0.5;
                if (random1 > random2){
                    aTime *= random1;
                } else {
                    aTime *= random2;
                }

                aTime = parseFloat( aTime.toFixed(2) );

                aboutImage.animation
                    .fromTo(element, aTime, { opacity: 0 }, { opacity: 1 }, delay)
                    .fromTo(element, aTime, { scale: 0.1 }, { scale: 1 }, delay)
                    .fromTo(element, aTime/2, { left: (oldLeft - left) + 'px' }, { left: oldLeft + 'px' }, delay)
                    .fromTo(element, aTime/2, { top: (oldTop - top) + 'px' }, { top: oldTop + 'px' }, delay + aTime/2);
            }
        }
    },
    show: function () {
        this.animation.timeScale(1);
        this.animation.play();
    },
    hide: function () {
        this.animation.timeScale(3);
        this.animation.reverse();
    }
};
var work = {
    animation: null,
    handled: false,
    init: function(){
        var section = $('#work');

        var h2 = section.find('h2'),
            h2lastProjects = h2.eq(0),
            h2used = h2.eq(1);

        if ( h2.find('.underline').length === 0 ){
            var underline = '<div class="underline"></div>';

            var h2lastProjectsSplited = helper.splitText(h2lastProjects);
            h2lastProjects.html(h2lastProjectsSplited + underline);

            var h2usedSplited = helper.splitText(h2used);
            h2used.html(h2usedSplited + underline);
        }

        slider.init();
        this.animationInit();
    },
    animationInit: function(){
        var allDelay = 0.1;

        if (this.animation === null){
            this.animation = new TimelineMax({
                paused: true
            });
        } else {
            this.animation.clear();
        }

        var section = $('#work');
        this.animation
            .fromTo(section, 0.1, { autoAlpha: 0 }, { autoAlpha: 1 }, allDelay);

        var underline = section.find('#work-portfolio h2 .underline');
        this.animation
            .fromTo( underline, 1.5, {width: '0%'},{width: '100%'}, allDelay );

        var amplitude = helper.vh2px(10);
        var i = 0;

        section.find('#work-portfolio h2 span').each(function () {
            var span = $(this);
            var random = Math.random(),
                random2 = Math.random();
            var left = Math.floor( random * amplitude ) - Math.floor( random2 * amplitude );
            var top = Math.floor( random2 * amplitude ) - Math.floor( random * amplitude );
            var delay = allDelay + ( i * 0.1 );
            var aTime = Math.floor( random * 100 ) / 200;

            work.animation
                .fromTo(span,  aTime,
                    { left: left + 'px' },
                    { left: '0px', ease: Power4.easeOut },
                    delay )
                .fromTo(span, aTime,
                    { top: top + 'px' },
                    { top: '0px', ease: Power4.easeOut },
                    delay + aTime / 2 )
                .fromTo(span, aTime*2,
                    { opacity: 0 },
                    { opacity: 1 },
                    delay );

            i += 1;
        });

        allDelay += 1;

        this.animation
            .set( section.find('.projects'), {opacity: 1}, allDelay);

        var project = $('#work-portfolio .projects .project.active');
        var title = project.find('.project-title');
        var imageDesktop = project.find('.project-images .desktop');
        var imageMobile = project.find('.project-images .mobile');
        var description = project.find('.project-description');
        this.animation
            .fromTo(title, 0.5, {opacity: 0, left: '-=5vh'}, {opacity: 1, left: '0vh'}, allDelay)
            .fromTo(imageDesktop, 0.75, {opacity:0, left: '-=15vh', transform: 'rotateY(90deg)'},{opacity: 1, left: '0vh', transform: 'rotateY(0deg)'}, allDelay+0.3)
            .fromTo(imageMobile, 0.5, {opacity:0, right: '-=15vh', yPercent: -50, transform: 'rotateY(-90deg)'},{opacity: 1, right: '0vh', yPercent: -50, transform: 'rotateY(0deg)'}, allDelay+0.45)
            .fromTo(description, 0.5, {opacity: 0, left: '-=5vh'}, {opacity: 1, left: '0vh'}, allDelay + 0.7);

        allDelay += 1;

        var arrowLeft = section.find('.projects i.fa-angle-left');
        var arrowRight = section.find('.projects i.fa-angle-right');
        this.animation
            .fromTo( arrowLeft, 0.25, {opacity:0, scale: 0.1}, {opacity:1, scale: 1}, allDelay )
            .fromTo( arrowRight, 0.25, {opacity:0, scale: 0.1}, {opacity:1, scale: 1}, allDelay+0.15 );


        work.animation
                    .fromTo( section.find('#work-species h2 .underline'), 1.5, {width: '0%'},{width: '100%'}, allDelay );

        i = 0;
        section.find('#work-species h2 span').each(function () {
            var span = $(this);
            var random = Math.random(),
                random2 = Math.random();
            var left = Math.floor( random * amplitude ) - Math.floor( random2 * amplitude );
            var top = Math.floor( random2 * amplitude ) - Math.floor( random * amplitude );
            var delay = allDelay + ( i * 0.1 );
            var aTime = Math.floor( random * 100 ) / 200;

            work.animation
                .fromTo(span,  aTime,
                    { left: left + 'px' },
                    { left: '0px', ease: Power4.easeOut },
                    delay )
                .fromTo(span, aTime,
                    { top: top + 'px' },
                    { top: '0px', ease: Power4.easeOut },
                    delay + aTime / 2 )
                .fromTo(span, aTime*2,
                    { opacity: 0 },
                    { opacity: 1 },
                    delay );

            i += 1;
        });

        allDelay += 1;

        var listPosition = section.find('ul li');
        this.animation
            .staggerFromTo(listPosition, 0.15, {left: '-=2.5vh', autoAlpha:0}, { left: 0, autoAlpha: 1 }, 0.1, allDelay);

    },
    show: function() {
        this.animation.timeScale(1);
        this.animation.play();
    },
    hide: function(){
        if ( this.animation.isActive() === true ){
            this.animation.timeScale(3);
            this.animation.reverse();
        } else {
            this.animationInit();

            var tc = this.animation.totalDuration();
            this.animation.seek(tc, false);
            this.animation.timeScale(3);
            this.animation.reverse();
        }
    },
    handle: function(){

    },
    resize: function(){

    }
};
var slider = {
    allDelay: 0,
    animation: null,
    init: function(){
        if( $('#work-portfolio .projects .project.active').length === 0 )
            $('#work-portfolio .projects .project:eq(0)').addClass('active');

        if ( this.animation === null ){
            this.animation = new TimelineMax({
                paused:true
            });

            this.handle();
        }
    },
    show: function () {
        var allDelay = this.allDelay;
        var project = $('#work-portfolio .projects .project.active');
        var title = project.find('.project-title');
        var imageDesktop = project.find('.project-images .desktop');
        var imageMobile = project.find('.project-images .mobile');
        var description = project.find('.project-description');
        work.animation
            .fromTo(title, 0.5, {opacity: 0, left: '-=5vh'}, {opacity: 1, left: '0vh'}, allDelay)
            .fromTo(imageDesktop, 0.75, {opacity:0, left: '-=15vh', transform: 'rotateY(90deg)'},{opacity: 1, left: '0vh', transform: 'rotateY(0deg)'}, allDelay+0.3)
            .fromTo(imageMobile, 0.5, {opacity:0, right: '-=15vh', yPercent: -50, transform: 'rotateY(-90deg)'},{opacity: 1, right: '0vh', yPercent: -50, transform: 'rotateY(0deg)'}, allDelay+0.45)
            .fromTo(description, 0.5, {opacity: 0, left: '-=5vh'}, {opacity: 1, left: '0vh'}, allDelay + 0.7);
    },
    hide: function () {
        var allDelay = this.allDelay;
        var project = $('#work-portfolio .projects .project');
        var title = project.find('.project-title');
        var imageDesktop = project.find('.project-images .desktop');
        var imageMobile = project.find('.project-images .mobile');
        var description = project.find('.project-description');
        work.animation
            .fromTo(title, 0.5, {opacity: 0, left: '-=5vh'}, {opacity: 1, left: '0vh'}, allDelay)
            .fromTo(imageDesktop, 0.75, {opacity:0, left: '-=15vh', transform: 'rotateY(90deg)'},{opacity: 1, left: '0vh', transform: 'rotateY(0deg)'}, allDelay+0.5)
            .fromTo(imageMobile, 0.5, {opacity:0, right: '-=15vh', yPercent: -50, transform: 'rotateY(-90deg)'},{opacity: 1, right: '0vh', yPercent: -50, transform: 'rotateY(0deg)'}, allDelay+0.65)
            .fromTo(description, 0.5, {opacity: 0, left: '-=5vh'}, {opacity: 1, left: '0vh'}, allDelay+0.9);
    },
    handle: function () {
        var left = $('#work-portfolio .projects .fa-angle-left');
        var right = $('#work-portfolio .projects .fa-angle-right');

        left.click(function(){
            if ( slider.animation.isActive() === false ){
                var slides = $('#work-portfolio .projects .project');
                var slideActive = null;
                var slideActiveId = null;

                slides.each(function (i) {
                    var element = $(this);
                    if( element.hasClass('active') ){
                        slideActive = element;
                        slideActiveId = i;
                        $('.projects .project').removeClass('active');
                    }
                });

                var slidePrev = slides.eq(slides.length - 1);
                if ( slideActiveId !== null && slideActiveId - 1 > 0 ){
                    slidePrev = slides.eq( slideActiveId - 1);
                }
                slidePrev.addClass('active');

                slider.showSlide( slideActive, slidePrev );
            }
        });
        right.click(function(){
            if ( slider.animation.isActive() === false ) {
                var slides = $('#work-portfolio .projects .project');
                var slideActive = null;
                var slideActiveId = null;

                slides.each(function (i) {
                    var element = $(this);
                    if (element.hasClass('active')) {
                        slideActive = element;
                        slideActiveId = i;
                        $('.projects .project').removeClass('active');
                    }
                });
                var slideNext = slides.eq(0);
                if (slideActiveId !== null && slideActiveId + 1 < slides.length) {
                    slideNext = slides.eq(slideActiveId + 1);
                }
                slideNext.addClass('active');

                slider.showSlide(slideActive, slideNext);
            }
        });
    },
    showSlide: function ( slideActive, slideNext ) {
        this.animation.clear();

        var title = slideActive.find('.project-title');
        var imageDesktop = slideActive.find('.project-images .desktop');
        var imageMobile = slideActive.find('.project-images .mobile');
        var description = slideActive.find('.project-description');

        this.animation
            .fromTo(title, 0.5, {opacity: 1, left: '0vh'}, {opacity: 0, left: '-=5vh'})
            .fromTo(imageDesktop, 0.75, {opacity: 1, left: '0vh', transform: 'rotateY(0deg)'}, {opacity:0, left: '-=15vh', transform: 'rotateY(90deg)'}, 0.15)
            .fromTo(imageMobile, 0.5, {opacity: 1, right: '0vh', yPercent: -50, transform: 'rotateY(0deg)'}, {opacity:0, right: '-=15vh', yPercent: -50, transform: 'rotateY(-90deg)'}, 0.3)
            .fromTo(description, 0.5, {opacity: 1, left: '0vh'}, {opacity: 0, left: '-=5vh'}, 0.45);

        title = slideNext.find('.project-title');
        imageDesktop = slideNext.find('.project-images .desktop');
        imageMobile = slideNext.find('.project-images .mobile');
        description = slideNext.find('.project-description');
        var allDelay = 0.75;
        this.animation
            .fromTo(title, 0.5, {opacity: 0, left: '-=5vh'}, {opacity: 1, left: '0vh'}, allDelay)
            .fromTo(imageDesktop, 0.75, {opacity:0, left: '-=15vh', transform: 'rotateY(90deg)'},{opacity: 1, left: '0vh', transform: 'rotateY(0deg)'}, allDelay+0.3)
            .fromTo(imageMobile, 0.5, {opacity:0, right: '-=15vh', yPercent: -50, transform: 'rotateY(-90deg)'},{opacity: 1, right: '0vh', yPercent: -50, transform: 'rotateY(0deg)'}, allDelay+0.45)
            .fromTo(description, 0.5, {opacity: 0, left: '-=5vh'}, {opacity: 1, left: '0vh'}, allDelay+0.7);

        this.animation.play();
    }
};

var contact = {
    animation: null,
    init: function(){
        form.init();

        if ( this.animation === null ){
            this.animation = new TimelineMax({
                paused: true
            });
        } else {
            this.animation.clear();
        }

        var section = $('#contact');
        this.animation
            .fromTo(section, 0.5, {autoAlpha: 0}, {autoAlpha: 1});

        var info = $('#contact-data-intro, #contact-data-phone, #contact-data-email');

        this.animation
            .staggerFromTo(info, 0.4, {left: '-5vh', opacity: 0}, {left: '0', opacity: 1}, 0.15, 0.3);

        var icons = $('#contact-data-socials a');
        this.animation
            .staggerFromTo(icons, 0.25, {scale: 0.1, opacity: 0}, {scale: 1, opacity: 1}, 0.15, 0.6);

        var rows = $('#contact-form .form-row');
        this.animation
            .staggerFromTo(rows, 0.5, {right: '-5vh', opacity: 0}, {right: '0', opacity: 1}, 0.15, 0.9);
    },
    show: function(){
        this.init();
        this.animation.timeScale(1);
        this.animation.play();
    },
    hide: function(){
        this.animation.timeScale(3);
        this.animation.reverse();
    },
    resize: function(){

    }
};
var form = {
    initialized: false,
    init: function () {
        if (this.initialized === false ){
            var form = $('#contact-form form');
            form.validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 3
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    message: {
                        required: true,
                        minlength: 10
                    }
                }
                // ,
                // messages: {
                //     name: {
                //         required: "Napisz swoje imię",
                //         minlength: "Napisz poprawne imię"
                //     },
                //     email: {
                //         required: "Napisz swój adres e-mail",
                //         email: "Napisz poprawny adres e-mail"
                //     },
                //     message: {
                //         required: "Napisz swoją wiadomość",
                //         minlength: "Twoja wiadomość musi skladać się z conajmniej 10 znaków"
                //     }
                // }
            });

            form.submit(function(e){
                e.preventDefault();
                if ( form.valid() ){
                    $.ajax({
                        type: "POST",
                        url: 'https://wroblewskipatryk.pl/sendmsg',
                        crossDomain: true,
                        data: new FormData(this),
                        dataType: "json",
                        processData: false,
                        contentType: false,
                        headers: {
                            "Accept": "application/json"
                        }
                    }).done(function(data) {
                        console.log(data);
                        // console.log(data.responseText);
                        // if ( data.responseText === "Success" ){
                        //     form.find('input').value('');
                        //     form.find('textarea').text('');
                            //
                            // alert('Thanks! Your messsage was sent!');
                        // }
                    }).fail(function(data) {
                        console.log(data);
                    });
                }
            });

            this.initialized = true;
        }
    }
};
