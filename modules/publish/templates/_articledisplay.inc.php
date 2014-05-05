<?php TBGContext::loadLibrary('publish/publish'); ?>
<div class="article">
	<?php if ($show_title): ?>
		<?php include_template('publish/header', array('article_name' => $article->getName(), 'article' => $article, 'show_actions' => $show_actions, 'mode' => $mode)); ?>
	<?php endif; ?>
	<?php if (!$embedded && $article->canDelete()): ?>
		<?php echo javascript_link_tag(__('Delete this article'), array('class' => 'button button-red', 'style' => 'float: right;', 'onclick' => "TBG.Main.Helpers.Dialog.show('".__('Please confirm')."', '".__('Do you really want to delete this article?')."', {yes: {click: function() {TBG.Main.Helpers.ajax('".make_url('publish_article_delete', array('article_name' => $article->getName()))."', {method: 'post'}); TBG.Main.Helpers.Dialog.dismiss(); }}, no: {click: TBG.Main.Helpers.Dialog.dismiss}})")); ?>
	<?php endif; ?>
	<?php if ($show_details && $show_article): ?>
		<div class="details">
			<?php if (isset($redirected_from)): ?>
				<div class="redirected_from">&rarr; <?php echo __('Redirected from %article_name%', array('%article_name%' => link_tag(make_url('publish_article_edit', array('article_name' => $redirected_from)), $redirected_from))); ?></div>
			<?php endif; ?>	
<?php // DO NOT SHOW AUTHOR AND UPDATE INFO IF USER IS NOT LOGGED IN ?>
<?php if (!$tbg_user->isGuest()): ?>
<?php // CHANGE WORDING Last updated at TO Updated ?>			
			<?php echo __('Updated %time%, by %user%', array('%time%' => tbg_formatTime($article->getPostedDate(), 3), '%user%' => '<b>'.(($article->getAuthor() instanceof TBGIdentifiable) ? '<a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.show(\'' . make_url('get_partial_for_backdrop', array('key' => 'usercard', 'user_id' => $article->getAuthor()->getID())) . '\');" class="faded_out">' . $article->getAuthor()->getName() . '</a>' : __('System')).'</b>')); ; ?>
<?php endif; ?></div>
	<?php endif; ?>
	<?php if ($show_article): ?>
		<div class="content"><?php echo tbg_parse_text($article->getContent(), true, $article->getID(), array('embedded' => $embedded, 'article' => $article)); ?></div>
	<?php endif; ?>
</div>

<?php	
//Removed category code lines 21 to 66 in version 3.2.7.1 for wiki pages so user does not see grey box before comments section telling them the article is not in any categories. Leave this in if you plan on using the category feature.  I am building such links into my wikipages and find the category feature to be just another navigation interface that might confuse users.
?>