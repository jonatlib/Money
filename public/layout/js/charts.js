
google.load('visualization', '1.0', {
    'packages':['corechart']
});
google.setOnLoadCallback(drawChart);

      
function drawChart() {
    groupsChart();
    spendingChart();
}

function spendingChart(){
//    var dchart = new google.visualization.DataTable();
//    dchart.addColumn('string', 'Topping');
//    dchart.addColumn('number', 'Slices');
    var options = {
        'title':'Money spend by this month.',
        'height':300
    };

    var dchart = null;
    $.ajax({
        url: baseUrl + 'ajax/linegraph',
        method: 'get',
        success: function(data){
            if(data['data'] == undefined) return;
            var rows = [];
            $.each(data['data'], function(k, v){
//                rows.push([ v['date'], Math.abs(v['sumary']) ]);
                rows.push(v);
            });
            dchart = google.visualization.arrayToDataTable(rows);
//            dchart.addRows(rows);
            chart.draw(dchart, options);
        }
    });

    var chart = new google.visualization.AreaChart(document.getElementById('chart_spending'));
    $(window).resize(function(){
        chart.draw(dchart, options);
    });
    chart.draw(dchart, options);
}

function groupsChart(){
    var dchart = new google.visualization.DataTable();
    dchart.addColumn('string', 'Topping');
    dchart.addColumn('number', 'Slices');
    var options = {
        'title':'Money spend by this month.',
        'height':150
    };

    $.ajax({
        url: baseUrl + 'ajax/index',
        method: 'get',
        success: function(data){
            if(data['data'] == undefined) return;
            var rows = [];
            $.each(data['data'], function(k, v){
                rows.push([ v['categName'], Math.abs(v['sumary']) ]);
            });
            dchart.addRows(rows);
            chart.draw(dchart, options);
        }
    });

    var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
    $(window).resize(function(){
        chart.draw(dchart, options);
    });
    chart.draw(dchart, options);
}