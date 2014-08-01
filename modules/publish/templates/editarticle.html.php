<?php 

	include_template('publish/wikibreadcrumbs', array('article_name' => $article_name, 'edit' => true));
	TBGContext::loadLibrary('publish/publish');
	$tbg_response->setTitle(__('Editing %article_name', array('%article_name' => $article_name)));

?>
<table style="margin-top: 0px; table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<td class="side_bar">
			<?php include_component('leftmenu', array('article' => $article)); ?>
		</td>
		<td class="main_area article edit at-<?php echo ($article->getArticleType() == TBGWikiArticle::TYPE_WIKI) ? 'wiki' : 'manual'; ?>" id="article-editor-main-container">
			<a name="top"></a>
			<?php if (isset($error)): ?>
				<div class="redbox" style="margin: 0 5px 5px 5px; font-size: 14px;">
					<?php echo $error; ?>
				</div>
			<?php endif; ?>
			<a name="edit_article"></a>
			<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url($article_route, $article_route_params); ?>" method="post" id="edit_article_form" onsubmit="Event.stopObserving(window, 'beforeunload');var isvisible = $('change_reason_container').visible() || $('article_preview').value == 1;$('change_reason_container').show();$('change_reason').focus();return isvisible;">
				<?php include_template('publish/header', array('article' => $article, 'show_actions' => true, 'mode' => 'edit')); ?>
				<?php if (isset($preview) && $preview): ?>
					<div class="rounded_box yellow borderless" style="margin: 0 5px 5px 5px; padding: 7px; font-size: 14px;">
						<?php echo __('This is a preview of the article'); ?><br>
						<b><?php echo __('The article has not been saved yet'); ?>&nbsp;&nbsp;</b>[<a href="#edit_article" onclick="$('article_content').focus();"><?php echo __('Continue editing'); ?></a>]
					</div>
					<?php include_component('articledisplay', array('article' => $article, 'show_article' => $preview, 'show_category_contains' => false, 'show_actions' => true, 'mode' => 'edit')); ?>
				<?php endif; ?>
				<input type="hidden" name="preview" value="0" id="article_preview">
				<input type="hidden" name="article_id" value="<?php echo ($article instanceof TBGWikiArticle) ? $article->getID() : 0; ?>">
				<input type="hidden" name="last_modified" value="<?php echo ($article instanceof TBGWikiArticle) ? $article->getPostedDate() : 0; ?>">
				<div class="editor_header">
					<table style="border: 0;" class="padded_table" cellpadding=0 cellspacing=0>
						<tr>
							<td><label for="article_type" id="article-type-label"><?php echo __('Article type'); ?></label></td>
							<td style="position: relative;">
								<input id="article-type-input" type="hidden" name="article_type" value="<?php echo $article->getArticleType(); ?>">
								<span id="article-type-name" class="<?php if (!$article->getParentArticle() instanceof TBGWikiArticle) echo ' changeable'; ?>"><?php echo ($article->getArticleType() == TBGWikiArticle::TYPE_WIKI) ? __('Classic wiki page') : __('Page in a handbook'); ?></span>
								<?php if (!$article->getParentArticle() instanceof TBGWikiArticle): ?>
									<ul class="article-type-selector" id="article-type-selector">
										<li data-article-type="<?php echo TBGWikiArticle::TYPE_WIKI; ?>" data-class-name="at-wiki" class="article-type <?php if ($article->getArticleType() == TBGWikiArticle::TYPE_WIKI) echo 'selected'; ?>">
											<h1><?php echo __('Classic wiki page'); ?></h1>
											<?php echo image_tag('icon-article-type-wiki.png'); ?>
											<p>
												<?php echo __('Choose this article type for pages that are organised like a traditional wiki. These pages will have be loosely coupled by links, and have a classic wiki sidebar.'); ?>
											</p>
										</li>
										<li data-article-type="<?php echo TBGWikiArticle::TYPE_MANUAL; ?>" data-class-name="at-manual" class="article-type <?php if ($article->getArticleType() == TBGWikiArticle::TYPE_MANUAL) echo 'selected'; ?>">
											<h1><?php echo __('Page in a handbook'); ?></h1>
											<?php echo image_tag('icon-article-type-manual.png'); ?>
											<p>
												<?php echo __('Choose this article type to group pages together in a handbook. These pages lets you create chapters and sub-pages, and have a sidebar that lets you navigate through the handbook.'); ?>
											</p>
										</li>
									</ul>
								<?php endif; ?>
							</td>
						</tr>
						<tbody id="article_parent_container" style="<?php if ($article->getArticleType() != TBGWikiArticle::TYPE_MANUAL) echo 'display: none;'; ?>">
							<?php /*<tr>
								<td style="padding: 5px;"><label for="manual_name"><?php echo __('Manual entry name'); ?></label></td>
								<td>
									<input type="text" name="manual_name" id="manual_name" value="<?php echo $article->getManualName(); ?>" style="width: 400px;"><br>
								</td>
							</tr> */ ?>
						</tbody>
					</table>
				</div>
				<br style="clear: both;">
				<div class="editor_container">
					<?php include_template('main/textarea', array('area_name' => 'article_content', 'area_id' => 'article_content', 'syntax' => $article->getContentSyntax(), 'markuppable' => !($article->getContentSyntax(true) == TBGSettings::SYNTAX_PT), 'height' => '500px', 'width' => '100%', 'value' => htmlspecialchars($article->getContent()))); ?>
				</div>
				<div id="change_reason_container" class="fullpage_backdrop" style="display: none;">
					<div class="backdrop_box large">
						<div class="backdrop_detail_header"><?php echo __('Saving article'); ?></div>
						<div class="backdrop_detail_content">
							<label for="change_reason" style="margin-left: 5px; clear: both;"><?php echo __('Change reason'); ?>
								<?php if (TBGPublish::getModule()->getSetting('require_change_reason') == 0) : ?>
									&nbsp;&nbsp;<span class="faded_out" style="font-weight: normal; font-size: 0.9em;"><?php echo __('Optional'); ?></span>
								<?php endif; ?>
							</label><br>
							<div style="margin: 5px 15px 5px 5px;">
								<input type="text" name="change_reason" id="change_reason" maxlength="255" value="<?php if (isset($change_reason)) echo $change_reason; ?>"><br>
							</div>
							<div class="faded_out dark" style="padding: 5px 5px 15px 5px; font-size: 13px;"><?php echo __('Enter a short reason summarizing your changes (max. 255 characters)'); ?></div>
							<div class="change_reason_actions">
								<a href="javascript:void(0);" onclick="$('change_reason_container').hide();"><?php echo __('Cancel'); ?></a>
								<input class="button button-green" type="submit" value="<?php echo ($article instanceof TBGWikiArticle) ? __('Save changes') : __('Create article'); ?>">
							</div>
						</div>
					</div>
				</div>
				<div class="publish_article_actions">
					<?php if ($article->getID()): ?>
						<?php echo link_tag((($article instanceof TBGWikiArticle) ? make_url('publish_article', array('article_name' => $article_name)) : make_url('publish')), __('Cancel')); ?>
					<?php endif; ?>
					<input class="button button-silver" type="submit" onclick="$('article_preview').value = 1;" value="<?php echo ($article instanceof TBGWikiArticle) ? __('Preview changes') : __('Preview article'); ?>">
					<input class="button button-green" type="submit" value="<?php echo ($article instanceof TBGWikiArticle) ? __('Save changes') : __('Create article'); ?>">
				</div>
			</form>
			<div id="parent_selector_container" class="fullpage_backdrop" style="display: none;" data-callback-url="<?php echo make_url('publish_article_parents', array('article_name' => $article->getName())); ?>">
				<div class="backdrop_box medium">
					<div class="backdrop_detail_header"><?php echo __('Select parent article'); ?></div>
					<div class="backdrop_detail_content">
						<?php /* <input type="search" name="parent_article_name"> */ ?>
						<?php echo image_tag('spinning_32.gif', array('id' => 'parent_selector_container_indicator')); ?>
						<ul id="parent_articles_list"></ul>
						<div class="change_reason_actions">
							<a href="javascript:void(0);" onclick="$('parent_selector_container').hide();"><?php echo __('Cancel'); ?></a>
						</div>
					</div>
				</div>
			</div>
			<input type="hidden" id="article_serialized" value="">
		</td>
	</tr>
</table>
<script type="text/javascript">
	document.observe('dom:loaded', function() {
		$('article_serialized').value = $('article_content').serialize();
	});

	$("article-type-name").on("click", function(e) {
		$(this).toggleClassName('selected');
	});
	$("article-type-label").on("click", function(e) {
		$("article-type-name").toggleClassName('selected');
	});

	if ($("article-type-selector") != undefined) {
		$("article-type-selector").select('li').each(function (el) {
			el.on("click", function(e) {
				var article_type = $(this).dataset.articleType;
				$(this).up('ul').select('li').each(function(elm) { elm.removeClassName('selected'); $('article-editor-main-container').removeClassName(elm.dataset.className); });
				$(this).addClassName('selected');
				$('article-editor-main-container').addClassName($(this).dataset.className);
				$('article-type-input').setValue(article_type);
				$('article-type-name').update($(this).down('h1').innerHTML);
				$('article-type-name').toggleClassName('selected');
			});
		});
	}

Event.observe(window, 'beforeunload', function(event) {
	if ($('article_content').serialize() != $F('article_serialized'))
	{
		event.stop();
	}
});
	
</script>
