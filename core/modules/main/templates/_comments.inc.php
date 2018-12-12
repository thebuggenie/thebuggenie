<?php $module = (isset($module)) ? $module : 'core'; ?>
<?php if ($tbg_user->canPostComments() && ((\thebuggenie\core\framework\Context::isProjectContext() && !\thebuggenie\core\framework\Context::getCurrentProject()->isArchived()) || !\thebuggenie\core\framework\Context::isProjectContext())): ?>
    <?php if (!isset($show_button) || $show_button == true): ?>
        <ul class="simple_list" id="add_comment_button_container">
            <li id="comment_add_button"><input class="button button-green first last" type="button" onclick="TBG.Main.Comment.showPost();" value="<?= __('Post comment'); ?>"></li>
        </ul>
    <?php endif; ?>
    <div id="comment_add" class="comment_add comment_editor" style="<?php if (!(isset($comment_error) && $comment_error)): ?>display: none; <?php endif; ?>margin-top: 5px;">
        <div class="backdrop_detail_header">
            <span><?= __('Create a comment'); ?></span>
            <?= javascript_link_tag(fa_image_tag('times'), ['onclick' => "$('comment_add').hide();$('comment_add_button').show();", 'class' => 'closer']); ?>
        </div>
        <div class="comment_add_main">
            <form id="comment_form" accept-charset="<?= mb_strtoupper(\thebuggenie\core\framework\Context::getI18n()->getCharset()); ?>" action="<?= make_url('comment_add', array('comment_applies_id' => $target_id, 'comment_applies_type' => $target_type, 'comment_module' => $module)); ?>" method="post" onSubmit="TBG.Main.Comment.add('<?= make_url('comment_add', array('comment_applies_id' => $target_id, 'comment_applies_type' => $target_type, 'comment_module' => 'core')); ?>', '<?= $comment_count_div; ?>');return false;">
                <label for="comment_visibility"><?= __('Comment visibility'); ?> <span class="faded_out">(<?= __('whether to hide this comment for "regular users"'); ?>)</span></label><br />
                <select class="comment_visibilitybox" id="comment_visibility" name="comment_visibility">
                    <option value="1"><?= __('Visible for all users'); ?></option>
                    <option value="0"><?= __('Visible for me, developers and administrators only'); ?></option>
                </select>
                <br />
                <label for="comment_bodybox"><?= __('Comment'); ?></label><br />
                <?php include_component('main/textarea', array('area_name' => 'comment_body', 'target_type' => $mentionable_target_type, 'target_id' => $target_id, 'area_id' => 'comment_bodybox', 'height' => '250px', 'width' => '100%', 'syntax' => $tbg_user->getPreferredCommentsSyntax(true), 'value' => ((isset($comment_error) && $comment_error) ? $comment_error_body : ''))); ?>
                <div id="comment_add_controls" class="backdrop_details_submit">
                    <span class="explanation">
                        <?php if ($target_type == \thebuggenie\core\entities\Comment::TYPE_ISSUE): ?>
                            <input type="checkbox" name="comment_save_changes" class="fancycheckbox" id="comment_save_changes" value="1"<?php if ($save_changes_checked): ?> checked<?php endif; ?>>&nbsp;<label for="comment_save_changes"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Save my changes with this comment'); ?></label>
                        <?php endif; ?>
                        <input type="hidden" name="forward_url" value="<?= $forward_url; ?>">
                    </span>
                    <div class="submit_container"><button type="submit" class="button button-silver"><?= image_tag('spinning_16.gif', ['id' => 'comment_add_indicator', 'style' => 'display: none;']) . __('Create comment'); ?></button></div>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<div class="faded_out comments_none" id="comments_none" <?php if (\thebuggenie\core\entities\Comment::countComments($target_id, $target_type) != 0): ?>style="display: none;"<?php endif; ?>><?= __('There are no comments'); ?></div>
<div class="initial-placeholder"><span><?= fa_image_tag('check-circle', ['class' => 'icon'], 'far'); ?><span><?= __('Issue created'); ?></span></span></div>
<div id="comments_box">
    <?php include_component('main/commentlist', compact('comment_count_div', 'mentionable_target_type', 'target_type', 'target_id', 'issue')); ?>
</div>
