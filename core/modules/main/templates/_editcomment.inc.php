<div id="comment_edit_<?= $comment->getID(); ?>" class="comment_edit comment_editor editor_container">
    <form id="comment_edit_form_<?= $comment->getID(); ?>" class="syntax_<?= \thebuggenie\core\framework\Settings::getSyntaxClass($comment->getSyntax()); ?>" action="<?= make_url('comment_update', array('comment_id' => $comment->getID())); ?>" method="post" onSubmit="TBG.Main.Comment.update('<?= make_url('comment_update', array('comment_id' => $comment->getID())); ?>', '<?= $comment->getID(); ?>'); return false;">
        <input type="hidden" name="comment_id" value="<?= $comment->getID(); ?>" />
        <label for="comment_visibility"><?= __('Comment visibility'); ?> <span class="faded_out">(<?= __('whether to hide this comment for "regular users"'); ?>)</span></label><br />
        <select class="comment_visibilitybox" id="comment_visibility" name="comment_visibility">
            <option value="1"<?php if ($comment->isPublic()): ?> selected="selected" <?php endif; ?>><?= __('Visible for all users'); ?></option>
            <option value="0"<?php if (!$comment->isPublic()): ?> selected="selected" <?php endif; ?>><?= __('Visible for me, developers and administrators only'); ?></option>
        </select>
        <br />
        <label for="comment_edit_<?= $comment->getId(); ?>_bodybox"><?= __('Comment'); ?></label><br />
        <?php include_component('main/textarea', array('area_name' => 'comment_body', 'target_type' => isset($mentionable_target_type) ? $mentionable_target_type : $comment->getTargetType(), 'target_id' => $comment->getTargetId(), 'area_id' => 'comment_edit_'.$comment->getID().'_bodybox', 'height' => '200px', 'width' => '100%', 'syntax' => \thebuggenie\core\framework\Settings::getSyntaxClass($comment->getSyntax()), 'value' => tbg_decodeUTF8($comment->getContent(), true))); ?>
        <div id="comment_edit_controls_<?= $comment->getID(); ?>" class="backdrop_details_submit">
            <?= javascript_link_tag(__('Cancel'), ['onclick' => "$('comment_edit_{$comment->getID()}').removeClassName('active');$('comment_view_{$comment->getID()}').show();$('comment_add_button').show();"]); ?>
            <div class="submit_container"><button type="submit" class="button button-silver"><?= image_tag('spinning_16.gif', ['id' => 'comment_edit_indicator_' . $comment->getID(), 'style' => 'display: none;']) . __('Save changes'); ?></button></div>
        </div>
    </form>
</div>
