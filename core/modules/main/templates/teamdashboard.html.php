<?php

    $tbg_response->addBreadcrumb(__('Teams'), null, tbg_get_breadcrumblinks('team_list'));
    if ($team instanceof \thebuggenie\core\entities\Team)
    {
        $tbg_response->setTitle(__('Team dashboard for %team_name', array('%team_name' => $team->getName())));
        $tbg_response->setPage('team');
        $tbg_response->addBreadcrumb(__($team->getName()), make_url('team_dashboard', array('team_id' => $team->getID())));
    }
    else
    {
        $tbg_response->setTitle(__('Team dashboard'));
        $tbg_response->addBreadcrumb(__('Team dashboard'));
    }

?>

<div class="team_dashboard">
    <div class="dashboard_team_info">
        <span class="dashboard_team_header"><?php echo $team->getName(); ?></span>
        <?php if ($tbg_user->canAccessConfigurationPage(\thebuggenie\core\framework\Settings::CONFIGURATION_SECTION_USERS)): ?>
            <div class="project_header_right button-group">
                <button class="button button-silver dropper first last" id="team_<?php echo $team->getID(); ?>_more_actions"><?= image_tag('spinning_16.gif', ['id' => 'team_members_' . $team->getID() . '_indicator', 'style' => 'display: none']); ?>&nbsp;<?php echo __('Actions'); ?></button>
                <ul style="margin-top: 28px; font-size: 1.1em;" class="simple_list rounded_box white shadowed popup_box more_actions_dropdown" onclick="$(this).toggle();">
                    <li><?php echo javascript_link_tag(fa_image_tag('user-plus').__('Add member(s) to this team'), array('onclick' => '$(\'addmember_team_'.$team->getID().'\').toggle(\'block\');')); ?></li>
                    <li class="separator"></li>
                    <li><?php echo javascript_link_tag(fa_image_tag('times', ['class' => 'delete']).__('Delete this team'), array('onclick' => "TBG.Main.Helpers.Dialog.show('".__('Do you really want to delete this team?')."', '".__('If you delete this team, then all users in this team will be lose the permissions given via this team')."', {yes: {click: function() {TBG.Config.Team.remove('".make_url('configure_users_delete_team', array('team_id' => $team->getID()))."', {$team->getID()}); }}, no: { click: TBG.Main.Helpers.Dialog.dismiss }});")); ?></li>
                </ul>
            </div>
        <?php endif; ?>
        <?php include_component('main/identifiableselector', [
            'html_id'       => "addmember_team_{$team->getID()}",
            'header'        => __('Add a member to this team'),
            'callback'      => "TBG.Config.Team.addMember('".make_url('configure_users_add_team_member', ['team_id' => $team->getID(), 'user_id' => '%identifiable_value'])."', ".$team->getID().", '%identifiable_value');$('addmember_team_{$team->getID()}').hide();",
            'base_id'       => "addmember_team_{$team->getID()}",
            'include_teams' => false,
            'allow_clear'   => false,
            'allow_close'   => true,
            'style'         => ['right' => '12px', 'top' => '35px'],
            'absolute'      => true
        ]); ?>
    </div>

    <table class="team_dashboard_table">
        <tr>
            <td class="team_dashboard_projects padded">
                <div class="header">
                    <?php echo __('Projects for %team', array('%team' => __($team->getName()))); ?>
                        <a style="float: right;" class="button button-silver" href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'team_archived_projects', 'tid' => $team->getID())); ?>');"><?php echo __('Show archived projects'); ?></a>
                </div>
                <?php if (count($projects) > 0): ?>
                    <ul class="project_list simple_list">
                    <?php foreach ($projects as $aProject): ?>
                        <li><?php include_component('project/overview', array('project' => $aProject)); ?></li>
                    <?php endforeach; ?>
                    </ul>
                    <div class="header" style="margin: 5px 0;"><?php echo __('Milestones / sprints'); ?></div>
                    <?php $milestone_cc = 0; ?>
                    <?php foreach ($projects as $project): ?>
                        <?php foreach ($project->getUpcomingMilestones() as $milestone): ?>
                            <?php if ($milestone->isScheduled() || $milestone->isOverdue()): ?>
                                <?php include_component('main/milestonedashboardbox', array('milestone' => $milestone)); ?>
                                <?php $milestone_cc++; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    <?php if ($milestone_cc == 0): ?>
                        <div class="faded_out"><?php echo __('There are no upcoming milestones for any of this team\'s associated projects'); ?></div>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="content faded_out"><?php echo __('There are no projects linked to this team'); ?>.</p>
                <?php endif; ?>
            </td>
            <td class="team_dashboard_users padded">
                <div class="header">
                    <?php echo __('Members of %team', array('%team' => __($team->getName()))); ?>&nbsp;(<span id="team_<?= $team->getID(); ?>_membercount"><?= $team->getNumberOfMembers(); ?></span>)
                </div>
                <div id="team_members_<?= $team->getID(); ?>_list">
                    <?= include_component('configuration/teamuserlist', compact('team', 'users')); ?>
                </div>
            </td>
        </tr>
    </table>
</div>
