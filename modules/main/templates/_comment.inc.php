<?php

	$options = (isset($issue)) ? array('issue' => $issue) : array();

?>
<div class="comment" id="comment_<?php echo $comment->getTargetType(); ?>_<?php echo $comment->getTargetID(); ?>_<?php echo $comment->getID(); ?>">
	<div style="padding: 5px;" id="comment_view_<?php echo $comment->getID(); ?>" class="comment_main">
		<div id="comment_<?php echo $comment->getID(); ?>_header" class="commentheader">
			<a href="#comment_<?php echo $comment->getTargetType(); ?>_<?php echo $comment->getTargetID(); ?>_<?php echo $comment->getID(); ?>" class="comment_hash">#<?php echo $comment->getCommentNumber(); ?></a>
			<div class="commenttitle">
				<?php if ($comment->isSystemComment()): ?>
					<?php echo __('Comment posted on behalf of %user%', array('%user%' => '<div style="display: inline;">'.get_component_html('main/userdropdown', array('user' => $comment->getPostedBy(), 'size' => 'small')).'</div>')); ?>
				<?php else: ?>
					<?php echo __('Comment posted by %user%', array('%user%' => '<div style="display: inline;">'.get_component_html('main/userdropdown', array('user' => $comment->getPostedBy(), 'size' => 'small')).'</div>')); ?>
				<?php endif; ?>
			</div>
			<div class="commentdate" id="comment_<?php echo $comment->getID(); ?>_date"><?php echo tbg_formattime($comment->getPosted(), 12); ?></div>
		</div>
		<div class="commentbody article" id="comment_<?php echo $comment->getID(); ?>_body">
			<?php echo tbg_parse_text($comment->getContent(), false, null, $options); ?>
		</div>
		<?php if ($comment->canUserEditComment() || $comment->canUserDeleteComment()) : ?>
			<div class="commenttools">
				<?php if ($comment->canUserEditComment()): ?><span style="margin-right: 10px;"><a href="javascript:void(0)" onClick="$('comment_view_<?php echo $comment->getID(); ?>').hide();$('comment_edit_<?php echo $comment->getID(); ?>').show();"><?php echo image_tag('icon_edit.png', array('title' => __('Edit'))); ?><?php echo __('Edit'); ?></a></span><?php endif; ?>
				<?php if ($comment->canUserDeleteComment()): ?><a href="javascript:void(0)" onClick="$('comment_delete_confirm_<?php echo $comment->getID(); ?>').show();"><?php echo image_tag('icon_comment_delete.png', array('title' => __('Delete'))); ?><?php echo __('Delete'); ?></a><?php endif; ?>
			</div>
		<?php endif; ?>
		<div class="rounded_box yellow borderless comment_delete" id="comment_delete_confirm_<?php echo $comment->getID(); ?>" style="display: none;">
			<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
			<div class="xboxcontent" style="padding: 5px;">
				<h4><?php echo __('Really delete'); ?></h4>
				<span class="question_header"><?php echo __('Are you sure you want to delete this comment?'); ?></span><br><br>
				<div id="comment_delete_controls_<?php echo $comment->getID(); ?>">
					<a href="javascript:void(0)" onClick="deleteComment('<?php echo make_url('comment_delete', array('comment_id' => $comment->getID())); ?>', <?php echo $comment->getID(); ?>)" class="xboxlink">Yes</a> :: <a href="javascript:void(0)" onClick="$('comment_delete_confirm_<?php echo $comment->getID(); ?>').hide();" class="xboxlink"><?php echo __('No'); ?></a>
				</div>
				<div id="comment_delete_indicator_<?php echo $comment->getID(); ?>" style="display: none;">
					<?php echo image_tag('spinning_16.gif', array('class' => 'spinning')); ?>
				</div>
			</div>
			<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
		</div>
	</div>
	
	<div id="comment_edit_<?php echo $comment->getID(); ?>" class="comment_edit" style="display: none;">
		<form id="comment_edit_form_<?php echo $comment->getID(); ?>" action="<?php echo make_url('comment_update', array('comment_id' => $comment->getID())); ?>" method="post" onSubmit="updateComment('<?php echo make_url('comment_update', array('comment_id' => $comment->getID())); ?>', '<?php echo $comment->getID(); ?>'); return false;">
			<input type="hidden" name="comment_id" value="<?php echo $comment->getID(); ?>" />
			<input type="text" class="comment_titlebox" name="comment_title" value="<?php echo $comment->getTitle(); ?>" /><br>
			<select class="comment_visibilitybox" id="comment_visibility" name="comment_visibility">
				<option value="1"<?php if ($comment->isPublic()): ?> selected="selected" <?php endif; ?>><?php echo __('Visible for all users'); ?></option>
				<option value="0"<?php if (!$comment->isPublic()): ?> selected="selected" <?php endif; ?>><?php echo __('Visible for me, developers and administrators only'); ?></option>
			</select>
			<?php include_template('main/textarea', array('area_name' => 'comment_body', 'area_id' => 'comment_bodybox', 'height' => '200px', 'width' => '100%', 'value' => ($comment->getContent()))); ?>
			
			<div id="comment_edit_indicator_<?php echo $comment->getID(); ?>" style="display: none;">
				<?php echo image_tag('spinning_16.gif', array('class' => 'spinning')); ?>
			</div>
			
			<div id="comment_edit_controls_<?php echo $comment->getID(); ?>" class="comment_controls">
				<input type="submit" class="comment_editsave" value="<?php echo __('Save changes'); ?>" /> <a href="javascript:void(0)" onClick="$('comment_edit_<?php echo $comment->getID(); ?>').hide();$('comment_view_<?php echo $comment->getID(); ?>').show();"><?php echo __('or cancel'); ?></a>
			</div>
		</form>
	</div>
</div>