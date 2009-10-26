<li id="scrum_story_<?php echo $issue->getID(); ?>" style="background: url('<?php echo BUGScontext::getTBGPath() . '/themes/' . BUGSsettings::getThemeName() . '/scrum_storycard.png'; ?>') repeat-x; background-color: #FFF;">
	<div class="story_color" style="background-color: <?php echo '#' . $issue->getScrumColor(); ?>;">&nbsp;</div>
	<div class="header"><?php echo $issue->getTitle(); ?></div>
	<div class="story_no"><?php echo $issue->getIssueNo(); ?></div>
	<div class="content"><?php echo $issue->getDescription(); ?></div>
	<div class="story_tags">
		<b><?php echo __('Tags'); ?></b>:
		<?php if (count($issue->getTags()) > 0): ?>
			<?php echo join(', ', $issue->getTags()); ?>
		<?php else: ?>
			<span class="faded_dark"><?php echo __('No tags attached'); ?></span>
		<?php endif; ?>
	</div>
	<div class="story_owner">
		<?php if ($issue->isAssigned()): ?>
			<table style="width: 170px; display: <?php if ($issue->isAssigned()): ?>inline<?php else: ?>none<?php endif; ?>;" cellpadding=0 cellspacing=0 id="scrum_story_<?php echo $issue->getID(); ?>_assigned_to">
				<?php if ($issue->getAssigneeType() == BUGSidentifiableclass::TYPE_USER): ?>
					<?php echo include_component('main/userdropdown', array('user' => $issue->getAssignee())); ?>
				<?php elseif ($issue->getAssigneeType() == BUGSidentifiableclass::TYPE_TEAM): ?>
					<?php echo include_component('main/teamdropdown', array('team' => $issue->getAssignee())); ?>
				<?php endif; ?>
			</table>
		<?php else: ?>
			<span class="faded_dark"><?php echo __('Not claimed by anyone'); ?></span>
		<?php endif; ?>
	</div>
	<div class="story_estimate"><b><?php echo __('Estim'); ?>: </b><?php echo $issue->getEstimatedPoints(); ?></div>
</li>