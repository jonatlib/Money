function scrollSideBar(){
    var win = $(window);
    var side = $('div#sidebar div.attach');
    if(side.length < 1) return;
    
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
    $('#widgetField a').click(function(e){
        if($('div#widgetCalendar:hidden').length > 0){
            $('div#widgetCalendar:hidden').fadeIn('slow');
        }else if($('div#widgetCalendar:visible').length > 0){
            $('div#widgetCalendar:visible').fadeOut('slow');
        }
        e.stopPropagation();
    });
    $(document).click(function(){
        $('div#widgetCalendar:visible').fadeOut('slow');
    });
    $('#widgetCalendar').show();
    
    var loadDate = function(url){
        var date = new Date();
        $.ajax({
            url: baseUrl + 'ajax/' + url,
            type: 'get',
            cache: false,
            async: false,
            success: function(data){
                if(data['date'] == undefined){
                    return;
                }
                date = new Date( parseInt(data['date']) * 1000 );
            }
        });
        return date;
    };
    
    var saveDate = function(start, stop){
        var r = function(d){
            return Math.round((new Date(d)).getTime() / 1000);
        };
        var data = {start: r(start), stop: r(stop)};
        $.ajax({
            url: baseUrl + 'ajax/datesave',
            type: 'POST',
            cache: false,
            async: true,
            data: data,
            success: function(data){
                if(data['result'] == undefined || data['result'] != 'success'){
                    alert(m_synchrone_translate('Could not save date.'));
                }else{ location.reload(true); }
            }
        });
    };
    
    var formatDate = function(date){
        return date.getDate() + "." + (date.getMonth() + 1) + "." + date.getFullYear();
    };
    
    var callback = function(formated, date, html, save){
        if(formated[0] == formated[1]) return;
        var a = new Date(date.pop());
        var b = new Date(date.pop());
        if(a < b){
            $('#widgetField div').get(0).innerHTML = formatDate(a) + ' - ' + formatDate(b);
            if(save == undefined) saveDate(a, b);
        }else{
            $('#widgetField div').get(0).innerHTML = formatDate(b) + ' - ' + formatDate(a);
            if(save == undefined) saveDate(b, a);
        }
    };
    
    var start = loadDate('datestart');
    var stop = loadDate('datestop');
    
    $('#widgetCalendar').DatePicker({
        flat: true,
        format: 'm-d-Y',
        date: [start, stop],
        calendars: 3,
        mode: 'range',
        starts: 1,
        locale: {
            days: m_synchrone_translate(["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"]),
            daysShort: m_synchrone_translate(["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"]),
            daysMin: m_synchrone_translate(["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa", "Su"]),
            months: m_synchrone_translate(["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]),
            monthsShort: m_synchrone_translate(["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]),
            weekMin: m_synchrone_translate('wk')
        },
        onChange: callback
    });
    callback([ start, stop ], [ start, stop ], null, false);
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

function initConfirm(){
    $('a.confirm').click(function(e){
        e.preventDefault();
        var element = $(this);
        m_translate("js-confirm-" + element.attr('rel'), function(data){
            var r = confirm(data);
            if(r){
                window.location = element.attr('href');
            }
        });
        return false;
    });
}

$(function(){
    initConfirm();
    initDatePicker();
    scrollSideBar();
    setTimeout(cleanAlerts, 3000);
    initDropDown();
});