<?php

	$page = 'publish';
	define ('THEBUGGENIE_PATH', '../../');
	
	require THEBUGGENIE_PATH . 'include/checkcookie.inc.php';
	require THEBUGGENIE_PATH . "include/b2_engine.inc.php";
	
	require TBGContext::getIncludePath() . "include/ui_functions.inc.php";

	TBGPublish::getModule()->activate();
	
	require TBGContext::getIncludePath() . "modules/publish/articles_logic.inc.php";
	require TBGContext::getIncludePath() . "include/header.inc.php";
	require TBGContext::getIncludePath() . "include/menu.inc.php";

?>
<table style="margin-top: 0px; table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<td style="width: 255px;" valign="top">
			<table class="b2_section_miniframe" cellpadding=0 cellspacing=0>
				<tr>
					<td class="b2_section_miniframe_header"><?php echo __('Common actions'); ?></td>
				</tr>
				<tr>
					<td class="td1">
						<table cellpadding=0 cellspacing=0>
							<tr>
								<td style="width: 20px;"><?php echo image_tag('tab_publish.png') ?></td>
								<td><a href="publish.php"><b><?php echo __('Visit News &amp; Articles center'); ?></b></a></td>
							</tr>
							<tr>
								<td style="width: 20px;"><?php echo image_tag('publish/icon_manage.png'); ?></td>
								<td><a href="billboard.php"><b><?php echo __('Look at billboard(s)'); ?></b></a></td>
							</tr>
							<tr><td colspan=2>&nbsp;</td></tr>
							<tr>
								<td style="width: 20px;"><?php echo image_tag('icon_printer.png') ?></td>
								<td><a href="javascript:void(0);" onclick="window.print();"><?php echo __('Print this article'); ?></a></td>
							</tr>
							<?php /* ?>
							<tr>
								<td style="width: 20px;"><?php echo image_tag('icon_mailfriend.png') ?></td>
								<td><a href="#"><?php echo __('Send this article to a friend'); ?></a></td>
							</tr>
							<?php */ ?>
							<?php if ((TBGContext::getUser()->hasPermission('publish_postonglobalbillboard', 0, "publish") || TBGContext::getUser()->hasPermission('publish_postonteambillboard', 0, "publish")) && $article->getArticleType() == TBGWikiArticle::ARTICLE_NORMAL): ?>
								<tr><td colspan=2>&nbsp;</td></tr>
								<tr>
									<td style="width: 20px;"><?php echo image_tag('publish/icon_new_link.png') ?></td>
									<td><a href="javascript:void(0);" onclick="$('post_on_billboard').toggle();"><?php echo __('Post article on billboard'); ?></a></td>
								</tr>
								<?php if ($is_published): ?>
									<tr><td colspan=2 style="padding-top: 3px;"><b><?php echo __('The article was posted on the billboard'); ?></b></td></tr>
								<?php endif; ?>
							<?php endif; ?>
						</table>
						<?php if (TBGContext::getUser()->hasPermission('publish_postonglobalbillboard', 0, "publish") || TBGContext::getUser()->hasPermission('publish_postonteambillboard', 0, "publish")): ?>
							<div id="post_on_billboard" style="position: absolute; display: none; width: 250px; padding: 5px; background-color: #FFF; border: 1px solid #DDD;">
							<?php echo __('Please select which billboard you would like to post this article to:'); ?><br>
							<?php if (TBGContext::getUser()->hasPermission('publish_postonglobalbillboard', 0, "publish")): ?>
								<a href="articles.php?article_id=<?php echo $article->getID(); ?>&amp;post_on_billboard=true&amp;billboard=0"><?php echo __('Post on global billboard'); ?></a><br>
							<?php endif; ?>
							<?php 
							
								if (TBGContext::getUser()->hasPermission('publish_postonteambillboard', 0, "publish"))
								{
									foreach (TBGContext::getUser()->getTeams() as $aTeamID)
									{
										$aTeam = TBGContext::factory()->TBGTeam($aTeamID);
										?><a href="articles.php?article_id=<?php echo $article->getID(); ?>&amp;post_on_billboard=true&amp;billboard=<?php echo $aTeamID ?>"><?php echo __('Post on %teamname% billboard', array('%teamname' => $aTeam->getName())); ?></a><br><?php
									}
								}
	
							?>
							</div>
						<?php endif; ?>
					</td>
				</tr>
			</table> 
			<?php
			
			if (TBGContext::getUser()->hasPermission('article_management', 0, 'publish'))
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
							<?php if (!TBGContext::getRequest()->getParameter('edit')): ?>
								<i><?php echo __('If you want to edit this article, please choose from any of the available actions below.'); ?></i></div>
							<?php endif; ?>
							<table cellpadding=0 cellspacing=0>
								<tr>
									<td style="width: 20px;"><?php echo image_tag('publish/icon_manage.png'); ?></td>
									<td><a href="publish.php?manage=true"><?php echo __('Manage all articles'); ?></a></td>
								</tr>
								<?php 
								
								if (TBGContext::getRequest()->getParameter('edit'))
								{  
									?>
									<tr>
										<td style="width: 20px;"><?php echo image_tag('publish/document.png') ?></td>
										<td><a href="articles.php?article_id=<?php echo $article->getID(); ?>"><?php echo __('Exit edit mode'); ?></a></td>
									</tr>
									<tr><td colspan=2 style="padding-top: 10px;"><?php echo __('When you are done editing, click "exit edit mode".'); ?>&nbsp;<b><?php echo __('Remember to save your changes first.'); ?></b></td></tr>
									<?php
								}
								else
								{
									?>
									<tr>
										<td style="width: 20px;"><?php echo image_tag('icon_edit.png') ?></td>
										<td><a href="articles.php?article_id=<?php echo $article->getID(); ?>&amp;edit=true"><?php echo __('Edit this article'); ?></a></td>
									</tr>
									<?php
									if ($article->isPublished())
									{  
										?>
										<tr>
											<td style="width: 20px;"><?php echo image_tag('publish/unpublish.png') ?></td>
											<td><a href="articles.php?article_id=<?php echo $article->getID(); ?>&amp;retract=true"><?php echo __('Retract this article'); ?></a></td>
										</tr>
										<?php
									}
									else
									{
										?>
										<tr>
											<td style="width: 20px;"><?php echo image_tag('publish/publish.png') ?></td>
											<td><a href="articles.php?article_id=<?php echo $article->getID(); ?>&amp;publish=true"><?php echo __('Publish this article'); ?></a></td>
										</tr>
										<?php
									}
	
									if (TBGPublish::getModule()->getSetting('featured_article') != $article->getID())
									{  
										?>
										<tr>
											<td style="width: 20px;"><?php echo image_tag('publish/icon_featured.png') ?></td>
											<td><a href="articles.php?article_id=<?php echo $article->getID(); ?>&amp;feature=true"><?php echo __('Show as featured article'); ?></a></td>
										</tr>
										<?php
									}
									else
									{
										?><tr><td colspan=2 style="padding-top: 3px;"><b><?php echo __('This article is shown as a featured article'); ?></b></td></tr><?php
									}
								}

								?>
							</table>
							</div>
						</td>
					</tr>
				</table> 
				<?php
			}
			
			?>
		</td>
		<td valign="top" align="left" style="padding-right: 10px; padding-top: 10px;">
			<?php
			
			if (TBGContext::getRequest()->getParameter('edit') && TBGContext::getUser()->hasPermission('article_management', 0, 'publish'))
			{
				?>
				<div style="font-size: 13px; background-color: #EEE; padding: 4px; margin-bottom: 5px;"><?php echo __('Editing article with id %id%', array('%id%' => $article->getID())); ?></div>
				<?php

				if ($issaved)
				{
					echo tbg_successStrip(__('Changes saved'), __('Your changes has been saved'));
				}
				
				?>
				<div style="padding-bottom: 15px; clear: both; width: 730px;"> 
				<?php echo __('The "save changes" button is at the bottom below the article content. If you cannot see it, use the scroll function in your browser.'); ?>
				</div>
				<script type="text/javascript">

					function updateIcon()
					{
						$('article_icon').src = '<?php echo TBGSettings::get('url_subdir') ?>themes/<?php echo TBGSettings::getThemeName() ?>/publish/large/' + $('icon_select').value + '.png';
					}
					
					function updateArticleType()
					{
						$('article_div').hide();
						$('link_div').hide();
						$('news_div').hide();
						if ($('article_type').value == 1)
						{
							$('article_div').show();
						}
						else if ($('article_type').value == 2)
						{
							$('news_div').show();
						}
						else if ($('article_type').value == 3)
						{
							$('link_div').show();
						}
					}

				</script>
				<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="articles.php" method="post">
					<div style="background-color: #EEE; width: 730px; padding: 5px; border: 1px solid #DDD;">
					<input type="hidden" name="article_id" value="<?php echo $article->getID(); ?>" >
					<input type="hidden" name="submit_article_changes" value="true" >
					<input type="hidden" name="edit" value="true" >
					<div style="font-weight: bold; width: 80px; padding: 2px; float: left;"><?php echo __('Title'); ?></div>
					<input type="text" name="title" value="<?php echo $article->getTitle(); ?>" style="width: 700px;" >
					<br style="clear: both;">
					<br style="clear: both;">
					<div id="icon_div" <?php if ($article->getArticleType() != TBGWikiArticle::ARTICLE_NORMAL) echo 'style="display: none;"' ?>>
						<div style="font-weight: bold; width: 80px; padding: 2px; float: left;"><?php echo __('Icon'); ?></div>
						<div style="background-color: #FFF; width: 130px; border: 1px solid #DDD; padding: 3px; float: left;">
						<?php echo image_tag('publish/large/' . $article->getIcon() . '.png', 'id="article_icon"'); ?>
						</div>
						<div style="width: 300px; float: left; padding: 5px;">
						<?php echo __('The icon selected here will be used in article view, and in the news &amp; articles center.'); ?>
						</div>
						<br style="clear: both;">
						<select name="icon_select" id="icon_select" onchange="updateIcon()" style="margin-left: 85px; width: 136px;">
							<?php foreach (TBGPublish::getModule()->getIcons() as $key => $value): ?>
								<option value="<?php echo $key; ?>" <?php if ($article->getIcon() == $key) echo 'selected'; ?>><?php echo $value; ?></option>
							<?php endforeach; ?>
						</select>
						<br style="clear: both;">
						<br style="clear: both;">
					</div>
					<div style="font-weight: bold; width: 80px; padding: 2px; float: left;"><?php echo __('Show in news'); ?></div>
					<input type="checkbox" name="is_news" value="1" <?php if ($article->isNews()) echo 'checked'; ?>>&nbsp;<?php echo __('If checked, this article will appear in the recent news list'); ?>
					<br style="clear: both;">
					<div style="font-weight: bold; width: 80px; padding: 2px; float: left;"><?php echo __('Published'); ?></div>
					<input type="checkbox" name="is_published" value="1" <?php if ($article->isPublished()) echo 'checked'; ?>>&nbsp;<?php echo __('If checked, this article will be visible to users'); ?>
					<br style="clear: both;">
					</div>
					<div id="articletype_div" style="display: none;">
						<br style="clear: both;">
						<div style="padding: 5px; padding-left: 0px; border-bottom: 1px solid #DDD; width: 730px;"><b><?php echo __('Article type'); ?></b></div>
						<div style="padding-top: 5px; padding-bottom: 5px; width: 730px;"><?php echo __('Please select which type of article this is.'); ?></div>
						<select name="article_type" id="article_type" onchange="updateArticleType()">
							<option value="1" <?php if ($article->hasAnyContent()) echo 'selected' ?>><?php echo __('Normal article or article with only intro'); ?></option>
							<option value="2" <?php if (!$article->hasAnyContent() && !$article->isLink()) echo 'selected' ?>><?php echo __('News item without any content'); ?></option>
							<option value="3" <?php if ($article->isLink()) echo 'selected' ?>><?php echo __('News item that is a link to a webpage'); ?></option>
						</select>
						<br style="clear: both;">
					</div>
					<br style="clear: both;">
					<div id="article_div" <?php if ($article->getArticleType() != TBGWikiArticle::ARTICLE_NORMAL) echo 'style="display: none;"' ?>>
						<div style="padding: 5px; padding-left: 0px; border-bottom: 1px solid #DDD; width: 730px;"><b><?php echo __('Intro text'); ?></b></div>
						<div style="padding-top: 5px; padding-bottom: 5px; width: 730px;"><?php echo __('The intro text will be used on summary pages, as well as after the article title when in article view.'); ?>&nbsp;<?php echo __('The intro text should be a short description about the article, and should contain at most one paragraph, in some cases two.'); ?></div>
						<?php echo tbg_newTextArea('intro', '70px', '730px', $article->getIntro()); ?><br>
						<div style="padding: 5px; padding-left: 0px; border-bottom: 1px solid #DDD; width: 730px;"><b><?php echo __('Article content'); ?></b></div>
						<div style="padding-top: 5px; padding-bottom: 5px; width: 730px;"><?php echo __('The article content is the article body - the main content of the article.'); ?></div>
						<?php echo tbg_newTextArea('content', '400px', '730px', $article->getContent()); ?>
					</div>
					<div id="news_div" <?php if ($article->getArticleType() != TBGWikiArticle::ARTICLE_NEWS) echo 'style="display: none;"' ?>>
						<div style="padding-top: 5px; padding-bottom: 5px;">
							<?php echo __('Remember to check the "Show in news" checkbox, as well as the "Published" checkbox so this item appears in the news overview.'); ?><br>
							<?php echo __('You do not need to select an icon for this item, since it will not be used.'); ?>
						</div>
					</div>
					<div id="link_div" <?php if ($article->getArticleType() != TBGWikiArticle::ARTICLE_LINK) echo 'style="display: none;"' ?>>
						<div style="font-weight: bold; width: 80px; padding: 2px; float: left;"><?php echo __('URL'); ?></div>
						<input type="text" name="link_url" value="<?php echo $article->getLinkURL(); ?>" style="width: 700px;" ><br>
						<div style="padding-top: 5px; padding-bottom: 5px;">
							<?php echo __('Remember to check the "Show in news" checkbox, as well as the "Published" checkbox so this item appears in the news overview.'); ?><br>
							<?php echo __('You do not need to select an icon for this item, since it will not be used.'); ?>
						</div>
					</div>
					<div style="background-color: #EEE; margin-top: 5px; margin-bottom: 15px; text-align: right; width: 730px; padding: 5px; border: 1px solid #DDD;"><?php echo __('When you are done, click here to save the article'); ?>&nbsp;&nbsp;<input type="submit" value="<?php echo __('Save article'); ?>"></div>
				</form>
				<?php
			}
			else
			{
				if (!$article->isPublished())
				{
					?><div style="background-color: #EEE; padding: 5px; margin-bottom: 5px;"><b><?php echo __('This article is not yet published'); ?></b> (<a href="articles.php?article_id=<?php echo $article->getID(); ?>&amp;publish=true"><b><?php echo __('Publish now'); ?></b></a>)</div><?php
				}
				else
				{
					$article->view();
				}
				
				?>
				<div style="width: 730px;">
				<b style="font-size: 13px;"><?php echo $article->getTitle(); ?></b><br>
				<div style="color: #AAA;"><?php echo __('Published %at%, by %user%', array('%at%' => tbg_formatTime($article->getPostedDate(), 3), '%user%' => $article->getAuthor())); ?><br>
				<?php echo __('This article has been read %number_of% times', array('%number_of%' => $article->getViews())); ?></div>
				<?php if ($article->hasContent()): ?>
					<div style="padding-top: 5px; font-size: 11px; font-weight: bold; padding-bottom: 5px; border-bottom: 1px solid #DDD; margin-bottom: 5px; width: auto;"><?php echo tbg_BBDecode($article->getIntro()); ?></div>
					<div style="display: block; float: left;"><?php echo image_tag('publish/large/' . $article->getIcon() . '.png') ?></div>
					<div style="text-align: left; padding-top: 5px;"><?php echo tbg_BBDecode($article->getContent()); ?></div>
				<?php else: ?>
					<div style="display: block; float: left;"><?php echo image_tag('publish/large/' . $article->getIcon() . '.png') ?></div>
					<div style="padding-top: 5px; font-size: 11px; font-weight: bold; padding-bottom: 5px; border-bottom: 1px solid #DDD; margin-bottom: 5px; width: auto;"><?php echo tbg_BBDecode($article->getIntro()); ?></div>
				<?php endif; ?>
				</div>
				<?php
			}
			
			?>
		</td>
	</tr>
</table>
<?php

	require_once TBGContext::getIncludePath() . "include/footer.inc.php";

?>		