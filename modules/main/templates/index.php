<?php 

	$tbg_response->setTitle('Frontpage');

?>
<?php if ($tbg_user->getID() == 1 && count(TBGProject::getAll()) == 1): ?>
	<?php include_component('main/hideableInfoBox', array('key' => 'index_single_project_mode', 'title' => __('Only using The Bug Genie to track issues for one project?'), 'content' => __("It looks likes you're only using The Bug Genie to track issues for one project. If you don't want to use this homepage, you can set The Bug Genie to <i>single project tracker mode</i>, which will automatically forward the frontpage to the project overview page.<br><br><i>Single project tracker mode</i> can be enabled from %configure_settings%.", array('%configure_settings%' => link_tag(make_url('configure_settings'), '<b>' . __('Configure &ndash;&gt; Settings') . '</b>'))))); ?>
<?php endif; ?>
<table style="margin-top: 0px; table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<td class="left_bar">
			<div class="rounded_box borderless" id="main_menu" style="margin: 10px 0 5px 5px;">
				<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
				<div class="xboxcontent" style="padding: 5px;">
					<div class="header">
						<?php echo javascript_link_tag(image_tag('action_add_link.png'), array('style' => 'float: right;', 'class' => 'image', 'onclick' => "$('attach_link').toggle();", 'title' => __('Add an item to the menu'))); ?>
						<?php echo javascript_link_tag(image_tag('icon_edit.png'), array('style' => 'float: right;', 'class' => 'image', 'onclick' => "$('main_menu').toggleClassName('menu_editing');", 'title' => __('Toggle main menu edit mode'))); ?>
						<?php echo __('Quick links'); ?>
					</div>
					<div class="content">
						<table style="table-layout: fixed; width: 100%;" cellpadding=0 cellspacing=0>
							<tbody id="main_menu_links">
								<?php foreach ($links as $link_id => $link): ?>
									<?php include_template('mainmenulink', array('link' => $link, 'link_id' => $link_id)); ?>
								<?php endforeach; ?>
							</tbody>
						</table>
						<div style="padding-left: 5px;<?php if (count($links) > 0): ?> display: none;<?php endif; ?>" class="no_items" id="main_menu_no_links"><?php echo __('There are no links in this menu'); ?></div>
					</div>
				</div>
				<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
			</div>
			<div class="rounded_box borderless" id="attach_link" style="margin: 5px 0 5px 5px; display: none">
				<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
				<div class="xboxcontent">
					<div class="header_div" style="margin: 0 0 5px 0;"><?php echo __('Add a link'); ?>:</div>
					<form action="<?php echo make_url('main_attach_link'); ?>" method="post" onsubmit="addMainMenuLink('<?php echo make_url('main_attach_link'); ?>');return false;" id="attach_link_form">
						<dl style="margin: 0;">
							<dt style="width: 80px; padding-top: 3px;"><label for="attach_link_url"><?php echo __('URL'); ?>:</label></dt>
							<dd style="margin-bottom: 0px;"><input type="text" name="link_url" id="attach_link_url" style="width: 235px;"></dd>
							<dt style="width: 80px; font-size: 10px; padding-top: 4px;"><label for="attach_link_description"><?php echo __('Description'); ?>:</label></dt>
							<dd style="margin-bottom: 0px;"><input type="text" name="description" id="attach_link_description" style="width: 235px;"></dd>
						</dl>
						<div style="font-size: 12px; padding: 15px 2px 10px 2px;" class="faded_medium" id="attach_link_submit"><?php echo __('Enter the link URL here, along with an optional description. Press "%add_link%" to add it to the main menu. To add a spacer, just press "%add_link%", without any url or description.', array('%add_link%' => __('Add link'))); ?></div>
						<div style="text-align: center; padding: 10px; display: none;" id="attach_link_indicator"><?php echo image_tag('spinning_26.gif'); ?></div>
						<div style="text-align: center;"><input type="submit" value="<?php echo __('Add link'); ?>" style="font-weight: bold;"><?php echo __('%attach_link% or %cancel%', array('%attach_link%' => '', '%cancel%' => '<b>'.javascript_link_tag(__('cancel'), array('onclick' => "$('attach_link').toggle();")).'</b>')); ?></div>
					</form>
				</div>
				<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
			</div>
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