<?php $options = (isset($issue)) ? array('issue' => $issue) : array(); ?>
<?php if ($comment->isViewableByUser($tbg_user)): ?>
<div class="comment<?php if ($comment->isSystemComment()): ?> system_comment<?php endif; if (!$comment->isPublic()): ?> private_comment<?php endif; ?> syntax_<?php echo \thebuggenie\core\framework\Settings::getSyntaxClass($comment->getSyntax()); ?>" id="comment_<?php echo $comment->getID(); ?>"<?php if ($comment->isSystemComment()): ?> style="display: none;"<?php endif; ?>>
    <div id="comment_view_<?php echo $comment->getID(); ?>" class="comment_main">
        <div id="comment_<?php echo $comment->getID(); ?>_header" class="commentheader">
            <a href="#comment_<?php echo $comment->getID(); ?>" class="comment_hash">#<?php echo $comment->getCommentNumber(); ?></a>
            <div class="commenttitle">
                <?php if(!$comment->isPublic()): ?>
                    <?php echo image_tag('icon_locked.png', array('class' => 'comment_restricted', 'title' => __('Access to this comment is restricted'))); ?>
                <?php endif; ?>
                <?php echo include_component('main/userdropdown', array('user' => $comment->getPostedBy(), 'size' => 'large')); ?>
            </div>
            <div class="commentdate" id="comment_<?php echo $comment->getID(); ?>_date">
                <?php if ($comment->isReply()): ?>
                    <?php echo image_tag('icon_reply.png', array('style' => 'margin-right: 5px; vertical-align: middle;')).__('%comment_date, in reply to comment %replied_comment_number', array('%comment_date' => tbg_formattime($comment->getPosted(), 12), '%replied_comment_number' => link_tag("#comment_{$comment->getReplyToComment()->getID()}", '#'.$comment->getReplyToComment()->getCommentNumber()))); ?>
                <?php else: ?>
                    <?php echo tbg_formattime($comment->getPosted(), 9); ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="commentbody article" id="comment_<?php echo $comment->getID(); ?>_body">
            <div class="commentcontent" id="comment_<?php echo $comment->getID(); ?>_content">
                <?php echo $comment->getParsedContent($options); ?>
            </div>
            <?php if ((\thebuggenie\core\framework\Context::isProjectContext() && !\thebuggenie\core\framework\Context::getCurrentProject()->isArchived()) || !\thebuggenie\core\framework\Context::isProjectContext()) : ?>
                <div class="commenttools">
                    <?php if (!$comment->isSystemComment() && $tbg_user->canPostComments() && ((\thebuggenie\core\framework\Context::isProjectContext() && !\thebuggenie\core\framework\Context::getCurrentProject()->isArchived()) || !\thebuggenie\core\framework\Context::isProjectContext())): ?>
                        <a href="javascript:void(0);" onclick="$$('.comment_editor').each(Element.hide);$('comment_reply_<?php echo $comment->getID(); ?>').show();$('comment_reply_bodybox_<?php echo $comment->getID(); ?>').focus();"><?php echo __('Reply'); ?></a>
                    <?php endif; ?>
                    <?php if ($comment->canUserEdit($tbg_user)): ?>
                        <a href="javascript:void(0)" onclick="$$('.comment_editor').each(Element.hide);$('comment_edit_<?php echo $comment->getID(); ?>').show();"><?php echo __('Edit'); ?></a>
                    <?php endif; ?>
                    <?php if ($comment->canUserDelete($tbg_user)): ?>
                        <?php echo javascript_link_tag(__('Delete'), array('onclick' => "TBG.Main.Helpers.Dialog.show('".__('Do you really want to delete this comment?')."', '".__('Please confirm that you want to delete this comment.')."', {yes: {click: function() {TBG.Main.Comment.remove('".make_url('comment_delete', array('comment_id' => $comment->getID()))."', ".$comment->getID()."); }}, no: { click: TBG.Main.Helpers.Dialog.dismiss }});")); ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <?php if ($comment->hasAssociatedChanges()): ?>
            <strong><?php echo __('Changes: %list_of_changes', array('%list_of_changes' => '')); ?></strong><br>
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
    </div>

    <div id="comment_edit_<?php echo $comment->getID(); ?>" class="comment_edit comment_editor" style="display: none;">
        <div class="comment_add_title"><?php echo __('Edit comment %comment_number', array('%comment_number' => "<a href='#comment_{$comment->getID()}'>#".$comment->getCommentNumber().'</a>')); ?></div><br>
        <form id="comment_edit_form_<?php echo $comment->getID(); ?>" class="syntax_<?php echo \thebuggenie\core\framework\Settings::getSyntaxClass($comment->getSyntax()); ?>" action="<?php echo make_url('comment_update', array('comment_id' => $comment->getID())); ?>" method="post" onSubmit="TBG.Main.Comment.update('<?php echo make_url('comment_update', array('comment_id' => $comment->getID())); ?>', '<?php echo $comment->getID(); ?>'); return false;">
            <input type="hidden" name="comment_id" value="<?php echo $comment->getID(); ?>" />
            <label for="comment_visibility"><?php echo __('Comment visibility'); ?> <span class="faded_out">(<?php echo __('whether to hide this comment for "regular users"'); ?>)</span></label><br />
            <select class="comment_visibilitybox" id="comment_visibility" name="comment_visibility">
                <option value="1"<?php if ($comment->isPublic()): ?> selected="selected" <?php endif; ?>><?php echo __('Visible for all users'); ?></option>
                <option value="0"<?php if (!$comment->isPublic()): ?> selected="selected" <?php endif; ?>><?php echo __('Visible for me, developers and administrators only'); ?></option>
            </select>
            <br />
            <label for="comment_edit_<?php echo $comment->getId(); ?>_bodybox"><?php echo __('Comment'); ?></label><br />
            <?php include_component('main/textarea', array('area_name' => 'comment_body', 'target_type' => $comment->getTargetType(), 'target_id' => $comment->getTargetId(), 'area_id' => 'comment_edit_'.$comment->getID().'_bodybox', 'height' => '200px', 'width' => '100%', 'syntax' => \thebuggenie\core\framework\Settings::getSyntaxClass($comment->getSyntax()), 'value' => tbg_decodeUTF8($comment->getContent(), true))); ?>
            <div id="comment_edit_indicator_<?php echo $comment->getID(); ?>" style="display: none; text-align: left;">
                <?php echo image_tag('spinning_16.gif'); ?>
            </div>
            <div id="comment_edit_controls_<?php echo $comment->getID(); ?>" class="comment_controls">
                <?php echo __('%save_changes or %cancel', array('%save_changes' => '<input type="submit" class="comment_editsave button button-silver" value="'.__('Save changes').'" />', '%cancel' => javascript_link_tag(__('cancel'), array('onclick' => "$('comment_edit_{$comment->getID()}').hide();$('comment_view_{$comment->getID()}').show();$('comment_add_button').show();")))); ?>
            </div>
        </form>
    </div>
    <div id="comment_reply_<?php echo $comment->getID(); ?>" class="comment_reply comment_editor" style="display: none;">
        <div class="comment_add_title"><?php echo __('Reply to comment %comment_number', array('%comment_number' => "<a href='#comment_{$comment->getID()}'>#".$comment->getCommentNumber().'</a>')); ?></div><br>
        <form id="comment_reply_form_<?php echo $comment->getID(); ?>" accept-charset="<?php echo mb_strtoupper(\thebuggenie\core\framework\Context::getI18n()->getCharset()); ?>" action="<?php echo make_url('comment_add', array('comment_applies_id' => $comment->getTargetID(), 'comment_applies_type' => $comment->getTargetType(), 'comment_module' => $comment->getModuleName())); ?>" method="post" onsubmit="TBG.Main.Comment.reply('<?php echo make_url('comment_add', array('comment_applies_id' => $comment->getTargetID(), 'comment_applies_type' => $comment->getTargetType(), 'comment_module' => $comment->getModuleName())); ?>', <?php echo $comment->getID(); ?>);return false;">
            <input type="hidden" name="reply_to_comment_id" value="<?php echo $comment->getID(); ?>" />
            <label for="comment_reply_visibility_<?php echo $comment->getID(); ?>"><?php echo __('Comment visibility'); ?> <span class="faded_out">(<?php echo __('whether to hide this comment for "regular users"'); ?>)</span></label><br />
            <select class="comment_visibilitybox" id="comment_reply_visibility_<?php echo $comment->getID(); ?>" name="comment_visibility">
                <option value="1"<?php if ($comment->isPublic()): ?> selected="selected" <?php endif; ?>><?php echo __('Visible for all users'); ?></option>
                <option value="0"<?php if (!$comment->isPublic()): ?> selected="selected" <?php endif; ?>><?php echo __('Visible for me, developers and administrators only'); ?></option>
            </select>
            <br />
            <?php include_component('main/textarea', array('area_name' => 'comment_body', 'target_type' => $comment->getTargetType(), 'target_id' => $comment->getTargetId(), 'area_id' => 'comment_reply_'.$comment->getID().'_bodybox', 'height' => '200px', 'width' => '100%', 'syntax' => \thebuggenie\core\framework\Settings::getSyntaxClass($comment->getSyntax()), 'value' => tbg_decodeUTF8("\n\n\n'''".__('%user wrote:', array('%user' => ($comment->getPostedBy() instanceof \thebuggenie\core\entities\common\Identifiable) ? $comment->getPostedBy()->getName() : __('Unknown user')))."'''\n>".str_replace("\n", "\n> ", wordwrap(html_entity_decode(strip_tags(tbg_decodeUTF8($comment->getContent(), true)), ENT_COMPAT, \thebuggenie\core\framework\Context::getI18n()->getCharset()), 75, "\n"))."\n", true))); ?>
            <div id="comment_reply_indicator_<?php echo $comment->getID(); ?>" style="display: none;">
                <?php echo image_tag('spinning_16.gif', array('class' => 'spinning')); ?>
            </div>
            <div id="comment_reply_controls_<?php echo $comment->getID(); ?>" class="comment_controls">
                <?php echo __('%post_reply or %cancel', array('%post_reply' => '<input type="submit" class="comment_replysave button button-silver" value="'.__('Post reply').'" />', '%cancel' => javascript_link_tag(__('cancel'), array('onclick' => "$('comment_reply_{$comment->getID()}').hide();$('comment_view_{$comment->getID()}').show();$('comment_add_button').show();")))); ?>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
