<?php $module = (isset($module)) ? $module : 'core'; ?>
<?php if ($tbg_user->canPostComments() && ((\thebuggenie\core\framework\Context::isProjectContext() && !\thebuggenie\core\framework\Context::getCurrentProject()->isArchived()) || !\thebuggenie\core\framework\Context::isProjectContext())): ?>
    <?php if (!isset($show_button) || $show_button == true): ?>
        <ul class="simple_list" id="add_comment_button_container">
            <li id="comment_add_button"><input class="button button-green first last" type="button" onclick="TBG.Main.Comment.showPost();" value="<?php echo __('Post comment'); ?>"></li>
        </ul>
    <?php endif; ?>
    <div id="comment_add" class="comment_add comment_editor" style="<?php if (!(isset($comment_error) && $comment_error)): ?>display: none; <?php endif; ?>margin-top: 5px;">
        <div class="comment_add_main">
            <div class="comment_add_title"><?php echo __('Create a comment'); ?></div><br>
            <form id="comment_form" accept-charset="<?php echo mb_strtoupper(\thebuggenie\core\framework\Context::getI18n()->getCharset()); ?>" action="<?php echo make_url('comment_add', array('comment_applies_id' => $target_id, 'comment_applies_type' => $target_type, 'comment_module' => $module)); ?>" method="post" onSubmit="TBG.Main.Comment.add('<?php echo make_url('comment_add', array('comment_applies_id' => $target_id, 'comment_applies_type' => $target_type, 'comment_module' => 'core')); ?>', '<?php echo $comment_count_div; ?>');return false;">
                <label for="comment_visibility"><?php echo __('Comment visibility'); ?> <span class="faded_out">(<?php echo __('whether to hide this comment for "regular users"'); ?>)</span></label><br />
                <select class="comment_visibilitybox" id="comment_visibility" name="comment_visibility">
                    <option value="1"><?php echo __('Visible for all users'); ?></option>
                    <option value="0"><?php echo __('Visible for me, developers and administrators only'); ?></option>
                </select>
                <br />
                <label for="comment_bodybox"><?php echo __('Comment'); ?></label><br />
                <?php include_component('main/textarea', array('area_name' => 'comment_body', 'target_type' => $mentionable_target_type, 'target_id' => $target_id, 'area_id' => 'comment_bodybox', 'height' => '250px', 'width' => '100%', 'syntax' => $tbg_user->getPreferredCommentsSyntax(true), 'value' => ((isset($comment_error) && $comment_error) ? $comment_error_body : ''))); ?>
                <div id="comment_add_indicator" style="display: none;">
                    <?php echo image_tag('spinning_20.gif'); ?>
                </div>
                <div id="comment_add_controls" class="comment_controls">
                    <?php if ($target_type == \thebuggenie\core\entities\Comment::TYPE_ISSUE): ?>
                        <input type="checkbox" name="comment_save_changes" id="comment_save_changes" value="1"<?php if ($save_changes_checked): ?> checked<?php endif; ?>>&nbsp;<label for="comment_save_changes"><?php echo __('Save my changes with this comment'); ?></label>
                        <br><br>
                    <?php endif; ?>
                    <input type="hidden" name="forward_url" value="<?php echo $forward_url; ?>">
                    <?php echo __('%create_comment or %cancel', array('%create_comment' => '<input type="submit" class="button button-green" value="'.__('Create comment').'" />', '%cancel' => javascript_link_tag(__('cancel'), array('onclick'=> "$('comment_add').hide();$('comment_add_button').show();")))); ?>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<div class="faded_out comments_none" id="comments_none" <?php if (\thebuggenie\core\entities\Comment::countComments($target_id, $target_type) != 0): ?>style="display: none;"<?php endif; ?>><?php echo __('There are no comments'); ?></div>
<div id="comments_box">
    <?php foreach (\thebuggenie\core\entities\Comment::getComments($target_id, $target_type) as $comment): ?>
        <?php

            $options = compact('comment', 'comment_count_div');
            if (isset($issue))
                $options['issue'] = $issue;

            include_component('main/comment', $options);

        ?>
    <?php endforeach; ?>
</div>
