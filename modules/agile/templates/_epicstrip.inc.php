<?php foreach ($board->getEpicIssues() as $epic): ?>
        <?php include_component('agile/milestoneepic', array('epic' => $epic, 'board' => $board)); ?>
<?php endforeach; ?>
<li class="add_epic_container" id="add_epic_container">
        <div class="planning_indicator" id="new_epic_indicator" style="display: none;"><?php echo image_tag('spinning_30.gif'); ?></div>
        <span class="plus" onclick="$(this).up('li').toggleClassName('selected');$('new_epic_title').focus();">+</span>
        <form action="<?php echo make_url('agile_addepic', array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID())); ?>" method="post" onsubmit="TBG.Project.Planning.addEpic(this);return false;">
                <label for="new_epic_title"><?php echo __('Title'); ?></label>
                <input type="text" name="title" placeholder="<?php echo __('Enter the name of the epic here'); ?>" id="new_epic_title">
                <label for="new_epic_label"><?php echo __('Label'); ?></label>
                <input type="text" name="shortname" placeholder="<?php echo __('Enter a very short label here'); ?>" id="new_epic_label">
                <div class="actionbuttons">
                        <?php echo __('%cancel or %add_epic', array('%cancel' => javascript_link_tag(__('cancel'), array('onclick' => "$(this).up('li').toggleClassName('selected');")), '%add_epic' => '<input type="submit" class="button button-silver" value="'.__('Add epic').'">')); ?>
                </div>
        </form>
</li>
