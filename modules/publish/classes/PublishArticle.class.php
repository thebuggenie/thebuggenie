<?php

	class PublishArticle extends PublishGenericContent 
	{
		
		const ARTICLE_NORMAL = 1;
		const ARTICLE_NEWS = 2;
		const ARTICLE_LINK = 3;
		
		/**
		 * related project
		 *
		 * @var BUGSproject
		 */
		protected $related_project = null;
		
		/**
		 * related team
		 *
		 * @var BUGSteam
		 */
		protected $related_team = null;
		
		protected $intro_text = '';
		protected $is_news = false;
		protected $icon = '';
		protected $_order = 0;
		protected $allowed = true;
		
		/**
		 * Article constructor. Takes integer $id or b2dbrow $id
		 *
		 * @param integer|B2DBrow $id
		 */
		public function __construct($id)
		{
			try
			{
				if (!$id instanceof B2DBRow)
				{
					$id = B2DB::getTable('B2tArticles')->doSelectById($id);
				}
			}
			catch (Exception $e)
			{
				throw new Exception('This article does not exist');
			}
			$this->_itemid = $id->get(B2tArticles::ID);
			
			$this->_name = $id->get(B2tArticles::ARTICLE_NAME);
			$this->_order = $id->get(B2tArticles::ORDER);
			
			$this->title = $id->get(B2tArticles::TITLE);
			$this->intro_text = $id->get(B2tArticles::INTRO_TEXT);
			$this->content = $id->get(B2tArticles::CONTENT);
			$this->posted_date = $id->get(B2tArticles::DATE);
			$this->author = $id->get(B2tArticles::AUTHOR);
			
			$this->is_news = ($id->get(B2tArticles::IS_NEWS) == 1) ? true : false;
			$this->is_published = ($id->get(B2tArticles::IS_PUBLISHED) == 1) ? true : false;
			$this->link_url = $id->get(B2tArticles::LINK);
			$this->icon = $id->get(B2tArticles::ICON);
			
			if ($id->get(B2tArticles::RELATED_PROJECT) != 0) $this->related_project = BUGSfactory::projectLab($id->get(B2tArticles::RELATED_PROJECT));
			if ($id->get(B2tArticles::RELATED_TEAM) != 0) $this->related_team = BUGSfactory::teamLab($id->get(B2tArticles::RELATED_TEAM));

			if ($this->related_project !== null && BUGScontext::getUser()->hasPermission('b2projectaccess', $this->related_project->getID(), 'core') == false)
			{
				$this->allowed = false;
			}
			elseif ($this->related_team !== null && BUGScontext::getUser()->isMemberOf($this->related_team->getID()) == false) 
			{
				$this->allowed = false;
			}
		}
		
		public function retract()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tArticles::IS_PUBLISHED, 0);
			$res = B2DB::getTable('B2tArticles')->doUpdateById($crit, $this->_itemid);
			$this->is_published = false;
		}

		public function publish()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tArticles::IS_PUBLISHED, 1);
			$res = B2DB::getTable('B2tArticles')->doUpdateById($crit, $this->_itemid);
			$this->is_published = true;
		}
		
		public function setOrder($val)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tArticles::ORDER, (int) $val);
			
			$res = B2DB::getTable('B2tArticles')->doUpdateById($crit, $this->_itemid);
			$this->_order = (int) $val;
		}
		
		public function getOrder()
		{
			return $this->_order;
		}

		public function showInNews()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tArticles::IS_NEWS, 1);
			$res = B2DB::getTable('B2tArticles')->doUpdateById($crit, $this->_itemid);
			$this->is_news = true;
		}

		public function hideFromNews()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tArticles::IS_NEWS, 0);
			$res = B2DB::getTable('B2tArticles')->doUpdateById($crit, $this->_itemid);
			$this->is_news = false;
		}
		
		public function view()
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tArticleViews::ARTICLE_ID, $this->getID());
			$crit->addWhere(B2tArticleViews::USER_ID, BUGScontext::getUser()->getID());
			if (B2DB::getTable('B2tArticleViews')->doCount($crit) == 0)
			{
				$crit = new B2DBCriteria();
				$crit->addInsert(B2tArticleViews::ARTICLE_ID, $this->getID());
				$crit->addInsert(B2tArticleViews::USER_ID, BUGScontext::getUser()->getID());
				$crit->addInsert(B2tArticleViews::SCOPE, BUGScontext::getScope()->getID());
				B2DB::getTable('B2tArticleViews')->doInsert($crit);
			}
		}
		
		public function getViews()
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tArticleViews::ARTICLE_ID, $this->getID());
			return B2DB::getTable('B2tArticleViews')->doCount($crit);
		}
		
		public function getArticleType()
		{
			if ($this->hasAnyContent())
			{
				return 1;
			}
			if ($this->isLink())
			{
				return 3;
			}
			else
			{
				return 2;
			}
		}
		
		public function hasIntro()
		{
			return ($this->intro_text != '') ? true : false;
		}
		
		public function hasAnyContent()
		{
			if ($this->hasIntro() || $this->hasContent())
			{
				return true;
			}
			return false;
		}
		
		public function getIntro()
		{
			return $this->intro_text;
		}
		
		public function isNews()
		{
			return $this->is_news;
		}
		
		public function hasIcon()
		{
			return ($this->icon != '') ? true : false;
		}
		
		public function getIcon()
		{
			return $this->icon;
		}
		
		public function canRead()
		{
			return $this->allowed;
		}
		
	}

?>