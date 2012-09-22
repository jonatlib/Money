function requiredInputs(){
    $('input.required').each(function(){
        var name = $(this).attr('name');
        var element = $('label[for="' + name + '"]');
        element.append('<span class="inputrequired" title="required">*</span>');
    });
}

$(function(){
    requiredInputs();
});