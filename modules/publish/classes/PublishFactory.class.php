<?php

	class PublishFactory
	{
		
		static protected $_articles = array();
		
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
			if (!isset(self::$_articles[$a_id]))
			{
				if ($row instanceof B2DBRow)
				{
					self::$_articles[$a_id] = new PublishArticle($row);
				}
				else
				{
					self::$_articles[$a_id] = new PublishArticle($a_id);
				}
			}
			return self::$_articles[$a_id];
		}
		
	}
