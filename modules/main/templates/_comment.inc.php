<?php $options = (isset($issue)) ? array('issue' => $issue) : array(); ?>
<div class="comment<?php if ($comment->isSystemComment()): ?> system_comment<?php endif; ?>" id="comment_<?php echo $comment->getID(); ?>"<?php if ($comment->isSystemComment()): ?> style="display: none;"<?php endif; ?>>
	<div style="position: relative; overflow: visible; padding: 5px;" id="comment_view_<?php echo $comment->getID(); ?>" class="comment_main">
		<div id="comment_<?php echo $comment->getID(); ?>_header" class="commentheader">
			<a href="#comment_<?php echo $comment->getTargetType(); ?>_<?php echo $comment->getTargetID(); ?>_<?php echo $comment->getID(); ?>" class="comment_hash">#<?php echo $comment->getCommentNumber(); ?></a>
			<?php if (($comment->canUserEditComment() || $comment->canUserDeleteComment()) && ((TBGContext::isProjectContext() && !TBGContext::getCurrentProject()->isArchived()) || !TBGContext::isProjectContext())) : ?>
				<div class="commenttools">
					<?php if ($comment->canUserEditComment()): ?><span style="margin-right: 10px;"><a href="javascript:void(0)" onClick="$('comment_view_<?php echo $comment->getID(); ?>').hide();$('comment_edit_<?php echo $comment->getID(); ?>').show();"><?php echo image_tag('icon_edit.png', array('title' => __('Edit'))); ?><?php echo __('Edit'); ?></a></span><?php endif; ?>
					<?php if ($comment->canUserDeleteComment()): ?><a href="javascript:void(0)" onClick="$('comment_delete_confirm_<?php echo $comment->getID(); ?>').toggle();"><?php echo image_tag('icon_comment_delete.png', array('title' => __('Delete'))); ?><?php echo __('Delete'); ?></a><?php endif; ?>
				</div>
			<?php endif; ?>
			<div class="commenttitle">
				<?php if ($comment->isSystemComment()): ?>
					<?php echo __('Comment posted on behalf of %user%', array('%user%' => '<div style="display: inline;">'.get_component_html('main/userdropdown', array('user' => $comment->getPostedBy(), 'size' => 'small')).'</div>')); ?>
				<?php else: ?>
					<?php echo __('Comment posted by %user%', array('%user%' => '<div style="display: inline;">'.get_component_html('main/userdropdown', array('user' => $comment->getPostedBy(), 'size' => 'small')).'</div>')); ?>
				<?php endif; ?>
			</div>
			<div class="commentdate" id="comment_<?php echo $comment->getID(); ?>_date"><?php echo tbg_formattime($comment->getPosted(), 12); ?></div>
		</div>
		<div class="rounded_box lightyellow borderless shadowed comment_delete" id="comment_delete_confirm_<?php echo $comment->getID(); ?>" style="display: none; width: 300px; position: absolute; right: 0; top: 0; padding: 5px; z-index: 20;">
			<h5><?php echo __('Really delete this comment?'); ?></h5>
			<div id="comment_delete_controls_<?php echo $comment->getID(); ?>" style="text-align: right; font-size: 12px;">
				<a href="javascript:void(0)" onClick="TBG.Main.Comment.remove('<?php echo make_url('comment_delete', array('comment_id' => $comment->getID())); ?>', <?php echo $comment->getID(); ?>)" class="xboxlink">Yes</a> :: <a href="javascript:void(0)" onClick="$('comment_delete_confirm_<?php echo $comment->getID(); ?>').hide();" class="xboxlink"><?php echo __('No'); ?></a>
			</div>
			<div id="comment_delete_indicator_<?php echo $comment->getID(); ?>" style="display: none;">
				<?php echo image_tag('spinning_16.gif', array('class' => 'spinning')); ?>
			</div>
		</div>
		<div class="commentbody article" id="comment_<?php echo $comment->getID(); ?>_body">
			<?php echo tbg_parse_text($comment->getContent(), false, null, $options); ?>
		</div>
	</div>
	
	<div id="comment_edit_<?php echo $comment->getID(); ?>" class="comment_edit" style="display: none;">
		<form id="comment_edit_form_<?php echo $comment->getID(); ?>" action="<?php echo make_url('comment_update', array('comment_id' => $comment->getID())); ?>" method="post" onSubmit="TBG.Main.Comment.update('<?php echo make_url('comment_update', array('comment_id' => $comment->getID())); ?>', '<?php echo $comment->getID(); ?>'); return false;">
			<input type="hidden" name="comment_id" value="<?php echo $comment->getID(); ?>" />
			<label for="comment_visibility"><?php echo __('Comment visibility'); ?> <span class="faded_out">(<?php echo __('whether to hide this comment for "regular users"'); ?>)</span></label><br />
			<select class="comment_visibilitybox" id="comment_visibility" name="comment_visibility">
				<option value="1"<?php if ($comment->isPublic()): ?> selected="selected" <?php endif; ?>><?php echo __('Visible for all users'); ?></option>
				<option value="0"<?php if (!$comment->isPublic()): ?> selected="selected" <?php endif; ?>><?php echo __('Visible for me, developers and administrators only'); ?></option>
			</select>
			<br />
			<label for="comment_bodybox"><?php echo __('Comment'); ?></label><br />
			<?php include_template('main/textarea', array('area_name' => 'comment_body', 'area_id' => 'comment_bodybox', 'height' => '200px', 'width' => '970px', 'value' => tbg_decodeUTF8($comment->getContent(), true))); ?>
			<div id="comment_edit_indicator_<?php echo $comment->getID(); ?>" style="display: none;">
				<?php echo image_tag('spinning_16.gif', array('class' => 'spinning')); ?>
			</div>
			<div id="comment_edit_controls_<?php echo $comment->getID(); ?>" class="comment_controls">
				<input type="submit" class="comment_editsave" value="<?php echo __('Save changes'); ?>" /> <a href="javascript:void(0)" onClick="$('comment_edit_<?php echo $comment->getID(); ?>').hide();$('comment_view_<?php echo $comment->getID(); ?>').show();"><?php echo __('or cancel'); ?></a>
			</div>
		</form>
	</div>
</div>