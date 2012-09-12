function timeTrigger(){
    $('input').trigger('time');
    setTimeout(function(){
        timeTrigger();
    }, 200);
}

var success1 = false;
var success2 = false;

$(function(){
    timeTrigger();    
    $('input#email').bind('change keyup focus blur time',function(){
        var element = $(this);
        var addon = $(element).next().children('i');
        if(element.val().match(/[0-9a-z\.\-]+\@[0-9a-z\.\-]+\.[a-z]+/i)){
            addon.removeClass().addClass('icon-ok-sign');
            success1 = true;
        }else{
            addon.removeClass().addClass('icon-question-sign');
            success1 = false;
        }
    });
    
    $('input#password').bind('change keyup focus blur time',function(){
        var element = $(this);
        var addon = $(element).next().children('i');
        if(element.val().length > 5){
            addon.removeClass().addClass('icon-ok-sign');
            success2 = true;
        }else{
            addon.removeClass().addClass('icon-question-sign');
            success2 = false;
        }
    });
    
    $.easing.speedInOut = function(t, millisecondsSince, startValue, endValue, totalDuration) {
        if(t <= 0) return startValue;
        if(t >= endValue) return endValue;
        var s = ( Math.PI / 2 ) * t;
        return endValue * Math.sin( s * 5 );
    }; 
    
    $('form#login').submit(function(){
        if(success1 && success2) return true;
        var easing = 'speedInOut';
        var speed = 800;
        $('div#content').animate({
            marginTop: 50 + 'px'
        }, speed, easing, function(){
            $('div#content').animate({
                marginTop: 0 + 'px'
            }, speed, easing);
        });
        return false;
    });
});