<script src="http://code.highcharts.com/highcharts.js"></script>
<script src="http://code.highcharts.com/highcharts-more.js"></script>
<script src="http://code.highcharts.com/modules/exporting.js"></script>

<script type="text/javascript">
    $(function () {

        $('#graph').highcharts({

            chart: {
                polar: true,
                type: 'line'
            },

            title: {
                text: 'Feedback 360',
                x: -80
            },

            pane: {
                size: '80%'
            },

            xAxis: {
                categories: <?php echo json_encode($categories) ?>,
                tickmarkPlacement: 'on',
                lineWidth: 0
            },

            yAxis: {
                gridLineInterpolation: 'polygon',
                lineWidth: 0,
                min: 0
            },

            tooltip: {
                shared: true,
                pointFormat: '<span style="color:{series.color}">{series.name}: <b>{point.y}</b><br/>'
            },

            legend: {
                align: 'right',
                verticalAlign: 'top',
                y: 70,
                layout: 'vertical'
            },

            series: [{
                name: 'Self Rating',
                data: <?php echo json_encode($self_rating) ?>,
                pointPlacement: 'on'
            }, {
                name: 'Group Rating',
                data: <?php echo json_encode($avg_rating) ?>,
                pointPlacement: 'on'
            }]

        });
    });
</script>