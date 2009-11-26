<div class="rounded_box borderless" id="wiki_menu" style="margin: 10px 0 5px 5px;">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="padding: 5px;">
		<div class="header"><?php echo __('Wiki menu'); ?></div>
		<div class="content">
			<ul>
				<li>
					<?php if (!$article instanceof PublishArticle || $article->getName() != 'MainPage'): ?>
						<?php echo link_tag(make_url('publish_article', array('article_name' => 'MainPage')), __('Wiki Frontpage')); ?>
					<?php else: ?>
						<b><?php echo __('Wiki Frontpage'); ?></b>
					<?php endif; ?>
				</li>
				<?php if (BUGScontext::getCurrentProject() instanceof BUGSproject): ?>
					<li>
						<?php echo link_tag(make_url('publish_article', array('article_name' => ucfirst(BUGScontext::getCurrentProject()->getKey()) .':MainPage')), __('Project Wiki Frontpage')); ?>
					</li>
				<?php endif; ?>
				<li>
					<?php if (!$article instanceof PublishArticle || $article->getName() != 'WikiFormatting'): ?>
						<?php echo link_tag(make_url('publish_article', array('article_name' => 'WikiFormatting')), __('Formatting help')); ?>
					<?php else: ?>
						<b><?php echo __('Formatting help'); ?></b>
					<?php endif; ?>
				</li>
				<?php if (count($links) > 0): ?>
					<?php foreach ($links as $link): ?>
						<?php if ($link['url'] == ''): ?>
							<li>&nbsp;</li>
						<?php else: ?>
							<li style="font-size: 12px;"><a href="<?php echo $link['url']; ?>" title="<?php echo $link['url']; ?>"><?php echo $link['description']; ?></a></li>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</ul>
		</div>
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>
<?php if ($article instanceof PublishArticle): ?>
	<div style="margin: 10px 0 5px 5px;">
	<div class="left_menu_header"><?php echo __('Links to this article'); ?></div>
		<?php if (count($whatlinkshere) > 0): ?>
			<ul class="article_list">
				<?php foreach ($whatlinkshere as $linking_article): ?>
					<li>
						<div>
							<?php echo image_tag('news_item.png', array('style' => 'float: left;'), false, 'publish'); ?>
							<?php echo link_tag(make_url('publish_article', array('article_name' => $linking_article->getName())), get_spaced_name($linking_article->getTitle())); ?>
							<br>
							<span><?php print bugs_formatTime($linking_article->getPostedDate(), 3); ?></span>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php else: ?>
			<span class="left_menu_content faded_medium"><?php echo __("No other articles links to this article"); ?></span>
		<?php endif; ?>
	</div>
<?php endif; ?>
<div style="margin: 10px 0 5px 5px;">
<div class="left_menu_header"><?php echo __('Your drafts'); ?></div>
	<?php if (count($user_drafts) > 0): ?>
		<ul class="article_list">
			<?php foreach ($user_drafts as $article): ?>
			<li><?php echo link_tag(make_url('publish_article', array('article_name' => $article->getName())), get_spaced_name($article->getTitle())); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<span class="faded_medium" style="padding-left: 5px; font-size: 12px;"><?php echo __("You don't have any unpublished pages"); ?></span>
	<?php endif; ?>
</div>