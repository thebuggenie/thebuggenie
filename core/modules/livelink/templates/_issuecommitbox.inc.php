<?php

    /** @var \thebuggenie\core\entities\Branch $branch */
    /** @var \thebuggenie\core\entities\Commit $commit */
    /** @var \thebuggenie\core\entities\Project $project */

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

?>
<div class="comment" id="commit_<?php echo $commit->getID(); ?>">
    <div id="commit_view_<?php echo $commit->getID(); ?>" class="comment_main">
        <div id="commit_<?php echo $commit->getID(); ?>_header" class="commentheader">
            <div class="commenttitle">
                <?php include_component('main/userdropdown', array('user' => $commit->getAuthor(), 'size' => 'large')); ?>
            </div>
            <div class="comment_hash">
                <a href="javascript:void(0)" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'livelink_getcommit', 'commit_id' => $commit->getID())); ?>');"><?php echo $commit->getRevisionString(); ?></a>
            </div>
            <div class="commentdate" id="commit_<?php echo $commit->getID(); ?>_date">
                <?php echo tbg_formattime($commit->getDate(), 9); ?>
            </div>
        </div>

        <div class="commentbody article commit_main" id="commit_<?php echo $commit->getID(); ?>_body">
            <?php echo tbg_parse_text(trim($commit->getLog()), false, null, array('target' => $commit)); ?>
        </div>
    </div>
</div>
