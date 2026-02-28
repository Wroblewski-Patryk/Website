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