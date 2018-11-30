<div id="comment_reply_<?= $comment->getID(); ?>" class="comment_reply comment_editor editor_container">
    <form id="comment_reply_form_<?= $comment->getID(); ?>" accept-charset="<?= mb_strtoupper(\thebuggenie\core\framework\Context::getI18n()->getCharset()); ?>" action="<?= make_url('comment_add', array('comment_applies_id' => $comment->getTargetID(), 'comment_applies_type' => $comment->getTargetType(), 'comment_module' => $comment->getModuleName())); ?>" method="post" onsubmit="TBG.Main.Comment.reply('<?= make_url('comment_add', array('comment_applies_id' => $comment->getTargetID(), 'comment_applies_type' => $comment->getTargetType(), 'comment_module' => $comment->getModuleName())); ?>', <?= $comment->getID(); ?>);return false;">
        <input type="hidden" name="reply_to_comment_id" value="<?= $comment->getID(); ?>" />
        <label for="comment_reply_visibility_<?= $comment->getID(); ?>"><?= __('Comment visibility'); ?> <span class="faded_out">(<?= __('whether to hide this comment for "regular users"'); ?>)</span></label><br />
        <select class="comment_visibilitybox" id="comment_reply_visibility_<?= $comment->getID(); ?>" name="comment_visibility">
            <option value="1"<?php if ($comment->isPublic()): ?> selected="selected" <?php endif; ?>><?= __('Visible for all users'); ?></option>
            <option value="0"<?php if (!$comment->isPublic()): ?> selected="selected" <?php endif; ?>><?= __('Visible for me, developers and administrators only'); ?></option>
        </select>
        <br />
        <?php include_component('main/textarea', array('area_name' => 'comment_body', 'placeholder' => __('Enter your reply here...'), 'target_type' => isset($mentionable_target_type) ? $mentionable_target_type : $comment->getTargetType(), 'target_id' => $comment->getTargetId(), 'area_id' => 'comment_reply_'.$comment->getID().'_bodybox', 'height' => '200px', 'width' => '100%', 'syntax' => \thebuggenie\core\framework\Settings::getSyntaxClass($comment->getSyntax()), 'value' => '')); ?>
        <div id="comment_reply_controls_<?= $comment->getID(); ?>" class="backdrop_details_submit">
            <?= javascript_link_tag(__('Cancel'), ['onclick' => "$('comment_reply_{$comment->getID()}').removeClassName('active');$('comment_view_{$comment->getID()}').show();$('comment_add_button').show();", 'class' => 'closer']); ?>
            <div class="submit_container"><button type="submit" class="button button-silver"><?= image_tag('spinning_16.gif', ['id' => 'comment_reply_indicator_' . $comment->getID(), 'style' => 'display: none;']) . __('Post reply'); ?></button></div>
        </div>
    </form>
</div>
