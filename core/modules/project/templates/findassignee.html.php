<?php if ($message): ?>
    <p class="faded_out" style="padding: 5px;"><?php echo __('Please specify something to search for'); ?></p>
<?php else: ?>
    <div class="config_header" style="margin-top: 10px;"><b><?php echo __('The following teams were found based on your search criteria'); ?>:</b></div>
    <?php if ($teams): ?>
        <div style="margin: 5px 0 0 10px;">
            <?php foreach ($teams as $team): ?>
                <div id="assign_team_<?php echo $team->getID(); ?>">
                    <label for="role_team_<?php echo $team->getID(); ?>"><?php echo image_tag('icon_team.png', array('style' => "width: 16px; height: 16px; margin: 2px 5px -2px 0;")); ?><?php echo $team->getName(); ?>:</label>&nbsp;
                    <select name="role" id="role_team_<?php echo $team->getID(); ?>">
                        <optgroup label="<?php echo __('Globally available roles'); ?>">
                            <?php foreach ($global_roles as $role): ?>
                                <option value="<?php echo $role->getId(); ?>"><?php echo $role->getName(); ?></option>
                            <?php endforeach ;?>
                        </optgroup>
                        <optgroup label="<?php echo __('Project-specific roles'); ?>">
                            <?php foreach ($project_roles as $role): ?>
                                <option value="<?php echo $role->getId(); ?>"><?php echo $role->getName(); ?></option>
                            <?php endforeach ;?>
                        </optgroup>
                    </select>
                    <input type="hidden" name="target" value="project_<?php echo $selected_project->getID(); ?>">
                    &nbsp;
                    <button onclick="TBG.Project.assign('<?php echo make_url('configure_project_add_assignee', array('project_id' => $selected_project->getID(), 'assignee_type' => 'team', 'assignee_id' => $team->getID())); ?>', 'assign_team_<?php echo $team->getID(); ?>');return false;"><?php echo __('Add team'); ?></button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="faded_out" style="padding: 2px 0 0 10px;"><?php echo __('Could not find any teams based on your search criteria'); ?></p>
    <?php endif;?>
    <div class="config_header" style="margin-top: 10px;"><b><?php echo __('The following users were found based on your search criteria'); ?>:</b></div>
    <?php if ($users): ?>
        <div style="margin: 5px 0 0 10px;">
            <?php foreach ($users as $user): ?>
                 <div id="assign_user_<?php echo $user->getID(); ?>">
                    <label for="role_<?php echo $user->getID(); ?>"><?php echo image_tag($user->getAvatarURL(), array('style' => "width: 16px; height: 16px; margin: 2px 5px -2px 0"), true); ?><?php echo $user->getNameWithUsername(); ?>:</label>&nbsp;
                    <select name="role" id="role_<?php echo $user->getID(); ?>">
                        <optgroup label="<?php echo __('Globally available roles'); ?>">
                            <?php foreach ($global_roles as $role): ?>
                                <option value="<?php echo $role->getId(); ?>"><?php echo $role->getName(); ?></option>
                            <?php endforeach ;?>
                        </optgroup>
                        <optgroup label="<?php echo __('Project-specific roles'); ?>">
                            <?php foreach ($project_roles as $role): ?>
                                <option value="<?php echo $role->getId(); ?>"><?php echo $role->getName(); ?></option>
                            <?php endforeach ;?>
                        </optgroup>
                    </select>
                    <input type="hidden" name="target" value="project_<?php echo $selected_project->getID(); ?>">
                    &nbsp;
                    <button onclick="TBG.Project.assign('<?php echo make_url('configure_project_add_assignee', array('project_id' => $selected_project->getID(), 'assignee_type' => 'user', 'assignee_id' => $user->getID())); ?>', 'assign_user_<?php echo $user->getID(); ?>');return false;"><?php echo __('Add user'); ?></button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="faded_out" style="padding: 2px 0 0 10px;"><?php echo __('Could not find any users based on your search criteria'); ?></p>
    <?php endif;?>
<?php endif; ?>
<div style="padding: 10px 0 10px 0; display: none;" id="assign_dev_indicator"><span style="float: left;"><?php echo image_tag('spinning_16.gif'); ?></span>&nbsp;<?php echo __('Please wait'); ?></div>
