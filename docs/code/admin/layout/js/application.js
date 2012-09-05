function scrollSideBar(){
    var win = $(window);
    var side = $('div#sidebar div.attach');
    var sidebar = $('div#sidebar');
    var top = side.position().top;
    var margin = parseInt(side.css('margin-top'));
    var height = $(document).height();

    $(document).ready(processScroll);
    win.on('scroll', processScroll);
    win.on('resize', processScroll);
    
    if($(side).length > 1){
        side = $(side[0]);
    }
    
    function processScroll (){
        var scroll = win.scrollTop();
        var marginTop = margin;
        if(scroll > top && win.height() > sidebar.height() - parseInt(side.css('margin-top')) ){
            marginTop = (scroll - top + (margin - (margin / 2)) + 10 ) + 'px';
        }
        //        var bottom = sidebar.position().top + sidebar.height() + parseInt(sidebar.css('margin-top')) + parseInt(marginTop); //FIXME
        var bottom = 0;
        if( bottom < height ){
            side.stop().clearQueue().animate({
                marginTop: marginTop
            }, 'slow');
        }
    }
}

function cleanAlerts(){
    if(!cleanAlerts.element) cleanAlerts.element = $('div.content.alert');
    if(!cleanAlerts.index) cleanAlerts.index = 1;
    
    $('div#messages').css('position', 'absolute'); //FIXME get real height
    var height = $('div#messages').outerHeight(true);
    $('div#messages').css('position', 'static');
    $('div#messages').css('height', height + 'px');
    
    setTimeout(function(){
        $(cleanAlerts.element[cleanAlerts.index++ - 1]).fadeOut('slow', function() {
            $(this).remove();
        });
        if(cleanAlerts.index <= cleanAlerts.element.length)
            cleanAlerts();
        else
            $('div#messages').slideUp('slow');
    }, 2000);
}

$(function(){
    scrollSideBar();
    setTimeout(cleanAlerts, 3000);
    
//    $('div.content').hover(
//        function(){
//            $(this).stop().animate({
//                backgroundColor: '#ffffef'
//            }, 'slow');
//        },
//        function(){
//            $(this).stop().animate({
//                backgroundColor: '#fff'
//            }, 'slow');
//        });
        
    $('div#headMenu div.subnav ul > li > a').hover(function(){
        $(this).addClass('hover');
    }, function(){
        $(this).removeClass('hover'); //.clearQueue()
    });
    
    
    $('input').tooltip({
        placement: 'top', 
        delay: {
            show: 0, 
            hide: 500
        }
    });
});