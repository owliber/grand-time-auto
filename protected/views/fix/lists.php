<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date May 22, 2015
 * @filename lists.php
 */

$this->widget('bootstrap.widgets.TbGridView', array(
    'id'=>'clients-grid',
    'type'=>  TbHtml::GRID_TYPE_HOVER,
    'dataProvider'=>$dataProvider,
    'enablePagination' => true,
    'htmlOptions'=>array('style'=>'font-size:11px'),
    'columns'=>array(
        array(
            'header' => '#',
            'value' => '$row + ($this->grid->dataProvider->pagination->currentPage * $this->grid->dataProvider->pagination->pageSize + 1)',
            'htmlOptions' => array('style' => 'text-align:center;'),
            'headerHtmlOptions' => array('style' => 'text-align:center'),
        ),
        array('name'=>'date_created', 
                'header'=>'Date Created',
                'htmlOptions'=>array('style'=>'text-align:left;'),
                'headerHtmlOptions' => array('style' => 'text-align:left'),
            ),
        array('name'=>'account_name', 
                'header'=>'Account Name',
                'htmlOptions'=>array('style'=>'text-align:left;'),
                'headerHtmlOptions' => array('style' => 'text-align:left'),
            ),
        array('name'=>'account_code', 
                'header'=>'Account Code',
                'htmlOptions'=>array('style'=>'text-align:left;'),
                'headerHtmlOptions' => array('style' => 'text-align:left'),
            ),
        array('name'=>'race_type', 
                'header'=>'Race Type',
                'htmlOptions'=>array('style'=>'text-align:left;'),
                'headerHtmlOptions' => array('style' => 'text-align:left'),
            ),
        array('name'=>'lap_no', 
                'header'=>'Lap No',
                'htmlOptions'=>array('style'=>'text-align:center;'),
                'headerHtmlOptions' => array('style' => 'text-align:center'),
            ),
        array('name'=>'total_clients', 
                'header'=>'Total Clients',
                'htmlOptions'=>array('style'=>'text-align:center;'),
                'headerHtmlOptions' => array('style' => 'text-align:center'),
            ),
    ),
));


