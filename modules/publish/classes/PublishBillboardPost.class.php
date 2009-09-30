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
				$id = B2DB::getTable('B2tBillboardPosts')->doSelectById($id);
			}
			$this->_itemid = $id->get(B2tBillboardPosts::ID);
			
			$this->_name = $id->get(B2tBillboardPosts::TITLE);
			$this->title = $this->_name;
			$this->author = $id->get(B2tBillboardPosts::AUTHOR);
			
			$this->content = $id->get(B2tBillboardPosts::CONTENT);
			$this->posted_date = $id->get(B2tBillboardPosts::DATE);
			$this->link_url = $id->get(B2tBillboardPosts::LINK);
			
			$this->is_published = ($id->get(B2tBillboardPosts::IS_DELETED) == 0) ? true : false;
			$this->related_article_id = $id->get(B2tBillboardPosts::ARTICLE_ID);
			
			$this->target_board = $id->get(B2tBillboardPosts::TARGET_BOARD);
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
			B2DB::getTable('B2tBillboardPosts')->doDeleteById($this->_itemid);
		}
		
	}

?>