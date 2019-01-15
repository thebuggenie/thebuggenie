<?php

    use thebuggenie\core\framework;

    /**
     * @var \thebuggenie\core\entities\SavedSearch[][] $saved_searches
     * @var framework\Response $tbg_response
     * @var \thebuggenie\core\entities\User $tbg_user
     */

    $saved_searches = \thebuggenie\core\entities\tables\SavedSearches::getTable()->getAllSavedSearchesByUserIDAndPossiblyProjectID(framework\Context::getUser()->getID(), framework\Context::getCurrentProject()->getID());
    $recent_issues = \thebuggenie\core\entities\tables\Issues::getSessionIssues();

?>
<nav class="header_menu project_context" id="project_menu">
    <ul>
        <?php $page = (in_array($tbg_response->getPage(), array('project_dashboard', 'project_scrum_sprint_details', 'project_timeline', 'project_team', 'project_roadmap', 'project_statistics', 'vcs_commitspage'))) ? $tbg_response->getPage() : 'project_dashboard'; ?>
        <li class="with-dropdown <?php if (in_array($tbg_response->getPage(), array('project_dashboard', 'project_scrum_sprint_details', 'project_timeline', 'project_team', 'project_roadmap', 'project_statistics', 'vcs_commitspage'))): ?>selected<?php endif; ?>">
            <?= link_tag(make_url($page, array('project_key' => framework\Context::getCurrentProject()->getKey())), fa_image_tag('columns') . tbg_get_pagename($tbg_response->getPage()) . fa_image_tag('caret-down', ['class' => 'dropdown-indicator']), ['class' => 'dropper']); ?>
            <ul id="project_information_menu" class="tab_menu_dropdown popup_box">
                <?php include_component('project/projectinfolinks', array('submenu' => true)); ?>
            </ul>
        </li>
        <?php if ($tbg_user->canSearchForIssues()): ?>
            <li class="with-dropdown <?php if (in_array($tbg_response->getPage(), array('project_issues', 'viewissue'))): ?>selected<?php endif; ?>">
                <?= link_tag(make_url('project_issues', array('project_key' => framework\Context::getCurrentProject()->getKey())), fa_image_tag('file-alt') . __('Issues') . fa_image_tag('caret-down', ['class' => 'dropdown-indicator']), ['class' => 'dropper']); ?>
                <div id="issues_menu" class="tab_menu_dropdown popup_box two-columns">
                    <ul>
                        <li class="header"><?= __('Predefined searches'); ?></li>
                        <li><?= link_tag(make_url('project_open_issues', array('project_key' => framework\Context::getCurrentProject()->getKey())), fa_image_tag('search') . __('Open issues for this project')); ?></li>
                        <li><?= link_tag(make_url('project_closed_issues', array('project_key' => framework\Context::getCurrentProject()->getKey())), fa_image_tag('search') . __('Closed issues for this project')); ?></li>
                        <li><?= link_tag(make_url('project_wishlist_issues', array('project_key' => framework\Context::getCurrentProject()->getKey())), fa_image_tag('search') . __('Wishlist for this project')); ?></li>
                        <li><?= link_tag(make_url('project_milestone_todo_list', array('project_key' => framework\Context::getCurrentProject()->getKey())), fa_image_tag('search') . __('Milestone todo-list for this project')); ?></li>
                        <li><?= link_tag(make_url('project_most_voted_issues', array('project_key' => framework\Context::getCurrentProject()->getKey())), fa_image_tag('search') . __('Most voted for issues')); ?></li>
                        <li><?= link_tag(make_url('project_month_issues', array('project_key' => framework\Context::getCurrentProject()->getKey())), fa_image_tag('search') . __('Issues reported this month')); ?></li>
                        <li><?= link_tag(make_url('project_last_issues', array('project_key' => framework\Context::getCurrentProject()->getKey(), 'units' => 30, 'time_unit' => 'days')), fa_image_tag('search') . __('Issues reported last 30 days')); ?></li>
                        <li class="header"><?= __('Saved searches'); ?></li>
                        <?php if (count($saved_searches['user']) + count($saved_searches['public'])): ?>
                            <?php if (!$tbg_user->isGuest()): ?>
                                <?php foreach ($saved_searches['user'] as $savedsearch): ?>
                                    <li><?= link_tag(make_url('project_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'saved_search' => $savedsearch->getID(), 'search' => true)), fa_image_tag('user', ['title' => __('This is a saved search only visible to you')], 'far') . __($savedsearch->getName())); ?></li>
                                <?php endforeach; ?>
                                <?php if (count($saved_searches['user']) && count($saved_searches['public'])): ?>
                                    <li class="separator"></li>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php foreach ($saved_searches['public'] as $savedsearch): ?>
                                <li><?= link_tag(make_url('project_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'saved_search' => $savedsearch->getID(), 'search' => true)), fa_image_tag('search') . __($savedsearch->getName())); ?></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="disabled"><?= __('No saved searches for this project'); ?></li>
                        <?php endif; ?>
                    </ul>
                    <ul>
                        <li class="header"><?= __('Recently watched issues'); ?></li>
                        <?php foreach ($recent_issues as $issue): ?>
                            <?php include_component('search/sessionissue', ['issue' => $issue]); ?>
                        <?php endforeach; ?>
                        <?php if (!count($recent_issues)): ?>
                            <li class="disabled" href="javascript:void(0);"><?= __('No recent issues'); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </li>
        <?php endif; ?>
        <?php framework\Event::createNew('core', 'templates/headermainmenu::projectmenulinks', framework\Context::getCurrentProject())->trigger(); ?>
    </ul>
    <?php if (!framework\Context::getCurrentProject()->isArchived() && !framework\Context::getCurrentProject()->isLocked() && $tbg_user->canReportIssues(framework\Context::getCurrentProject())): ?>
        <div class="reportissue_button_container">
            <?= javascript_link_tag(fa_image_tag('plus') . '<span>'.__('Report an issue').'</span>', array('onclick' => "TBG.Issues.Add('" . make_url('get_partial_for_backdrop', array('key' => 'reportissue', 'project_id' => framework\Context::getCurrentProject()->getId())) . "');", 'class' => 'button button-lightblue', 'id' => 'reportissue_button')); ?>
        </div>
        <script type="text/javascript">
            var TBG;

            require(['domReady', 'thebuggenie/tbg', 'jquery'], function (domReady, tbgjs, jQuery) {
                domReady(function () {
                    TBG = tbgjs;
                    var hash = window.location.hash;

                    if (hash != undefined && hash.indexOf('report_an_issue') == 1) {
                        jQuery('#reportissue_button').trigger('click');
                    }
                });
            });
        </script>
    <?php endif; ?>
</nav>
