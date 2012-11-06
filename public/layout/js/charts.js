
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
    createChart({
        'title':'Money spend by category for last 30 days.',
        'height':400,
        'isStacked': true
    }, 'ajax/linegraph', function(data){
        var dchart = null;
        var rows = [];
            $.each(data, function(k, v){
                rows.push(v);
            });
            dchart = google.visualization.arrayToDataTable(rows);
        return dchart;
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