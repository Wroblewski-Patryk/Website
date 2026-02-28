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
