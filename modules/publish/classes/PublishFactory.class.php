<?php

	class PublishFactory
	{
		
		static protected $_articles = array();
		static protected $_article_names = array();
		
		/**
		 * Returns a publish article
		 * 
		 * @param $a_id
		 * @param $row
		 * 
		 * @return PublishArticle
		 */
		static function articleLab($a_id, $row = null)
		{
			if ($a_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_articles[$a_id]))
			{
				try
				{
					self::$_articles[$a_id] = new PublishArticle($a_id, $row);
					self::$_article_names[self::$_articles[$a_id]->getName()] = $a_id;
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_articles[$a_id];
		}

		static function articleNameLab($article_name, $row)
		{
			if (!isset(self::$_article_names[$article_name]))
			{
				try
				{
					$article = PublishArticle::getByName($article_name);
					if ($article instanceof PublishArticle)
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
