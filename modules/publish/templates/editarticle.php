<?php BUGScontext::loadLibrary('publish/publish'); ?>
<table style="margin-top: 0px; table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<td class="left_bar" style="width: 250px;">
			<?php include_component('leftmenu', array('article' => $article)); ?>
		</td>
		<td class="main_area article edit">
			<a name="top"></a>
			<div class="rounded_box iceblue_borderless" style="margin: 0 0 5px 0; width: 740px;">
				<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
				<div class="xboxcontent faded_dark" style="padding: 3px; font-size: 14px;">
					<?php echo __('If you cannot see the "%save%"-button, scroll all the way down', array('%save%' => __('Save'))); ?>
				</div>
				<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
			</div>
			<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="<?php echo make_url('publish_article_edit', array('article_name' => $article_name)); ?>" method="post"
				<table style="width: 740px;" class="padded_table" cellpadding=0 cellspacing=0>
					<tr>
						<td style="width: 175px; padding: 5px;"><label for="article_title"><?php echo __('Article title'); ?></label></td>
						<td style="width: auto;"><input type="text" name="new_article_title" id="article_title" value="<?php echo $article_title; ?>" style="width: 555px;"></td>
					</tr>
					<tr>
						<td style="padding: 5px;"><label for="article_name" class="small"><?php echo __('Article name'); ?></label></td>
						<td><input type="text" name="new_article_name" id="article_name" value="<?php echo $article_name; ?>"></td>
					</tr>
				</table>
				<br style="clear: both;">
				<label for="article_intro" style="margin-left: 5px; clear: both;"><?php echo __('Article content'); ?></label><br>
				<textarea id="article_intro" style="width: 725px; margin: 10px 0 5px 5px; font-size: 13px; height: 250px;"><?php echo $article_content; ?></textarea>
				<div class="publish_article_actions">
					<div class="sub_header"><?php echo __('Actions available'); ?></div>
					<input type="submit" value="<?php echo ($article instanceof PublishArticle) ? __('Save changes') : __('Create article'); ?>" style="float: left;">
					<?php echo link_tag(make_url('publish_article', array('article_name' => $article_name)), __('Cancel'), array('style' => 'float: left; font-size: 13px; margin: 5px 0 0 10px;')); ?>
				</div>
				<br style="clear: both;">
			</form>
		</td>
	</tr>
</table>