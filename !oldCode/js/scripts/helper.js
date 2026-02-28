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
