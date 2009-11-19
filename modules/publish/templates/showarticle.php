<table style="margin-top: 0px; table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<td class="left_bar">
			some wiki menu stuff goes here
		</td>
		<td class="main_area article">
			<?php if ($article instanceof PublishArticle): ?>
				<?php include_component('articledisplay', array('article' => $article)); ?>
			<?php else: ?>
				<div class="header" style="padding: 5px;"><?php echo $article_name; ?></div>
				<div style="font-size: 14px;">
					<?php echo __('This article has not been created yet. Click below to create it and start editing.'); ?>
				</div>
			<?php endif; ?>
		</td>
	</tr>
</table>