<div class="project_header">
	<div class="project_header_right">
		<?php TBGEvent::createNew('core', 'project_header_buttons')->trigger(); ?>
		<?php if ($tbg_response->getPage() != 'project_releases'): ?>
			<div class="button button-orange"><?php echo link_tag(make_url('project_releases', array('project_key' => $selected_project->getKey())), image_tag('icon_download.png').__('Download')); ?></div>
		<?php endif; ?>
		<?php if (TBGContext::getUser()->canReportIssues($selected_project)): ?>
			<div class="button button-green report_button" style="overflow: visible; position: relative;">
				<span><?php echo image_tag('tab_reportissue.png'); ?> <?php echo __('Report an issue'); ?></span>
				<div class="report_button_hover rounded_box green shadowed borderless">
					<div class="tab_menu_dropdown">
						<?php $cc = 1; ?>
						<?php foreach ($selected_project->getIssuetypeScheme()->getIssuetypes() as $issuetype): ?>
							<?php if ($cc == 1)
									$class = 'first';
								elseif ($cc == count($selected_project->getIssuetypeScheme()->getIssuetypes()))
									$class = 'last';
								else
									$class = '';

								$cc++;
							?>
							<?php echo link_tag(make_url('project_reportissue_with_issuetype', array('project_key' => $selected_project->getKey(), 'issuetype' => $issuetype->getKey())), image_tag($issuetype->getIcon() . '_tiny.png' ) . __($issuetype->getName()), array('class' => $class)); ?>
						<?php endforeach;?>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<?php if ($tbg_user->canEditProjectDetails($selected_project) && $tbg_response->getPage() != 'project_settings'): ?>
			<div class="button button-silver config_link" title="<?php echo __('Edit project settings'); ?>"><?php echo link_tag(make_url('project_settings', array('project_key' => $selected_project->getKey())), image_tag('icon_edit.png').__('Edit project')); ?></div>
			<div id="project_settings_popout" class="rounded_box shadowed white" style="display: none; width: 500px; text-align: center; position: absolute; right: 15px; top: 125px; padding: 6px;">
				<div class="button button-blue" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'project_config', 'project_id' => $selected_project->getID())); ?>');$('project_settings_popout').toggle();" ><span><?php echo image_tag('quickopen.png').__('Quick edit project'); ?></span></div>
				<div class="button button-blue"><?php echo link_tag(make_url('project_settings', array('project_key' => $selected_project->getKey())), '<span>'.image_tag('icon_edit.png').__('Edit project').'</span>'); ?></div>
				<div class="button button-green"><?php echo link_tag(make_url('project_release_center', array('project_key' => $selected_project->getKey())), '<span>'.image_tag('icon_releasecenter.png').__('Release center').'</span>'); ?></a></div>
			</div>
		<?php endif; ?>
	</div>
	<div class="project_header_left">
		<div id="project_name">
			<?php echo image_tag($selected_project->getLargeIconName(), array('class' => 'logo', 'style' => 'width: 32px; height: 32px;'), $selected_project->hasLargeIcon()); ?>
			<span id="project_name_span"><?php echo $selected_project->getName(); ?></span>
			<span id="project_key_span">(<?php echo $selected_project->getKey(); ?>)</span>
		</div>
	</div>
</div>