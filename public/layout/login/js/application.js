function timeTrigger(){
    $('input').trigger('time');
    setTimeout(function(){
        timeTrigger();
    }, 200);
}

$(function(){
    timeTrigger();    
    $('input#email').bind('change keyup focus blur time',function(){
        var element = $(this);
        var addon = $(element).next().children('i');
        if(element.val().match(/[0-9a-z\.\-]+\@[0-9a-z\.\-]+\.[a-z]+/i)){
            addon.removeClass().addClass('icon-ok-sign');
        }else{
            addon.removeClass().addClass('icon-question-sign');
        }
    });
    
    $('input#password').bind('change keyup focus blur time',function(){
        var element = $(this);
        var addon = $(element).next().children('i');
        if(element.val().length > 5){
            addon.removeClass().addClass('icon-ok-sign');
        }else{
            addon.removeClass().addClass('icon-question-sign');
        }
    });
});