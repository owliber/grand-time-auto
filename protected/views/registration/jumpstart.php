<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Apr 14, 2015
 * @filename overview.php
 */

Yii::app()->clientScript->registerScript('ui','
    
        $(\'input[rel="tooltip"]\').tooltip(); 
        //$("#RegistrationForm[referrer_name]" ).autocomplete( "option", "appendTo", "#regform-dialog" );
        
', CClientScript::POS_END);
 
$this->breadcrumbs = array('Registration'=>array('#'),'Jump Start'); ?>

<?php
Yii::app()->user->setFlash(TbHtml::ALERT_COLOR_WARNING,
    '<strong>Jump Start </strong> &mdash; '. TbHtml::icon(TbHtml::ICON_FLAG) . '1st Lap Registration');
?>
<?php $this->widget('bootstrap.widgets.TbAlert', array(
    'block'=>true,
    'fade'=>true, // use transitions?
    'closeText'=>false,
)); ?>

<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'layout' => TbHtml::FORM_LAYOUT_SEARCH,
    'id'=>'search-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'clientOptions' => array(
         'validateOnSubmit' => true,
         'validateOnChange' => false,
         'validateOnType' => false
      ),
    'htmlOptions'=>array(
        'class'=>'well',
    )
));

echo $form->hiddenField($model, 'client_id');
$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
    'model'=>$model,
    'attribute'=>'account_code',
    'sourceUrl'=>  Yii::app()->createUrl('registration/clients'),
    'options'=>array(
        'minLength'=>'3',
        'showAnim'=>'fold',
        'focus' => 'js:function(event, ui){$("#RegistrationForm_account_code").val(ui.item["value"])}',
        'select' => 'js:function(event, ui){$("#RegistrationForm_client_id").val(ui.item["id"]); }',
    ),
    'htmlOptions'=>array(
        'class'=>'span3',
        'rel'=>'tooltip',
        'title'=>'Please type a name or account code.',
        'autocomplete'=>'off',
    ),        
)); 
?>

<?php echo TbHtml::submitButton('Select', array('color'=>  TbHtml::BUTTON_COLOR_DANGER));?>
<?php echo TbHtml::linkButton('Base Account', array(
    'url'=>'#',
    'icon'=>  TbHtml::ICON_HOME,
    'color' => TbHtml::BUTTON_COLOR_INFO,
    'class'=>'pull-right',
    'onclick'=>'window.location.href="'.Yii::app()->createUrl('registration/jumpstart').'";'
)); ?>
<?php $this->endWidget();?>

<!-- #### Lap Table ### -->
<!-- ## LEVEL 2 -->
<div id="lap-level">
    <!-- Array 2 -->
    <?php $array2 = Network::showNetwork($clients->account_id, $clients->account_type_id, 1, 0, 0, false, true); ?>
    <div class="<?php echo Network::getLapStatus($array2['client_id']);  echo $array2['cssClass']; ?> red span-5">
        <?php echo $array2['link']; ?><br />
        <?php echo TbHtml::labelTb($array2['client_name']); ?>
    </div>
    <!-- Array 3 -->
    <?php $array3 = Network::showNetwork($clients->account_id, $clients->account_type_id, 1, 0, 1, false, true); ?>
    <div class="<?php echo Network::getLapStatus($array3['client_id']); echo $array3['cssClass']; ?> red span-5">
       <?php echo $array3['link']; ?><br />
        <?php echo TbHtml::labelTb($array3['client_name']); ?>
    </div>
    <!-- Array 4 -->
    <?php $array4 = Network::showNetwork($clients->account_id, $clients->account_type_id, 1, 1, 0, false, true); ?>
    <div class="<?php echo Network::getLapStatus($array4['client_id']); echo $array4['cssClass']; ?> red span-5">
       <?php echo $array4['link']; ?><br />
        <?php echo TbHtml::labelTb($array4['client_name']); ?>
    </div>
    <!-- Array 5 -->
    <?php $array5 = Network::showNetwork($clients->account_id, $clients->account_type_id, 1, 1, 1, false, true); ?>
    <div class="<?php echo Network::getLapStatus($array5['client_id']); echo $array5['cssClass']; ?> red span-5">
        <?php echo $array5['link']; ?><br />
        <?php echo TbHtml::labelTb($array5['client_name']); ?>
    </div>
</div>
<!-- ## LEVEL 1 ## -->
<div id="lap-level">
    <!-- Array 0 -->
    <?php $array0 = Network::showNetwork($clients->account_id, $clients->account_type_id, 1, 0, 0, true, true); ?>
    <div class="<?php echo Network::getLapStatus($array0['client_id']); echo $array0['cssClass']; ?> red span-10a">
        <?php echo $array0['link']; ?><br />
        <?php echo TbHtml::labelTb($array0['client_name']); ?>
    </div>
    <!-- Array 1 -->
    <?php $array1 = Network::showNetwork($clients->account_id, $clients->account_type_id, 1, 1, 1, true, true); ?>
    <div class="<?php echo Network::getLapStatus($array1['client_id']); echo $array1['cssClass']; ?> red span-10a">
        <?php echo $array1['link']; ?><br />
        <?php echo TbHtml::labelTb($array1['client_name']); ?>
    </div>
</div>
<div id="lap-level">
    <!-- Base account -->
    <div class="<?php echo Network::getLapStatus($clients->account_id); ?> red span-20a">
    <b>
        <?php echo '('.$client_info['account_code'].')'; ?>
    </b><br />
    <?php echo TbHtml::labelTb($client_info['first_name'] . ' '.$client_info['last_name']); ?>
    </div>
</div>
<?php
$ajaxLoader = '<span id="ajaxloader" class="pull-left" style="display: none"><img src="'.Yii::app()->request->baseUrl.'/images/loader.gif" width="32px" height="32px" /> Engine starting, please wait...</span>';
    $this->widget('bootstrap.widgets.TbModal', array(
    'id' => 'regform-dialog',
    'header' => 'Jump Start Registration',
    'content' => $this->renderPartial('_form', array('model'=>$model), true),
    'footer'=>array($ajaxLoader,
        TbHtml::ajaxSubmitButton('Register',CHtml::normalizeUrl(array('registration/register','render'=>true)),
                 array(
                     'data'=>'js:$("#jumpstart-form").serialize()',
                     'dataType'=>'json',
                     'type'=>'post',
                     'success'=>'function(data) {
                        $("#ajaxloader").hide();  
                        if(data.result_code == 0)
                        {
                            alert(data.result_msg);
                            window.location.href = "'.Yii::app()->request->url.'";
                        }
                        else if(data.result_code == 1 || data.result_code == 2)
                        {
                            alert(data.result_msg);
                        }
                        else
                        {
                            $.each(data, function(key, val) {
                                $("#"+key+"_em_").text(val);
                                $(".control-group").removeClass("control-group").addClass("control-group error");
                                $("#"+key+"_em_").removeClass("help-block").addClass("help-block control-group error");
                                $("#"+key+"_em_").show();
                            });
                        }       
                    }',                    
                        'beforeSend'=>'function(){                        
                              $("#ajaxloader").show();
                         }'
                     ),array('id'=>'ajaxBtnRegister','color'=>  TbHtml::BUTTON_COLOR_DANGER)),
        
        TbHtml::button('Close', array('data-dismiss' => 'modal')),
    ),
));   


    
