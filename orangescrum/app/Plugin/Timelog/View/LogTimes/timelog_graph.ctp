<div id="dboardtimelog" class="tlog-chrt-prnt" style="height:280px"></div>
<script>
$(function () {
    var dt = <?php echo $dt_arr; ?>;
    var series =<?php echo $series; ?>;
    $('#dboardtimelog').highcharts({

        chart: {
            type: 'column'
        },

        title: {
            align: 'left',
            text: ''
        },
        exporting: {
            enabled: false,
            buttons: {
                contextButton: {
                    symbolStrokeWidth: 2,
                    symbolStroke: '#969696',
                    menuItems: [{
                        text: 'PNG',
                        onclick: function() {
                            this.exportChart();
                        },
                        separator: false
                    }, {
                        text: 'JPEG',
                        onclick: function() {
                            this.exportChart({
                                type: 'image/jpeg'
                            });
                        },
                        separator: false
                    }, {
                        text: 'PDF',
                        onclick: function() {
                            this.exportChart({
                                type: 'application/pdf'
                            });
                        },
                        separator: false
                    }, {
                        text: 'Print',
                        onclick: function() {
                            this.print();
                        },
                        separator: false
                    }]
                }
            },
        },
        xAxis: {
            type:'datetime',
            categories: eval(dt),
            showFirstLabel:true,
            showLastLabel:true,
            tickInterval: 3,
        },

        yAxis: {
            allowDecimals: false,
            gridLineWidth: 0,
            minorGridLineWidth: 0,
            min: 0,
            title: {
                text: _('Hour(s) Spent')
            }
        },

        tooltip: {
            formatter: function () {
                return '<b>' + this.x + '</b><br/>' +
                    this.series.name + ': ' + this.y + '<br/>' +
                    _('Total')+': ' + this.point.stackTotal;
            }
        },
		credits: {
			enabled: false
		},
        plotOptions: {
            column: {
                stacking: 'normal'
            }
        },

        series: series
    });
});
</script>