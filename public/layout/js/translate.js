function m_translate_dictionary(handle, async){
    if(async === undefined){ async = true; }
    if(m_translate_dictionary.dic === undefined){
        $.ajax({
            url: baseUrl + 'ajax/dictionary',
            type: 'get',
            cache: true,
            async: async,
            success: function(data){
                if(data['dictionary'] == undefined){ handle([]); }
                m_translate_dictionary.dic = data['dictionary'];
                handle(data['dictionary']);
            }
        });
    }else{
        handle(m_translate_dictionary.dic);
    }
}

function m_translate_dictionary_text(text, handle, async){
    m_translate_dictionary(function(data){
        if(data[text]){
            handle(data[text]);
        }else{
            handle(text);
        }
    }, false);
}

function m_translate_dictionary_value(text, handle, async){
    if( text instanceof Array ){
        var result = new Array();
        for(var i = 0; i < text.length; i++){
            m_translate_dictionary_value(text[i], function(data){
                result[i] = data;
            }, false);
        }
        handle(result);
    }else{
        m_translate_dictionary_text(text, handle, async);
    }
}

function m_translate(text, handle, async){
    m_translate_dictionary_value(text, handle, async)
}

function m_synchrone_translate(text){
    var res = '';
    m_translate(text, function(data){ res = data; }, false);
    return res;
}