<li class="story_card" id="scrum_story_<?php echo $issue->getID(); ?>" style="background: url('<?php echo BUGScontext::getTBGPath() . '/themes/' . BUGSsettings::getThemeName() . '/scrum_storycard.png'; ?>') repeat-x; background-color: #FFF;">
	<div style="display: none;" class="story_color_selector" id="color_selector_<?php echo $issue->getID(); ?>">
		<div style="float: left;">
			<?php foreach ($colors as $color): ?>
				<div onclick="setStoryColor('<?php echo make_url('project_scrum_story_setcolor', array('project_key' => $issue->getProject()->getKey(), 'story_id' => $issue->getID())); ?>', <?php echo $issue->getID(); ?>, '<?php echo $color; ?>');" class="story_color_selector_item c_sel_red_1" style="background-color: <?php echo $color; ?>;">&nbsp;</div>
			<?php endforeach; ?>
		</div>
		<div style="float: left; position: relative;">
			<div class="header" style="margin-left: 5px;"><?php echo __('Pick a color for this user story'); ?></div>
			<div style="margin-left: 5px; width: 240px;"><?php echo __('Selecting a color makes the story easily recognizable'); ?>.</div>
			<?php echo image_tag('spinning_20.gif', array('id' => 'color_selector_'.$issue->getID().'_indicator', 'style' => 'position: absolute; right: 2px; top: 2px; display: none;')); ?>
		</div>
	</div>
	<div class="story_color" id="story_color_<?php echo $issue->getID(); ?>" onclick="$('color_selector_<?php echo $issue->getID(); ?>').toggle();" style="cursor: pointer; background-color: <?php echo $issue->getScrumColor(); ?>;">&nbsp;</div>
	<div class="header"><?php echo $issue->getTitle(); ?></div>
	<div class="story_no"><?php echo $issue->getIssueNo(); ?></div>
	<input type="hidden" id="scrum_story_<?php echo $issue->getID(); ?>_id" value="<?php echo $issue->getIssueNo(); ?>">
	<?php if ($issue->hasDescription()): ?>
		<div class="content"><?php echo $issue->getDescription(); ?></div>
	<?php endif; ?>
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