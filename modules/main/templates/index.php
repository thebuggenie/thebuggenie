<?php 

	$tbg_response->setTitle('Frontpage');

?>
<?php if ($tbg_user->getID() == 1 && count(TBGProject::getAll()) == 1): ?>
	<?php include_component('main/hideableInfoBox', array('key' => 'index_single_project_mode', 'title' => __('Only using The Bug Genie to track issues for one project?'), 'content' => __("It looks likes you're only using The Bug Genie to track issues for one project. If you don't want to use this homepage, you can set The Bug Genie to <i>single project tracker mode</i>, which will automatically forward the frontpage to the project overview page.<br><br><i>Single project tracker mode</i> can be enabled from %configure_settings%.", array('%configure_settings%' => link_tag(make_url('configure_settings'), '<b>' . __('Configure &ndash;&gt; Settings') . '</b>'))))); ?>
<?php endif; ?>
<table style="margin-top: 0px; table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<?php if ($showleftbar): ?>
			<td class="left_bar">
				<div class="rounded_box borderless" id="main_menu" style="margin: 10px 0 5px 5px;">
					<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
					<div class="xboxcontent" style="padding: 5px;">
						<div class="header"><?php echo __('Quick links'); ?></div>
						<div class="content">
						<?php if (count($links) > 0): ?>
							<ul>
								<?php foreach ($links as $link): ?>
									<?php if ($link['url'] == ''): ?>
										<li>&nbsp;</li>
									<?php else: ?>
										<li style="font-size: 12px;"><?php echo link_tag($link['url'], $link['description'], array('title' => $link['url'])); ?></a></li>
									<?php endif; ?>
								<?php endforeach; ?>
							</ul>
						<?php else: ?>
							<div style="padding-left: 5px;" class="faded_medium"><?php echo __('There are no links in this menu'); ?></div>
						<?php endif; ?>
						</div>
					</div>
					<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
				</div>
				<?php
				
					TBGContext::trigger('core', 'index_left_top');
					TBGContext::trigger('core', 'index_left_middle');
					TBGContext::trigger('core', 'index_left_bottom');
				
				?>
			</td>
		<?php endif; ?>
		<td class="main_area">
			<?php
			
				TBGContext::trigger('core', 'index_right_top');
			
				TBGContext::trigger('core', 'index_right_middle');
				TBGContext::trigger('core', 'index_right_middle_top');
				
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
							<?php if ($tbg_user->hasPermission('b2viewconfig', 10) || $tbg_user->hasPermission('b2saveconfig', 10)): ?>
								<br>
								<b><?php echo __('Click the icon in the header above to go to project management'); ?></b>
							<?php else: ?>
								<?php echo __('Projects can only be created by an administrator'); ?>.
							<?php endif; ?>
						</p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php 
			
				TBGContext::trigger('core', 'index_right_middle_bottom');
				TBGContext::trigger('core', 'index_right_bottom');
			
			?>
		</td>
	</tr>
</table>