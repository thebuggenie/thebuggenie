<?php

	if ($tbg_user->canEditProjectDetails($selected_project))
	{
		$tbg_response->addJavascript('config/projects_ajax.js');
	}
	
?>
<table style="width: 100%;" cellpadding="0" cellspacing="0"<?php if (isset($table_id)): ?> id="<?php echo $table_id; ?>"<?php endif; ?>>
	<tr>
		<td class="project_information_sidebar" id="project_information_sidebar">
			<div id="project_header_container">
				<?php echo image_tag('sidebar_collapse.png', array('id' => 'project_sidebar_collapse', 'onclick' => "\$('project_information_sidebar').addClassName('collapsed');$(this).hide();\$('project_sidebar_expand').show();")); ?>
				<?php echo image_tag('sidebar_expand.png', array('id' => 'project_sidebar_expand', 'style' => 'display: none;', 'onclick' => "\$('project_information_sidebar').removeClassName('collapsed');$(this).hide();\$('project_sidebar_collapse').show();")); ?>
				<div>
					<?php if ($tbg_user->canEditProjectDetails($selected_project)): ?><?php echo javascript_link_tag(image_tag('cfg_icon_projectheader.png', array('class' => 'config_link')), array('onclick' => "showFadedBackdrop('".make_url('get_partial_for_backdrop', array('key' => 'project_config', 'project_id' => $selected_project->getID()))."');")); ?><?php endif; ?>
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
					<div id="project_owner">
						<div style="font-weight: bold; float: left; margin: 0 10px 0 0;"><?php echo __('Owned by'); ?>:</div>
						<?php if ($selected_project->hasOwner()): ?>
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
							<div class="faded_out" style="font-weight: normal;"><?php echo __('noone'); ?></div>
						<?php endif; ?>
					</div>
					<div id="project_leader">
						<div style="font-weight: bold; float: left; margin: 0 10px 0 0;"><?php echo __('Lead by'); ?>:</div>
						<?php if ($selected_project->hasLeader()): ?>
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
							<div class="faded_out" style="font-weight: normal;"><?php echo __('noone'); ?></div>
						<?php endif; ?>
					</div>
					<div id="project_qa">
						<div style="font-weight: bold; float: left; margin: 0 10px 0 0;"><?php echo __('QA responsible'); ?>:</div>
						<?php if ($selected_project->hasQaResponsible()): ?>
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
							<div class="faded_out" style="font-weight: normal;"><?php echo __('noone'); ?></div>
						<?php endif; ?>
					</div>
					<div class="sidebar_links">
						<?php include_template('project/projectinfolinks'); ?>
					</div>
				</div>
			</div>
		</td>
		<td class="project_information_main">
