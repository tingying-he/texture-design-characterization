<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Iconic Texture Pie Chart</title>

    <script type="text/javascript" src="../lib/jquery.min.js"></script>
    <script type="text/javascript" src="../lib/d3.v7.min.js"></script>

    <link rel="stylesheet" href="../lib/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="texture_style.css">

    <script src="../tools/loading.js"></script>

    <script type = "text/javascript" >
        //when you click the back button of the browser, this script prevent you from going bac
        function preventBack(){window.history.forward();}
        setTimeout("preventBack()", 0);
        window.onunload=function(){null};
    </script>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col bg-light textContent">
            <?php
            include '../modules/design_task_description.php';
            ?>
            <p>
                There is no time limit for this task<span id="last_task"></span>
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div id="toolbar"></div>
        </div>
    </div>
    <div class="row gx-3">
        <div class="col-auto legend_drag_div">
            <div id="legend"></div>
            <div id="dragdrop" class="bg-light">
            </div>
        </div>
        <div class="col-auto">
            <div id="chartDiv" class="chartDiv"></div>
            <div id="outlineDiv" style="padding-left: 200px;"></div>
        </div>
        <div class="col-auto">
            <div id="controllerDiv">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <button id="pie_icon_next_btn" class="btn btn-primary float-end">Next
            </button>
        </div>
    </div>
</div>

<script>
    let timestamp_pie_icon_start = Date.now() //the starting time for this design

    let measurements = JSON.parse(localStorage.getItem('measurements'))
    let condition_texture = measurements['condition_texture']
    switch (condition_texture){
        case 0: //geo_icon
            document.getElementById('last_task').innerHTML=". This is the last design task."
            break;
        case 1: //icon_geo
            document.getElementById('last_task').innerHTML=", but keep in mind that we will ask you to complete one other design after this one."
            break;
    }

    document.getElementById('pie_icon_next_btn').onclick = function(){

        //write data to measurements
        // measurements['parameters_pie_icon'] = JSON.stringify(parameters)
        // measurements['timestamp_design_finished_pie_icon'] = Date.now()

        let timestamp_pie_icon_end = Date.now() //the end time for this design
        let pie_icon_design_time = timestamp_pie_icon_end - timestamp_pie_icon_start // time used for this design

        //update measurements to localStorage
        localStorage.setItem('measurements', JSON.stringify(measurements))

        //send the svg of chart design to server
        //remove the indicators
        let indicatorsSVG = document.getElementById('indicator_group').outerHTML
        let fullSVG = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="600" height="600">' + $("#chartA")[0].innerHTML + $("#chart")[0].innerHTML  + $("#chartWhiteStroke")[0].innerHTML + $("#chartBlackStroke")[0].innerHTML + '</svg>'
        let svgData = fullSVG.replace(indicatorsSVG, '')

        //send the svg and the parameters
        let obj = {}
        obj.svgData = svgData
        obj.filename = 'pie_icon_'+ measurements['participant_id'] + '_v' + measurements['trial_num']
        obj.parameters = parameters

        $.ajax({
            url: '../ajax/results_svg.php',
            type: 'POST',
            data: JSON.stringify(obj),
            contentType: 'application/json',
            dataType: "json",
            async: false, // Make the request synchronous
            success: function () {
                console.log('success')
            },
            error:function (e){
                console.log('Getting svg failed')
            }
        })

        $.ajax({
            url: '../ajax/results_texture_parameters.php',
            type: 'POST',
            data: JSON.stringify(obj),
            contentType: 'application/json',
            dataType: "json",
            async: false, // Make the request synchronous
            success: function () {
                console.log('success')
            },
            error:function (e){
                console.log('Getting json failed')
            }
        })

        //jump to next page
        switch (condition_texture){
            case 0: //geo_icon: this design (pie_icon) is the second design

                measurements['second_design_time'] = pie_icon_design_time

                //update measurements to localStorage
                localStorage.setItem('measurements', JSON.stringify(measurements))

                window.location.href = 'question_design_strategies.php';
                break;
            case 1: //icon_geo: this design (pie_icon) is the first design

                measurements['first_design_time'] = pie_icon_design_time

                //update measurements to localStorage
                localStorage.setItem('measurements', JSON.stringify(measurements))

                window.location.href = 'between_two_design.php';
                break;
        }
    }
</script>

<script src="texture_functions.js"></script>
<script src="texture_constants.js"></script>

<script>
    const chartName = 'pieIcon'

    drawToolbar('toolbar')
    drawDragDropInfo('dragdrop')
    drawIconLegend('legend',fruits)

    drawChartDiv('chartDiv', svgWidth, svgHeight, 'chart')
    iconTextures(d3.select('#chartA'),fruits)
    drawIconicControllers()
</script>


<script src="texture_ui_elements.js"></script>

<script>
    //data
    let data = getDatasetForChart()

    drawIconPieWithTexture(data, pieRadius, 'chart')
    drawPieIndicators(data, pieRadius, 'chart', 30)

    //initialize
    icon_setInitialParameters(chartName)

    setSelectCat(chartName)
    //switch texture by drag and drop
    icon_switchTextures(chartName)

</script>


</body>
</html>
