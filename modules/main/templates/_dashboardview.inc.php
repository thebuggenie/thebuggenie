<div class="container_div" style="margin: 0 0 5px 5px;">
	<div class="header">
		<?php if ($view->hasRSS()): ?>
			<?php echo link_tag($view->getRSSUrl(), image_tag('icon_rss.png'), array('title' => __('Download feed'), 'style' => 'float: right; margin-left: 5px;', 'class' => 'image')); ?>
			<?php $tbg_response->addFeed($view->getRSSUrl(), $view->getTitle()); ?>
		<?php endif; ?>
		<?php echo image_tag('collapse_small.png', array('id' => 'dashboard_'.$view->getID().'_collapse', 'style' => 'float: left; margin: 3px 5px 0 5px;', 'onclick' => "\$('dashboard_{$view->getID()}').toggle(); this.src = (this.src == '" . image_url('collapse_small.png', false, 'core', false) . "') ? '" . image_url('expand_small.png', false, 'core', false) . "' : '" . image_url('collapse_small.png', false, 'core', false) . "'")); ?>
		<?php echo $view->getTitle(); ?>
	</div>
	<div id="dashboard_<?php echo $view->getID(); ?>" class="<?php if ($view->getTargetType() == TBGDashboardView::TYPE_PROJECT): ?>dashboard_view_content<?php endif; ?>">
		<?php if ($view->shouldBePreloaded()): ?>
			<?php include_component($view->getTemplate(), array('view' => $view)); ?>
		<?php endif; ?>
	</div>
	<?php if (!$view->shouldBePreloaded()): ?>
		<div style="text-align: center; padding: 20px 0;" id="dashboard_<?php echo $view->getID(); ?>_indicator">
			<?php echo image_tag('spinning_26.gif'); ?>
		</div>
	<?php endif; ?>
</div>
<?php if (!$view->shouldBePreloaded()): ?>
	<script type="text/javascript">
		TBG.Main.Dashboard.views.push(<?php echo $view->getID(); ?>);
	</script>
<?php endif; ?>