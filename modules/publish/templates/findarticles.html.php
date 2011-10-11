<?php

	include_template('publish/wikibreadcrumbs');
	TBGContext::loadLibrary('publish/publish');
	$tbg_response->setTitle($article_name);

?>
<table style="margin-top: 0px; table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<td class="side_bar">
			<?php include_component('leftmenu', array('article' => $article)); ?>
		</td>
		<td class="main_area article">
			<a name="top"></a>
			<?php if (isset($error) && $error): ?>
				<div class="rounded_box red borderless" style="margin: 0 0 5px 0; padding: 8px; font-size: 14px; color: #FFF;">
					<?php echo $error; ?>
				</div>
			<?php endif; ?>
			<?php if (isset($message) && $message): ?>
				<div class="rounded_box green borderless" style="margin: 0 0 5px 5px; padding: 8px; font-size: 14px;">
					<b><?php echo $message; ?></b>
				</div>
			<?php endif; ?>
			<h2><?php echo __('Find articles'); ?></h2>
			<form action="<?php echo (TBGContext::isProjectContext()) ? make_url('publish_find_project_articles', array('project_key' => TBGContext::getCurrentProject()->getKey())) : make_url('publish_find_articles'); ?>" method="get" accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>">
				<label><?php echo __('Find articles by name'); ?>:<input type="text" name="articlename" value="<?php echo $articlename; ?>" style="width: 400px; margin-left: 5px; padding: 4px; font-size: 1.3em;"></label>
				<input type="submit" value="<?php echo __('Find'); ?>" style="font-size: 1.3em; padding: 3px;">
			</form>
			<br style="clear: both;">
			<?php if (isset($resultcount)): ?>
				<?php if ($resultcount): ?>
					<div class="header_div" style="font-size: 1.3em;"><?php echo __('Found %num% article(s)', array('%num%' => $resultcount)); ?></div>
					<ul class="simple_list">
						<?php foreach ($articles as $article): ?>
						<li style="margin-bottom: 0;">
							<?php echo link_tag(make_url('publish_article', array('article_name' => $article->getName())), $article->getName(), array('style' => 'font-size: 1.1em;')); ?><br>
							<div class="faded_out"><?php echo __('Last updated %updated_at%', array('%updated_at%' => tbg_formatTime($article->getLastUpdatedDate(), 6))); ?></div>
							<div class="article_preview">
								<?php echo tbg_parse_text(mb_substr($article->getContent(), 0, 300), false, null, array('headers' => false, 'show_title' => false, 'show_details' => false, 'show_actions' => false, 'embedded' => true)); ?>
							</div>
						</li>
						<?php endforeach; ?>
					</ul>
				<?php else: ?>
					<div class="faded_out" style="font-size: 1.3em;"><?php echo __('No articles found'); ?></div>
				<?php endif; ?>
			<?php else: ?>
				<div class="faded_out" style="font-size: 1.3em;"><?php echo __('Enter something to search for in the input box above'); ?></div>
			<?php endif; ?>
		</td>
	</tr>
</table>