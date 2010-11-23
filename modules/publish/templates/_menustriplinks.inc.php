<li<?php if ($selected_tab == 'wiki'): ?> class="selected"<?php endif; ?>>
	<div>
		<?php echo link_tag(((isset($project_url)) ? $project_url : $url), image_tag('tab_publish.png', array(), false, 'publish') . TBGContext::getModule('publish')->getMenuTitle()); ?>
		<?php if (count(TBGProject::getAll())): ?>
			<?php echo javascript_link_tag(image_tag('tabmenu_dropdown.png', array('class' => 'menu_dropdown'))); ?>
		<?php endif; ?>
	</div>
	<?php if (count(TBGProject::getAll())): ?>
		<div id="wiki_dropdown_menu" class="tab_menu_dropdown shadowed">
			<?php if (TBGContext::isProjectContext()): ?>
				<div class="header"><?php echo __('Currently selected project'); ?></div>
				<?php echo link_tag($project_url, __('Project wiki frontpage')); ?>
			<?php endif; ?>
			<div class="header"><?php echo __('Global content'); ?></div>
			<?php echo link_tag($url, TBGContext::getModule('publish')->getMenuTitle(false)); ?>
			<?php if (count(TBGProject::getAll()) > (int) TBGContext::isProjectContext()): ?>
				<div class="header"><?php echo __('Project wikis'); ?></div>
				<?php foreach (TBGProject::getAll() as $project): ?>
					<?php if (isset($project_url) && $project->getID() == TBGContext::getCurrentProject()->getID()) continue; ?>
					<?php echo link_tag(make_url('publish_article', array('article_name' => ucfirst($project->getKey()).':MainPage')), $project->getName()); ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</li>