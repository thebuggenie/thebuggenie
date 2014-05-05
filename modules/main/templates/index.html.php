<?php 

	$tbg_response->setTitle(__('Frontpage'));
	$tbg_response->addBreadcrumb(__('Frontpage'), make_url('home'), tbg_get_breadcrumblinks('main_links'));

?>
<?php if ($show_project_config_link && $show_project_list): ?>
	<?php if ($project_count == 1): ?>
		<?php include_component('main/hideableInfoBox', array('key' => 'index_single_project_mode', 'title' => __('Only using The Bug Genie to track issues for one project?'), 'content' => __("It looks likes you're only using The Bug Genie to track issues for one project. If you don't want to use this homepage, you can set The Bug Genie to <i>single project tracker mode</i>, which will automatically forward the frontpage to the project overview page.<br><br><i>Single project tracker mode</i> can be enabled from %configure_settings%.", array('%configure_settings%' => link_tag(make_url('configure_settings'), '<b>' . __('Configure &ndash;&gt; Settings') . '</b>'))))); ?>
	<?php elseif ($project_count == 0): ?>
		<?php include_component('main/hideableInfoBox', array('key' => 'index_no_projects', 'title' => __('Oh noes! There are no projects!'), 'content' => __("It doesn't look like you have had the chance to add any projects yet. If you want to play around a bit with The Bug Genie before you start using it for your own projects, you can import some sample data before adding your own projects.").'<br>'. __("Sample data can be imported from %configure_import%.", array('%configure_import%' => link_tag(make_url('configure_import'), '<b>' . __('Configure &ndash;&gt; Import') . '</b>'))))); ?>
	<?php endif; ?>
<?php endif; ?>
<table style="margin-top: 0px; table-layout: fixed; width: 100%;" cellpadding=0 cellspacing=0>
	<tr>
<?php //		<td class="side_bar"> ?>
<?php //			<?php include_template('main/menulinks', array('links' => $links, 'target_type' => 'main_menu', 'target_id' => 0, 'title' => __('Quick links'))); ?>
<?php //			<?php TBGEvent::createNew('core', 'index_left')->trigger(); ?>
<?php //		</td> ?>
		<td class="main_area frontpage">
			<?php TBGEvent::createNew('core', 'index_right_top')->trigger(); ?>
			<?php if ($show_project_list): ?>
				<div class="project_overview">
					<div class="header">
						<div class="button-group">
							<?php if ($show_project_config_link): ?>
								<?php echo link_tag(make_url('configure_projects'), __('Manage projects'), array('class' => 'button button-silver')); ?>
							<?php endif; ?>
							<a class="button button-silver" href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'archived_projects')); ?>');"><?php echo __('Show archived projects'); ?></a>
						</div>
						<?php echo __('Projects'); ?>
					</div>
					<?php if ($project_count > 0): ?>
						<ul class="project_list simple_list">
						<?php foreach ($projects as $project): ?>
							<li><?php include_component('project/overview', array('project' => $project)); ?></li>
						<?php endforeach; ?>
						</ul>
					<?php else: ?>
						<p class="content">
							<?php echo __('There are no top-level projects'); ?>.
							<?php if ($show_project_config_link): ?>
								<?php echo link_tag(make_url('configure_projects'), __('Go to project management').' &gt;&gt;'); ?>
							<?php else: ?>
								<?php echo __('Projects can only be created by an administrator'); ?>.
							<?php endif; ?>
						</p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php TBGEvent::createNew('core', 'index_right_bottom')->trigger(); ?>
		</td>
	</tr>
</table>