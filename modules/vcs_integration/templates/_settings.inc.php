<p><?php echo __('Use this page to configure the interface between The Bug Genie and your VCS system. Note that further configuration is necessary to use this feature - please refer to the %help% for further details on these settings and other necessary configuration.', array('%help%' => link_tag(make_url('publish_article', array('article_name' => 'VCSIntegration')), __('help'), array('target' => '_blank')))); ?></p>
<div style="margin-top: 5px; width: 750px; clear: both; height: 30px;" class="tab_menu">
	<ul id="vcsintegration_settings_menu">
		<li class="selected" id="tab_general_settings"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_general_settings', 'vcsintegration_settings_menu');" href="javascript:void(0);"><?php echo image_tag('cfg_icon_general.png', array('style' => 'float: left;')).__('General settings'); ?></a></li>
		<li id="tab_project_settings"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_project_settings', 'vcsintegration_settings_menu');" href="javascript:void(0);"><?php echo image_tag('cfg_icon_projects.png', array('style' => 'float: left;')).__('Project settings'); ?></a></li>
	</ul>
</div>
<div id="vcsintegration_settings_menu_panes">
	<div id="tab_general_settings_pane" class="rounded_box borderless mediumgrey" style="margin: 10px 0 0 0; width: 700px;<?php if ($access_level == TBGSettings::ACCESS_FULL): ?> border-bottom: 0;<?php endif; ?>">
		<div class="header"><?php echo __('General settings'); ?></div>
		<div class="content" style="padding-bottom: 10px;">all of this has been removed and is depricated</div>
	</div>
	<div id="tab_project_settings_pane" class="rounded_box borderless mediumgrey<?php if ($access_level == TBGSettings::ACCESS_FULL): ?> cut_bottom<?php endif; ?>" style="margin: 10px 0 0 0; display: none; width: 700px;<?php if ($access_level == TBGSettings::ACCESS_FULL): ?> border-bottom: 0;<?php endif; ?>">
		<div class="header"><?php echo __('Project settings'); ?></div>
		<div class="content" style="padding-bottom: 10px;">see project configuration for details - click these URLs</div>
		<table style="width: 680px;" class="padded_table" cellpadding=0 cellspacing=0 id="vcsintegration_settings_table">
			<?php
			$allProjects = TBGProject::getAll();
			foreach ($allProjects as $aProject)
			{
				echo javascript_link_tag($aProject->getName(), array('onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'project_config', 'section' => 'vcs', 'project_id' => $aProject->getID()))."')")).'<br>';
			}
			?>
		</table>
	</div>
</div>