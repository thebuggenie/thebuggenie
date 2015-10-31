<?php

    if ($show_results)
    {
        $tbg_response->setTitle($searchtitle);
    }
    else
    {
        $tbg_response->setTitle((\thebuggenie\core\framework\Context::isProjectContext()) ? __('Find issues for %project_name', array('%project_name' => \thebuggenie\core\framework\Context::getCurrentProject()->getName())) : __('Find issues'));
    }
    if (\thebuggenie\core\framework\Context::isProjectContext())
    {
        $tbg_response->addBreadcrumb(__('Issues'), make_url('project_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())));
        $tbg_response->addFeed(make_url('project_open_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'format' => 'rss')), __('Open issues for %project_name', array('%project_name' => \thebuggenie\core\framework\Context::getCurrentProject()->getName())));
        $tbg_response->addFeed(make_url('project_allopen_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'format' => 'rss')), __('Open issues for %project_name (including subprojects)', array('%project_name' => \thebuggenie\core\framework\Context::getCurrentProject()->getName())));
        $tbg_response->addFeed(make_url('project_closed_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'format' => 'rss')), __('Closed issues for %project_name', array('%project_name' => \thebuggenie\core\framework\Context::getCurrentProject()->getName())));
        $tbg_response->addFeed(make_url('project_allclosed_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'format' => 'rss')), __('Closed issues for %project_name (including subprojects)', array('%project_name' => \thebuggenie\core\framework\Context::getCurrentProject()->getName())));
        $tbg_response->addFeed(make_url('project_wishlist_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'format' => 'rss')), __('Wishlist for %project_name', array('%project_name' => \thebuggenie\core\framework\Context::getCurrentProject()->getName())));
        $tbg_response->addFeed(make_url('project_milestone_todo_list', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'format' => 'rss')), __('Milestone todo-list for %project_name', array('%project_name' => \thebuggenie\core\framework\Context::getCurrentProject()->getName())));
        $tbg_response->addFeed(make_url('project_month_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'format' => 'rss')), __('Issues reported for %project_name this month', array('%project_name' => \thebuggenie\core\framework\Context::getCurrentProject()->getName())));
        $tbg_response->addFeed(make_url('project_last_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'format' => 'rss', 'units' => 30, 'time_unit' => 'days')), __('Issues reported for %project_name last 30 days', array('%project_name' => \thebuggenie\core\framework\Context::getCurrentProject()->getName())));
        if (!\thebuggenie\core\entities\User::isThisGuest())
        {
            $tbg_response->addFeed(make_url('project_my_reported_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'format' => 'rss')), __('Issues reported by me') . ' ('.\thebuggenie\core\framework\Context::getCurrentProject()->getName().')');
            $tbg_response->addFeed(make_url('project_my_assigned_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'format' => 'rss')), __('Open issues assigned to me') . ' ('.\thebuggenie\core\framework\Context::getCurrentProject()->getName().')');
            $tbg_response->addFeed(make_url('project_my_teams_assigned_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'format' => 'rss')), __('Open issues assigned to my teams') . ' ('.\thebuggenie\core\framework\Context::getCurrentProject()->getName().')');
        }
    }
    else
    {
        $tbg_response->addBreadcrumb(__('Issues'), make_url('search'));
        if (!\thebuggenie\core\entities\User::isThisGuest())
        {
            $tbg_response->addFeed(make_url('my_reported_issues', array('format' => 'rss')), __('Issues reported by me'));
            $tbg_response->addFeed(make_url('my_assigned_issues', array('format' => 'rss')), __('Open issues assigned to you'));
            $tbg_response->addFeed(make_url('my_teams_assigned_issues', array('format' => 'rss')), __('Open issues assigned to your teams'));
        }
    }

?>
<table style="width: 100%; height: 100;" cellpadding="0" cellspacing="0">
    <tr>
        <?php include_component('search/sidebar', array('hide' => ($show_results && $resultcount))); ?>
        <td id="find_issues">
            <?php if ($search_error !== null): ?>
                <div class="redbox" style="margin: 0; vertical-align: middle;" id="search_error">
                    <div class="header"><?php echo $search_error; ?></div>
                </div>
            <?php endif; ?>
            <?php if ($search_message == 'saved_search'): ?>
                <?php include_component('main/hideableInfoBoxModal', array('template' => 'search/infobox_saved_search_saved', 'title' => __('Search details have been saved'), 'button_label' => __('Got it!'))); ?>
            <?php elseif ($search_message !== null): ?>
                <div class="greenbox" style="margin: 0; vertical-align: middle;" id="search_message">
                    <div class="header"><?php echo $search_message; ?></div>
                </div>
            <?php endif; ?>
            <div class="results_header">
                <span id="findissues_search_title" style="<?php if (!$searchtitle) echo 'display: none'; ?>"><?php echo $searchtitle; ?></span>
                <span id="findissues_search_generictitle" style="<?php if ($searchtitle) echo 'display: none'; ?>"><?php echo __("Find issues"); ?></span>
                &nbsp;&nbsp;<span id="findissues_num_results" class="faded_out" style="<?php if (!$show_results) echo 'display: none;'; ?>"><?php echo __('%number_of issue(s)', array('%number_of' => '<span id="findissues_num_results_span">-</span>')); ?></span>
            </div>
            <?php include_component('search/searchbuilder', compact('search_object', 'show_results')); ?>
            <div id="search_results_container">
                <div id="search_results_loading_indicator" style="display: none;"><?php echo image_tag('spinning_30.gif'); ?></div>
                <div id="search_results" class="search_results">
                </div>
            </div>
            <script>
                var TBG;
                require(['domReady', 'thebuggenie/tbg'], function (domReady, tbgjs) {
                    domReady(function () {
                        TBG = tbgjs;
                        TBG.Search.initializeFilters();
                        <?php if ($tbg_user->isKeyboardNavigationEnabled()): ?>
                            TBG.Search.initializeKeyboardNavigation();
                        <?php endif; ?>
                        <?php if ($show_results): ?>
                            setTimeout(function() { TBG.Search.liveUpdate(true); }, 250);
                        <?php else: ?>
                            TBG.Search.updateSavedSearchCounts();
                        <?php endif; ?>

                        var hash = window.location.hash;

                        if (hash != undefined && hash.indexOf('edit_modal') == 1) {
                            $('saved_search_details').toggle('block');
                        }
                    });
                });
            </script>
        </td>
    </tr>
</table>
