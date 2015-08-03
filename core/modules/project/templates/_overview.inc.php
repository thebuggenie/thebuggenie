<?php

    if ($tbg_user->hasPageAccess('project_timeline', $project->getID()) || $tbg_user->hasPageAccess('project_allpages', $project->getID()))
    {
        $tbg_response->addFeed(make_url('project_timeline', array('project_key' => $project->getKey(), 'format' => 'rss')), __('"%project_name" project timeline', array('%project_name' => $project->getName())));
    }

?>
<div class="rounded_box <?php if (!($project->isIssuelistVisibleInFrontpageSummary() && count($project->getVisibleIssuetypes()))): ?>invisible <?php else: ?> white borderless <?php endif; ?>project_strip">
    <div style="float: left; font-weight: normal;">
        <?php echo image_tag($project->getSmallIconName(), array('style' => 'float: left; margin: 3px 5px 0 0; width: 16px; height: 16px;'), $project->hasSmallIcon()); ?>
        <b class="project_name"><?php echo link_tag(make_url('project_dashboard', array('project_key' => $project->getKey())), '<span id="project_name_span">'.$project->getName()."</span>"); ?> <?php if ($project->usePrefix()): ?>(<?php echo mb_strtoupper($project->getPrefix()); ?>)<?php endif; ?></b><?php if ($tbg_user->canEditProjectDetails($project)): ?>&nbsp;&nbsp;<span class="faded_out button-group project-config-buttons" style="float: none;"><?php echo javascript_link_tag(__('Quick edit'), array('class' => 'button button-silver project-quick-edit', 'onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'project_config', 'project_id' => $project->getID()))."');")); ?><?php echo link_tag(make_url('project_settings', array('project_key' => $project->getKey())), __('Settings'), array('class' => 'button button-silver project-settings')); ?></span><?php endif; ?><br>
        <?php if ($project->hasHomepage()): ?>
            <a href="<?php echo $project->getHomepage(); ?>" target="_blank"><?php echo __('Go to project website'); ?></a>
        <?php endif; ?>
        <?php if ($project->hasHomepage() && $project->hasDocumentationURL()): ?>
        |
        <?php endif; ?>
        <?php if ($project->hasDocumentationURL()): ?>
            <a href="<?php echo $project->getDocumentationURL(); ?>" target="_blank"><?php echo __('Open documentation'); ?></a>
        <?php endif; ?>
    </div>
    <nav class="button-group" style="position: relative;">
<?php if ($tbg_user->hasPageAccess('project_dashboard', $project->getID()) || $tbg_user->hasPageAccess('project_allpages', $project->getID())) echo link_tag(make_url('project_dashboard', array('project_key' => $project->getKey())), __('Dashboard'), array('class' => 'button button-silver button-dashboard')); ?>
<?php if ($tbg_user->canSearchForIssues() && ($tbg_user->hasPageAccess('project_issues', $project->getID()) || $tbg_user->hasPageAccess('project_allpages', $project->getID()))): ?>
    <?php echo link_tag(make_url('project_open_issues', array('project_key' => $project->getKey())), __('Issues'), array('class' => 'button button-silver button-issues righthugging')); ?>
    <a class="button button-silver lefthugging dropper" onclick="setTimeout(function() { $('goto_issue_<?php echo $project->getID(); ?>_input').focus(); }, 100);" style="font-size: 0.9em;" href="javascript:void(0);">&#x25BC;</a>
    <ul id="goto_issue_<?php echo $project->getID(); ?>" class="more_actions_dropdown popup_box" style="position: absolute; margin-top: 25px; display: none;">
        <li class="finduser_container">
            <label for="goto_issue_<?php echo $project->getID(); ?>_input"><?php echo __('Jump to an issue'); ?>:</label><br>
            <form action="<?php echo make_url('project_quicksearch', array('project_key' => $project->getKey())); ?>" method="post">
                <input type="hidden" name="fs[text][o]" value="=">
                <input type="search" name="fs[text][v]" id="goto_issue_<?php echo $project->getID(); ?>_input" value="" placeholder="<?php echo __('Enter an issue number to jump to an issue'); ?>">&nbsp;<input type="submit" value="<?php echo __('Go to'); ?>">
            </form>
        </li>
    </ul>
<?php endif; ?>
<?php \thebuggenie\core\framework\Event::createNew('core', 'project_overview_item_links', $project)->trigger(); ?>
<?php if (!$project->isLocked() && $tbg_user->canReportIssues($project)): ?>
    <?php echo javascript_link_tag(__('Report an issue'), array('onclick' => "TBG.Issues.Add('" . make_url('get_partial_for_backdrop', array('key' => 'reportissue', 'project_id' => $project->getId())) . "', this);", 'class' => 'button button-green button-report-issue righthugging')); ?>
    <a class="dropper button button-green last lefthugging reportissue_dropdown_button" style="font-size: 0.9em;" href="javascript:void(0);">&#x25BC;</a>
    <ul id="create_issue_<?php echo $project->getID(); ?>" class="more_actions_dropdown popup_box" style="position: absolute; right: 0; margin-top: 25px; display: none;">
        <?php foreach ($project->getIssuetypeScheme()->getReportableIssuetypes() as $issuetype): ?>
            <li><?php echo javascript_link_tag(image_tag($issuetype->getIcon() . '_tiny.png' ) . __($issuetype->getName()), array('onclick' => "TBG.Issues.Add('" . make_url('get_partial_for_backdrop', array('key' => 'reportissue', 'project_id' => $project->getId(), 'issuetype' => $issuetype->getKey())) . "', this);")); ?></li>
        <?php endforeach;?>
    </ul>
<?php endif; ?>
    </nav>
    <?php if ($project->hasChildren()): ?>
    <div class="subprojects_list">
        <?php echo __('Subprojects'); ?>
        <?php foreach ($project->getChildren() as $child): ?>
            <span class="subproject_link"><?php echo link_tag(make_url('project_dashboard', array('project_key' => $child->getKey())), $child->getName()); ?></span>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <?php if ($project->isIssuetypesVisibleInFrontpageSummary() && count($project->getVisibleIssuetypes())): ?>
        <table style="width: 100%; margin-top: 5px;" cellpadding=0 cellspacing=0>
        <?php foreach ($project->getVisibleIssuetypes() as $issuetype): ?>
            <tr>
                <td style="padding-bottom: 2px; width: 200px; padding-right: 10px;"><b><?php echo $issuetype->getName(); ?>:</b></td>
                <td style="padding-bottom: 2px; width: auto; position: relative;">
                    <div style="color: #222; position: absolute; right: 20px; text-align: right;"><?php echo __('%closed closed of %issues reported', array('%closed' => '<b>'.$project->countClosedIssuesByType($issuetype->getID()).'</b>', '%issues' => '<b>'.$project->countIssuesByType($issuetype->getID()).'</b>')); ?></div>
                    <?php include_component('main/percentbar', array('percent' => $project->getClosedPercentageByType($issuetype->getID()), 'height' => 20)); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </table>
    <?php elseif ($project->isIssuelistVisibleInFrontpageSummary() && count($project->getVisibleIssuetypes())): ?>
        <div class="search_results" style="clear: both;">
            <?php include_component('search/results_normal', array('search_object' => $project->getOpenIssuesSearchForFrontpageSummary(), 'actionable' => false)); ?>
        </div>
    <?php elseif ($project->isMilestonesVisibleInFrontpageSummary() && count($project->getVisibleMilestones())): ?>
        <table style="width: 100%; margin-top: 5px;" cellpadding=0 cellspacing=0>
        <?php foreach ($project->getVisibleMilestones() as $milestone): ?>
            <tr>
                <td style="padding-bottom: 2px; width: 200px; padding-right: 10px;"><b><?php echo $milestone->getName(); ?>:</b></td>
                <td style="padding-bottom: 2px; width: auto; position: relative;">
                    <div style="color: #222; position: absolute; right: 20px; text-align: right;"><?php echo __('%closed closed of %issues assigned', array('%closed' => '<b>'.$project->countClosedIssuesByMilestone($milestone->getID()).'</b>', '%issues' => '<b>'.$project->countIssuesByMilestone($milestone->getID()).'</b>')); ?></div>
                    <?php include_component('main/percentbar', array('percent' => $project->getClosedPercentageByMilestone($milestone->getID()), 'height' => 20)); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </table>
    <?php endif; ?>
    <div style="clear: both;"> </div>
</div>
