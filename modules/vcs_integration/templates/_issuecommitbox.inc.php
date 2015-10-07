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

    $misc_data_array = $commit->getMiscDataArray();
    $reposname = null;

    if (array_key_exists('gitlab_repos_ns', $misc_data_array))
    {
        $reposname = $misc_data_array['gitlab_repos_ns'];
    }

?>
<div class="comment" id="commit_<?php echo $commit->getID(); ?>">
    <div id="commit_view_<?php echo $commit->getID(); ?>" class="comment_main">
        <div id="commit_<?php echo $commit->getID(); ?>_header" class="commentheader">
            <div class="commenttitle">
                <?php include_component('main/userdropdown', array('user' => $commit->getAuthor(), 'size' => 'large')); ?>
            </div>
            <div class="comment_hash">
                <a href="javascript:void(0)" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'vcs_integration_getcommit', 'commit_id' => $commit->getID())); ?>');"><?php echo $commit->getRevisionString(); ?></a>
            </div>
            <div class="commentdate" id="commit_<?php echo $commit->getID(); ?>_date">
                <?php echo tbg_formattime($commit->getDate(), 9); ?>
            </div>
            <div class="commit_repos_branch">
                <?php if ($reposname !== null): ?><span class="commitrepos"><?php echo $reposname; ?>/</span> <?php endif; ?>
                <?php if ($branchname !== null): ?><span class="commitbranch"><?php echo $branchname; ?></span> <?php endif; ?>
            </div>
        </div>

        <div class="commentbody article commit_main" id="commit_<?php echo $commit->getID(); ?>_body">
            <?php echo tbg_parse_text(trim($commit->getLog())); ?>
        </div>
    </div>
</div>
