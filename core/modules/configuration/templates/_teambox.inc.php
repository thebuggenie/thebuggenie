<div class="greybox" style="padding: 0; margin-top: 5px;" id="teambox_<?= $team->getID(); ?>">
    <div style="padding: 5px; position: relative;">
        <?= image_tag('team_large.png', array('style' => 'float: left; margin-right: 5px;')); ?>
        <div style="position: absolute; right: 12px; top: 12px;">
            <button class="button button-silver dropper" id="team_<?= $team->getID(); ?>_more_actions"><?= __('Actions'); ?></button>
            <ul id="team_<?= $team->getID(); ?>_more_actions_dropdown" style="font-size: 1.1em; width: 200px; top: 23px; margin-top: 0; text-align: right; z-index: 1000;" class="simple_list rounded_box white shadowed popup_box more_actions_dropdown" onclick="$(this).previous().toggleClassName('button-pressed');$(this).toggle();">
                <li>
                    <?= javascript_link_tag(__('Add member(s) to this team'), array('onclick' => '$(\'addmember_team_'.$team->getID().'\').toggle(\'block\');')); ?>
                </li>
                <li>
                    <?= javascript_link_tag(__('List users in this team'), array('onclick' => 'TBG.Config.Team.showMembers(\''.make_url('configure_users_get_team_members', array('team_id' => $team->getID())).'\', '.$team->getID().');')); ?>
                </li>
                <li>
                    <?= javascript_link_tag(__('Edit permissions for this team'), array('onclick' => "TBG.Config.Team.getPermissionsBlock('".make_url('configure_permissions_get_configurator', array('team_id' => $team->getID(), 'base_id' => $team->getID())). "', ".$team->getID().");", 'id' => 'team_permissions_'.$team->getID().'_link')); ?>
                </li>
                <li>
                    <?= javascript_link_tag(__('Clone this user team'), array('onclick' => '$(\'clone_team_'.$team->getID().'\').toggle();')); ?>
                </li>
                <li>
                    <?= javascript_link_tag(__('Delete this user team'), array('onclick' => "TBG.Main.Helpers.Dialog.show('".__('Do you really want to delete this team?')."', '".__('If you delete this team, then all users in this team will be lose the permissions given via this team')."', {yes: {click: function() {TBG.Config.Team.remove('".make_url('configure_users_delete_team', array('team_id' => $team->getID()))."', {$team->getID()}); }}, no: { click: TBG.Main.Helpers.Dialog.dismiss }});")); ?>
                </li>
            </ul>
        </div>
        <p class="teambox_header"><?= $team->getName(); ?></p>
        <p class="teambox_membercount"><?= __('ID: %id', array('%id' => $team->getID())); ?> - <?= __('%number_of member(s)', array('%number_of' => '<span id="team_'.$team->getID().'_membercount">'.$team->getNumberOfMembers().'</span>')); ?></p>
        <div class="rounded_box white shadowed" style="margin: 5px; display: none;" id="clone_team_<?= $team->getID(); ?>">
            <div class="dropdown_header"><?= __('Please specify what parts of this team you want to clone'); ?></div>
            <div class="dropdown_content copy_team_link">
                <form id="clone_team_<?= $team->getID(); ?>_form" action="<?= make_url('configure_users_clone_team', array('team_id' => $team->getID())); ?>" method="post" accept-charset="<?= \thebuggenie\core\framework\Settings::getCharset(); ?>" onsubmit="TBG.Config.Team.clone('<?= make_url('configure_users_clone_team', array('team_id' => $team->getID())); ?>');return false;">
                    <div id="add_team">
                        <label for="clone_team_<?= $team->getID(); ?>_new_name"><?= __('New team name'); ?></label>
                        <input type="text" id="clone_team_<?= $team->getID(); ?>_new_name" name="team_name"><br />
                        <input type="checkbox" class="fancycheckbox" id="clone_team_<?= $team->getID(); ?>_permissions" name="clone_permissions" value="1" checked />
                        <label for="clone_team_<?= $team->getID(); ?>_permissions" style="font-weight: normal;"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Clone permissions from the old team for the new team'); ?></label><br />
                        <input type="checkbox" class="fancycheckbox" id="clone_team_<?= $team->getID(); ?>_memberships" name="clone_memberships" value="1" checked />
                        <label for="clone_team_<?= $team->getID(); ?>_memberships" style="font-weight: normal;"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Clone memberships (make members in the old team also members in the new, cloned team)'); ?></label>
                    </div>
                </form>
                <div style="text-align: right;">
                    <?= javascript_link_tag(__('Clone this team'), array('onclick' => 'TBG.Config.Team.clone(\''.make_url('configure_users_clone_team', array('team_id' => $team->getID())).'\', '.$team->getID().');')); ?> :: <b><?= javascript_link_tag(__('Cancel'), array('onclick' => '$(\'clone_team_'.$team->getID().'\').toggle();')); ?></b>
                </div>
                <table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="clone_team_<?= $team->getID(); ?>_indicator">
                    <tr>
                        <td style="width: 20px; padding: 2px;"><?= image_tag('spinning_20.gif'); ?></td>
                        <td style="padding: 0px; text-align: left;"><?= __('Cloning team, please wait'); ?>...</td>
                    </tr>
                </table>
            </div>
        </div>
        <?php include_component('main/identifiableselector', array(    'html_id'        => "addmember_team_{$team->getID()}",
                                                                'header'             => __('Add a member to this team'),
                                                                'callback'             => "TBG.Config.Team.addMember('".make_url('configure_users_add_team_member', array('team_id' => $team->getID(), 'user_id' => '%identifiable_value'))."', ".$team->getID().", '%identifiable_value');$('addmember_team_{$team->getID()}').hide();",
                                                                'base_id'            => "addmember_team_{$team->getID()}",
                                                                'include_teams'        => false,
                                                                'allow_clear'        => false,
                                                                'allow_close'        => true,
                                                                'style'                => array('right' => '12px', 'top' => '35px'),
                                                                'absolute'            => true)); ?>
    </div>
    <div class="rounded_box lightgrey" style="margin-bottom: 5px; display: none;" id="team_members_<?= $team->getID(); ?>_container">
        <div class="dropdown_header"><?= __('Users in this team'); ?></div>
        <table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="team_members_<?= $team->getID(); ?>_indicator">
            <tr>
                <td style="width: 20px; padding: 2px;"><?= image_tag('spinning_20.gif'); ?></td>
                <td style="padding: 0px; text-align: left;"><?= __('Retrieving members, please wait'); ?>...</td>
            </tr>
        </table>
        <div id="team_members_<?= $team->getID(); ?>_list"></div>
    </div>
</div>
<div id="team_<?= $team->getID(); ?>_permissions_container" style="display: none;" class="fullpage_backdrop">
    <div class="fullpage_backdrop_content backdrop_box large">
        <div class="backdrop_detail_header">
            <span><?= __('Configure advanced permissions for %teamname', array('%teamname' => $team->getName())); ?></span>
            <?= javascript_link_tag(fa_image_tag('times'), array('class' => 'closer', 'onclick' => "$('team_".$team->getID()."_permissions_container').toggle();")); ?>
        </div>
        <?= image_tag('spinning_32.gif', array('id' => 'team_'.$team->getID().'_permissions_indicator', 'style' => 'display: none;')); ?>
        <div class="backdrop_detail_content config_permissions" id="team_<?= $team->getID(); ?>_permissions"></div>
    </div>
</div>
