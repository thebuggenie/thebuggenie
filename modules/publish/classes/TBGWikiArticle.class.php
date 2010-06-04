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
		 * The old article content, used for history when saving
		 *
		 * @var string
		 */
		protected $_old_content = null;

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

		protected $_history = null;

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
					$row = TBGArticlesTable::getTable()->doSelectById($id);
				}

				if ($row instanceof B2DBRow)
				{
					$this->_itemid = $row->get(TBGArticlesTable::ID);

					$this->_name = $row->get(TBGArticlesTable::ARTICLE_NAME);

					$this->_title = $row->get(TBGArticlesTable::TITLE);
					$this->_intro_text = $row->get(TBGArticlesTable::INTRO_TEXT);
					$this->_content = str_replace("\r\n", "\n", $row->get(TBGArticlesTable::CONTENT));
					$this->_old_content = $this->_content;
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
				$row = TBGArticlesTable::getTable()->getArticleByName($article_name);
			}
			if ($row instanceof B2DBRow)
			{
				return PublishFactory::articleLab($row->get(TBGArticlesTable::ID), $row);
			}
			return null;
		}

		public static function deleteByName($article_name)
		{
			TBGArticlesTable::getTable()->deleteArticleByName($article_name);
			TBGArticleLinksTable::getTable()->deleteLinksByArticle($article_name);
		}

		public static function createNew($name, $content, $published, $scope = null, $options = array())
		{
			$user_id = (TBGContext::getUser() instanceof TBGUser) ? TBGContext::getUser()->getID() : 0;
			$article_id = TBGArticlesTable::getTable()->save($name, $content, $published, $user_id, null, $scope);
			$article = PublishFactory::articleLab($article_id);
			$article->save($options);
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
			$this->_content = str_replace("\r\n", "\n", $content);
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
				if ($res = TBGArticleLinksTable::getTable()->getLinkingArticles($this->getName()))
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
				if ($res = TBGArticleCategoriesTable::getTable()->getSubCategories($this->getCategoryName()))
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
				if ($res = TBGArticleCategoriesTable::getTable()->getCategoryArticles($this->getCategoryName()))
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
					if ($res = TBGArticleCategoriesTable::getTable()->getArticleCategories($this->getName()))
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

		protected function _populateHistory()
		{
			if ($this->_history === null)
			{
				$this->_history = array();
				$history = TBGArticleHistoryTable::getTable()->getHistoryByArticleName($this->getName());

				if ($history)
				{
					while ($row = $history->getNextRow())
					{
						$author = ($row->get(TBGArticleHistoryTable::AUTHOR)) ? TBGFactory::userLab($row->get(TBGArticleHistoryTable::AUTHOR)) : null;
						$this->_history[$row->get(TBGArticleHistoryTable::REVISION)] = array('old_content' => $row->get(TBGArticleHistoryTable::OLD_CONTENT), 'new_content' => $row->get(TBGArticleHistoryTable::NEW_CONTENT), 'change_reason' => $row->get(TBGArticleHistoryTable::REASON), 'updated' => $row->get(TBGArticleHistoryTable::DATE), 'author' => $author);
					}
				}
			}
		}

		public function getHistory()
		{
			$this->_populateHistory();
			return $this->_history;
		}

		public function save($options = array(), $reason = null)
		{
			if (TBGArticlesTable::getTable()->doesNameConflictExist($this->_name, $this->_itemid))
			{
				throw new Exception(TBGContext::getI18n()->__('Another article with this name already exists'));
			}
			$user_id = (TBGContext::getUser() instanceof TBGUser) ? TBGContext::getUser()->getID() : 0;

			if (!isset($options['revert']) || !$options['revert'])
			{
				TBGArticleHistoryTable::getTable()->addArticleHistory($this->_name, $this->_old_content, $this->_content, $user_id, $reason);
			}
			TBGArticlesTable::getTable()->save($this->_name, $this->_content, $this->_is_published, $user_id, $this->_itemid);

			$this->_old_content = $this->_content;

			TBGArticleLinksTable::getTable()->deleteLinksByArticle($this->_name);
			TBGArticleCategoriesTable::getTable()->deleteCategoriesByArticle($this->_name);

			list ($links, $categories) = $this->_retrieveLinksAndCategoriesFromContent($options);

			foreach ($links as $link => $occurrences)
			{
				TBGArticleLinksTable::getTable()->addArticleLink($this->_name, $link);
			}

			foreach ($categories as $category => $occurrences)
			{
				TBGArticleCategoriesTable::getTable()->addArticleCategory($this->_name, $category, $this->isCategory());
			}

			$this->_history  = null;

			return true;
		}

		public function retract()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGArticlesTable::IS_PUBLISHED, 0);
			$res = TBGArticlesTable::getTable()->doUpdateById($crit, $this->_itemid);
			$this->is_published = false;
		}

		public function publish()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGArticlesTable::IS_PUBLISHED, 1);
			$res = TBGArticlesTable::getTable()->doUpdateById($crit, $this->_itemid);
			$this->is_published = true;
		}

		public function hideFromNews()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGArticlesTable::IS_NEWS, 0);
			$res = TBGArticlesTable::getTable()->doUpdateById($crit, $this->_itemid);
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

		/**
		 * Compare to revisions of this article, and return the diff output, as well as revision information
		 *
		 * @param integer $from_revision
		 * @param integer $to_revision
		 *
		 * @return array
		 */
		public function compareRevisions($from_revision, $to_revision)
		{
			$content = TBGArticleHistoryTable::getTable()->getRevisionContentFromArticleName($this->getName(), $from_revision, $to_revision);
			$old_content = htmlspecialchars($content[$from_revision]['new_content']);
			$new_content = htmlspecialchars($content[$to_revision]['new_content']);

			$diff = new TBGTextDiff();
			$result = $diff->stringDiff($old_content, $new_content);
			$changes = $diff->sequentialChanges($result);
			return array($content, $diff->renderDiff($result));
		}

		public function restoreRevision($revision)
		{
			TBGArticleHistoryTable::getTable()->removeArticleRevisionsSince($this->getName(), $revision);
			$content = TBGArticleHistoryTable::getTable()->getRevisionContentFromArticleName($this->getName(), $revision);
			$this->setContent($content['new_content']);
			$this->save(array('revert' => true));
		}

		public function setRevision($revision = null)
		{
			$content = TBGArticleHistoryTable::getTable()->getRevisionContentFromArticleName($this->getName(), $revision);
			if (array_key_exists('new_content', $content))
			{
				$this->setContent($content['new_content']);
				$this->_posted_date = $content['date'];
				$this->_author = $content['author'];
			}
			else
			{
				throw new Exception('No such revision');
			}
		}

	}

?>