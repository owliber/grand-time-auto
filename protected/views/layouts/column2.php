<?php /* @var $this Controller */ ?>
<?php $this->beginContent('//layouts/main'); ?>

<div class="span-5">
	<div id="sidebar">
        <?php echo TbHtml::stackedTabs(UserMenu::userMenus()); ?>
	</div><!-- sidebar -->
</div>
<div class="span-24 last">
	<div id="content">
		<?php echo $content; ?>
	</div><!-- content -->
        
</div>

<?php $this->endContent(); ?>