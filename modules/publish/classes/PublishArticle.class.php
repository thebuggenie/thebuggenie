<?php

	class PublishArticle extends BUGSidentifiableclass implements BUGSidentifiable
	{

		/**
		 * The article author
		 *
		 * @var BUGSuser
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
		 * Article constructor
		 *
		 * @param integer $id
		 * @param B2DBrow $row[optional]
		 */
		public function __construct($id, $row = null)
		{
			if ($row === null)
			{
				$row = B2DB::getTable('B2tArticles')->doSelectById($id);
			}

			if ($row instanceof B2DBRow)
			{
				$this->_itemid = $row->get(B2tArticles::ID);

				$this->_name = $row->get(B2tArticles::ARTICLE_NAME);

				$this->_title = $row->get(B2tArticles::TITLE);
				$this->_intro_text = $row->get(B2tArticles::INTRO_TEXT);
				$this->_content = $row->get(B2tArticles::CONTENT);
				$this->_posted_date = $row->get(B2tArticles::DATE);
				$this->_author = $row->get(B2tArticles::AUTHOR);

				$this->_is_published = ($row->get(B2tArticles::IS_PUBLISHED) == 1) ? true : false;
			}
			else
			{
				throw new Exception('This article does not exist');
			}
		}

		public static function getByName($article_name, $row = null)
		{
			if ($row === null)
			{
				$row = B2DB::getTable('B2tArticles')->getArticleByName($article_name);
			}
			if ($row instanceof B2DBRow)
			{
				return PublishFactory::articleLab($row->get(B2tArticles::ID), $row);
			}
			return null;
		}

		public static function deleteByName($article_name)
		{
			B2DB::getTable('B2tArticles')->deleteArticleByName($article_name);
			B2DB::getTable('B2tArticleLinks')->deleteLinksByArticle($article_name);
		}

		public static function createNew($name, $content, $published)
		{
			$article_id = B2DB::getTable('B2tArticles')->save($name, $content, $published, BUGScontext::getUser()->getID());
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
				if ($res = B2DB::getTable('B2tArticleLinks')->getLinkingArticles($this->getName()))
				{
					while ($row = $res->getNextRow())
					{
						$this->_linking_articles[$row->get(B2tArticleLinks::ARTICLE_NAME)] = PublishFactory::articleNameLab($row->get(B2tArticleLinks::ARTICLE_NAME));
					}
				}
			}
		}

		public function getLinkingArticles()
		{
			$this->_populateLinkingArticles();
			return $this->_linking_articles;
		}

		protected function _retrieveInternalLinksFromContent()
		{
			$parser = new BUGSTextParser(html_entity_decode($this->_content));
			$parser->doParse();
			return $parser->getInternalLinks();
		}

		public function save()
		{
			if (B2DB::getTable('B2tArticles')->doesNameConflictExist($this->_name, $this->_itemid))
			{
				throw new Exception(BUGScontext::getI18n()->__('Another article with this name already exists'));
			}
			B2DB::getTable('B2tArticles')->save($this->_name, $this->_content, $this->_is_published, BUGScontext::getUser()->getID(), $this->_itemid);
			B2DB::getTable('B2tArticleLinks')->deleteLinksByArticle($this->_name);
			foreach ($this->_retrieveInternalLinksFromContent() as $link => $occurrences)
			{
				B2DB::getTable('B2tArticleLinks')->addArticleLink($this->_name, $link);
			}

			return true;
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
		 * @return BUGSuser
		 */
		public function getAuthor()
		{
			if (is_numeric($this->_author))
			{
				try
				{
					$this->_author = BUGSfactory::userLab($this->_author);
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