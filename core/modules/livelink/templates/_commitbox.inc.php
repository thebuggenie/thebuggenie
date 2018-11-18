<?php

    /** @var \thebuggenie\core\entities\Branch $branch */
    /** @var \thebuggenie\core\entities\Commit $commit */
    /** @var \thebuggenie\core\entities\Project $project */

    $base_url = \thebuggenie\core\framework\Context::getModule('livelink')->getSetting('browser_url_' . $project->getID());

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

    $link_base = \thebuggenie\core\framework\Context::getModule('livelink')->getSetting('commit_url_' . $project->getID());
    $link_base = str_replace('%branch', $branch->getName(), $link_base);

    $misc_data_array = $commit->getMiscData();

    $link_rev = $base_url.str_replace('%revno', $revision, $link_base);
    $link_old = $base_url.str_replace('%revno', $oldrevision, $link_base);


?>
<div class="comment commit branch_<?php echo $branch->getName(); ?>" id="commit_<?php echo $commit->getID(); ?>">
    <div style="position: relative; overflow: visible; padding: 5px;" id="commit_view_<?php echo $commit->getID(); ?>" class="comment_main">
        <div id="commit_<?php echo $commit->getID(); ?>_header" class="commentheader">
            <a href="<?php echo $link_rev; ?>" class="comment_hash" target="_blank"><?php if (!is_numeric($commit->getRevision())): echo mb_substr($commit->getRevision(), 0, 7); else: echo $commit->getRevision(); endif; ?></a>
            <div class="commenttitle">
                <div class="commit_repos_branch">
                    <span class="commitbranch"><?php echo $branch->getName(); ?></span>
                </div>
                <div class="commitrev"><?php echo __('Revision %rev', array('%rev' => '<a href="'.$link_rev.'" target="_blank">'.$commit->getRevision().'</a>')); ?></div>
                <div class="commitauthor"><?php echo __('By %user', array('%user' => get_component_html('main/userdropdown', array('user' => $commit->getAuthor(), 'size' => 'large')))); ?></div>
            </div>
            <div class="commentdate" id="commit_<?php echo $commit->getID(); ?>_date"><?php echo tbg_formattime($commit->getDate(), 12); ?> - <?php echo __('Preceeded by %prev', array('%prev' => '<a href="'.$link_old.'" target="_blank">'.$commit->getPreviousRevision().'</a>'))?></div>
        </div>

        <div class="commentbody article commit_main" id="commit_<?php echo $commit->getID(); ?>_body">
            <pre><?php echo trim($commit->getLog()); ?></pre>
            <div class="commit_expander" style="<?php if (isset($expanded) && $expanded == true) echo 'display: none;'; ?>">
                <a href="javascript:void(0);" style="padding-right: 5px;" id="checkin_expand_<?php echo $commit->getID(); ?>" onclick="$('checkin_details_<?php echo $commit->getID(); ?>').show(); $('checkin_expand_<?php echo $commit->getID(); ?>').hide(); $('checkin_collapse_<?php echo $commit->getID(); ?>').show();"><?php echo fa_image_tag('plus-square'); ?> <?php echo __("Show more details"); ?></a>
                <a href="javascript:void(0);" style="display: none; padding-right: 5px;" id="checkin_collapse_<?php echo $commit->getID(); ?>" onclick="$('checkin_details_<?php echo $commit->getID(); ?>').hide(); $('checkin_expand_<?php echo $commit->getID(); ?>').show(); $('checkin_collapse_<?php echo $commit->getID(); ?>').hide();"><?php echo fa_image_tag('minus-square'); ?> <?php echo __("Hide details"); ?></a>
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
                                        switch ($action) {
                                            case \thebuggenie\core\entities\CommitFile::ACTION_ADDED:
                                                $image = 'plus';
                                                break;
                                            case \thebuggenie\core\entities\CommitFile::ACTION_MODIFIED:
                                                $image = 'pencil';
                                                break;
                                            case \thebuggenie\core\entities\CommitFile::ACTION_DELETED:
                                                $image = 'minus';
                                                break;
                                            case \thebuggenie\core\entities\CommitFile::ACTION_RENAMED:
                                                $image = 'copy';
                                                break;
                                        }

                                        echo '<td class="imgtd">' . fa_image_tag($image) . '</td>';

                                        $link_file = str_replace('%revno', $revision, \thebuggenie\core\framework\Context::getModule('livelink')->getSetting('log_url_' . $project->getID()));
                                        $link_file = str_replace('%oldrev', $oldrevision, $link_file);
                                        $link_file = str_replace('%branch', $branch->getName(), $link_file);

                                        $link_file = $base_url.str_replace('%file', $file->getFile(), $link_file);

                                        $link_diff = str_replace('%revno', $revision, \thebuggenie\core\framework\Context::getModule('livelink')->getSetting('diff_url_' . $project->getID()));
                                        $link_diff = str_replace('%oldrev', $oldrevision, $link_diff);
                                        $link_diff = str_replace('%branch', $branch->getName(), $link_diff);

                                        $link_diff = $base_url.str_replace('%file', $file->getFile(), $link_diff);

                                        $link_view = str_replace('%revno', $revision, \thebuggenie\core\framework\Context::getModule('livelink')->getSetting('blob_url_' . $project->getID()));
                                        $link_view = str_replace('%oldrev', $oldrevision, $link_view);
                                        $link_view = str_replace('%branch', $branch->getName(), $link_view);

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
</div>
