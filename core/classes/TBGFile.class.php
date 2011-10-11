<?php

	class TBGFile extends TBGIdentifiableClass
	{
		
		static protected $_b2dbtablename = 'TBGFilesTable';

		protected $_content_type;

		protected $_uploaded_by;

		protected $_uploaded_at;

		protected $_real_filename;

		protected $_original_filename;

		protected $_content;

		protected $_description;
		
		protected $_uid;

		public static function getByIssueID($issue_id)
		{
			return TBGIssueFilesTable::getTable()->getByIssueID($issue_id);
		}

		public static function countByIssueID($issue_id)
		{
			return TBGIssueFilesTable::getTable()->countByIssueID($issue_id);
		}

		public static function getByArticleID($article_id)
		{
			return TBGArticleFilesTable::getTable()->getByArticleID($article_id);
		}

		public static function getImageContentTypes()
		{
			return array('image/png', 'image/jpeg', 'image/jpg', 'image/bmp', 'image/gif');
		}

		public static function getMimeType($filename)
		{
			$content_type = null;
			if (function_exists('finfo_open'))
			{
				$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
				$content_type = finfo_file($finfo, $filename);
				finfo_close($finfo);
			}
			elseif (function_exists('mime_content_type'))
			{
				$content_type = mime_content_type($filename);
			}

			return $content_type;
		}

		public function getContentType()
		{
			return $this->_content_type;
		}

		public function setContentType($content_type)
		{
			$this->_content_type = $content_type;
		}

		public function isImage()
		{
			return in_array($this->_content_type, self::getImageContentTypes());
		}

		public function getUploadedBy()
		{
			return $this->_uploaded_by;
		}

		public function setUploadedBy($uploaded_by)
		{
			$this->_uploaded_by = $uploaded_by;
		}

		public function getUploadedAt()
		{
			return $this->_uploaded_at;
		}

		public function setUploadedAt($uploaded_at)
		{
			$this->_uploaded_at = $uploaded_at;
		}

		public function getRealFilename()
		{
			return $this->_real_filename;
		}

		public function setRealFilename($real_filename)
		{
			$this->_real_filename = $real_filename;
		}

		public function getOriginalFilename()
		{
			return $this->_original_filename;
		}

		public function setOriginalFilename($original_filename)
		{
			$this->_original_filename = $original_filename;
		}

		public function getContent()
		{
			return $this->_content;
		}

		public function setContent($content)
		{
			$this->_content = $content;
		}

		public function getFullpath()
		{
			return TBGSettings::getUploadsLocalpath() . $this->getRealFilename();
		}

		public function doesFileExistOnDisk()
		{
			return file_exists($this->getFullpath());
		}

		public function _preDelete()
		{
			if ($this->doesFileExistOnDisk())
			{
				unlink($this->getFullpath());
			}
		}

		public function getDescription()
		{
			return $this->_description;
		}

		public function hasDescription()
		{
			return (bool) ($this->getDescription() != '');
		}

		public function setDescription($description)
		{
			$this->_description = $description;
		}
		
		public function move($target_path)
		{
			if (TBGSettings::getUploadStorage() == 'files')
			{
				rename($this->getFullpath(), TBGSettings::getUploadsLocalpath() . $target_path);
			}
			$this->setRealFilename($target_path);
			$this->save();
		}
		
	}