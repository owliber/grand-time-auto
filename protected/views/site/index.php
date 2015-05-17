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

<?php
$lap1_js = Tools::get_value('LAP1_JS_TOTAL');
$lap2_js = Tools::get_value('LAP2_JS_TOTAL');
$lap3_js = Tools::get_value('LAP3_JS_TOTAL');
?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
      var data = google.visualization.arrayToDataTable([
        ['Laps', 'Jump Start', 'Main Turbo','VIP Nitro'],
        ['Lap 1', <?php echo $lap1_js; ?>,1,1],
        ['Lap 2', <?php echo $lap2_js; ?>,0,0],
        ['Lap 3', <?php echo $lap3_js; ?>,0,0]
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
        width: 900,
        bar: {groupWidth: "75%"},
        //legend: { position: "none" },
//        series: {
//            0: { axis: 'clients' }, // Bind series 0 to an axis named 'clients'.
//            1: { axis: 'laps' } // Bind series 1 to an axis named 'laps'.
//          },
//        axes: {
//            x: {
//              clients: {label: 'Total Clients'}, // Left y-axis.
//              laps: {side:'bottom',label: 'Laps'}
//            }
//          }
      };
      var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values"));
      chart.draw(view, options);
  }
  </script>
<div class="clearfix"></div>
<div id="columnchart_values"></div>
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
