<?php 

	$tbg_response->setTitle('Frontpage');

?>
<?php if ($tbg_user->getID() == 1 && count(TBGProject::getAll()) == 1): ?>
	<?php include_component('main/hideableInfoBox', array('key' => 'index_single_project_mode', 'title' => __('Only using The Bug Genie to track issues for one project?'), 'content' => __("It looks likes you're only using The Bug Genie to track issues for one project. If you don't want to use this homepage, you can set The Bug Genie to <i>single project tracker mode</i>, which will automatically forward the frontpage to the project overview page.<br><br><i>Single project tracker mode</i> can be enabled from %configure_settings%.", array('%configure_settings%' => link_tag(make_url('configure_settings'), '<b>' . __('Configure &ndash;&gt; Settings') . '</b>'))))); ?>
<?php endif; ?>
<table style="margin-top: 0px; table-layout: fixed; width: 100%;" cellpadding=0 cellspacing=0>
	<tr>
		<td class="left_bar">
			<?php include_template('main/menulinks', array('links' => $links, 'target_type' => 'main_menu', 'target_id' => 0, 'title' => __('Quick links'))); ?>
			<?php

				TBGEvent::createNew('core', 'index_left_top')->trigger();
				TBGEvent::createNew('core', 'index_left_middle')->trigger();
				TBGEvent::createNew('core', 'index_left_bottom')->trigger();

			?>
		</td>
		<td class="main_area">
			<?php
			
				TBGEvent::createNew('core', 'index_right_top')->trigger();
			
				TBGEvent::createNew('core', 'index_right_middle')->trigger();
				TBGEvent::createNew('core', 'index_right_middle_top')->trigger();
				
			?>
			<?php if (TBGSettings::isProjectOverviewEnabled()): ?>
				<div class="project_overview">
					<div class="header">
						<?php if ($tbg_user->canAccessConfigurationPage(TBGSettings::CONFIGURATION_SECTION_PROJECTS)): ?>
							<?php echo link_tag(make_url('configure_projects'), image_tag('cfg_icon_projectheader.png', array('style' => 'float: left; margin-right: 5px;'))); ?>
						<?php endif; ?>
						<?php echo __('Projects'); ?>
					</div>
					<?php if (count(TBGProject::getAll()) > 0): ?>
						<ul class="project_list">
						<?php foreach (TBGProject::getAll() as $aProject): ?>
							<li><?php include_component('project/overview', array('project' => $aProject)); ?></li>
						<?php endforeach; ?>
						</ul>
					<?php else: ?>
						<p class="content"><?php echo __('There are no projects'); ?>.
							<?php if ($tbg_user->canAccessConfigurationPage(TBGSettings::CONFIGURATION_SECTION_PROJECTS)): ?>
								<?php echo link_tag(make_url('configure_projects'), __('Go to project management').' &gt;&gt;'); ?>
							<?php else: ?>
								<?php echo __('Projects can only be created by an administrator'); ?>.
							<?php endif; ?>
						</p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php 
			
				TBGEvent::createNew('core', 'index_right_middle_bottom')->trigger();
				TBGEvent::createNew('core', 'index_right_bottom')->trigger();
			
			?>
		</td>
	</tr>
</table>