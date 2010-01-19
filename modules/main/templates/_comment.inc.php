<div class="comment" id="comment_<?php echo $aComment->getID(); ?>">
	<div style="padding: 5px;" id="comment_view_<?php echo $aComment->getID(); ?>">
		<div class="commentheader" id="comment_<?php echo $aComment->getID(); ?>_header"><?php echo $aComment->getTitle(); ?></div>
		<?php
			if ($aComment->isSystemComment())
			{
				$postedby = __('on behalf of ');
			}
			else
			{
				$postedby = __('by ');
			}
		?>
		<div class="commentdate" id="comment_<?php echo $aComment->getID(); ?>_date"><table cellpadding="0" cellspacing="0"><tr><td><?php echo __('Posted').' <i>'.tbg_formattime($aComment->getPosted(), 12).'</i> '.$postedby; ?></td><td><table style="display: inline;"><?php echo include_component('main/userdropdown', array('user' => $aComment->getPostedBy(), 'size' => 'small')); ?></table></td></tr></table></div>
		<div class="commentbody" id="comment_<?php echo $aComment->getID(); ?>_body"><?php echo tbg_parse_text($aComment->getContent()); ?></div>
		<?php if (TBGComment::getCommentAccess($theIssue->getID(), 'edit', $aComment->getID()) || TBGComment::getCommentAccess($theIssue->getID(), 'delete', $aComment->getID())) : ?>
			<div class="commenttools">
				<?php if (TBGComment::getCommentAccess($theIssue->getID(), 'edit', $aComment->getID())): echo '<span style="margin-right: 10px;"><a href="javascript:void(0)" onClick="$(\'comment_view_'.$aComment->getID().'\').hide();$(\'comment_edit_'.$aComment->getID().'\').show();">'; echo image_tag('icon_edit.png', array('title' => __('Edit'))).__('Edit'); ?></a></span><?php endif; ?>
				<?php if (TBGComment::getCommentAccess($theIssue->getID(), 'delete', $aComment->getID())): echo '<a href="javascript:void(0)" onClick="$(\'comment_delete_confirm_'.$aComment->getID().'\').show();">'.image_tag('icon_comment_delete.png', array('title' => __('Delete'))).__('Delete').'</a>'; endif; ?>
			</div>
		<?php endif; ?>
		<div class="rounded_box white comment_delete" id="comment_delete_confirm_<?php echo $aComment->getID(); ?>" style="display: none;">
			<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
			<div class="xboxcontent" style="padding: 5px;">
				<h4><?php echo __('Really delete'); ?></h4>
				<span class="xboxlarge"><?php echo __('Are you sure you want to delete this comment?'); ?></span><br><br>
				<div id="comment_delete_controls_<?php echo $aComment->getID(); ?>">
					<a href="javascript:void(0)" onClick="deleteComment('<?php echo make_url('comment_delete', array('comment_id' => $aComment->getID())); ?>', <?php echo $aComment->getID(); ?>)" class="xboxlink">Yes</a> :: <a href="javascript:void(0)" onClick="$('comment_delete_confirm_<?php echo $aComment->getID(); ?>').hide();" class="xboxlink"><?php echo __('No'); ?></a>
				</div>
				<div id="comment_delete_indicator_<?php echo $aComment->getID(); ?>" style="display: none;">
					<?php echo image_tag('spinning_16.gif', array('class' => 'spinning')); ?>
				</div>
			</div>
			<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
		</div>
	</div>
	
	<div id="comment_edit_<?php echo $aComment->getID(); ?>" class="comment_edit" style="display: none;">
		<form id="comment_edit_form_<?php echo $aComment->getID(); ?>" action="<?php echo make_url('comment_update', array('comment_id' => $aComment->getID())); ?>" method="post" onSubmit="updateComment('<?php echo make_url('comment_update', array('comment_id' => $aComment->getID())); ?>', '<?php echo $aComment->getID(); ?>'); return false;">
			<input type="hidden" name="comment_id" value="<?php echo $aComment->getID(); ?>" />
			<input type="text" class="comment_titlebox" name="comment_title" value="<?php echo $aComment->getTitle(); ?>" /><br>
			<?php include_template('main/textarea', array('area_name' => 'comment_body', 'area_id' => 'comment_bodybox', 'height' => '200px', 'width' => '100%', 'value' => ($aComment->getContent()))); ?>
			
			<div id="comment_edit_indicator_<?php echo $aComment->getID(); ?>" style="display: none;">
				<?php echo image_tag('spinning_16.gif', array('class' => 'spinning')); ?>
			</div>
			
			<div id="comment_edit_controls_<?php echo $aComment->getID(); ?>">
				<input type="submit" class="comment_editsave" value="<?php echo __('Save changes'); ?>" /> <a href="javascript:void(0)" onClick="$('comment_edit_<?php echo $aComment->getID(); ?>').hide();$('comment_view_<?php echo $aComment->getID(); ?>').show();"><?php echo __('or cancel'); ?></a>
			</div>
		</form>
	</div>
</div>