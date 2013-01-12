
google.load('visualization', '1.0', {
    'packages':['corechart']
});
google.setOnLoadCallback(drawChart);

function createChart(options, url, callbackData, id, chartType){
    $('#' + id).html('<span class="info">' + m_synchrone_translate('Preparing data...') + '</span>');
    options['title'] = m_synchrone_translate(options['title']);
    $.ajax({
        url: baseUrl + url,
        method: 'get',
        cache: false,
        async: true,
        success: function(data){
            if(data['data'] == undefined) return;
            var dchart = callbackData(data['data']);
            
            var chart = new google.visualization[chartType](document.getElementById(id));
            $(window).bind('redraw', function(){
                chart.draw(dchart, options);
            });
            $(window).resize(function(){
                chart.draw(dchart, options);
            });
            chart.draw(dchart, options);
        }
    });
}
      
function drawChart() {
    groupsChart();
    spendingChart();
    spendingCategoryChart();
    summaryChart();
}

function summaryChart(){
    createChart({
        'title':'Money summary for last 30 days.',
        'height':300,
        'pointSize' : 3
    }, 'ajax/money', function(data){
        var dchart = new google.visualization.DataTable();
        dchart.addColumn('string', m_synchrone_translate('Date') );
        dchart.addColumn('number', m_synchrone_translate('Summary') );
        var rows = [];
        $.each(data, function(k, v){
            rows.push([ v['date'], parseInt(v['sumary']) ]);
        });
        dchart.addRows(rows);
        return dchart;
    }, 'chart_summary', 'AreaChart');
}

function spendingChart(){
    createChart({
        'title':'Money spend by for last 30 days.',
        'height':300,
        'pointSize' : 3,
        'colors' : ['a00']
    }, 'ajax/spending', function(data){
        var dchart = new google.visualization.DataTable();
        dchart.addColumn('string', m_synchrone_translate('Date') );
        dchart.addColumn('number', m_synchrone_translate('Summary') );
        var rows = [];
        $.each(data, function(k, v){
            rows.push([ v['date'], Math.abs(v['sumary']) ]);
        });
        dchart.addRows(rows);
        return dchart;
    }, 'chart_spending', 'AreaChart');
}

function spendingCategoryChart(){
    if(spendingCategoryChart.dchart != undefined) return;
    createChart({
        'title':'Money spend by category for last 30 days.',
        'height':400,
        'isStacked': true
    }, 'ajax/linegraph', function(data){
        spendingCategoryChart.dchart = null;
        var rows = [], groups = [];
        $.each(data, function(k, v){
            rows.push(v);
        });
        spendingCategoryChart.columns = [];
        $.each(rows[0], function(k, v){
            spendingCategoryChart.columns.push({
                title: v,
                index: k,
                visible: true
            });
            if(k > 0) groups.push(v);
        });
        spendingCategoryChart.dchart = google.visualization.arrayToDataTable(rows);
        spendingCategoryChart.view = new google.visualization.DataView(spendingCategoryChart.dchart);
        setVisibilityOfColumn = function(column, value){
            $.each(spendingCategoryChart.columns, function(k, v){
                if(column == v.title && k > 0){
                    spendingCategoryChart.columns[k].visible = value;
                    return;
                }
            });
        };
        getVisibleColumns = function(){
            var result = [];
            $.each(spendingCategoryChart.columns, function(k, v){
                if(v.visible){
                    result.push(v.index);
                }
            });
            return result;
        };
        createCheckBox = function(title){
            if(createCheckBox.num == undefined){
                createCheckBox.num = 0;
            }
            var value = title.replace(/[^a-z0-9]+/i, '');
            var name = 'checkbox_' + value + '_' + createCheckBox.num++;
            $('#' + name).live('change', function(e){
                var title = $(this).val();
                var checked = ($(this).is(':checked')) ? true : false;
                setVisibilityOfColumn(title, checked);
                spendingCategoryChart.view.setColumns(getVisibleColumns());
                $(window).trigger('redraw');
            });
            return '<label style="background: #eee;" for="' + name + '">' + title + '<input type="checkbox" style="margin:0 0 0 10px;" value="' + title + '" checked="checked" name="' + name + '" id="' + name + '" /></label>';
        };
        getCheckBoxes = function(data){
            var result = [];
            result.push('<style> div#checkbox_group > * { float:left; margin: 0 10px 0 0; } </style>');
            result.push('<div id="checkbox_group">');
            $.each(data, function(k, v){
                result.push(createCheckBox(v));
            });
            result.push('</div>');
            return result.join("\n");
        };
        $('#chart_spending_category').parent().append( getCheckBoxes(groups) );
        return spendingCategoryChart.view;
    }, 'chart_spending_category', 'ColumnChart');
}

function groupsChart(){
    createChart({
        'title':'Money spend by this month.',
        'height':200
    }, 'ajax/index', function(data){
        var dchart = new google.visualization.DataTable();
        dchart.addColumn('string', m_synchrone_translate('Date') );
        dchart.addColumn('number', m_synchrone_translate('Summary') );
        var rows = [];
        $.each(data, function(k, v){
            rows.push([ v['categName'], Math.abs(v['sumary']) ]);
        });
        dchart.addRows(rows);
        return dchart;
    }, 'chart_div', 'PieChart');
}