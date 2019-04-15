<div id="piechart"></div>
<script>
var piedata = <?php echo $piearr; ?>;
$(function () {
        // Create the chart
        $('#piechart').highcharts({
        	credits: {
			enabled: false
		},
            chart: {
                type: 'pie',
                height: 270
            },
            title: {
                text: ''
            },
            yAxis: {
                title: {
                    text: ''
                }
            },
            plotOptions: {
                pie: {
                    shadow: false,
                    center: ['50%', '50%'],
                    showInLegend: true,
                    dataLabels: {
                	distance: -30,
                	color: 'white',
                        formatter: function () {
                        var precsson = 3;
                        if (this.point.percentage < 1)
                            precsson = 2;
                        if (this.point.percentage >= 10)
                            precsson = 4;
                        return this.point.percentage > 1 ? parseFloat((this.point.percentage).toPrecision(precsson)) + '%' : null;
            		}
                }
                }
            },
            tooltip: {
        	    formatter: function () {
                var precsson = 3;
                if (this.point.percentage < 1)
                    precsson = 2;
                if (this.point.percentage >= 10)
                    precsson = 4;
                return '<b>' + this.point.name + '</b>: ' + parseFloat((this.point.percentage).toPrecision(precsson)) + ' %';
                    }
            },
            series: [{
                name: '<?php echo __("# of Tasks Report"); ?>',
                data: eval(piedata),
                size: '110%',
                innerSize: '50%'
            }]
        });
    });
</script>