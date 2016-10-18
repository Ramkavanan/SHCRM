$(document).ready(function () {
    
//    $.ajax({url: "/app/index.php/meetings/default/GetLastMonthMeetings",
//        context: document.body,
//        success: function (data) {
//            $("#count_of_meetings").text(data);
//        }});

    $.ajax({url: "/app/index.php/opportunities/default/GetoptFinalAmtTotalByStage",
        dataType: 'json',
        success: function (data) {
            $.each(data, function (key, value) {
                $.each(value, function (FieldNameType, TotalValue) {
                    var totFinalAmt = TotalValue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    $("#totalFinalAmt").text('$' + totFinalAmt);
                })
            })
        }});

    $.ajax({url: "/app/index.php/agreements/default/GetAgmntAmountByStatus",
        dataType: 'json',
        success: function (data) {
            $.each(data, function (key, value) {
                $.each(value, function (AgmntType, AgmntValue) {
                    if (AgmntType == 'RecurringAgmnt') {
                        var RecAgmntValue = AgmntValue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        $("#Total_Rec_Amt").text('$' + RecAgmntValue);
                    }
                })
            })
        }});
    
    $.ajax({url: "/app/index.php/opportunities/default/GetCurrentyearWonOpportunities",
        dataType: 'json',
        success: function (data) {
            $.each(data, function (key, value) {
                $.each(value, function (NameType, TotalValue) {
                    var totFinalAmt = TotalValue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    $("#Total_Project_Amt").text('$' + totFinalAmt);
                })
            })
        }});
    
    /**
     * Set height for chartContainer(Chart render using zurmo)
     */
    $('#chartContainerProject').css("height","250px");
    $('#chartContainerRecurring').css("height","250px");
    $('#chartContaineragmnt_Vs_tracking').css("height","250px");

    /**
     * Chart render for Pipeline chart in dashboard (Chart render without using zurmo)
     */
    $.ajax({url: "/app/index.php/opportunities/default/GetPipelineChartData",
    dataType: 'json',
    success: function (data) {
        AmCharts.makeChart("pipeline_chartdiv",
                {
                        "type": "serial",
                        "categoryField": "category",
                        "rotate": true,
                        "angle": 30,
                        "startDuration": 1,
                        "categoryAxis": {
                                "gridPosition": "start",
                                "gridAlpha": 0,
                                "titleBold": false,
                        },
                        "trendLines": [],
                        "graphs": [
                                {
                                        "balloonText": "[[title]] of [[category]]:[[value]]",
                                        "fillAlphas": 1,
                                        "fillColors": "#056F00",
                                        "lineThickness": 0,
                                        "id": "AmGraph-1",
                                        "title": "Project Amount",
                                        "type": "column",
                                        "valueField": "column-1"
                                },
                                {
                                        "balloonText": "[[title]] of [[category]]:[[value]]",
                                        "fillAlphas": 1,
                                        "fillColors": "#75B749",
                                        "lineThickness": 0,
                                        "id": "AmGraph-2",
                                        "title": "Recurring Amount",
                                        "type": "column",
                                        "valueField": "column-2"
                                }
                        ],
                        "guides": [],
                        "valueAxes": [
                                {
                                        "id": "ValueAxis-1",
                                        "stackType": "regular",
                                        "labelRotation": 45,
                                        "gridThickness": 0,
                                        "title": "Amount ($)",
                                        "titleBold": false
                                        
                                }
                        ],
                        "allLabels": [],
                        "balloon": {},
                        "legend": {
                                "enabled": true,
                                "useGraphSettings": true
                        },
//					"titles": [
//						{
//							"id": "Title-1",
//							"size": 15,
//							"text": "Chart Title"
//						}
//					],
                        "dataProvider": data
                }
        );
    }});


    /**
     * Chart render for Closed sales chart in dashboard (Chart render without using zurmo)    
     */
    $.ajax({url: "/app/index.php/opportunities/default/GetClosedSalesChartData",
    dataType: 'json',
    success: function (data) {
        AmCharts.makeChart("closed_sales_chartdiv",
                {
                        "type": "serial",
                        "categoryField": "category",
                        "rotate": true,
                        "angle": 30,
                        "startDuration": 1,
                        "categoryAxis": {
                                "gridPosition": "start",
                                "gridAlpha": 0,
                                "titleBold": false
                        },
                        "trendLines": [],
                        "graphs": [
                                {
                                        "balloonText": "[[title]] of [[category]]:[[value]]",
                                        "fillAlphas": 1,
                                        "fillColors": "#E65100",
                                        "lineThickness": 0,
                                        "id": "AmGraph-1",
                                        "title": "Project Amount",
                                        "type": "column",
                                        "valueField": "column-1"
                                },
                                {
                                        "balloonText": "[[title]] of [[category]]:[[value]]",
                                        "fillAlphas": 1,
                                        "fillColors": "#FF9800",
                                        "lineThickness": 0,
                                        "id": "AmGraph-2",
                                        "title": "Recurring Amount",
                                        "type": "column",
                                        "valueField": "column-2"
                                }
                        ],
                        "guides": [],
                        "valueAxes": [
                                {
                                        "id": "ValueAxis-1",
                                        "stackType": "regular",
                                        "title": "Amount ($)",
                                        "labelRotation": 45,
                                        "gridThickness": 0,
                                        "titleBold": false
                                }
                        ],
                        "allLabels": [],
                        "balloon": {},
                        "legend": {
                                "enabled": true,
                                "useGraphSettings": true
                        },
//					"titles": [
//						{
//							"id": "Title-1",
//							"size": 15,
//							"text": "Chart Title"
//						}
//					],
                        "dataProvider": data
                }
        );
    }});
});