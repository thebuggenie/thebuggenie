<div class="story_color <?php if ($issue->canEditIssue()) echo 'dropper'; ?>" title="<?php echo ($issue->canEditIssue()) ? __('Click to select a planning color for this issue') : __('Planning color for this issue'); ?>" id="story_color_<?php echo $issue->getID(); ?>" style="background-color: <?php echo $issue->getAgileColor(); ?>;">&nbsp;</div>
<?php if ($issue->canEditIssue()): ?>
    <div style="display: none;" class="rounded_box shadowed white story_color_selector popup_box" id="color_selector_<?php echo $issue->getID(); ?>">
        <div>
            <div class="header" style="margin-left: 5px;"><?php echo __('Pick a planning color for this issue'); ?></div>
            <div style="margin-left: 5px;"><?php echo __('Selecting a color makes the issue easily recognizable in the planning view'); ?>.</div>
            <?php echo image_tag('spinning_20.gif', array('id' => 'color_selector_'.$issue->getID().'_indicator', 'style' => 'position: absolute; right: 2px; top: 2px; display: none;')); ?>
        </div>
        <div class="color_items">
            <?php foreach ($colors as $color): ?>
                <div <?php if ($issue->canEditIssue()): ?>onclick="TBG.Project.Scrum.Story.setColor('<?php echo make_url('project_scrum_story_setcolor', array('project_key' => $issue->getProject()->getKey(), 'story_id' => $issue->getID())); ?>', <?php echo $issue->getID(); ?>, '<?php echo $color; ?>', event);" <?php endif; ?>class="story_color_selector_item" style="background-color: <?php echo $color; ?>;">&nbsp;</div>
            <?php endforeach; ?>
        </div>
        <br style="clear: both;">
        <div style="margin: 5px;">
            <?php echo javascript_link_tag(__('%color_list or keep the current color', array('%color_list' => '')), array('onclick' => "$('color_selector_{$issue->getID()}').toggle()")); ?>
        </div>
    </div>
<?php endif; ?>
