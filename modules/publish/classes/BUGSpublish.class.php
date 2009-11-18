<?php

	class BUGSpublish extends BUGSmodule 
	{

		public function __construct($m_id, $res = null)
		{
			parent::__construct($m_id, $res);
			$this->_module_version = '1.0';
			$this->setLongName(BUGScontext::getI18n()->__('News &amp; Articles'));
			$this->setMenuTitle(BUGScontext::getI18n()->__('News &amp; Articles'));
			$this->setConfigTitle(BUGScontext::getI18n()->__('News &amp; Articles'));
			$this->setDescription(BUGScontext::getI18n()->__('Enables articles, news and billboards'));
			$this->setConfigDescription(BUGScontext::getI18n()->__('Set up the News &amp; Articles module from this section'));
			$this->setHasConfigSettings();
			$this->addAvailablePermission('article_management', 'Can create and manage articles');
			$this->addAvailablePermission('manage_billboard', 'Can delete billboard posts');
			$this->addAvailablePermission('publish_postonglobalbillboard', 'Can post articles on global billboard');
			$this->addAvailablePermission('publish_postonteambillboard', 'Can post articles on team billboard');
			$this->addAvailableListener('core', 'index_left_middle', 'section_latestNewsBox', 'Frontpage "Last news items"');
			$this->addAvailableListener('core', 'index_right_middle', 'section_latestNews', 'Frontpage billboard overview');

			$this->addRoute('publish', '/articles', 'index');
		}

		public function initialize()
		{

		}

		static public function install($scope = null)
		{
  			$scope = ($scope === null) ? BUGScontext::getScope()->getID() : $scope;

			$module = parent::_install('publish', 'BUGSpublish','1.0', true, true, false, $scope);

			BUGScontext::setPermission('article_management', 0, 'publish', 0, 1, 0, true, $scope);
			BUGScontext::setPermission('publish_postonglobalbillboard', 0, 'publish', 0, 1, 0, true, $scope);
			BUGScontext::setPermission('publish_postonteambillboard', 0, 'publish', 0, 1, 0, true, $scope);
			BUGScontext::setPermission('manage_billboard', 0, 'publish', 0, 1, 0, true, $scope);
			$module->saveSetting('enablebillboards', 1);
			$module->saveSetting('enableteambillboards', 1);
			$module->saveSetting('featured_article', 1);
			$module->enableListenerSaved('core', 'index_left_middle');
			$module->enableListenerSaved('core', 'index_right_middle');
  									  
			if ($scope == BUGScontext::getScope()->getID())
			{
				B2DB::getTable('B2tArticles')->create();
				B2DB::getTable('B2tArticleViews')->create();
				B2DB::getTable('B2tBillboardPosts')->create();
			}
			
			try
			{
				self::loadFixtures($scope);
			}
			catch (Exception $e)
			{
				throw $e;
			}

			return true;
		}
		
		static function loadFixtures($scope)
		{
			try
			{
				B2DB::getTable('B2tArticles')->loadFixtures($scope);
				B2DB::getTable('B2tBillboardPosts')->loadFixtures($scope);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
		
		public function uninstall()
		{
			if (BUGScontext::getScope()->getID() == 1)
			{
				B2DB::getTable('B2tArticles')->drop();
				B2DB::getTable('B2tBillboardPosts')->drop();
			}
			parent::_uninstall();
		}

		public function getRoute()
		{
			return BUGScontext::getRouting()->generate('publish');
		}

		public function getBillboardPosts($target_board = 0, $posts = 5)
		{
			$sql = "";
			
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tBillboardPosts::SCOPE, BUGScontext::getScope()->getID());
			$crit->addWhere(B2tBillboardPosts::IS_DELETED, 0);
			$crit->setLimit($posts);
			$crit->addOrderBy(B2tBillboardPosts::DATE, 'desc');
			if (is_array($target_board))
			{
				$crit->addWhere(B2tBillboardPosts::TARGET_BOARD, $target_board, B2DBCriteria::DB_IN);
			}
			else
			{
				$crit->addWhere(B2tBillboardPosts::TARGET_BOARD, $target_board);
			}
	
			$posts = array();
	
			$res = B2DB::getTable('B2tBillboardPosts')->doSelect($crit);
			while ($row = $res->getNextRow())
			{
				$posts[] = new PublishBillboardPost($row);
			}
	
			return $posts;
		}
		
		public function getNews($num_news = 5)
		{
			return $this->getArticles($num_news, true);
		}
	
		public function getAllArticles()
		{
			$crit = new B2DBCriteria();
			$crit->addOrderBy(B2tArticles::ORDER, 'asc');
			$crit->addOrderBy(B2tArticles::DATE, 'desc');
			$res = B2DB::getTable('B2tArticles')->doSelect($crit);
			$articles = array();
			while ($row = $res->getNextRow())
			{
				$articles[] = PublishFactory::articleLab($row->get(B2tArticles::ID), $row);
			}
			return $articles;
		}
		
		public function getArticles($num_articles = 5, $news = false, $published = true)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tArticles::SCOPE, BUGScontext::getScope()->getID());
			
			$crit->addOrderBy(B2tArticles::ORDER, 'asc');
			$crit->addOrderBy(B2tArticles::DATE, 'desc');
			
			if ($news)
			{
				$crit->addWhere(B2tArticles::IS_NEWS, 1);
			}
			else
			{
				$crit->addWhere(B2tArticles::INTRO_TEXT, '', B2DBCriteria::DB_NOT_EQUALS);
				$crit->addWhere(B2tArticles::TITLE, '', B2DBCriteria::DB_NOT_EQUALS);
				$crit->addWhere(B2tArticles::LINK, null, B2DBCriteria::DB_IS_NULL);
			}
			if ($published) $crit->addWhere(B2tArticles::IS_PUBLISHED, 1);
	
			$articles = array();
			
			if ($res = B2DB::getTable('B2tArticles')->doSelect($crit))
			{
				while (($row = $res->getNextRow()) && (count($articles) < $num_articles))
				{
					try
					{
						$article = PublishFactory::articleLab($row->get(B2tArticles::ID), $row);
					}
					catch (Exception $e) 
					{
						continue;
					}
					
					if ($article->canRead())
					{
						$articles[] = $article;
					}
				}
			}
	
			return $articles;
		}
		
		public function getIcons()
		{
			$arr = array('abiword_abi'		=> 'Writing',
						 'applix'			=> 'Style',
						 'bc'				=> 'Books',
						 'cdimage'			=> 'Release',
						 'cdtrack'			=> 'Audio CD',
						 'core'				=> 'Bugs and issues',
						 'encrypted'		=> 'Lock',
						 'exec_wine'		=> 'Leisure',
						 'file_temporary'	=> 'History',
						 'gettext'			=> 'Font',
						 'html'				=> 'Internet',
						 'image_gimp'		=> 'Painting',
						 'info'				=> 'Information',
						 'install'			=> 'Software',
						 'kchart_chrt'		=> 'Statistics',
						 'kformula_kfo'		=> 'Math',
						 'kget_list'		=> 'Download',
						 'kpresenter_kpr'	=> 'Presentation',
						 'krec_filerec'		=> 'Record',
						 'krita_kra'		=> 'Crayons',
						 'kspread_ksp'		=> 'Accounting',
						 'log'				=> 'Stocks',
						 'man'				=> 'Rescue',
						 'message'			=> 'Email',
						 'pdf'				=> 'PDF',
						 'postscript'		=> 'Printing',
						 'readme'			=> 'Notification',
						 'recycled'			=> 'Recycle',
						 'sound'			=> 'Sound',
						 'source'			=> 'Source',
						 'source_c'			=> 'Source C',
						 'source_cpp'		=> 'Source C++',
						 'source_css'		=> 'Source CSS',
						 'source_f'			=> 'Source F',
						 'source_h'			=> 'Source H',
						 'source_j'			=> 'Source J',
						 'source_o'			=> 'Source O',
						 'source_p'			=> 'Source P',
						 'source_php'		=> 'Source PHP',
						 'source_pl'		=> 'Source Perl',
						 'source_py'		=> 'Source Python',
						 'source_s'			=> 'Source S',
						 'source_y'			=> 'Source Y',
						 'source_java'		=> 'Coffee',
						 'source_moc'		=> 'Components',
						 'txt'				=> 'Draft',
						 'vcalendar'		=> 'Calendar',
						 'vcard'			=> 'Card',
						 'video'			=> 'Video and animation',
						 'wordprocessing'	=> 'Article',
			);
			asort($arr);
			return $arr;
		}
		
		/**
		 * Prints a billboard post
		 *
		 * @param PublishBillboardPost $billboardpost
		 */
		protected function _printBillboardPost($billboardpost)
		{
			if ($billboardpost->isLink())
			{
				?><div class="billboardpostdiv">
				<b><?php 
				print '<a href="' . $billboardpost->getLinkURL() . '" target="_blank">' . $billboardpost->getTitle() . '</a>';
				?></b>
				<div class="billboardinfo"><?php print bugs_formatTime($billboardpost->getPostedDate(), 3); ?></div>
				<div class="billboardpost"><?php
			}
			else
			{
				?><div class="billboardpostdiv">
				<b><?php echo $billboardpost->getTitle(); ?></b>
				<div class="billboardinfo"><?php print bugs_formatTime($billboardpost->getPostedDate(), 3); ?></div>
				<?php if ($billboardpost->getTargetBoard() == 0): ?>
					<?php print bugs_BBDecode($billboardpost->getContent()); ?>
				<?php endif; ?>
				<div class="billboardpost"><?php
			}
			
			if ($billboardpost->isLinkToArticle())
			{
				print '<a href="' . BUGScontext::getTBGPath() . 'modules/publish/articles.php?article_id=' . $billboardpost->getRelatedArticleID() . '">' . BUGScontext::getI18n()->__('Click here to go to this article') . '</a>';
			}
			elseif (!$billboardpost->isLink())
			{
				print '<a href="' . BUGScontext::getTBGPath() . 'modules/publish/billboard.php?billboard=' . $billboardpost->getTargetBoard() . '&amp;post_id=' . $billboardpost->getID() . '">' . BUGScontext::getI18n()->__('Open this board') . '</a>';
			}
			?></div>
			</div>
			<?php			
		}
		
		/**
		 * Prints a billboard post
		 *
		 * @param PublishBillboardPost $billboardpost
		 */
		public function printBillboardPostOnBillboard($billboardpost)
		{
			echo '<li style="padding: 5px; margin: 2px; position relative; margin-bottom: 5px; border: 1px solid #DDD; background-color: #F9F9F9;" id="billboardpost_' . $billboardpost->getID() . '">';
			if (BUGScontext::getUser()->hasPermission('manage_billboard', 0, 'publish'))
			{
				echo '<div style="position: relative;">';
				echo '<a class="image" href="javascript:void(0);" style="position: absolute; top: 2px; right: 2px;" onclick="removeBillboardPost(' . $billboardpost->getID() . ');">' . image_tag('action_cancel_small.png') . '</a>';
				echo '</div>';
			}
			if ($billboardpost->isLink())
			{
				echo '<b><a href="' . $billboardpost->getLinkURL() . '" target="_blank">' . $billboardpost->getTitle() . '</a></b>';
			}
			else
			{
				?><b><?php echo $billboardpost->getTitle(); ?></b><?php
			}

			?><div class="billboardinfo" style="background-color: #F9F9F9;"><?php
			echo $billboardpost->getAuthor() . ' - ' . bugs_formatTime($billboardpost->getPostedDate(), 3); ?></div><?php
			
			if ($billboardpost->isLinkToArticle())
			{
				print '<div style="padding: 2px; padding-top: 7px; padding-bottom: 7px; border: 1px solid #EEE; background-color: #FFF;">' . bugs_BBDecode($billboardpost->getContent()) . '</div>';
				print '<div style="text-align: right;"><a href="' . BUGScontext::getTBGPath() . 'modules/publish/articles.php?article_id=' . $billboardpost->getRelatedArticleID() . '">' . BUGScontext::getI18n()->__('Click here to go to this article') . '</a></div>';
			}
			elseif (!$billboardpost->isLink())
			{
				print '<div style="padding: 2px; padding-top: 7px; padding-bottom: 7px; border: 1px solid #EEE; background-color: #FFF;">' . bugs_BBDecode($billboardpost->getContent()) . '</div>';
			}
			echo '</li>';
		}
		
		public function section_latestNews()
		{
			//echo '<div style="background-color: #EEE; padding: 5px; font-weight: bold; font-size: 13px;">' . BUGScontext::getI18n()->__('Recent articles') . '</div>';

			$previous_articles = BUGScontext::getModule('publish')->getArticles(3);
			if (count($previous_articles) > 0)
			{
				foreach ($previous_articles as $article)
				{ 
					?>
					<div style="width: auto; padding: 5px;">
					<?php echo image_tag('publish/' . $article->getIcon() . '.png', array('style' => "float: left; margin-right: 5px;")); ?>
					<b style="font-size: 13px;"><?php echo $article->getTitle(); ?></b><br>
					<div style="color: #AAA;"><?php print bugs_formatTime($article->getPostedDate(), 3); ?> by <?php echo $article->getAuthor(); ?></div>
					<div style="padding-top: 5px; font-size: 11px; padding-bottom: 5px;"><?php echo bugs_BBDecode($article->getIntro()); ?></div>
					<div style="text-align: right;"><a href="<?php echo BUGScontext::getTBGPath(); ?>modules/publish/articles.php?article_id=<?php echo $article->getID(); ?>"><?php echo BUGScontext::getI18n()->__('Read more'); ?></a></div>
					</div>
					<?php
				}
			}
			if (count($previous_articles) == 0)
			{
				?><div style="color: #AAA; padding: 5px;"><?php echo BUGScontext::getI18n()->__('There are no recent articles'); ?></div><?php
			}
			
		}
		
		public function section_latestNewsBox()
		{
			include_component('publish/latestNewsBox');
			return;
			if (BUGScontext::getModule('publish')->hasAccess() && BUGScontext::getModule('publish')->getSetting('showlastarticlesonfrontpage') == 1)
			{
				?>
				<table class="b2_section_miniframe" cellpadding=0 cellspacing=0>
				<tr>
				<td class="b2_section_miniframe_header"><?php echo BUGScontext::getI18n()->__('Latest news'); ?></td>
				</tr>
				<tr>
				<td class="td1">
				<table cellpadding=0 cellspacing=0 style="width: 100%;">
				<?php
	
					$news = array();
					$this->log('retrieving news items');
					if ($bugs_response->getPage() == 'publish')
					{
						$news = $this->getNews(10);
					}
					else
					{
						$news = $this->getNews();
					}
					$this->log('done');
	
					foreach($news as $anews)
					{
						?>
						<tr>
						<td class="imgtd">
						<?php
						
						if ($anews->isLink())
						{
							?>
							<?php echo image_tag('news_link.png') ?></td>
							<td><a href="<?php print $anews->getLinkURL(); ?>" target="_blank"><?php print $anews->getTitle(); ?></a><br>
							<?php
						}
						elseif ($anews->hasAnyContent())
						{
							?>
							<?php echo image_tag('news_item.png') ?></td>
							<td><a href="<?php print BUGScontext::getTBGPath(); ?>modules/publish/articles.php?article_id=<?php print $anews->getID(); ?>"><?php print $anews->getTitle(); ?></a><br>
							<?php
						}
						else
						{
							?>
							<?php echo image_tag('news_item.png') ?></td>
							<td><?php print $anews->getTitle(); ?><br>
							<?php
						}

						?>
						<div style="font-size: 7pt;"><?php print bugs_formatTime($anews->getPostedDate(), 3); ?></div></td>
						</tr>
						<?php
					}
	
					if (count($news) >= 1 && $bugs_response->getPage() != 'publish')
					{
						?>
						<tr>
						<td class="imgtd" style="padding-top: 10px;"><?php echo image_tag('news_item.png') ?></td>
						<td style="padding-top: 10px;"><a href="<?php print BUGScontext::getTBGPath(); ?>modules/publish/publish.php"><?php echo BUGScontext::getI18n()->__('Click to read more news'); ?></a></td>
						</tr>
						<?php
					}
					elseif (count($news) == 0)
					{
						?>
						<tr><td style="color: #AAA;"><?php echo BUGScontext::getI18n()->__('There are no news published'); ?></td></tr>
						<?php
					}
	
				?>
				</table>
				</td>
				</tr>
				</table>
				<?php
			}
		}
		
	}

?>
