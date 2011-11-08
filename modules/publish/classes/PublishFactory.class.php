<?php

	class PublishFactory
	{
		
		protected static $_articles = array();
		protected static $_article_names = array();
		
		/**
		 * Returns a publish article
		 * 
		 * @param $a_id
		 * @param $row
		 * 
		 * @return TBGWikiArticle
		 */
		static function article($a_id, $row = null)
		{
			if ($a_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_articles[$a_id]))
			{
				try
				{
					self::$_articles[$a_id] = new TBGWikiArticle($a_id, $row);
					self::$_article_names[self::$_articles[$a_id]->getName()] = $a_id;
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_articles[$a_id];
		}

		/**
		 * Returns a publish article
		 *
		 * @param $a_id
		 * @param $row
		 *
		 * @return TBGWikiArticle
		 */
		static function articleName($article_name, $row = null)
		{
			if (!isset(self::$_article_names[$article_name]))
			{
				try
				{
					$article = TBGWikiArticle::getByName($article_name);
					if ($article instanceof TBGWikiArticle)
					{
						self::$_articles[$article->getID()] = $article;
						self::$_article_names[$article_name] = $article->getID();
					}
					else
					{
						throw new Exception('No such article');
					}
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_articles[self::$_article_names[$article_name]];
		}
		
	}
