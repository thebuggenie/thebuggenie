<div id="project_description_span">
	<?php if (TBGContext::getCurrentProject()->hasDescription()) echo tbg_parse_text(TBGContext::getCurrentProject()->getDescription()); ?>
</div>
<div id="project_no_description"<?php if (TBGContext::getCurrentProject()->hasDescription()): ?> style="display: none;"<?php endif; ?>><?php echo __('This project has no description'); ?></div>
<div id="project_owner">
	<?php if (TBGContext::getCurrentProject()->hasOwner()): ?>
		<div style="font-weight: bold; float: left; margin: 0 10px 0 0;"><?php echo __('Owned by: %name%', array('%name%' => '')); ?></div>
		<?php if (TBGContext::getCurrentProject()->getOwner() instanceof TBGUser): ?>
			<div style="width: auto; display: table-cell; clear: none; padding: 0 10px 0 0; ">
				<?php echo include_component('main/userdropdown', array('user' => TBGContext::getCurrentProject()->getOwner())); ?>
			</div>
		<?php else: ?>
			<div style="width: auto; display: table-cell; clear: none; padding: 0 10px 0 0; ">
				<?php echo include_component('main/teamdropdown', array('team' => TBGContext::getCurrentProject()->getOwner())); ?>
			</div>
		<?php endif; ?>
	<?php else: ?>
		<div class="faded_out" style="font-weight: normal;"><?php echo __('No project owner specified'); ?></div>
	<?php endif; ?>
</div>
<div id="project_leader">
	<?php if (TBGContext::getCurrentProject()->hasLeader()): ?>
		<div style="font-weight: bold; float: left; margin: 0 10px 0 0;"><?php echo __('Lead by: %name%', array('%name%' => '')); ?></div>
		<?php if (TBGContext::getCurrentProject()->getLeader() instanceof TBGUser): ?>
			<div style="width: auto; display: table-cell; clear: none; padding: 0 10px 0 0; ">
				<?php echo include_component('main/userdropdown', array('user' => TBGContext::getCurrentProject()->getLeader())); ?>
			</div>
		<?php else: ?>
			<div style="width: auto; display: table-cell; clear: none; padding: 0 10px 0 0; ">
				<?php echo include_component('main/teamdropdown', array('team' => TBGContext::getCurrentProject()->getLeader())); ?>
			</div>
		<?php endif; ?>
	<?php else: ?>
		<div class="faded_out" style="font-weight: normal;"><?php echo __('No project leader specified'); ?></div>
	<?php endif; ?>
</div>
<div id="project_qa">
	<?php if (TBGContext::getCurrentProject()->hasQaResponsible()): ?>
		<div style="font-weight: bold; float: left; margin: 0 10px 0 0;"><?php echo __('QA responsible: %name%', array('%name%' => '')); ?></div>
		<?php if (TBGContext::getCurrentProject()->getQaResponsible() instanceof TBGUser): ?>
			<div style="width: auto; display: table-cell; clear: none; padding: 0 10px 0 0; ">
				<?php echo include_component('main/userdropdown', array('user' => TBGContext::getCurrentProject()->getQaResponsible())); ?>
			</div>
		<?php else: ?>
			<div style="width: auto; display: table-cell; clear: none; padding: 0 10px 0 0; ">
				<?php echo include_component('main/teamdropdown', array('team' => TBGContext::getCurrentProject()->getQaResponsible())); ?>
			</div>
		<?php endif; ?>
	<?php else: ?>
		<div class="faded_out" style="font-weight: normal;"><?php echo __('No QA responsible specified'); ?></div>
	<?php endif; ?>
</div>
<?php if (TBGContext::getCurrentProject()->hasHomepage()): ?>
	<a class="button button-silver" href="<?php echo TBGContext::getCurrentProject()->getHomepage(); ?>" target="_blank"><?php echo __('Visit homepage'); ?></a>
<?php endif; ?>
<?php if (TBGContext::getCurrentProject()->hasDocumentationURL()): ?>
	<a class="button button-silver" href="<?php echo TBGContext::getCurrentProject()->getDocumentationURL(); ?>" target="_blank"><?php echo __('Open documentation'); ?></a>
<?php endif; ?>
<br style="clear: both;">