<li<?php if ($selected_tab == 'wiki'): ?> class="selected"<?php endif; ?>>
	<div>
		<?php if (!isset($wiki_url)): ?>
			<?php echo link_tag(((isset($project_url)) ? $project_url : $url), image_tag('tab_publish.png', array(), false, 'publish') . TBGContext::getModule('publish')->getMenuTitle()); ?>
		<?php else: ?>
			<?php echo link_tag($wiki_url, TBGContext::getModule('publish')->getMenuTitle(), array('target' => 'blank')) ?>
		<?php endif; ?>
		<?php if (count(TBGProject::getAll())): ?>
			<?php echo javascript_link_tag(image_tag('tabmenu_dropdown.png', array('class' => 'menu_dropdown'))); ?>
		<?php endif; ?>
	</div>
	<?php if (count(TBGProject::getAll())): ?>
		<div id="wiki_dropdown_menu" class="tab_menu_dropdown">
			<?php if (TBGContext::isProjectContext()): ?>
				<div class="header"><?php echo __('Currently selected project'); ?></div>
				<?php if (!isset($wiki_url)): ?>
					<?php echo link_tag($project_url, __('Project wiki frontpage')); ?>
					<?php $quicksearch_title = __('Find project article (press enter to search)'); ?>
					<div style="font-weight: normal; margin: 0 0 15px 5px;">
						<form action="<?php echo make_url('publish_find_project_articles', array('project_key' => TBGContext::getCurrentProject()->getKey())); ?>" method="get" accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>">
							<input type="text" name="articlename" value="<?php echo $quicksearch_title; ?>" style="width: 230px; font-size: 0.9em;" onblur="if ($(this).getValue() == '') { $(this).value = '<?php echo $quicksearch_title; ?>'; $(this).addClassName('faded_out'); }" onfocus="if ($(this).getValue() == '<?php echo $quicksearch_title; ?>') { $(this).clear(); } $(this).removeClassName('faded_out');" class="faded_out">
						</form>
					</div>
				<?php else: ?>
					<?php echo link_tag($wiki_url, __('Project wiki frontpage'), array('target' => 'blank')) ?>
				<?php endif; ?>
			<?php endif; ?>
			<div class="header"><?php echo __('Global content'); ?></div>
			<?php echo link_tag($url, TBGContext::getModule('publish')->getMenuTitle(false)); ?>
			<?php $quicksearch_title = __('Find any article (press enter to search)'); ?>
			<div style="font-weight: normal; margin: 0 0 15px 5px;">
				<form action="<?php echo make_url('publish_find_articles'); ?>" method="get" accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>">
					<input type="text" name="articlename" value="<?php echo $quicksearch_title; ?>" style="width: 230px; font-size: 0.9em;" onblur="if ($(this).getValue() == '') { $(this).value = '<?php echo $quicksearch_title; ?>'; $(this).addClassName('faded_out'); }" onfocus="if ($(this).getValue() == '<?php echo $quicksearch_title; ?>') { $(this).clear(); } $(this).removeClassName('faded_out');" class="faded_out">
				</form>
			</div>
			<?php if (count(TBGProject::getAll()) > (int) TBGContext::isProjectContext()): ?>
				<div class="header"><?php echo __('Project wikis'); ?></div>
				<?php foreach (TBGProject::getAll() as $project): ?>
					<?php if (!$project->hasAccess() || (isset($project_url) && $project->getID() == TBGContext::getCurrentProject()->getID())) continue; ?>
					<?php if (!$project->hasWikiURL()): ?>
						<?php echo link_tag(make_url('publish_article', array('article_name' => ucfirst($project->getKey()).':MainPage')), $project->getName()); ?>
					<?php else: ?>
						<?php echo link_tag($project->getWikiURL(), $project->getName(), array('target' => 'blank')) ?>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</li>