<?php
    $base_url = \thebuggenie\core\framework\Context::getModule('vcs_integration')->getSetting('browser_url_' . $projectId);

    if (mb_strstr($commit->getRevision(), ':'))
    {
        $revision = explode(':', $commit->getRevision());
        $revision = $revision[1];
    }
    else
    {
        $revision = $commit->getRevision();
    }

    if (mb_strstr($commit->getPreviousRevision(), ':'))
    {
        $oldrevision = explode(':', $commit->getPreviousRevision());
        $oldrevision = $oldrevision[1];
    }
    else
    {
        $oldrevision = $commit->getPreviousRevision();
    }

    $misc_data = explode('|', $commit->getMiscData());

    $branchname = null;

    foreach ($misc_data as $data)
    {
        if (mb_strstr($data, 'branch'))
        {
            $branch = explode(':', $data);
            if (count($branch) == 2)
            {
                $branchname = $branch[1];
            }
        }
    }

    $link_base = \thebuggenie\core\framework\Context::getModule('vcs_integration')->getSetting('commit_url_' . $projectId);

    if ($branchname !== null)
    {
        $link_base = str_replace('%branch', $branchname, $link_base);
    }

    $misc_data_array = $commit->getMiscDataArray();

    if (array_key_exists('gitlab_repos_ns', $misc_data_array))
    {
        $reposname = $misc_data_array['gitlab_repos_ns'];
        $base_url = rtrim($base_url, '/').'/'.$reposname;
    }

    $link_rev = $base_url.str_replace('%revno', $revision, $link_base);
    $link_old = $base_url.str_replace('%revno', $oldrevision, $link_base);


