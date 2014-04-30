<?php
	$projectHasDescription = TBGContext::getCurrentProject()->hasDescription();
?>
<div id="project_description"<?php if(!$projectHasDescription) echo ' class="none"'; ?>>
	<?php
		if ($projectHasDescription)
			echo tbg_parse_text(TBGContext::getCurrentProject()->getDescription());
		else
			echo __('This project has no description');
	?>
</div>
<?php if (TBGContext::getCurrentProject()->hasOwner()): ?>
	<div class="project_role">
		<div class="label"><?php echo __('Owned by: %name', array('%name' => '')); ?></div>
		<div class="value">
		<?php
			echo include_component(
			TBGContext::getCurrentProject()->getOwner() instanceof TBGUser
			? 'main/userdropdown'
			: 'main/teamdropdown', array('user' => TBGContext::getCurrentProject()->getOwner()));
		?>
		</div>
	</div>
<?php endif; ?>
<?php if (TBGContext::getCurrentProject()->hasLeader()): ?>
	<div class="project_role">
		<div class="label"><?php echo __('Lead by: %name', array('%name' => '')); ?></div>
		<div class="value">
		<?php
			echo include_component(
			TBGContext::getCurrentProject()->getLeader() instanceof TBGUser
			? 'main/userdropdown'
			: 'main/teamdropdown', array('user' => TBGContext::getCurrentProject()->getLeader()));
		?>
		</div>
	</div>
<?php endif; ?>
<?php if (TBGContext::getCurrentProject()->hasQaResponsible()): ?>
	<div class="project_role">
		<div class="label"><?php echo __('QA responsible: %name', array('%name' => '')); ?></div>
		<div class="value">
		<?php
			echo include_component(
			TBGContext::getCurrentProject()->getQaResponsible() instanceof TBGUser
			? 'main/userdropdown'
			: 'main/teamdropdown', array('user' => TBGContext::getCurrentProject()->getQaResponsible()));
		?>
		</div>
	</div>
<?php endif; ?>
<?php if (TBGContext::getCurrentProject()->hasHomepage()): ?>
	<a class="button button-silver dash" href="<?php echo TBGContext::getCurrentProject()->getHomepage(); ?>" target="_blank"><?php echo __('Visit homepage'); ?></a>
<?php endif; ?>
<?php if (TBGContext::getCurrentProject()->hasDocumentationURL()): ?>
	<a class="button button-silver dash" href="<?php echo TBGContext::getCurrentProject()->getDocumentationURL(); ?>" target="_blank"><?php echo __('Open documentation'); ?></a>
<?php endif; ?>
<br style="clear: both;">
