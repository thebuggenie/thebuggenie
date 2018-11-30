<?php if ($comment->isReply()): ?>
    <div class="comment reply <?php if (!$comment->isPublic()): ?> private_comment<?php endif; ?> syntax_<?= \thebuggenie\core\framework\Settings::getSyntaxClass($comment->getSyntax()); ?>" id="comment_<?= $comment->getID(); ?>">
        <div id="comment_view_<?= $comment->getID(); ?>" class="comment_main">
<?php endif; ?>
            <div id="comment_<?= $comment->getID(); ?>_header" class="commentheader">
                <div class="commenttitle">
                    <?php if(!$comment->isPublic()): ?>
                        <?= fa_image_tag('lock', ['class' => 'comment_restricted', 'title' => __('Access to this comment is restricted')]); ?>
                    <?php endif; ?>
                    <?= include_component('main/userdropdown', array('user' => $comment->getPostedBy(), 'size' => 'large')); ?>
                </div>
                <div class="commentdate" id="comment_<?= $comment->getID(); ?>_date">
                    <?= tbg_formattime($comment->getPosted(), 25); ?>
                </div>
            </div>
            <?php include_component('main/editcomment', ['comment' => $comment, 'mentionable_target_type' => isset($mentionable_target_type) ? $mentionable_target_type : $comment->getTargetType()]); ?>
            <div class="commentbody article" id="comment_<?= $comment->getID(); ?>_body">
                <div class="commentcontent" id="comment_<?= $comment->getID(); ?>_content">
                    <?= $comment->getParsedContent($options); ?>
                </div>
                <?php if ((\thebuggenie\core\framework\Context::isProjectContext() && !\thebuggenie\core\framework\Context::getCurrentProject()->isArchived()) || !\thebuggenie\core\framework\Context::isProjectContext()) : ?>
                    <div class="commenttools action-buttons">
                        <a class="action-button" href="#comment_<?= $comment->getID(); ?>"><?= fa_image_tag('link'); ?></a>
                        <?php if ($comment->canUserEdit($tbg_user)): ?>
                            <a class="action-button" href="javascript:void(0)" onclick="$$('.comment_editor').each(function (elm) { elm.removeClassName('active'); });$('comment_edit_<?= $comment->getID(); ?>').addClassName('active');"><?= fa_image_tag('edit'); ?></a>
                        <?php endif; ?>
                        <?php if ($comment->canUserDelete($tbg_user)): ?>
                            <?= javascript_link_tag(fa_image_tag('trash-alt'), ['class' => 'action-button', 'onclick' => "TBG.Main.Helpers.Dialog.show('".__('Do you really want to delete this comment?')."', '".__('Please confirm that you want to delete this comment.')."', {yes: {click: function() {TBG.Main.Comment.remove('".make_url('comment_delete', ['comment_id' => $comment->getID()])."', ".$comment->getID().", '".$comment_count_div."'); }}, no: { click: TBG.Main.Helpers.Dialog.dismiss }});"]); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if ($comment->hasAssociatedChanges()): ?>
                    <h5 class="change-list"><?= __('Changes: %list_of_changes', ['%list_of_changes' => '']); ?></h5>
                    <ul class="comment_log_items">
                        <?php foreach ($comment->getLogItems() as $item): ?>
                            <?php if (!$item instanceof \thebuggenie\core\entities\LogItem) continue; ?>
                            <?php /* Pass item's own time in order to prevent issuelogitem template from including timestamp for the item. The timestamp span is additionally hidden by the CSS.*/ ?>
                            <?php $previous_time = $item->getTime(); ?>
                            <?php include_component('main/issuelogitem', compact('item', 'previous_time')); ?>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
<?php if ($comment->isReply()): ?>
        </div>
    </div>
<?php endif; ?>