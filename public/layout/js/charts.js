
google.load('visualization', '1.0', {
    'packages':['corechart']
});
google.setOnLoadCallback(drawChart);

      
function drawChart() {
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Topping');
    data.addColumn('number', 'Slices');
    data.addRows([
        ['Mushrooms', 3],
        ['Onions', 1],
        ['Olives', 1],
        ['Zucchini', 1],
        ['Pepperoni', 2]
        ]);
    var options = {
        'title':'Money spend by this day.',
        'height':300
    };

    var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
    $(window).resize(function(){
        chart.draw(data, options);
    });
    chart.draw(data, options);
}