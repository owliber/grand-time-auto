<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="language" content="en">

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection">
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print">
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection">
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css">
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css">
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/custom.css">

         <?php Yii::app()->bootstrap->register(); ?>

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>
    <?php
    if(isset(Yii::app()->session['account_type_id']))
    {
        $account_label = Yii::app()->user->getAccountType();
    }
    else
    {
        $account_label = "";
    }
    
    $this->widget('bootstrap.widgets.TbNavbar', array(
        'brandLabel' => '<img src ="' . Yii::app()->request->baseUrl . '/images/header-logo.png" />',
        'brandUrl' => array('/site/index'),
        'brandOptions' => array(),
        'display' => null, // default is static to top
        'collapse' => true,
        'items' => array(
            array(
                'class' => 'bootstrap.widgets.TbNav',
                'items' => array(
                    array('label' => 'Home', 'url' => array('/site/index'), 'visible' => !Yii::app()->user->isGuest, 'htmlOptions' => array('icon' => TbHtml::ICON_HOME)),
                    array('label' => 'Profile', 'url' => '', 'visible' => !Yii::app()->user->isGuest, 'htmlOptions' => array(
                        'icon' => TbHtml::ICON_USER,
                        'items'=>array(
                            array('label'=>'Change Password','url'=> array('/accounts/changepassword')),
                            array('label'=>'Update Profile','url'=> array('/accounts/profile')),
                        ),
                    )),
                    array('label' => 'Login', 'url' => array('/site/login'), 'visible' => Yii::app()->user->isGuest, 'htmlOptions' => array('icon' => TbHtml::ICON_LOCK )),
                    array('label' => 'Logout (' . Yii::app()->user->getClientName() . ')', 'url' => array('/site/logout'), 'visible' => !Yii::app()->user->isGuest, 'htmlOptions' => array('icon' => TbHtml::ICON_LOCK )),
                    array('label' => $account_label, 'url' => '#', 'visible' => !Yii::app()->user->isGuest, 'htmlOptions' => array('style'=>'font-size:18px;','class'=>'pull-right nav')),
                ),
            ),
        ),
    ));
    ?>
    <div style="margin:5px 10px 0 0; right:10px; color: #000; font-size:9px; position:absolute;">GTAApp Ver <?php echo Yii::app()->params['version']; ?></div>
    <?php if (isset($this->breadcrumbs)): ?>
        <?php
        $this->widget('bootstrap.widgets.TbBreadcrumb', array(
            'homeUrl' => array('site/index'),
            'links' => $this->breadcrumbs,
        ));
        ?>
    <?php endif ?>

    <?php echo $content; ?>

    <div class="clear"></div>

    <div id="footer">
        Copyright &copy; <?php echo date('Y'); ?> by <?php echo Yii::app()->params['companyName']; ?> &mdash; www.grandtimeauto.com <br />
        <span id="siteseal"><script type="text/javascript" src="https://seal.starfieldtech.com/getSeal?sealID=sq7VhoG3Y9KK9BiltGfHAoSGJc6q1fvbNb7dzkJEnO2pWTcoEYs8wBmwvlAZ"></script></span>
    </div><!-- footer -->

</body>
</html>
