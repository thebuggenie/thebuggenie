<?php

	class TBGWikiArticle extends TBGIdentifiableClass
	{

		/**
		 * The article author
		 *
		 * @var TBGUser
		 */
		protected $_author = null;

		/**
		 * When the article was posted
		 *
		 * @var integer
		 */
		protected $_posted_date = null;

		/**
		 * The article name
		 *
		 * @var string
		 */
		protected $_name = null;

		/**
		 * The article title
		 * 
		 * @var string
		 */
		protected $_title = null;

		/**
		 * The article intro
		 *
		 * @var string
		 */
		protected $_intro_text = null;

		/**
		 * The article content
		 *
		 * @var string
		 */
		protected $_content = null;

		/**
		 * Whether the article is published or not
		 * @var boolean
		 */
		protected $_is_published = false;

		/**
		 * A list of articles that links to this article
		 *
		 * @var array
		 */
		protected $_linking_articles = null;

		/**
		 * A list of categories this article is in
		 *
		 * @var array
		 */
		protected $_categories = null;

		/**
		 * A list of subcategories for this category
		 *
		 * @var array
		 */
		protected $_subcategories = null;

		/**
		 * A list of page in this category
		 *
		 * @var array
		 */
		protected $_category_articles = null;

		/**
		 * Whether or not this page is a category page
		 *
		 * @var boolean
		 */
		protected $_is_category = null;

		protected $_category_name = null;

		/**
		 * Article constructor
		 *
		 * @param integer $id
		 * @param B2DBrow $row[optional]
		 */
		public function __construct($id = null, $row = null)
		{
			if ($id !== null)
			{
				if ($row === null)
				{
					$row = B2DB::getTable('TBGArticlesTable')->doSelectById($id);
				}

				if ($row instanceof B2DBRow)
				{
					$this->_itemid = $row->get(TBGArticlesTable::ID);

					$this->_name = $row->get(TBGArticlesTable::ARTICLE_NAME);

					$this->_title = $row->get(TBGArticlesTable::TITLE);
					$this->_intro_text = $row->get(TBGArticlesTable::INTRO_TEXT);
					$this->_content = $row->get(TBGArticlesTable::CONTENT);
					$this->_posted_date = $row->get(TBGArticlesTable::DATE);
					$this->_author = $row->get(TBGArticlesTable::AUTHOR);
					$this->_is_published = ($row->get(TBGArticlesTable::IS_PUBLISHED) == 1) ? true : false;
				}
				else
				{
					throw new Exception('This article does not exist');
				}
			}
		}

		public static function getByName($article_name, $row = null)
		{
			if ($row === null)
			{
				$row = B2DB::getTable('TBGArticlesTable')->getArticleByName($article_name);
			}
			if ($row instanceof B2DBRow)
			{
				return PublishFactory::articleLab($row->get(TBGArticlesTable::ID), $row);
			}
			return null;
		}

		public static function deleteByName($article_name)
		{
			B2DB::getTable('TBGArticlesTable')->deleteArticleByName($article_name);
			B2DB::getTable('TBGArticleLinksTable')->deleteLinksByArticle($article_name);
		}

		public static function createNew($name, $content, $published, $scope = null, $options = array())
		{
			$user_id = (TBGContext::getUser() instanceof TBGUser) ? TBGContext::getUser()->getID() : 0;
			$article_id = B2DB::getTable('TBGArticlesTable')->save($name, $content, $published, $user_id, null, $scope);
			PublishFactory::articleLab($article_id)->save($options);
			return $article_id;
		}

		public function __toString()
		{
			return $this->_content;
		}

		public function getTitle()
		{
			return $this->getName();
		}

		public function setTitle($title)
		{
			$this->_title = $title;
		}

		public function hasContent()
		{
			return ($this->_content != '') ? true : false;
		}

		public function getContent()
		{
			return $this->_content;
		}

		public function setContent($content)
		{
			$this->_content = $content;
			$parser = new TBGTextParser($content);
			$parser->doParse();
			$this->_populateCategories($parser->getCategories());
		}

		public function setName($name)
		{
			$this->_name = $name;
		}

		public function getLastUpdatedDate()
		{
			return $this->getPostedDate();
		}
		
		protected function _populateLinkingArticles()
		{
			if ($this->_linking_articles === null)
			{
				$this->_linking_articles = array();
				if ($res = B2DB::getTable('TBGArticleLinksTable')->getLinkingArticles($this->getName()))
				{
					while ($row = $res->getNextRow())
					{
						$this->_linking_articles[$row->get(TBGArticleLinksTable::ARTICLE_NAME)] = PublishFactory::articleNameLab($row->get(TBGArticleLinksTable::ARTICLE_NAME));
					}
				}
			}
		}

		public function getLinkingArticles()
		{
			$this->_populateLinkingArticles();
			return $this->_linking_articles;
		}

		protected function _populateSubCategories()
		{
			if ($this->_subcategories === null)
			{
				$this->_subcategories = array();
				if ($res = B2DB::getTable('TBGArticleCategoriesTable')->getSubCategories($this->getCategoryName()))
				{
					while ($row = $res->getNextRow())
					{
						$this->_subcategories[$row->get(TBGArticleCategoriesTable::ARTICLE_NAME)] = PublishFactory::articleNameLab($row->get(TBGArticleCategoriesTable::ARTICLE_NAME));
					}
				}
			}
		}

		public function getSubCategories()
		{
			$this->_populateSubCategories();
			return $this->_subcategories;
		}

		protected function _populateCategoryArticles()
		{
			if ($this->_category_articles === null)
			{
				$this->_category_articles = array();
				if ($res = B2DB::getTable('TBGArticleCategoriesTable')->getCategoryArticles($this->getCategoryName()))
				{
					while ($row = $res->getNextRow())
					{
						$this->_category_articles[$row->get(TBGArticleCategoriesTable::ARTICLE_NAME)] = PublishFactory::articleNameLab($row->get(TBGArticleCategoriesTable::ARTICLE_NAME));
					}
				}
			}
		}

		public function getCategoryArticles()
		{
			$this->_populateCategoryArticles();
			return $this->_category_articles;
		}

		protected function _populateCategories($categories = null)
		{
			if ($this->_categories === null || $categories !== null)
			{
				$this->_categories = array();
				if ($categories === null)
				{
					if ($res = B2DB::getTable('TBGArticleCategoriesTable')->getArticleCategories($this->getName()))
					{
						while ($row = $res->getNextRow())
						{
							$this->_categories[] = $row->get(TBGArticleCategoriesTable::CATEGORY_NAME);
						}
					}
				}
				else
				{
					foreach ($categories as $category => $occurrences)
					{
						$this->_categories[] = $category;
					}
				}
			}
		}

		public function getCategories()
		{
			$this->_populateCategories();
			return $this->_categories;
		}

		protected function _retrieveLinksAndCategoriesFromContent($options = array())
		{
			$parser = new TBGTextParser(html_entity_decode($this->_content));
			$parser->doParse($options);
			return array($parser->getInternalLinks(), $parser->getCategories());
		}

		public function isCategory()
		{
			if ($this->_is_category === null)
			{
				$names = explode(':', $this->_name);
				if (count($names) > 0)
				{
					$this->_is_category = (bool) ($names[0] == 'Category');
				}
				else
				{
					$this->_is_category = false;
				}
			}
			return $this->_is_category;
		}

		public function getCategoryName()
		{
			if ($this->_category_name === null)
			{
				$this->_category_name = substr($this->_name, strpos($this->_name, ':') + 1);
			}
			return $this->_category_name;
		}

		public function save($options = array())
		{
			if (B2DB::getTable('TBGArticlesTable')->doesNameConflictExist($this->_name, $this->_itemid))
			{
				throw new Exception(TBGContext::getI18n()->__('Another article with this name already exists'));
			}
			$user_id = (TBGContext::getUser() instanceof TBGUser) ? TBGContext::getUser()->getID() : 0;
			B2DB::getTable('TBGArticlesTable')->save($this->_name, $this->_content, $this->_is_published, $user_id, $this->_itemid);

			B2DB::getTable('TBGArticleLinksTable')->deleteLinksByArticle($this->_name);
			B2DB::getTable('TBGArticleCategoriesTable')->deleteCategoriesByArticle($this->_name);

			list ($links, $categories) = $this->_retrieveLinksAndCategoriesFromContent($options);

			foreach ($links as $link => $occurrences)
			{
				B2DB::getTable('TBGArticleLinksTable')->addArticleLink($this->_name, $link);
			}

			foreach ($categories as $category => $occurrences)
			{
				B2DB::getTable('TBGArticleCategoriesTable')->addArticleCategory($this->_name, $category, $this->isCategory());
			}

			return true;
		}

		public function retract()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGArticlesTable::IS_PUBLISHED, 0);
			$res = B2DB::getTable('TBGArticlesTable')->doUpdateById($crit, $this->_itemid);
			$this->is_published = false;
		}

		public function publish()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGArticlesTable::IS_PUBLISHED, 1);
			$res = B2DB::getTable('TBGArticlesTable')->doUpdateById($crit, $this->_itemid);
			$this->is_published = true;
		}

		public function hideFromNews()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGArticlesTable::IS_NEWS, 0);
			$res = B2DB::getTable('TBGArticlesTable')->doUpdateById($crit, $this->_itemid);
			$this->is_news = false;
		}
		
		public function view()
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGArticleViewsTable::ARTICLE_ID, $this->getID());
			$crit->addWhere(TBGArticleViewsTable::USER_ID, TBGContext::getUser()->getID());
			if (B2DB::getTable('TBGArticleViewsTable')->doCount($crit) == 0)
			{
				$crit = new B2DBCriteria();
				$crit->addInsert(TBGArticleViewsTable::ARTICLE_ID, $this->getID());
				$crit->addInsert(TBGArticleViewsTable::USER_ID, TBGContext::getUser()->getID());
				$crit->addInsert(TBGArticleViewsTable::SCOPE, TBGContext::getScope()->getID());
				B2DB::getTable('TBGArticleViewsTable')->doInsert($crit);
			}
		}
		
		public function getViews()
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGArticleViewsTable::ARTICLE_ID, $this->getID());
			return B2DB::getTable('TBGArticleViewsTable')->doCount($crit);
		}
		
		public function hasIntro()
		{
			return ($this->_intro_text != '') ? true : false;
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
			return $this->_intro_text;
		}
		
		public function canRead()
		{
			return true;
		}

		public function isPublished()
		{
			return $this->_is_published;
		}

		public function getPostedDate()
		{
			return $this->_posted_date;
		}

		/**
		 * REturns the author
		 *
		 * @return TBGUser
		 */
		public function getAuthor()
		{
			if (is_numeric($this->_author))
			{
				try
				{
					$this->_author = TBGFactory::userLab($this->_author);
				}
				catch (Exception $e)
				{
					$this->_author = null;
				}
			}
			return $this->_author;
		}


	}

?>