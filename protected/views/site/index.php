<?php
/* @var $this SiteController */
$this->breadcrumbs = array('Welcome '.ucfirst(Yii::app()->user->getClientName()).'! You are last logged on '.date('M d, Y h:i a',strtotime($lastLogin)));
$this->pageTitle= 'GTA Admin Portal';
?>
<?php echo TbHtml::alert(TbHtml::ALERT_COLOR_SUCCESS, 'Overall Total Payins<br /> <strong>P'.$total['total_payins'].'</strong>',array(
    'class'=>'first span-9'
)); ?>
<?php echo TbHtml::alert(TbHtml::ALERT_COLOR_INFO, 'Total Net Earnings<br /> <strong>P'.$total['total_deductions'].'</strong>',array(
    'class'=>'second span-9'
)); ?>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
      var data = google.visualization.arrayToDataTable([
        ['Laps', 'Jump Start', 'Main Turbo','VIP Nitro'],
        ['Lap 1', <?php echo $stats['JS1'].','.$stats['MT1'].','.$stats['VN1']; ?>],
        ['Lap 2', <?php echo $stats['JS2'].','.$stats['MT2'].','.$stats['VN2']; ?>],
        ['Lap 3', <?php echo $stats['JS3'].','.$stats['MT3'].','.$stats['VN3']; ?>]
      ]);

      var view = new google.visualization.DataView(data);
//      view.setColumns([0, 1,
//                       { calc: "stringify",
//                         sourceColumn: 1,
//                         type: "string",
//                         role: "annotation" },
//                       2]);

      var options = {
         chart: {
            title: 'Client Statistics',
            subtitle: 'Total clients per package/laps',
        },
        height: 300,
        vAxis: {
            title: "Total Client Count"
        },
//        chartArea: {
//            left: "25%",
//            top: "3%",
//            height: "80%",
//            width: "100%"
//        }
//        hAxis: {
//            title: "Laps"
//        }
        //width: '100%',
        //bar: {groupWidth: "75%"},
        //legend: { position: "none" }
      };
      var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values"));
      chart.draw(view, options);
  }
  </script>
<div class="clearfix"></div>
<div id="columnchart_values" class="chart"></div>
<div class="clearfix"></div>
<?php echo TbHtml::alert(TbHtml::ALERT_COLOR_SUCCESS, 'Total Payouts<br /> <strong>P'.$total['total_net_pay'].'</strong>',array(
    'class'=>'last span-5'
)); ?>
<?php echo TbHtml::alert(TbHtml::ALERT_COLOR_INFO, 'Total Processed Payouts<br /> <strong>P'.$total['total_processed'].'</strong>',array(
    'class'=>'last span-5'
)); ?>
<?php echo TbHtml::alert(TbHtml::ALERT_COLOR_DANGER, 'Total Unprocessed Payouts<br /> <strong>P'.$total['total_unprocessed'].'</strong>',array(
    'class'=>'last span-5'
));
