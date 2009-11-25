<?php BUGScontext::loadLibrary('publish/publish'); ?>
<table style="margin-top: 0px; table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<td class="left_bar" style="width: 250px;">
			<?php include_component('leftmenu', array('article' => $article)); ?>
		</td>
		<td class="main_area article edit">
			<a name="top"></a>
			<?php if (isset($error)): ?>
				<div class="rounded_box red_borderless" style="margin: 0 5px 5px 5px;">
					<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
					<div class="xboxcontent" style="padding: 3px; font-size: 14px; color: #FFF;">
						<?php echo $error; ?>
					</div>
					<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
				</div>
			<?php endif; ?>
			<div class="rounded_box iceblue_borderless" style="margin: 0 5px 5px 5px;">
				<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
				<div class="xboxcontent faded_dark" style="padding: 3px; font-size: 14px;">
					<?php echo __('If you cannot see the "%save%"-button, scroll all the way down', array('%save%' => __('Save'))); ?>
				</div>
				<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
			</div>
			<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="<?php echo make_url('publish_article_edit', array('article_name' => $article_name)); ?>" method="post"
				<input type="hidden" name="preview" value="0">
				<input type="hidden" name="article_id" value="<?php echo ($article instanceof PublishArticle) ? $article->getID() : 0; ?>">
				<input type="hidden" name="last_modified" value="<?php echo ($article instanceof PublishArticle) ? $article->getPostedDate() : 0; ?>">
				<table style="margin-right: 5px;" class="padded_table" cellpadding=0 cellspacing=0>
					<tr>
						<td style="padding: 5px;"><label for="article_name"><?php echo __('Article name'); ?></label></td>
						<td>
							<input type="text" name="new_article_name" id="article_name" value="<?php echo $article_name; ?>" style="width: 250px;">
							&nbsp;<span style="font-size: 13px;" class="faded_medium"><?php echo __('This is the name you use when you link to this article'); ?></span>
						</td>
					</tr>
				</table>
				<br style="clear: both;">
				<label for="article_content" style="margin-left: 5px; clear: both;"><?php echo __('Article content'); ?></label><br>
				<div style="margin: 5px 10px 5px 5px;">
					<textarea id="article_content" name="new_article_content" style="width: 100%; margin: 0; font-size: 12px; height: 350px;"><?php echo $article_content; ?></textarea>
				</div>
				<div class="faded_dark" style="padding: 5px; font-size: 13px;"><?php echo __('For help and tips on how to format your article, see %wiki_formatting%', array('%wiki_formatting%' => link_tag(make_url('publish_article', array('article_name' => 'WikiFormatting')), 'WikiFormatting', array('target' => '_new')))); ?></div>
				<div class="publish_article_actions">
					<div class="sub_header"><?php echo __('Actions available'); ?></div>
					<input type="submit" value="<?php echo ($article instanceof PublishArticle) ? __('Save changes') : __('Create article'); ?>" style="float: left;">
					<?php echo link_tag((($article instanceof PublishArticle) ? make_url('publish_article', array('article_name' => $article_name)) : make_url('publish')), __('Cancel'), array('style' => 'float: left; font-size: 13px; margin: 5px 0 0 10px;')); ?>
				</div>
				<br style="clear: both;">
			</form>
		</td>
	</tr>
</table>