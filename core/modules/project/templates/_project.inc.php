<?php

    if ($tbg_user->hasPageAccess('project_timeline', $project->getID()) || $tbg_user->hasPageAccess('project_allpages', $project->getID()))
    {
        $tbg_response->addFeed(make_url('project_timeline', array('project_key' => $project->getKey(), 'format' => 'rss')), __('"%project_name" project timeline', array('%project_name' => $project->getName())));
    }

?>
<div class="project_strip">
    <?= image_tag($project->getLargeIconName(), array('class' => 'icon-large', 'alt' => '[i]'), $project->hasLargeIcon()); ?>
    <div class="project_information_block">
        <div class="project_information_container">
            <span class="project_name">
                <?= link_tag(make_url('project_dashboard', array('project_key' => $project->getKey())), '<span class="project_name_span">'.$project->getName()."</span>"); ?><?php if ($project->usePrefix()) echo '<span class="project_prefix_span">'.mb_strtoupper($project->getPrefix()).'</span>'; ?>
            </span>
            <div class="project_description">
                <?php
                    $project_description_uid = trim($project->getDescription());
                    if (!empty($project_description_uid)) {
                        echo tbg_parse_text($project_description_uid);
                    } else {
                        echo '<span style="opacity:0.5;">'.__('No project description')."</span>\n";
                    }
                ?>
            </div>
        </div>
        <?php if ($project->hasChildren()): ?>
            <div class="subprojects_list">
                <?= __('Subprojects'); ?>
                <?php foreach ($project->getChildren() as $child): ?>
                    <span class="subproject_link"><?= link_tag(make_url('project_dashboard', array('project_key' => $child->getKey())), $child->getName()); ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <nav class="button-group">
        <?php if ($project->hasHomepage()): ?>
            <a href="<?= $project->getHomepage(); ?>" target="_blank" class="nav-button button-website"><?= fa_image_tag('globe') . __('Website'); ?></a>
        <?php endif; ?>
        <?php if ($project->hasDocumentationURL()): ?>
            <a href="<?= $project->getDocumentationURL(); ?>" target="_blank" class="nav-button button-documentation"><?= fa_image_tag('book') . __('Documentation'); ?></a>
        <?php endif; ?>
        <?php \thebuggenie\core\framework\Event::createNew('core', 'project_overview_item_links', $project)->trigger(); ?>
        <?php if ($tbg_user->canSearchForIssues() && $tbg_user->hasPageAccess('project_issues', $project->getID())): ?>
            <?= link_tag(make_url('project_open_issues', array('project_key' => $project->getKey())), fa_image_tag('file-alt') . '<span>'.__('Issues').'</span>', ['class' => 'nav-button button-issues']); ?>
        <?php endif; ?><?php if (!$project->isLocked() && $tbg_user->canReportIssues($project)): ?>
            <?= javascript_link_tag(fa_image_tag('plus-square') . '<span>'.__('New issue').'</span>', ['onclick' => "TBG.Issues.Add('" . make_url('get_partial_for_backdrop', ['key' => 'reportissue', 'project_id' => $project->getId()]) . "', this);", 'class' => 'nav-button button-report-issue']); ?>
        <?php endif; ?>
    </nav>
</div>
<?php if ($project->isIssuetypesVisibleInFrontpageSummary() && count($project->getVisibleIssuetypes())): ?>
<div class="frontpage-results">
    <table style="width: 100%; margin-top: 5px;" cellpadding=0 cellspacing=0>
        <?php foreach ($project->getVisibleIssuetypes() as $issuetype): ?>
            <tr>
                <td style="padding-bottom: 2px; width: 200px; padding-right: 10px;"><b><?= $issuetype->getName(); ?>:</b></td>
                <td style="padding-bottom: 2px; width: auto; position: relative;">
                    <div style="color: #222; position: absolute; right: 20px; text-align: right;"><?= __('%closed closed of %issues reported', array('%closed' => '<b>'.$project->countClosedIssuesByType($issuetype->getID()).'</b>', '%issues' => '<b>'.$project->countIssuesByType($issuetype->getID()).'</b>')); ?></div>
                    <?php include_component('main/percentbar', array('percent' => $project->getClosedPercentageByType($issuetype->getID()), 'height' => 20)); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php elseif ($project->isIssuelistVisibleInFrontpageSummary() && count($project->getVisibleIssuetypes())): ?>
    <div class="frontpage-results">
        <div class="search_results" style="clear: both;">
            <?php $current_spent_time = -1; ?>
            <?php include_component(
                'search/results_normal',
                array(
                    'search_object' => $project->getOpenIssuesSearchForFrontpageSummary(),
                    'actionable'    => false,
                    'show_summary'  => false
                )); ?>
        </div>
    </div>
<?php elseif ($project->isMilestonesVisibleInFrontpageSummary() && count($project->getVisibleMilestones())): ?>
    <div class="frontpage-results">
        <table style="width: 100%; margin-top: 5px;" cellpadding=0 cellspacing=0>
            <?php foreach ($project->getVisibleMilestones() as $milestone): ?>
                <tr>
                    <td style="padding-bottom: 2px; width: 200px; padding-right: 10px;"><b><?= $milestone->getName(); ?>:</b></td>
                    <td style="padding-bottom: 2px; width: auto; position: relative;">
                        <div style="color: #222; position: absolute; right: 20px; text-align: right;"><?= __('%closed closed of %issues assigned', array('%closed' => '<b>'.$project->countClosedIssuesByMilestone($milestone->getID()).'</b>', '%issues' => '<b>'.$project->countIssuesByMilestone($milestone->getID()).'</b>')); ?></div>
                        <?php include_component('main/percentbar', array('percent' => $project->getClosedPercentageByMilestone($milestone->getID()), 'height' => 20)); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php endif; ?>

