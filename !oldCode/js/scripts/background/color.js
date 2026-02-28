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