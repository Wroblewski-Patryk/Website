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
