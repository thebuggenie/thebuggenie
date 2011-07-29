<table style="width: 100%;" cellpadding="0" cellspacing="0"<?php if (isset($table_id)): ?> id="<?php echo $table_id; ?>"<?php endif; ?>>
	<tr>
		<td class="project_information_sidebar" id="project_information_sidebar">
			<div id="project_header_container">
				<?php echo image_tag('sidebar_collapse.png', array('id' => 'project_sidebar_collapse', 'onclick' => "\$('project_information_sidebar').addClassName('collapsed');$(this).hide();\$('project_sidebar_expand').show();")); ?>
				<?php echo image_tag('sidebar_expand.png', array('id' => 'project_sidebar_expand', 'style' => 'display: none;', 'onclick' => "\$('project_information_sidebar').removeClassName('collapsed');$(this).hide();\$('project_sidebar_collapse').show();")); ?>
				<div>
					<?php if ($tbg_user->canEditProjectDetails($selected_project)): ?>
						<div onclick="$('project_settings_popout').toggle();" class="button button-silver button-icon config_link" title="<?php echo __('Edit project settings'); ?>"><span><?php echo image_tag('cfg_icon_projectheader.png'); ?></span></div>
						<div id="project_settings_popout" class="smaller_buttons rounded_box shadowed white" style="display: none; width: 500px; text-align: center; position: absolute; left: 282px; top: -9px; padding: 6px;">
							<div class="button button-blue" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'project_config', 'project_id' => $selected_project->getID())); ?>');$('project_settings_popout').toggle();" ><span><?php echo __('Quick edit project'); ?></span></div>
							<div class="button button-blue"><?php echo link_tag(make_url('project_settings', array('project_key' => $selected_project->getKey())), '<span>'.__('Edit project').'</span>'); ?></a></div>
							<div class="button button-green"><?php echo link_tag(make_url('project_releases', array('project_key' => $selected_project->getKey())), '<span>'.__('Release center').'</span>'); ?></a></div>
						</div>
					<?php endif; ?>
					<div id="project_name">
						<?php echo image_tag($selected_project->getIcon(), array('class' => 'logo'), $selected_project->hasIcon(), 'core', !$selected_project->hasIcon()); ?>
						<span id="project_name_span"><?php echo $selected_project->getName(); ?></span><br>
						<span id="project_key_span"><?php echo $selected_project->getKey(); ?></span>
					</div>
					<div>
						<span id="project_description_span">
							<?php if ($selected_project->hasDescription()): ?>
								<?php echo tbg_parse_text($selected_project->getDescription()); ?>
							<?php endif; ?>
						</span>
					</div>
					<div id="project_no_description"<?php if ($selected_project->hasDescription()): ?> style="display: none;"<?php endif; ?>>
						<?php echo __('This project has no description'); ?>
					</div>
					<div id="project_documentation_url"<?php if (!$selected_project->hasDocumentationUrl()): ?> style="display: none;"<?php endif; ?>>
						<a href="<?php echo $selected_project->getDocumentationUrl(); ?>" target="_blank"><?php echo __('View documentation'); ?></a>
					</div>
					<div class="download_links">
						<div class="button button-green small-button"><span><?php echo image_tag('icon_download.png').__('Download stable'); ?></span></div>
						<div class="button button-green small-button"><span><?php echo image_tag('icon_download.png').__('Download beta'); ?></span></div>
					</div>
					<div id="project_owner">
						<?php if ($selected_project->hasOwner()): ?>
							<div style="font-weight: bold; float: left; margin: 0 10px 0 0;"><?php echo __('Owned by: %name%', array('%name%' => '')); ?></div>
							<?php if ($selected_project->getOwnerType() == TBGIdentifiableClass::TYPE_USER): ?>
								<div style="width: auto; display: table-cell; clear: none; padding: 0 10px 0 0; ">
									<?php echo include_component('main/userdropdown', array('user' => $selected_project->getOwner())); ?>
								</div>
							<?php else: ?>
								<div style="width: auto; display: table-cell; clear: none; padding: 0 10px 0 0; ">
									<?php echo include_component('main/teamdropdown', array('team' => $selected_project->getOwner())); ?>
								</div>
							<?php endif; ?>
						<?php else: ?>
							<div class="faded_out" style="font-weight: normal;"><?php echo __('No project owner specified'); ?></div>
						<?php endif; ?>
					</div>
					<div id="project_leader">
						<?php if ($selected_project->hasLeader()): ?>
							<div style="font-weight: bold; float: left; margin: 0 10px 0 0;"><?php echo __('Lead by: %name%', array('%name%' => '')); ?></div>
							<?php if ($selected_project->getLeaderType() == TBGIdentifiableClass::TYPE_USER): ?>
								<div style="width: auto; display: table-cell; clear: none; padding: 0 10px 0 0; ">
									<?php echo include_component('main/userdropdown', array('user' => $selected_project->getLeader())); ?>
								</div>
							<?php else: ?>
								<div style="width: auto; display: table-cell; clear: none; padding: 0 10px 0 0; ">
									<?php echo include_component('main/teamdropdown', array('team' => $selected_project->getLeader())); ?>
								</div>
							<?php endif; ?>
						<?php else: ?>
							<div class="faded_out" style="font-weight: normal;"><?php echo __('Nor project leader specified'); ?></div>
						<?php endif; ?>
					</div>
					<div id="project_qa">
						<?php if ($selected_project->hasQaResponsible()): ?>
							<div style="font-weight: bold; float: left; margin: 0 10px 0 0;"><?php echo __('QA responsible: %name%', array('%name%' => '')); ?></div>
							<?php if ($selected_project->getQaResponsibleType() == TBGIdentifiableClass::TYPE_USER): ?>
								<div style="width: auto; display: table-cell; clear: none; padding: 0 10px 0 0; ">
									<?php echo include_component('main/userdropdown', array('user' => $selected_project->getQaResponsible())); ?>
								</div>
							<?php else: ?>
								<div style="width: auto; display: table-cell; clear: none; padding: 0 10px 0 0; ">
									<?php echo include_component('main/teamdropdown', array('team' => $selected_project->getQaResponsible())); ?>
								</div>
							<?php endif; ?>
						<?php else: ?>
							<div class="faded_out" style="font-weight: normal;"><?php echo __('No QA responsible specified'); ?></div>
						<?php endif; ?>
					</div>
					<div class="sidebar_links">
						<?php include_template('project/projectinfolinks'); ?>
					</div>
				</div>
			</div>
		</td>
		<td class="project_information_main">
