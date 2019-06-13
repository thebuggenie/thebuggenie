<div class="backdrop_box medium issue_access_policy_box" id="viewissue_add_item_div">
    <div class="backdrop_detail_header">
        <span><?php echo __('Issue access policy'); ?></span>
        <a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.reset();" class="closer"><?php echo fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <input type="radio" name="issue_access" id="issue_access_public" onchange="TBG.Issues.ACL.toggle_checkboxes(this, <?php echo $issue->getID(); ?>, 'public');" value="public"<?php if($issue->isUnlocked() && $issue->isUnlockedCategory()): ?> checked<?php endif; ?>><label for="issue_access_public"><?php echo __('Available to anyone with access to project'); ?></label><br>
        <input type="radio" name="issue_access" id="issue_access_public_category" onchange="TBG.Issues.ACL.toggle_checkboxes(this, <?php echo $issue->getID(); ?>, 'public_category');" value="public_category"<?php if($issue->isUnlocked() && $issue->isLockedCategory()): ?> checked<?php endif; ?>><label for="issue_access_public_category"><?php echo __('Available to anyone with access to project, category and those listed below'); ?></label><br>
        <input type="radio" name="issue_access" id="issue_access_restricted" onchange="TBG.Issues.ACL.toggle_checkboxes(this, <?php echo $issue->getID(); ?>, 'restricted');" value="restricted"<?php if($issue->isLocked()): ?> checked<?php endif; ?>><label for="issue_access_restricted"><?php echo __('Available only to you and those listed below'); ?></label><br>
        <?php image_tag('spinning_16.gif', array('id' => 'acl_indicator_'.$issue->getID(), 'style' => '')); ?>
        <div id="acl-users-teams-selector" style="<?php if($issue->isUnlocked() && $issue->isUnlockedCategory()): ?> display: none;<?php endif; ?>">
            <h4 style="margin-top: 10px;">
                <?php echo javascript_link_tag(__('Add a user or team'), array('onclick' => "var tempPopup = $('popup_find_acl_{$issue->getID()}');  if (tempPopup.style.opacity == 0) { tempPopup.toggle('block'); tempPopup.style.opacity = 1.0; tempPopup.style.transform = 'none'; tempPopup.style.display = 'block';} else { tempPopup.toggle('block'); }", 'style' => 'float: right;', 'class' => 'button button-silver')); ?>
                <?php echo __('Users or teams who can see this issue'); ?>
            </h4>
            <?php include_component('main/identifiableselector', array(    'html_id'             => "popup_find_acl_{$issue->getID()}",
                                                                      'header'             => __('Give someone access to this issue'),
                                                                      'callback'             => "TBG.Issues.ACL.addTarget('" . make_url('getacl_formentry', array('identifiable_type' => 'user', 'identifiable_value' => '%identifiable_value')) . "', {$issue->getID()});",
                                                                      'team_callback'     => "TBG.Issues.ACL.addTarget('" . make_url('getacl_formentry', array('identifiable_type' => 'team', 'identifiable_value' => '%identifiable_value')) . "', {$issue->getID()});",
                                                                      'base_id'            => "popup_find_acl_{$issue->getID()}",
                                                                      'include_teams'        => true,
                                                                      'allow_clear'        => false,
                                                                      'absolute'            => true)); ?>
        </div>
    </div>
    <div id="acl_<?php echo $issue->getID(); ?>_public" style="<?php if($issue->isLocked()): ?> display: none;<?php endif; ?>">
        <form action="<?php echo make_url('unlock_issue', array('issue_id' => $issue->getID())); ?>" onsubmit="TBG.Issues.ACL.set('<?php echo make_url('unlock_issue', array('issue_id' => $issue->getID())); ?>', <?php echo $issue->getID(); ?>, 'public');return false;" method="post" id="acl_<?php echo $issue->getID(); ?>_publicform">
            <div class="backdrop_detail_content">
                <ul class="issue_access_list simple_list" id="issue_<?php echo $issue->getID(); ?>_public_category_access_list" style="<?php if($issue->isUnlocked() && $issue->isUnlockedCategory()): ?> display: none;<?php endif; ?>">
                    <li id="issue_<?php echo $issue->getID(); ?>_public_category_access_list_none" class="faded_out" style="<?php if (count($al_items)): ?>display: none; <?php endif; ?>padding: 5px;"><?php echo __('Noone else can see this issue'); ?></li>
                    <?php foreach ($al_items as $item): ?>
                        <?php include_component('main/issueaclformentry', array('target' => $item['target'])); ?>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="backdrop_details_submit">
                <span class="explanation"></span>
                <div class="submit_container">
                    <input id="issue_access_public_category_input" type="hidden" name="public_category"<?php if($issue->isUnlocked() && $issue->isUnlockedCategory()): ?> disabled<?php endif; ?>>
                    <input type="submit" value="<?php echo __('Save changes'); ?>" class="button button-silver">
                </div>
            </div>
        </form>
    </div>
    <div id="acl_<?php echo $issue->getID(); ?>_restricted" style="<?php if($issue->isUnlocked()): ?> display: none;<?php endif; ?>">
        <form action="<?php echo make_url('move_issue', array('issue_id' => $issue->getID())); ?>" method="post" onsubmit="TBG.Issues.ACL.set('<?php echo make_url('lock_issue', array('issue_id' => $issue->getID())); ?>', <?php echo $issue->getID(); ?>, 'restricted');return false;" id="acl_<?php echo $issue->getID(); ?>_restrictedform">
            <div class="backdrop_detail_content">
                <ul class="issue_access_list simple_list" id="issue_<?php echo $issue->getID(); ?>_restricted_access_list">
                    <li id="issue_<?php echo $issue->getID(); ?>_restricted_access_list_none" class="faded_out" style="<?php if (count($al_items)): ?>display: none; <?php endif; ?>padding: 5px;"><?php echo __('Noone else can see this issue'); ?></li>
                    <?php foreach ($al_items as $item): ?>
                        <?php include_component('main/issueaclformentry', array('target' => $item['target'])); ?>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="backdrop_details_submit">
                <span class="explanation"></span>
                <div class="submit_container">
                    <input type="submit" value="<?php echo __('Save changes'); ?>" class="button button-silver">
                </div>
            </div>
        </form>
    </div>
</div>
