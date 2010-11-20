<?php

	class PublishBillboardPost extends PublishGenericContent 
	{

		protected $related_article_id = 0;
		
		protected $target_board = 0;
		
		/**
		 * Billboard post constructor. Takes integer $id or b2dbrow $id
		 *
		 * @param integer|B2DBrow $id
		 */
		public function __construct($id)
		{
			if (!$id instanceof B2DBRow)
			{
				$id = B2DB::getTable('TBGBillboardPostsTable')->doSelectById($id);
			}
			$this->_id = $id->get(TBGBillboardPostsTable::ID);
			
			$this->_name = $id->get(TBGBillboardPostsTable::TITLE);
			$this->title = $this->_name;
			$this->author = $id->get(TBGBillboardPostsTable::AUTHOR);
			
			$this->content = $id->get(TBGBillboardPostsTable::CONTENT);
			$this->posted_date = $id->get(TBGBillboardPostsTable::DATE);
			$this->link_url = $id->get(TBGBillboardPostsTable::LINK);
			
			$this->is_published = ($id->get(TBGBillboardPostsTable::IS_DELETED) == 0) ? true : false;
			$this->related_article_id = $id->get(TBGBillboardPostsTable::ARTICLE_ID);
			
			$this->target_board = $id->get(TBGBillboardPostsTable::TARGET_BOARD);
		}
		
		public function getTargetBoard()
		{
			return $this->target_board;
		}
		
		public function isLinkToArticle()
		{
			return ($this->related_article_id != 0) ? true : false;
		}
		
		public function getRelatedArticleID()
		{
			return $this->related_article_id;
		}
		
		public function delete()
		{
			B2DB::getTable('TBGBillboardPostsTable')->doDeleteById($this->_id);
		}
		
	}

