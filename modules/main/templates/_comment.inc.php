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
			<div class="commentbody"><?php echo $aComment->getContent(); ?></div>
		</div>
		<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
	</div>
</div>