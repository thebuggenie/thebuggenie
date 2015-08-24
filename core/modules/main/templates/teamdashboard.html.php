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
        <span class="dashboard_team_header"><?php echo $team->getName(); ?></span><br />
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
                    <?php echo __('Members of %team', array('%team' => __($team->getName()))); ?>
                </div>
                <?php if (count($users) > 0): ?>
                    <ul class="team_users">
                    <?php foreach ($users as $user): ?>
                        <li><?php echo include_component('main/userdropdown', array('user' => $user)); ?></li>
                    <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="content faded_out"><?php echo __('This team has no members'); ?>.</p>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</div>
