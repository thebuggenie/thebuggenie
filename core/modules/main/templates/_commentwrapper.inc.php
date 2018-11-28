<?php $options = (isset($issue)) ? array('issue' => $issue) : array(); ?>
<?php if ($comment->isViewableByUser($tbg_user)): ?>
    <div class="comment<?php if ($comment->isSystemComment()): ?> system_comment<?php endif; if (!$comment->isPublic()): ?> private_comment<?php endif; ?> syntax_<?= \thebuggenie\core\framework\Settings::getSyntaxClass($comment->getSyntax()); ?>" id="comment_<?= $comment->getID(); ?>">
        <div id="comment_view_<?= $comment->getID(); ?>" class="comment_main">
            <?php include_component('main/comment', ['comment' => $comment, 'options' => $options, 'comment_count_div' => $comment_count_div]); ?>
            <div class="comment-replies" id="comment_<?= $comment->getID(); ?>_replies">
                <?php foreach ($comment->getReplies() as $reply): ?>
                    <?php include_component('main/comment', ['comment' => $reply, 'options' => $options, 'comment_count_div' => $comment_count_div]); ?>
                <?php endforeach; ?>
            </div>
            <?php if (!$comment->isSystemComment() && $tbg_user->canPostComments() && ((\thebuggenie\core\framework\Context::isProjectContext() && !\thebuggenie\core\framework\Context::getCurrentProject()->isArchived()) || !\thebuggenie\core\framework\Context::isProjectContext())): ?>
                <div class="reply-container">
                    <?php include_component('main/replycomment', ['comment' => $comment, 'mentionable_target_type' => isset($mentionable_target_type) ? $mentionable_target_type : $comment->getTargetType()]); ?>
                    <a class="fake-reply" href="javascript:void(0);" onclick="$$('.comment_editor').each(function (elm) { elm.removeClassName('active'); });$('comment_reply_<?= $comment->getID(); ?>').addClassName('active');$('comment_reply_bodybox_<?= $comment->getID(); ?>').focus();"><?= __('Reply ...'); ?></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
