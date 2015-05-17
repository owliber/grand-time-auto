<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date May 1, 2015
 * @filename _details.php
 */

?>
<table class="payout-details">
<thead>
<tr>
    <th>Account Name</th>
    <td><?php echo $summary['account_name']; ?></td>
</tr>
<tr>
    <th>Account Code</th>
    <td><?php echo $summary['account_code']; ?></td>
</tr>
<tr>
    <th>Payout Date</th>
    <td><?php echo date('M d, Y',strtotime($summary['date_created'])); ?></td>
</tr>
<tr>
    <th>Total Payout</th>
    <td style="text-align:right"><?php echo $summary['total_amount']; ?></td>
</tr>
<tr>
    <th>Total Deductions</th>
    <td style="text-align:right">(<?php echo $summary['total_deductions']; ?>)</td>
</tr>
<tr>
    <th>Total Net Pay</th>
    <td style="text-align:right"><strong><?php echo TbHtml::labelTb($summary['net_pay'],array('color'=>  TbHtml::ALERT_COLOR_SUCCESS,'style'=>'font-size:14px')); ?></strong></td>
</tr>
</table>
<?php echo TbHtml::labelTb('Deductions', array('color'=>  TbHtml::LABEL_COLOR_WARNING, 'style'=>'font-size:14px')); ?>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id'=>'payout-details-grid',
    'type'=>  TbHtml::GRID_TYPE_HOVER,
    'dataProvider'=>$dataProvider,
    'enablePagination' =>false,
    'htmlOptions'=>array(
        
    ),
    'columns'=>array(
        array(
            'header' => '#',
            'value' => '$row + ($this->grid->dataProvider->pagination->currentPage * $this->grid->dataProvider->pagination->pageSize + 1)',
            'htmlOptions' => array('style' => 'text-align:center;'),
            'headerHtmlOptions' => array('style' => 'text-align:center'),
        ),        
        array('name'=>'description', 
            'header'=>'Description',
            'htmlOptions'=>array('style'=>'text-align:left;'),
            'headerHtmlOptions' => array('style' => 'text-align:left'),
        ),
        array('name'=>'amount', 
            'header'=>'Deduction Amount',
            'htmlOptions'=>array('style'=>'text-align:right;'),
            'headerHtmlOptions' => array('style' => 'text-align:right'),
        ),
    ),
)); 



