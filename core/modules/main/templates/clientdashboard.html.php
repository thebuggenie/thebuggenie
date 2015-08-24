<?php

    $tbg_response->addBreadcrumb(__('Clients'), null, tbg_get_breadcrumblinks('client_list'));
    if ($client instanceof \thebuggenie\core\entities\Client)
    {
        $tbg_response->setTitle(__('Client dashboard for %client_name', array('%client_name' => $client->getName())));
        $tbg_response->setPage('client');
        $tbg_response->addBreadcrumb($client->getName(), make_url('client_dashboard', array('client_id' => $client->getID())));
    }
    else
    {
        $tbg_response->setTitle(__('Client dashboard'));
        $tbg_response->addBreadcrumb(__('Client dashboard'));
    }

?>

<?php if ($client instanceof \thebuggenie\core\entities\Client): ?>
    <div class="client_dashboard">
        <div class="dashboard_client_info">
            <span class="dashboard_client_header"><?php echo $client->getName(); ?></span>
            <table>
                <tr>
                    <td style="padding-right: 10px">
                        <b><?php echo __('Website:'); ?></b> <?php if ($client->getWebsite() == ''): ?><span class="faded_out"><?php echo __('none'); ?></span><?php else: ?><a href="<?php echo $client->getWebsite(); ?>" target="_blank"><?php echo $client->getWebsite(); ?></a><?php endif; ?>
                    </td>
                    <td style="padding: 0px 10px">
                        <b><?php echo __('Email address:'); ?></b> <?php if ($client->getEmail() == ''): ?><span class="faded_out"><?php echo __('none'); ?></span><?php else: ?><a href="mailto:<?php echo $client->getEmail(); ?>" target="_blank"><?php echo $client->getEmail(); ?></a><?php endif; ?>
                    </td>
                    <td style="padding: 0px 10px">
                        <b><?php echo __('Telephone:'); ?></b> <?php if ($client->getTelephone() == ''): ?><span class="faded_out"><?php echo __('none'); ?></span><?php else: ?><?php echo $client->getTelephone(); endif; ?>
                    </td>
                    <td style="padding: 0px 10px">
                        <b><?php echo __('Fax:'); ?></b> <?php if ($client->getFax() == ''): ?><span class="faded_out"><?php echo __('none'); ?></span><?php else: ?><?php echo $client->getFax(); endif; ?>
                    </td>
                </tr>
            </table>
        </div>

        <table class="client_dashboard_table">
            <tr>
                <td class="client_dashboard_projects padded">
                    <div class="header">
                        <?php echo __('Projects for %client', array('%client' => $client->getName())); ?>
                        <a style="float: right;" class="button button-silver" href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'client_archived_projects', 'cid' => $client->getID())); ?>');"><?php echo __('Show archived projects'); ?></a>
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
                                <?php if ($milestone->isScheduled()): ?>
                                    <?php include_component('main/milestonedashboardbox', array('milestone' => $milestone)); ?>
                                    <?php $milestone_cc++; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                        <?php if ($milestone_cc == 0): ?>
                            <div class="faded_out"><?php echo __('There are no upcoming milestones for any of this client\'s associated projects'); ?></div>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="content faded_out"><?php echo __('There are no projects assigned to this client'); ?>.</p>
                    <?php endif; ?>
                </td>
            <td class="client_dashboard_users padded">
                <div class="header">
                    <?php echo __('Members of %client', array('%client' => $client->getName())); ?>
                </div>
                <?php if ($client->getNumberOfMembers() > 0): ?>
                    <ul class="client_users">
                    <?php foreach ($client->getMembers() as $user): ?>
                        <li><?php echo include_component('main/userdropdown', array('user' => $user)); ?></li>
                    <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="content faded_out"><?php echo __('This client has no members'); ?>.</p>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</div>
<?php else: ?>
<div class="rounded_box red borderless issue_info aligned">
    <?php echo __('This client does not exist'); ?>
</div>
<?php endif; ?>
