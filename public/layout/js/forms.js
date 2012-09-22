function requiredInputs(){
    $('input.required').each(function(){
        var name = $(this).attr('name');
        $('label[for="' + name + '"]').append('<span class="inputrequired">*</span>');
    });
}

$(function(){
    requiredInputs();
});