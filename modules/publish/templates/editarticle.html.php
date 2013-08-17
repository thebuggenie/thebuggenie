<?php 

	include_template('publish/wikibreadcrumbs', array('article_name' => $article_name, 'edit' => true));
	TBGContext::loadLibrary('publish/publish');
	$tbg_response->setTitle(__('Editing %article_name%', array('%article_name%' => $article_name)));

?>
<table style="margin-top: 0px; table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<td class="side_bar">
			<?php include_component('leftmenu', array('article' => $article)); ?>
		</td>
		<td class="main_area article edit">
			<a name="top"></a>
			<?php if (isset($error)): ?>
				<div class="redbox" style="margin: 0 5px 5px 5px; font-size: 14px;">
					<?php echo $error; ?>
				</div>
			<?php endif; ?>
			<?php include_template('publish/header', array('article' => $article, 'show_actions' => true, 'mode' => 'edit')); ?>
			<?php if (isset($preview) && $preview): ?>
				<div class="rounded_box yellow borderless" style="margin: 0 5px 5px 5px; padding: 7px; font-size: 14px;">
					<?php echo __('This is a preview of the article'); ?><br>
					<b><?php echo __('The article has not been saved yet'); ?>&nbsp;&nbsp;</b>[<a href="#edit_article" onclick="$('article_content').focus();"><?php echo __('Continue editing'); ?></a>]
				</div>
				<?php include_component('articledisplay', array('article' => $article, 'show_article' => $preview, 'show_category_contains' => false, 'show_actions' => true, 'mode' => 'edit')); ?>
			<?php endif; ?>
			<a name="edit_article"></a>
			<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('publish_article_edit', array('article_name' => $article_name)); ?>" method="post" id="edit_article_form" onsubmit="Event.stopObserving(window, 'beforeunload');">
				<input type="hidden" name="preview" value="0" id="article_preview">
				<input type="hidden" name="article_id" value="<?php echo ($article instanceof TBGWikiArticle) ? $article->getID() : 0; ?>">
				<input type="hidden" name="last_modified" value="<?php echo ($article instanceof TBGWikiArticle) ? $article->getPostedDate() : 0; ?>">
				<div class="greenbox" style="margin: 5px;">
					<table style="border: 0;" class="padded_table" cellpadding=0 cellspacing=0>
						<tr>
							<td style="padding: 5px;"><label for="new_article_name"><?php echo __('Article name'); ?></label></td>
							<td>
								<input type="text" name="new_article_name" id="new_article_name" value="<?php echo $article->getName(); ?>" style="width: 400px;">
							</td>
						</tr>
						<tr>
							<td style="padding: 5px;"><label for="article_type"><?php echo __('Article type'); ?></label></td>
							<td>
								<select name="article_type" id="article_type" style="width: 200px;" onchange="if ($(this).getValue() == <?php echo TBGWikiArticle::TYPE_WIKI; ?>) { $('article_parent_container').hide(); } else { $('article_parent_container').show(); }">
									<option value="<?php echo TBGWikiArticle::TYPE_WIKI; ?>" <?php if ($article->getArticleType() == TBGWikiArticle::TYPE_WIKI) echo 'selected'; ?>><?php echo __('Classic wiki'); ?></option>
									<option value="<?php echo TBGWikiArticle::TYPE_MANUAL; ?>" <?php if ($article->getArticleType() == TBGWikiArticle::TYPE_MANUAL) echo 'selected'; ?>><?php echo __('Manual style'); ?></option>
								</select>
							</td>
						</tr>
						<tr id="article_parent_container" style="<?php if ($article->getArticleType() != TBGWikiArticle::TYPE_MANUAL) echo 'display: none;'; ?>">
							<td style="padding: 5px;"><label for="parent_article_name"><?php echo __('Parent article'); ?></label></td>
							<td>
								<input type="text" name="parent_article_name" id="parent_article_name" value="<?php echo $article->getParentArticleName(); ?>" style="width: 400px;"><br>
							</td>
						</tr>
					</table>
				</div>
				<br style="clear: both;">
				<label for="article_content" style="margin-left: 5px; clear: both;"><?php echo __('Article content'); ?></label><br>
				<div style="margin: 5px 10px 5px 5px;">
					<?php include_template('main/textarea', array('area_name' => 'article_content', 'area_id' => 'article_content', 'syntax' => $article->getContentSyntax(), 'height' => '350px', 'width' => '100%', 'value' => htmlspecialchars($article->getContent()))); ?>
				</div>
				<label for="change_reason" style="margin-left: 5px; clear: both;"><?php echo __('Change reason'); ?>
					<?php if (TBGPublish::getModule()->getSetting('require_change_reason') == 0) : ?>
						&nbsp;&nbsp;<span class="faded_out" style="font-weight: normal; font-size: 0.9em;"><?php echo __('Optional'); ?></span>
					<?php endif; ?>
				</label><br>
				<div style="margin: 5px 15px 5px 5px;">
					<input type="text" name="change_reason" id="change_reason" style="width: 100%;" maxlength="255" value="<?php if (isset($change_reason)) echo $change_reason; ?>"><br>
				</div>
				<div class="faded_out dark" style="padding: 5px 5px 15px 5px; font-size: 13px;"><?php echo __('Enter a short reason summarizing your changes (max. 255 characters)'); ?></div>
				<div class="rounded_box lightgrey borderless" style="margin: 0 5px 5px 5px; padding: 7px; min-height: 27px;">
					<div class="publish_article_actions">
						<div class="sub_header"><?php echo __('Actions available'); ?></div>
						<input class="button button-green" type="submit" value="<?php echo ($article instanceof TBGWikiArticle) ? __('Save changes') : __('Create article'); ?>">
						<input class="button button-blue" type="submit" onclick="$('article_preview').value = 1;" value="<?php echo ($article instanceof TBGWikiArticle) ? __('Preview changes') : __('Preview article'); ?>">
						<?php echo link_tag((($article instanceof TBGWikiArticle) ? make_url('publish_article', array('article_name' => $article_name)) : make_url('publish')), __('Cancel'), array('class' => 'button button-silver')); ?>
					</div>
				</div>
				<br style="clear: both;">
			</form>
			<input type="hidden" id="article_serialized" value="">
		</td>
	</tr>
</table>
<script type="text/javascript">
document.observe('dom:loaded', function() {
	$('article_serialized').value = $('article_content').serialize();
});
	
Event.observe(window, 'beforeunload', function(event) {
	if ($('article_content').serialize() != $F('article_serialized'))
	{
		event.stop();
	}
});
	
</script>
