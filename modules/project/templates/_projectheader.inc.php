<div class="project_header">
	<div class="project_header_right">
		<?php TBGEvent::createNew('core', 'project_header_buttons')->trigger(); ?>
		<?php if ($selected_project->hasDownloads() && $tbg_response->getPage() != 'project_releases'): ?>
			<?php echo link_tag(make_url('project_releases', array('project_key' => $selected_project->getKey())), image_tag('icon_download.png').__('Download'), array('class' => 'button button-orange')); ?>
		<?php endif; ?>
		<?php if (TBGContext::getUser()->canReportIssues($selected_project) && !$selected_project->isLocked()): ?>
			<?php echo link_tag(make_url('project_reportissue', array('project_key' => $selected_project->getKey())), image_tag('tab_reportissue.png' ) . __('Report an issue'), array('class' => 'button button-green')); ?>
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