<?php TBGContext::loadLibrary('publish/publish'); ?>
<table style="margin-top: 0px; table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<td class="left_bar" style="width: 310px;">
			<?php include_component('leftmenu', array('article' => $article)); ?>
		</td>
		<td class="main_area article edit">
			<a name="top"></a>
			<?php if (isset($error)): ?>
				<div class="rounded_box red borderless" style="margin: 0 5px 5px 5px; padding: 7px; font-size: 14px; color: #FFF;">
					<?php echo $error; ?>
				</div>
			<?php endif; ?>
			<?php if ($preview): ?>
				<div class="rounded_box yellow borderless" style="margin: 0 5px 5px 5px; padding: 7px; font-size: 14px;">
					<?php echo __('This is a preview of the article'); ?><br>
					<b><?php echo __('The article has not been saved yet'); ?>&nbsp;&nbsp;</b>[<a href="#edit_article" onclick="$('article_content').focus();"><?php echo __('Continue editing'); ?></a>]
				</div>
			<?php endif; ?>
			<?php if ($preview && $article instanceof TBGWikiArticle): ?>
				<?php include_component('articledisplay', array('article' => $article, 'show_category_contains' => false, 'show_actions' => false)); ?>
			<?php endif; ?>
			<a name="edit_article"></a>
			<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('publish_article_edit', array('article_name' => $article_name)); ?>" method="post">
				<input type="hidden" name="preview" value="0" id="article_preview">
				<input type="hidden" name="article_id" value="<?php echo ($article instanceof TBGWikiArticle) ? $article->getID() : 0; ?>">
				<input type="hidden" name="last_modified" value="<?php echo ($article instanceof TBGWikiArticle) ? $article->getPostedDate() : 0; ?>">
				<div class="rounded_box green borderless" style="margin: 5px;">
					<table style="border: 0;" class="padded_table" cellpadding=0 cellspacing=0>
						<tr>
							<td style="padding: 5px;"><label for="article_name"><?php echo __('Article name'); ?></label></td>
							<td>
								<input type="text" name="new_article_name" id="article_name" value="<?php echo $article_name; ?>" style="width: 250px;">
								&nbsp;<span style="font-size: 13px;" class="faded_medium"><?php echo __('This is the name you use when you link to this article'); ?></span>
							</td>
						</tr>
					</table>
				</div>
				<br style="clear: both;">
				<label for="article_content" style="margin-left: 5px; clear: both;"><?php echo __('Article content'); ?></label><br>
				<div style="margin: 5px 10px 5px 5px;">
					<textarea id="article_content" name="new_article_content" style="width: 100%; margin: 0; font-size: 12px; height: 350px;"><?php echo htmlspecialchars($article_content); ?></textarea>
				</div>
				<div class="faded_dark" style="padding: 5px; font-size: 13px;"><?php echo __('For help and tips on how to format your article, see %wiki_formatting%', array('%wiki_formatting%' => link_tag(make_url('publish_article', array('article_name' => 'TheBugGenie:WikiFormatting')), 'WikiFormatting', array('target' => '_new')))); ?></div>
				<label for="change_reason" style="margin-left: 5px; clear: both;"><?php echo __('Change reason'); ?></label><br>
				<div style="margin: 5px 15px 5px 5px;">
					<input type="text" name="change_reason" id="change_reason" style="width: 100%;" maxlength="255" value="<?php echo $change_reason; ?>"><br>
				</div>
				<div class="faded_dark" style="padding: 5px 5px 15px 5px; font-size: 13px;"><?php echo __('Enter a short reason summarizing your changes (max. 255 characters)'); ?></div>
				<div class="rounded_box lightgrey borderless" style="margin: 0 5px 5px 5px; padding: 7px; min-height: 27px;">
					<div class="publish_article_actions">
						<div class="sub_header"><?php echo __('Actions available'); ?></div>
						<input type="submit" value="<?php echo ($article instanceof TBGWikiArticle) ? __('Save changes') : __('Create article'); ?>" style="float: left;">
						<input type="submit" onclick="$('article_preview').value = 1;" value="<?php echo ($article instanceof TBGWikiArticle) ? __('Preview changes') : __('Preview article'); ?>" style="float: left; margin-left: 10px;">
						<?php echo link_tag((($article instanceof TBGWikiArticle) ? make_url('publish_article', array('article_name' => $article_name)) : make_url('publish')), __('Cancel'), array('style' => 'float: left; font-size: 13px; margin: 5px 0 0 10px;')); ?>
					</div>
				</div>
				<br style="clear: both;">
			</form>
		</td>
	</tr>
</table>