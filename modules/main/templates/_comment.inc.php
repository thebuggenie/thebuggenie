<div class="comment">
	<div class="rounded_box iceblue_borderless">
		<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
		<div class="xboxcontent" style="padding: 5px;">
			<div class="commentheader"><?php echo $aComment->getTitle(); ?></div>
			<?php
				if ($aComment->isSystemComment())
				{
					$postedby = __('on behalf of ').$aComment->getPostedBy();
				}
				else
				{
					$postedby = __('by ').$aComment->getPostedBy();
				}
			?>
			<div class="commentdate"><?php echo __('Posted').' <i>'.tbg_formattime($aComment->getPosted(), 12).'</i> '.$postedby; ?></div>
		</div>
	</div>
	<div class="rounded_box mediumgrey_borderless">
		<div class="xboxcontent" style="padding: 5px;">
			<div class="commentbody"><?php echo tbg_parse_text($aComment->getContent()); ?></div>
		<?php if (TBGComment::getCommentAccess($theIssue->getID(), 'edit', $aComment->getID()) || TBGComment::getCommentAccess($theIssue->getID(), 'delete', $aComment->getID())) : ?>
			<div class="commenttools">
				<?php if (TBGComment::getCommentAccess($theIssue->getID(), 'edit', $aComment->getID())): echo image_tag('icon_edit.png', array('title' => __('Edit'))); ?><span style="margin-right: 10px;">Edit</span><?php endif; ?>
				<?php if (TBGComment::getCommentAccess($theIssue->getID(), 'delete', $aComment->getID())): echo image_tag('icon_comment_delete.png', array('title' => __('Delete'))); ?>Delete<?php endif; ?></div>
		<?php endif; ?>
		</div>
		<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
	</div>
</div>