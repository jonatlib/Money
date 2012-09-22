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

function initDatePicker(){
    $('#widgetField a').click(function(){
        if($('div#widgetCalendar:hidden').length > 0){
            $('div#widgetCalendar:hidden').fadeIn('slow');
            return;
        }
        if($('div#widgetCalendar:visible').length > 0){
            $('div#widgetCalendar:visible').fadeOut('slow');
            return;
        }
    });
    $('#widgetCalendar').show();
    $('#widgetCalendar').DatePicker({
        flat: true,
        format: 'd.m.Y',
        date: [new Date(), new Date()],
        calendars: 3,
        mode: 'range',
        starts: 1,
        locale: {
            days: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
            daysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
            daysMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa", "Su"],
            months: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
            monthsShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            weekMin: 'wk'
        },
        onChange: function(formated) {
            $('#widgetField div').get(0).innerHTML = formated.join(' - ');
        }
    });
    $('#widgetCalendar').hide();
}

function initDropDown(){
    $('ul.dropdown-menu li').hover(function(){
        var e = $(this).find('i');
        e.addClass('icon-white');
    }, function(){
        var e = $(this).find('i');
        e.removeClass('icon-white');
    });
}

$(function(){
    initDatePicker();
    scrollSideBar();
    setTimeout(cleanAlerts, 3000);
    initDropDown();
    
    
});