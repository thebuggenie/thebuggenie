<?php

	class PublishGenericContent extends BUGSidentifiableclass implements BUGSidentifiable 
	{
		
		protected $title = '';
		protected $content = '';
		protected $posted_date = 0;
		protected $author = null;
		
		protected $link_url = '';
		
		protected $is_published = false;
		
		public function isPublished()
		{
			return $this->is_published;
		}

		public function getPostedDate()
		{
			return $this->posted_date;
		}
		
		/**
		 * REturns the author
		 *
		 * @return BUGSuser
		 */
		public function getAuthor()
		{
			if (!$this->author instanceof BUGSuser)
			{
				$this->author = BUGSfactory::userLab($this->author);
			}
			return $this->author;
		}

		public function isLink()
		{
			return ($this->link_url != '') ? true : false;
		}
		
		public function getLinkURL()
		{
			return $this->link_url;
		}
		
		public function getTitle()
		{
			return $this->title;
		}
		
		public function hasContent()
		{
			return ($this->content != '') ? true : false;
		}
		
		public function getContent()
		{
			return $this->content;
		}
		
		public function getID()
		{
			return $this->_itemid;
		}
		
		public function getName()
		{
			return $this->_name;
		}
		
		public function __toString()
		{
			return $this->title;
		}

	}

?>