?>
<div class="comment commit <?php if ($branchname !== null): ?>branch_<?php echo $branchname; ?><?php endif; ?>" id="commit_<?php echo $commit->getID(); ?>">
    <div style="position: relative; overflow: visible; padding: 5px;" id="commit_view_<?php echo $commit->getID(); ?>" class="comment_main">
        <div id="commit_<?php echo $commit->getID(); ?>_header" class="commentheader">
            <a href="<?php echo $link_rev; ?>" class="comment_hash" target="_blank"><?php if (!is_numeric($commit->getRevision())): echo mb_substr($commit->getRevision(), 0, 7); else: echo $commit->getRevision(); endif; ?></a>
            <div class="commenttitle">
                <div class="commit_repos_branch">
                    <?php if ($reposname !== null): ?><span class="commitrepos"><?php echo $reposname; ?>/</span> <?php endif; ?>
                    <?php if ($branchname !== null): ?><span class="commitbranch"><?php echo $branchname; ?></span> <?php endif; ?>
                </div>
                <div class="commitrev"><?php echo __('Revision %rev', array('%rev' => '<a href="'.$link_rev.'" target="_blank">'.$commit->getRevision().'</a>')); ?></div>
                <div class="commitauthor"><?php echo __('By %user', array('%user' => get_component_html('main/userdropdown', array('user' => $commit->getAuthor(), 'size' => 'large')))); ?></div>
            </div>
            <div class="commentdate" id="commit_<?php echo $commit->getID(); ?>_date"><?php echo tbg_formattime($commit->getDate(), 12); ?> - <?php echo __('Preceeded by %prev', array('%prev' => '<a href="'.$link_old.'" target="_blank">'.$commit->getPreviousRevision().'</a>'))?></div>
        </div>

        <div class="commentbody article commit_main" id="commit_<?php echo $commit->getID(); ?>_body">
            <pre><?php echo trim($commit->getLog()); ?></pre>
            <div class="commit_expander" style="<?php if (isset($expanded) && $expanded == true) echo 'display: none;'; ?>">
                <a href="javascript:void(0);" style="padding-right: 5px;" id="checkin_expand_<?php echo $commit->getID(); ?>" onclick="$('checkin_details_<?php echo $commit->getID(); ?>').show(); $('checkin_expand_<?php echo $commit->getID(); ?>').hide(); $('checkin_collapse_<?php echo $commit->getID(); ?>').show();"><?php echo image_tag('expand.png'); ?> <?php echo __("Show more details"); ?></a>
                <a href="javascript:void(0);" style="display: none; padding-right: 5px;" id="checkin_collapse_<?php echo $commit->getID(); ?>" onclick="$('checkin_details_<?php echo $commit->getID(); ?>').hide(); $('checkin_expand_<?php echo $commit->getID(); ?>').show(); $('checkin_collapse_<?php echo $commit->getID(); ?>').hide();"><?php echo image_tag('collapse.png'); ?> <?php echo __("Hide details"); ?></a>
            </div>

            <div id="checkin_details_<?php echo $commit->getID(); ?>" style="<?php if (!isset($expanded) || $expanded == false) echo 'display: none;'; ?>">
                <?php
                if (! array_key_exists('gitlab_repos_ns', $misc_data_array))
                {
                ?>
                    <div class="commit_left">
                        <div class="commit_header"><?php echo __('Changed files'); ?></div>
                        <table border=0 cellpadding=0 cellspacing=0 style="width: 100%;">
                            <?php

                            if (count($commit->getFiles()) == 0)
                            {
                                echo '<span class="faded_out">'.__('No files have been affected by this commit').'</span>';
                            }
                            else
                            {
                                foreach ($commit->getFiles() as $file)
                                {
                                    echo '<tr>';

                                    $action = $file->getAction();
                                    if ($action == 'M'): $action = 'U'; endif;

                                    echo '<td class="imgtd">' . image_tag('icon_action_' . $action . '.png', array(), false, 'vcs_integration') . '</td>';

                                    $link_file = str_replace('%revno', $revision, \thebuggenie\core\framework\Context::getModule('vcs_integration')->getSetting('log_url_' . $projectId));
                                    $link_file = str_replace('%oldrev', $oldrevision, $link_file);

                                    if ($branchname !== null)
                                    {
                                        $link_file = str_replace('%branch', $branchname, $link_file);
                                    }

                                    $link_file = $base_url.str_replace('%file', $file->getFile(), $link_file);

                                    $link_diff = str_replace('%revno', $revision, \thebuggenie\core\framework\Context::getModule('vcs_integration')->getSetting('diff_url_' . $projectId));
                                    $link_diff = str_replace('%oldrev', $oldrevision, $link_diff);

                                    if ($branchname !== null)
                                    {
                                        $link_diff = str_replace('%branch', $branchname, $link_diff);
                                    }

                                    $link_diff = $base_url.str_replace('%file', $file->getFile(), $link_diff);

                                    $link_view = str_replace('%revno', $revision, \thebuggenie\core\framework\Context::getModule('vcs_integration')->getSetting('blob_url_' . $projectId));
                                    $link_view = str_replace('%oldrev', $oldrevision, $link_view);

                                    if ($branchname !== null)
                                    {
                                        $link_view = str_replace('%branch', $branchname, $link_view);
                                    }

                                    $link_view = $base_url.str_replace('%file', $file->getFile(), $link_view);

                                    echo '<td><a href="' . $link_file . '" target="_new"><b>' . $file->getFile() . '</b></a></td>';
                                    if ($action == "U" || $action == "M")
                                    {
                                        if (mb_substr($file->getFile(), -1) == '/' || mb_substr($file->getFile(), -1) == '\\')
                                        {
                                            echo '<td style="width: 75px;" class="faded_out">' . __('directory') . '</td>';
                                        }
                                        else
                                        {
                                            echo '<td style="width: 75px;"><a href="' . $link_diff . '" target="_new"><b>' . __('Diff') . '</b></a></td>';
                                        }
                                    }

                                    if ($action == "D")
                                    {
                                        echo '<td colspan="2" class="faded_out" style="width: 150px;">'.__('deleted').'</td>';
                                    }
                                    elseif ($action == "A")
                                    {
                                        echo '<td class="faded_out" style="width: 75px;">'.__('new file').'</td>';
                                    }
                                    elseif ($action != "D")
                                    {
                                        echo '<td style="width: 75px;"><a href="' . $link_view . '" target="_new"><b>' . __('View') . '</b></a></td>';
                                    }
                                    echo '</tr>';
                                }
                            }
                            ?>
                        </table>
                    </div>
                <?php
                }
                ?>
                <div class="commit_right">
                    <div class="commit_header"><?php echo __('Affected issues'); ?></div>
                    <?php
                    $valid_issues = array();

                    foreach ($commit->getIssues() as $issue)
                    {
                        if ($issue instanceof \thebuggenie\core\entities\Issue && $issue->hasAccess())
                        {
                            $valid_issues[] = $issue;
                        }
                    }

                    if (!count($valid_issues))
                    {
                        echo '<span class="faded_out">'.__('This commit affects no issues').'</span>';
                    }
                    else
                    {
                        $c = 0;
                        echo '<ul>';
                        foreach ($valid_issues as $issue)
                        {
                            echo '<li>'.link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), $issue->getFormattedIssueNo(true, true)).'</li>';
                        }
                        echo '</ul>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
