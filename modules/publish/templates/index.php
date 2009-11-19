<table style="margin-top: 0px; table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<td style="width: 255px;" valign="top">
			<?php
			
			if (BUGScontext::getUser()->hasPermission('article_management', 0, 'publish'))
			{
				?>
				<table class="b2_section_miniframe" cellpadding=0 cellspacing=0>
					<tr>
						<td class="b2_section_miniframe_header"><?php echo __('Publishing actions'); ?></td>
					</tr>
					<tr>
						<td class="td1">
							<div style="padding-bottom: 5px;"><?php echo __('As a publisher, you can perform different actions related to article management.'); ?><br>
							<br>
							<table cellpadding=0 cellspacing=0>
								<tr>
									<td style="width: 20px;"><?php echo image_tag('publish/icon_new_article.png') ?></td>
									<td><a href="articles.php?create_new=true&amp;article_type=1&amp;edit=true"><?php echo __('Create new article'); ?></a></td>
								</tr>
								<tr>
									<td style="width: 20px;"><?php echo image_tag('publish/icon_new_news.png') ?></td>
									<td><a href="articles.php?create_new=true&amp;article_type=2&amp;edit=true"><?php echo __('Create new news headline'); ?></a></td>
								</tr>
								<tr>
									<td style="width: 20px;"><?php echo image_tag('publish/icon_new_link.png') ?></td>
									<td><a href="articles.php?create_new=true&amp;article_type=3&amp;edit=true"><?php echo __('Create new link to webpage'); ?></a></td>
								</tr>
								<tr><td colspan=2 style="padding-top: 10px; padding-bottom: 15px;"><?php echo __('To make an article a "Featured article" &ndash; which makes it appear on the top of this page &ndash; use the left-hand menu when viewing the article.'); ?></td></tr>
								<?php 
								
								if (BUGScontext::getUser()->hasPermission('article_management', 0, 'publish') && BUGScontext::getRequest()->getParameter('manage'))
								{
									?>
									<tr>
										<td style="width: 20px;"><?php echo image_tag('tab_publish.png') ?></td>
										<td><a href="publish.php"><b><?php echo __('Back to News &amp; Articles center'); ?></b></a></td>
									</tr>
									<?php 
								}
								else
								{
									?>
									<tr>
										<td style="width: 20px;"><?php echo image_tag('publish/icon_manage.png') ?></td>
										<td><a href="publish.php?manage=true"><?php echo __('Manage all articles'); ?></a></td>
									</tr>
									<?php 
								}

								?>
							</table>
							</div>
						</td>
					</tr>
				</table>
				<?php 
				
				if (BUGScontext::getModule('publish')->getSetting('enablebillboards') == 1)
				{
					?> 
					<table class="b2_section_miniframe" cellpadding=0 cellspacing=0>
						<tr>
							<td class="b2_section_miniframe_header"><?php echo __('Billboard'); ?></td>
						</tr>
						<tr>
							<td class="td1">
								<?php echo __('The billboard is a place where users and developers can share ideas, links or interesting articles.'); ?>
								<table cellpadding=0 cellspacing=0 style="margin-top: 10px;">
									<tr>
										<td style="width: 20px; padding: 2px;"><?php echo image_tag('publish/icon_manage.png'); ?></td>
										<td style="width: auto; padding: 2px;"><b><a href="<?php echo BUGScontext::getTBGPath(); ?>modules/publish/billboard.php"><?php echo __('Show billboard(s)'); ?></a></b></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					<?php 
				}
			}
						
			BUGScontext::getModule('publish')->listen_latestArticles();
			
			?>
		</td>
		<td valign="top" align="left" style="padding-right: 10px; padding-top: 10px;">
		<?php 
		
		if (BUGScontext::getRequest()->getParameter('manage') && BUGScontext::getUser()->hasPermission('article_management', 0, 'publish'))
		{
			?><div style="background-color: #EEE; padding: 5px; font-weight: bold; font-size: 13px;"><?php echo __('All articles'); ?></div>
			<div style="padding: 3px; padding-bottom: 10px;"><?php echo __('This is where you can manage articles. Use the actions icons to change "published" status, and whether to show them in the "latest news" column. You can also drag articles up and down to change their order.'); ?><span id="article_order_saved" style="font-weight: bold; display: none;"><?php echo __('Article order saved!'); ?></span></div>
			<table style="width: 100%;" cellpadding=0 cellspacing=0>
				<tr>
					<td style="background-color: #F5F5F5; padding: 3px; font-weight: bold; font-size: 10px; width: 100px;"><?php echo __('Actions'); ?></td>
					<td style="background-color: #F5F5F5; padding: 3px; font-weight: bold; font-size: 10px; width: auto;"><?php echo __('Title'); ?></td>
					<td style="background-color: #F5F5F5; padding: 3px; font-weight: bold; font-size: 10px; width: 120px;"><?php echo __('Article type'); ?></td>
					<td style="background-color: #F5F5F5; padding: 3px; text-align: center; font-weight: bold; font-size: 10px; width: 80px;"><?php echo __('Published'); ?></td>
					<td style="background-color: #F5F5F5; padding: 3px; text-align: center; font-weight: bold; font-size: 10px; width: 80px;"><?php echo __('Show in news'); ?></td>
					<td style="background-color: #F5F5F5; padding: 3px; font-weight: bold; font-size: 10px; width: 110px;"><?php echo __('Last updated'); ?></td>
				</tr>
			</table>
			<ul id="article_list">
				<?php 
				
				foreach (BUGScontext::getModule('publish')->getAllArticles() as $article)
				{
					?>
					<li id="article_list_<?php echo $article->getID(); ?>">
					<table style="width: 100%;" cellpadding=0 cellspacing=0>
					<tr>
						<td style="border-bottom: 1px solid #DDD; padding: 3px; width: 100px;">
							<a class="image" href="articles.php?article_id=<?php echo $article->getID(); ?>&amp;edit=true"><?php echo image_tag('icon_edit.png', '', __('Edit this article'), __('Edit this article')); ?></a>
							&nbsp;
							<?php 
							
							if ($article->isPublished())
							{
								?>
								<a class="image" href="publish.php?manage=true&amp;article_id=<?php echo $article->getID(); ?>&amp;retract=true"><?php echo image_tag('publish/unpublish.png', '', __('Retract this article'), __('Retract this article')); ?></a>
								<?php 
							}
							else
							{
								?>
								<a class="image" href="publish.php?manage=true&amp;article_id=<?php echo $article->getID(); ?>&amp;publish=true"><?php echo image_tag('publish/publish.png', '', __('Publish this article'), __('Publish this article')); ?></a>
								<?php 
							}

							?>
							&nbsp;
							<?php 
							
							if ($article->isNews())
							{
								?>
								<a class="image" href="publish.php?manage=true&amp;article_id=<?php echo $article->getID(); ?>&amp;hide=true"><?php echo image_tag('publish/icon_hidefromnews.png', '', __('Hide from news list'), __('Hide from news list')); ?></a>
								<?php 
							}
							else
							{
								?>
								<a class="image" href="publish.php?manage=true&amp;article_id=<?php echo $article->getID(); ?>&amp;show=true"><?php echo image_tag('publish/icon_showinnews.png', '', __('Show in news list'), __('Show in news list')); ?></a>
								<?php 
							}

							?>
						</td>
						<td style="border-bottom: 1px solid #DDD; padding: 3px; width: auto;">
						<?php 
						
						if ($article->getArticleType() == PublishArticle::ARTICLE_NORMAL)
						{
							?>
							<a href="articles.php?article_id=<?php echo $article->getID(); ?>"><?php echo $article->getTitle(); ?></a>
							<?php 
						}
						else
						{
							echo $article->getTitle();
						}
						
						?>
						&nbsp;</td>
						<td style="border-bottom: 1px solid #DDD; padding: 3px; width: 120px;">
						<?php 

						switch ($article->getArticleType())
						{
							case PublishArticle::ARTICLE_NORMAL:
								echo 'Normal article';
								break;
							case PublishArticle::ARTICLE_NEWS:
								echo 'News headline';
								break;
							case PublishArticle::ARTICLE_LINK:
								echo 'Link to webpage';
								break;
						}
						?></td>
						<td style="border-bottom: 1px solid #DDD; text-align: center; padding: 3px; width: 80px;"><?php echo ($article->isPublished()) ? image_tag('action_ok.png') : '&nbsp;'; ?></td>
						<td style="border-bottom: 1px solid #DDD; text-align: center; padding: 3px; width: 80px;"><?php echo ($article->isNews()) ? image_tag('action_ok.png') : '&nbsp;'; ?></td>
						<td style="border-bottom: 1px solid #DDD; padding: 3px; width: 110px;"><?php echo bugs_formatTime($article->getPostedDate(), 3); ?></td>
					</tr>
					</table>
					</li>
					<?php 
				}

				?>
			</ul>
			<script type="text/javascript">
			Sortable.create('article_list',{ onUpdate: function (savearticleorder) { saveArticleOrder(); } });
			</script>
			<?php
		}
		else
		{
			$featured_article = BUGScontext::getModule('publish')->getSetting('featured_article');
			
			if ($featured_article != '')
			{
				$f_article = new PublishArticle($featured_article);
				if ($f_article->isPublished())
				{
					?>
					<div style="width: auto; border: 1px solid #DDD; padding: 5px;">
					<b style="font-size: 14px;"><?php echo $f_article->getTitle(); ?></b><br>
					<div style="color: #AAA;"><b><?php print bugs_formatTime($f_article->getPostedDate(), 3); ?></b> by <b><?php echo $f_article->getAuthor(); ?></b></div>
					<div style="padding-top: 5px; font-size: 11.5px; padding-bottom: 5px; font-weight: bold;"><?php echo bugs_BBDecode($f_article->getIntro()); ?></div>
					<div style="text-align: right;"><a href="articles.php?article_id=<?php echo $f_article->getID(); ?>"><?php echo __('Read more'); ?></a></div>
					</div>
					<?php
				}
			}
			
			$previous_articles = BUGScontext::getModule('publish')->getArticles(30);
					
			?>
			<table style="width: 100%;" cellpadding=0 cellspacing=0>
				<tr>
					<td style="width: auto; padding-right: 5px; padding-top: 5px; vertical-align: top;">
					<div style="background-color: #EEE; padding: 5px; font-weight: bold; font-size: 13px;"><?php echo __('Recent articles'); ?></div>
					<?php

					$cc = 0;
	
					if (count($previous_articles) > 0)
					{
						for ($cc = 1; $cc <= 10; $cc++)
						{ 
							$article = array_shift($previous_articles);
							if ($article->getID() != $featured_article)
							{
								?>
								<div style="width: auto; padding: 5px;">
								<?php echo image_tag('publish/' . $article->getIcon() . '.png', ' style="float: left; margin-right: 5px;"') ?>
								<b style="font-size: 13px;"><?php echo $article->getTitle(); ?></b><br>
								<div style="color: #AAA;"><?php print bugs_formatTime($article->getPostedDate(), 3); ?> by <?php echo $article->getAuthor(); ?></div>
								<div style="padding-top: 5px; font-size: 11px; padding-bottom: 5px;"><?php echo bugs_BBDecode($article->getIntro()); ?></div>
								<div style="text-align: right;"><a href="articles.php?article_id=<?php echo $article->getID(); ?>"><?php echo __('Read more'); ?></a></div>
								</div>
								<?php
							}
							if (count($previous_articles) == 0) break;
						}
					}
					if ($cc == 0)
					{
						?><div style="color: #AAA; padding: 5px;"><?php echo __('There are no recent articles'); ?></div><?php
					}
					
					?>
					</td>
					<td style="width: 350px; padding-right: 5px; padding-top: 5px; vertical-align: top;">
					<div style="background-color: #EEE; padding: 5px; font-weight: bold; font-size: 13px;"><?php echo __('Articles in the archive'); ?></div>
					<?php
	
					if (count($previous_articles) > 0)
					{
						while (count($previous_articles) > 0)
						{ 
							$article = array_shift($previous_articles);
							if ($article->getID() != $featured_article)
							{
								?>
								<div style="width: auto; padding: 5px;">
								<?php echo image_tag('publish/' . $article->getIcon() . '.png', ' style="float: left; margin-right: 5px;"') ?>
								<b style="font-size: 13px;"><?php echo $article->getTitle(); ?></b><br>
								<div style="color: #AAA;"><?php print bugs_formatTime($article->getPostedDate(), 3); ?> by <?php echo $article->getAuthor(); ?></div>
								<div style="text-align: right;"><a href="articles.php?article_id=<?php echo $article->getID(); ?>"><?php echo __('Read more'); ?></a></div>
								</div>
								<?php
							}
						}
					}
					else
					{
						?><div style="color: #AAA; padding: 5px;"><?php echo __('There are no articles in the archive'); ?></div><?php
					}
					
					?>
					</td>
				</tr>
			</table>
			<?php
		}

		?>
		</td>
	</tr>
</table>