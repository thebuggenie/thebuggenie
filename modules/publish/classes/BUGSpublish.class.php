<?php

	class BUGSpublish extends BUGSmodule 
	{

		public function __construct($m_id, $res = null)
		{
			parent::__construct($m_id, $res);
			$this->_module_version = '1.0';
			$this->setLongName(BUGScontext::getI18n()->__('Wiki'));
			$this->setMenuTitle(BUGScontext::getI18n()->__('Wiki'));
			$this->setConfigTitle(BUGScontext::getI18n()->__('Wiki'));
			$this->setDescription(BUGScontext::getI18n()->__('Enables Wiki-functionality'));
			$this->setConfigDescription(BUGScontext::getI18n()->__('Set up the Wiki module from this section'));
			$this->setHasConfigSettings();
			$this->addAvailablePermission('article_management', 'Can create and manage articles');
			$this->addAvailablePermission('manage_billboard', 'Can delete billboard posts');
			$this->addAvailablePermission('publish_postonglobalbillboard', 'Can post articles on global billboard');
			$this->addAvailablePermission('publish_postonteambillboard', 'Can post articles on team billboard');
			$this->addAvailableListener('core', 'index_left_middle', 'listen_latestArticles', 'Frontpage "Last news items"');
			$this->addAvailableListener('core', 'index_right_middle', 'listen_indexMessage', 'Frontpage article');
			$this->addAvailableListener('core', 'project_overview_item_links', 'listen_projectLinks', 'Project overview links');
			$this->addAvailableListener('core', 'project_menustrip_item_links', 'listen_projectMenustripLinks', 'Project menustrip links');

			$this->addRoute('publish', '/wiki', 'showArticle', array('article_name' => 'MainPage'));
			$this->addRoute('publish_article', '/wiki/:article_name', 'showArticle');
			$this->addRoute('publish_article_edit', '/wiki/:article_name/edit', 'editArticle');
			$this->addRoute('publish_article_history', '/wiki/:article_name/history', 'showArticle');
		}

		public function initialize()
		{

		}

		public static function install($scope = null)
		{
  			$scope = ($scope === null) ? BUGScontext::getScope()->getID() : $scope;

			$module = parent::_install('publish', 'BUGSpublish','1.0', true, true, false, $scope);

			BUGScontext::setPermission('article_management', 0, 'publish', 0, 1, 0, true, $scope);
			BUGScontext::setPermission('publish_postonglobalbillboard', 0, 'publish', 0, 1, 0, true, $scope);
			BUGScontext::setPermission('publish_postonteambillboard', 0, 'publish', 0, 1, 0, true, $scope);
			BUGScontext::setPermission('manage_billboard', 0, 'publish', 0, 1, 0, true, $scope);
			$module->saveSetting('allow_camelcase_links', 1);

			$module->enableListenerSaved('core', 'index_left_middle');
			$module->enableListenerSaved('core', 'index_left_middle');
			$module->enableListenerSaved('core', 'project_overview_item_links');
			$module->enableListenerSaved('core', 'project_menustrip_item_links');
  									  
			if ($scope == BUGScontext::getScope()->getID())
			{
				B2DB::getTable('B2tArticles')->create();
				B2DB::getTable('B2tArticleViews')->create();
				B2DB::getTable('B2tBillboardPosts')->create();
			}

			/*function fu($string)
			{
				return '{link:'.$string.'}';
			}
			echo preg_replace('/(?<!\!)\b[A-Z]+[a-z]+[A-Z][A-Za-z]*\b/e', 'fu("\\0")', $text);*/
			
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

		public function hasProjectAwareRoute()
		{
			return true;
		}

		public function getProjectAwareRoute($project_key)
		{
			return BUGScontext::getRouting()->generate('publish_article', array('article_name' => ucfirst($project_key).":MainPage"));
		}

		public function postConfigSettings()
		{
			$settings = array('allow_camelcase_links', 'menu_title');
			foreach ($settings as $setting)
			{
				if (BUGScontext::getRequest()->hasParameter($setting))
				{
					$this->saveSetting($setting, BUGScontext::getRequest()->getParameter($setting));
				}
			}
		}

		public function getMenuTitle()
		{
			if (($menu_title = $this->getSetting('menu_title')) !== null)
			{
				$i18n = BUGScontext::getI18n();
				switch ($menu_title)
				{
					case 5: return $i18n->__('Archive');
					case 3: return $i18n->__('Documentation');
					case 4: return $i18n->__('Documents');
					case 2: return $i18n->__('Help');
					case 1: return $i18n->__('Wiki');
				}

			}
			return parent::getMenuTitle();
		}

		public function getBillboardPosts($target_board = 0, $posts = 5)
		{
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
		
		public function getLatestArticles($limit = 5)
		{
			return $this->getArticles($limit, true);
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
			
			$crit->addOrderBy(B2tArticles::DATE, 'desc');
			
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

		public function getMenuItems()
		{
			return array();
		}

		public function getUserDrafts()
		{
			$articles = array();

			if ($res = B2DB::getTable('B2tArticles')->getUnpublishedArticlesByUser(BUGScontext::getUser()->getID()))
			{
				while ($row = $res->getNextRow())
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

		public function getIndexMessage()
		{
			if ($row = B2DB::getTable('B2tArticles')->getArticleByName('IndexMessage'))
			{
				return PublishFactory::articleLab($row->get(B2tArticles::ID), $row);
			}
			return null;
		}
		
		public function listen_indexMessage()
		{
			$index_article = $this->getIndexMessage();
			if ($index_article instanceof PublishArticle)
			{
				BUGSactioncomponent::includeComponent('publish/articledisplay', array('article' => $index_article, 'show_title' => true, 'show_details' => false, 'show_intro' => false, 'show_actions' => false));
			}
		}

		public function listen_latestArticles()
		{
			BUGSactioncomponent::includeComponent('publish/latestArticles');
		}

		public function listen_projectLinks($params)
		{
			BUGSactioncomponent::includeTemplate('publish/projectlinks', $params);
		}

		public function listen_projectMenustripLinks($params)
		{
			BUGSactioncomponent::includeTemplate('publish/projectmenustriplinks', $params);
		}

		public function getTabKey()
		{
			return (BUGScontext::getCurrentProject() instanceof BUGSproject) ? parent::getTabKey() : 'wiki';
		}

	}

?>
