<td class="saved_searches side_bar<?php if ($hide): ?> collapsed<?php endif; ?>" id="search_sidebar" data-project-id="<?php echo (\thebuggenie\core\framework\Context::isProjectContext()) ? \thebuggenie\core\framework\Context::getCurrentProject()->getId() : ''; ?>">
    <div class="collapser_link" onclick="$('search_sidebar').toggleClassName('collapsed');">
        <a href="javascript:void(0);">
            <?php echo image_tag('sidebar_collapse.png', array('class' => 'collapser')); ?>
            <?php echo image_tag('sidebar_expand.png', array('class' => 'expander')); ?>
        </a>
    </div>
    <div class="container_divs_wrapper">
        <div class="container_div">
            <div class="header"><?php echo __('Predefined searches'); ?></div>
            <ul class="simple_list content" style="font-size: 1em;">
                <?php if (\thebuggenie\core\framework\Context::isProjectContext()): ?>
                    <li class="savedsearch-item" data-search-id="predefined_<?php echo \thebuggenie\core\entities\SavedSearch::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES; ?>" style="clear: both;">
                        <?php echo link_tag(make_url('project_open_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'))); ?>
                        <?php echo link_tag(make_url('project_open_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())), __('Open issues for this project')); ?><span class="num_results_badge">-</span>
                    </li>
                    <li class="savedsearch-item" data-search-id="predefined_<?php echo \thebuggenie\core\entities\SavedSearch::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES_INCLUDING_SUBPROJECTS; ?>" style="clear: both;">
                        <?php echo link_tag(make_url('project_allopen_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'))); ?>
                        <?php echo link_tag(make_url('project_allopen_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())), __('Open issues (including subprojects)')); ?><span class="num_results_badge">-</span>
                    </li>
                    <li class="savedsearch-item" data-search-id="predefined_<?php echo \thebuggenie\core\entities\SavedSearch::PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES; ?>" style="clear: both;">
                        <?php echo link_tag(make_url('project_closed_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'))); ?>
                        <?php echo link_tag(make_url('project_closed_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())), __('Closed issues for this project')); ?>
                    </li>
                    <li class="savedsearch-item" data-search-id="predefined_<?php echo \thebuggenie\core\entities\SavedSearch::PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES_INCLUDING_SUBPROJECTS; ?>" style="clear: both;">
                        <?php echo link_tag(make_url('project_allclosed_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'))); ?>
                        <?php echo link_tag(make_url('project_allclosed_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())), __('Closed issues (including subprojects)')); ?>
                    </li>
                    <li class="savedsearch-item" data-search-id="predefined_<?php echo \thebuggenie\core\entities\SavedSearch::PREDEFINED_SEARCH_PROJECT_WISHLIST; ?>" style="clear: both; margin-bottom: 15px;">
                        <?php echo link_tag(make_url('project_wishlist_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'))); ?>
                        <?php echo link_tag(make_url('project_wishlist_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())), __('Project wishlist')); ?><span class="num_results_badge">-</span>
                    </li>
                    <li class="savedsearch-item" data-search-id="predefined_<?php echo \thebuggenie\core\entities\SavedSearch::PREDEFINED_SEARCH_PROJECT_MILESTONE_TODO; ?>">
                        <?php echo link_tag(make_url('project_milestone_todo_list', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'))); ?>
                        <?php echo link_tag(make_url('project_milestone_todo_list', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())), __('Milestone todo-list for this project')); ?>
                    </li>
                    <li class="savedsearch-item" data-search-id="predefined_<?php echo \thebuggenie\core\entities\SavedSearch::PREDEFINED_SEARCH_PROJECT_MOST_VOTED; ?>" style="clear: both; margin-bottom: 15px;">
                        <?php echo link_tag(make_url('project_most_voted_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'))); ?>
                        <?php echo link_tag(make_url('project_most_voted_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())), __('Most voted for issues')); ?>
                    </li>
                    <li class="savedsearch-item" data-search-id="predefined_<?php echo \thebuggenie\core\entities\SavedSearch::PREDEFINED_SEARCH_PROJECT_REPORTED_THIS_MONTH; ?>" style="clear: both;">
                        <?php echo link_tag(make_url('project_month_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'))); ?>
                        <?php echo link_tag(make_url('project_month_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())), __('Issues reported this month')); ?><span class="num_results_badge">-</span>
                    </li>
                    <?php if (!$tbg_user->isGuest()): ?>
                        <li class="savedsearch-item" data-search-id="predefined_<?php echo \thebuggenie\core\entities\SavedSearch::PREDEFINED_SEARCH_MY_REPORTED_ISSUES; ?>" style="clear: both;">
                            <?php echo link_tag(make_url('project_my_reported_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'))); ?>
                            <?php echo link_tag(make_url('project_my_reported_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())), __('Issues reported by me')); ?><span class="num_results_badge">-</span>
                        </li>
                        <li class="savedsearch-item" data-search-id="predefined_<?php echo \thebuggenie\core\entities\SavedSearch::PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES; ?>" style="clear: both;">
                            <?php echo link_tag(make_url('project_my_assigned_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'))); ?>
                            <?php echo link_tag(make_url('project_my_assigned_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())), __('Open issues assigned to me')); ?><span class="num_results_badge">-</span>
                        </li>
                        <li class="savedsearch-item" data-search-id="predefined_<?php echo \thebuggenie\core\entities\SavedSearch::PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES; ?>" style="clear: both;">
                            <?php echo link_tag(make_url('project_my_teams_assigned_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'))); ?>
                            <?php echo link_tag(make_url('project_my_teams_assigned_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())), __('Open issues assigned to my teams')); ?><span class="num_results_badge">-</span>
                        </li>
                    <?php endif; ?>
                <?php elseif (\thebuggenie\core\framework\Context::isProjectContext() || !$tbg_user->isGuest()): ?>
                    <?php if (\thebuggenie\core\framework\Context::isProjectContext()): ?>
                        <li class="savedsearch-item" data-search-id="predefined_<?php echo \thebuggenie\core\entities\SavedSearch::PREDEFINED_SEARCH_PROJECT_REPORTED_THIS_MONTH; ?>" style="clear: both;">
                            <?php echo link_tag(make_url('project_month_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'))); ?>
                            <?php echo link_tag(make_url('project_month_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())), __('Issues reported this month')); ?><span class="num_results_badge">-</span>
                        </li>
                    <?php endif; ?>
                    <?php if (!$tbg_user->isGuest()): ?>
                        <li class="savedsearch-item" data-search-id="predefined_<?php echo \thebuggenie\core\entities\SavedSearch::PREDEFINED_SEARCH_MY_REPORTED_ISSUES; ?>" style="clear: both;">
                            <?php echo link_tag(make_url('my_reported_issues', array('format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'))); ?>
                            <?php echo link_tag(make_url('my_reported_issues'), __('Issues reported by me')); ?><span class="num_results_badge">-</span>
                        </li>
                        <li class="savedsearch-item" data-search-id="predefined_<?php echo \thebuggenie\core\entities\SavedSearch::PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES; ?>" style="clear: both;">
                            <?php echo link_tag(make_url('my_assigned_issues', array('format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'))); ?>
                            <?php echo link_tag(make_url('my_assigned_issues'), __('Open issues assigned to me')); ?><span class="num_results_badge">-</span>
                        </li>
                        <li class="savedsearch-item" data-search-id="predefined_<?php echo \thebuggenie\core\entities\SavedSearch::PREDEFINED_SEARCH_MY_OWNED_OPEN_ISSUES; ?>" style="clear: both;">
                            <?php echo link_tag(make_url('my_owned_issues', array('format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'))); ?>
                            <?php echo link_tag(make_url('my_owned_issues'), __('Open issues owned by me')); ?><span class="num_results_badge">-</span>
                        </li>
                        <?php if ($tbg_user->hasTeams()): ?>
                            <li class="savedsearch-item" data-search-id="predefined_<?php echo \thebuggenie\core\entities\SavedSearch::PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES; ?>" style="clear: both;">
                                <?php echo link_tag(make_url('my_teams_assigned_issues', array('format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'))); ?>
                                <?php echo link_tag(make_url('my_teams_assigned_issues'), __('Open issues assigned to my teams')); ?><span class="num_results_badge">-</span>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </div>
        <div class="container_div">
            <div class="header"><?php echo (\thebuggenie\core\framework\Context::isProjectContext()) ? __('Your saved searches for this project') : __('Your saved searches'); ?></div>
            <ul class="simple_list content my_saved_searches">
                <?php if (count($savedsearches['user']) > 0): ?>
                    <?php foreach ($savedsearches['user'] as $a_savedsearch): ?>
                        <li id="saved_search_<?php echo $a_savedsearch->getID(); ?>_container" class="savedsearch-item" data-search-id="<?php echo $a_savedsearch->getID(); ?>">
                            <?php if (\thebuggenie\core\framework\Context::isProjectContext()): ?>
                                <div style="clear: both;">
                                    <?php echo link_tag(make_url('project_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'saved_search' => $a_savedsearch->getID(), 'search' => true, 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'))); ?>
                                    <?php if (!\thebuggenie\core\framework\Context::getCurrentProject()->isArchived()): ?>
                                        <div class="action_icons">
                                            <?php echo link_tag(make_url('project_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'saved_search' => $a_savedsearch->getID(), 'search' => 0)).'#edit_modal', image_tag('icon_edit.png'), array('title' => __('Edit saved search'))); ?>
                                            <?php echo javascript_link_tag(image_tag('icon_delete.png', array('title' => __('Delete saved search'))), array('onclick' => "$('delete_search_".$a_savedsearch->getID()."').toggle();")); ?>
                                        </div>
                                    <?php endif; ?>
                                    <span class="num_results_badge">-</span>
                                    <?php echo link_tag(make_url('project_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'saved_search' => $a_savedsearch->getID(), 'search' => true)), __($a_savedsearch->getName())); ?>
                                </div>
                                <?php if (!\thebuggenie\core\framework\Context::getCurrentProject()->isArchived()): ?>
                                <div class="rounded_box white shadowed" style="position: absolute; width: 300px; display: none;" id="delete_search_<?php echo $a_savedsearch->getID(); ?>">
                                    <div class="header"><?php echo __('Do you really want to delete this saved search?'); ?></div>
                                    <div class="content">
                                        <?php echo __('This action cannot be reverted. Note: this will not modify any issues affected by this search'); ?>
                                        <div style="text-align: right; margin-top: 10px;">
                                            <?php echo image_tag('spinning_16.gif', array('style' => 'margin-left: 5px; display: none;', 'id' => 'delete_search_'.$a_savedsearch->getID().'_indicator')); ?>
                                            <input type="submit" onclick="TBG.Search.deleteSavedSearch('<?php echo make_url('project_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'saved_search_id' => $a_savedsearch->getID(), 'search' => 0, 'delete_saved_search' => true)); ?>', <?php echo $a_savedsearch->getID(); ?>);" value="<?php echo __('Yes, delete'); ?>" style="font-weight: bold;">
                                            <?php echo __('%yes_delete or %cancel', array('%yes_delete' => '', '%cancel' => javascript_link_tag(__('cancel'), array('onclick' => "$('delete_search_".$a_savedsearch->getID()."').toggle();")))); ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if ($a_savedsearch->getDescription() != ''): ?>
                                    <div style="clear: both; padding: 0 0 10px 26px; font-style: italic; font-size: 0.9em;"><?php echo $a_savedsearch->getDescription(); ?></div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div style="clear: both;">
                                    <?php echo link_tag(make_url('search', array('saved_search' => $a_savedsearch->getID(), 'search' => true, 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'))); ?>
                                    <div class="action_icons">
                                        <?php echo link_tag(make_url('search', array('saved_search' => $a_savedsearch->getID(), 'search' => 0)).'#edit_modal', image_tag('icon_edit.png'), array('title' => __('Edit saved search'))); ?>
                                        <?php echo javascript_link_tag(image_tag('icon_delete.png', array('title' => __('Delete saved search'))), array('onclick' => "$('delete_search_".$a_savedsearch->getID()."').toggle();")); ?>
                                    </div>
                                    <span class="num_results_badge">-</span>
                                    <?php echo link_tag(make_url('search', array('saved_search' => $a_savedsearch->getID(), 'search' => true)), __($a_savedsearch->getName())); ?>
                                </div>
                                <div class="rounded_box white shadowed" style="position: absolute; width: 300px; display: none;" id="delete_search_<?php echo $a_savedsearch->getID(); ?>">
                                    <div class="header"><?php echo __('Do you really want to delete this saved search?'); ?></div>
                                    <div class="content">
                                        <?php echo __('This action cannot be reverted. Note: this will not modify any issues affected by this search'); ?>
                                        <div style="text-align: right; margin-top: 10px;">
                                            <?php echo image_tag('spinning_16.gif', array('style' => 'margin-left: 5px; display: none;', 'id' => 'delete_search_'.$a_savedsearch->getID().'_indicator')); ?>
                                            <input type="submit" onclick="TBG.Search.deleteSavedSearch('<?php echo make_url('search', array('saved_search_id' => $a_savedsearch->getID(), 'search' => 0, 'delete_saved_search' => true)); ?>', <?php echo $a_savedsearch->getID(); ?>);" value="<?php echo __('Yes, delete'); ?>" style="font-weight: bold;">
                                            <?php echo __('%yes_delete or %cancel', array('%yes_delete' => '', '%cancel' => javascript_link_tag(__('cancel'), array('onclick' => "$('delete_search_".$a_savedsearch->getID()."').toggle();")))); ?>
                                        </div>
                                    </div>
                                </div>
                                <?php if ($a_savedsearch->getDescription() != ''): ?>
                                    <div style="clear: both; padding: 0 0 10px 3px;"><?php echo $a_savedsearch->getDescription(); ?></div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="no_items" style="font-size: 1em;" id="no_user_saved_searches"><?php echo (\thebuggenie\core\framework\Context::isProjectContext()) ? __("You don't have any saved searches for this project") : __("You don't have any saved searches"); ?></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="container_div">
            <div class="header"><?php echo (\thebuggenie\core\framework\Context::isProjectContext()) ? __('Public saved searches for this project') : __('Public saved searches'); ?></div>
            <ul class="simple_list content my_saved_searches">
                <?php if (count($savedsearches['public']) > 0): ?>
                    <?php foreach ($savedsearches['public'] as $a_savedsearch): ?>
                        <li id="saved_search_<?php echo $a_savedsearch->getID(); ?>_container" class="savedsearch-item" data-search-id="<?php echo $a_savedsearch->getID(); ?>">
                            <div style="clear: both;">
                                <?php if (\thebuggenie\core\framework\Context::isProjectContext()): ?>
                                    <?php echo link_tag(make_url('project_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'saved_search' => $a_savedsearch->getID(), 'search' => true, 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'))); ?>
                                    <?php if ($tbg_user->canCreatePublicSearches()): ?>
                                        <div class="action_icons">
                                            <?php echo link_tag(make_url('project_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'saved_search' => $a_savedsearch->getID(), 'search' => 0)).'#edit_modal', image_tag('icon_edit.png'), array('title' => __('Edit saved search'))); ?>
                                            <?php echo javascript_link_tag(image_tag('icon_delete.png', array('title' => __('Delete saved search'))), array('onclick' => "$('delete_search_".$a_savedsearch->getID()."').toggle();")); ?>
                                        </div>
                                    <?php endif; ?>
                                    <span class="num_results_badge">-</span>
                                    <?php echo link_tag(make_url('project_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'saved_search' => $a_savedsearch->getID(), 'search' => true)), __($a_savedsearch->getName())); ?>
                                <?php else: ?>
                                    <?php echo link_tag(make_url('search', array('saved_search' => $a_savedsearch->getID(), 'search' => true, 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'))); ?>
                                    <?php if ($tbg_user->canCreatePublicSearches()): ?>
                                        <div class="action_icons">
                                            <?php echo javascript_link_tag(image_tag('icon_delete.png', array('title' => __('Delete saved search'))), array('onclick' => "$('delete_search_".$a_savedsearch->getID()."').toggle();")); ?>
                                            <?php echo link_tag(make_url('search', array('saved_search' => $a_savedsearch->getID(), 'search' => 0)).'#edit_modal', image_tag('icon_edit.png'), array('title' => __('Edit saved search'))); ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php echo link_tag(make_url('search', array('saved_search' => $a_savedsearch->getID(), 'search' => true)), __($a_savedsearch->getName())); ?>
                                <?php endif; ?>
                            </div>
                            <div class="rounded_box white shadowed" style="position: absolute; width: 300px; display: none;" id="delete_search_<?php echo $a_savedsearch->getID(); ?>">
                                <div class="header"><?php echo __('Do you really want to delete this saved search?'); ?></div>
                                <div class="content">
                                    <?php echo __('This action cannot be reverted. Note: this will not modify any issues affected by this search'); ?>
                                    <div style="text-align: right; margin-top: 10px;">
                                        <?php echo image_tag('spinning_16.gif', array('style' => 'margin-left: 5px; display: none;', 'id' => 'delete_search_'.$a_savedsearch->getID().'_indicator')); ?>
                                        <?php if (\thebuggenie\core\framework\Context::isProjectContext()): ?>
                                            <input type="submit" onclick="TBG.Search.deleteSavedSearch('<?php echo make_url('project_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'saved_search_id' => $a_savedsearch->getID(), 'search' => 0, 'delete_saved_search' => true)); ?>', <?php echo $a_savedsearch->getID(); ?>);" value="<?php echo __('Yes, delete'); ?>" style="font-weight: bold;">
                                        <?php else: ?>
                                            <input type="submit" onclick="TBG.Search.deleteSavedSearch('<?php echo make_url('search', array('saved_search_id' => $a_savedsearch->getID(), 'search' => 0, 'delete_saved_search' => true)); ?>', <?php echo $a_savedsearch->getID(); ?>);" value="<?php echo __('Yes, delete'); ?>" style="font-weight: bold;">
                                        <?php endif; ?>
                                        <?php echo __('%yes_delete or %cancel', array('%yes_delete' => '', '%cancel' => javascript_link_tag(__('cancel'), array('onclick' => "$('delete_search_".$a_savedsearch->getID()."').toggle();")))); ?>
                                    </div>
                                </div>
                            </div>
                            <?php if ($a_savedsearch->getDescription() != ''): ?>
                                <div style="clear: both; padding: 0 0 10px 3px;"><?php echo $a_savedsearch->getDescription(); ?></div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="no_items" style="font-size: 1em;" id="no_public_saved_searches"><?php echo (\thebuggenie\core\framework\Context::isProjectContext()) ? __("There are no saved searches for this project") : __("There are no public saved searches"); ?></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</td>
