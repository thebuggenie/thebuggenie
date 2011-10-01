<div class="project_header">
	<div class="project_header_right">
		<?php TBGEvent::createNew('core', 'project_header_buttons')->trigger(); ?>
		<?php if ($tbg_response->getPage() != 'project_releases'): ?>
			<?php echo link_tag(make_url('project_releases', array('project_key' => $selected_project->getKey())), image_tag('icon_download.png').__('Download'), array('class' => 'button button-orange')); ?>
		<?php endif; ?>
		<?php if (TBGContext::getUser()->canReportIssues($selected_project)): ?>
			<div class="report_button">
				<span class="button button-green"><?php echo image_tag('tab_reportissue.png'); ?> <?php echo __('Report an issue'); ?></span>
				<div class="report_button_hover rounded_box green shadowed borderless">
					<div class="tab_menu_dropdown">
						<?php $cc = 1; ?>
						<?php foreach ($selected_project->getIssuetypeScheme()->getReportableIssuetypes() as $issuetype): ?>
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
	</div>
	<div class="project_header_left">
		<div id="project_name">
			<?php echo image_tag($selected_project->getLargeIconName(), array('class' => 'logo', 'style' => 'width: 32px; height: 32px;'), $selected_project->hasLargeIcon()); ?>
			<span id="project_name_span"><?php echo $selected_project->getName(); ?></span>
			<span id="project_key_span">(<?php echo $selected_project->getKey(); ?>)</span>
		</div>
	</div>
</div>