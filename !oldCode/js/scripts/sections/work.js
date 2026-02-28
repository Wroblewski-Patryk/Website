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
