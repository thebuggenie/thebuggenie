<?php

	class TBGFile extends TBGIdentifiableClass
	{
		
		protected $_b2dbtablename = 'TBGFilesTable';

		protected $_content_type;

		protected $_uploaded_by;

		protected $_uploaded_at;

		protected $_real_filename;

		protected $_original_filename;

		protected $_content;

		protected $_description;

		public static function getByIssueID($issue_id)
		{
			return TBGIssueFilesTable::getTable()->getByIssueID($issue_id);
		}

		public static function getImageContentTypes()
		{
			return array('image/png', 'image/jpeg', 'image/jpg', 'image/bmp', 'image/gif');
		}

		/**
		 * Create a new file and save it
		 *
		 * @param <type> $real_filename
		 * @param <type> $original_filename
		 * @param <type> $content_type
		 * @param <type> $description
		 * @param <type> $content
		 *
		 * @return TBGFile
		 */
		public static function createNew($real_filename, $original_filename, $content_type, $description = null, $content = null)
		{
			$file_id = TBGFilesTable::getTable()->saveFile($real_filename, $original_filename, $content_type, $description, $content);
			return new TBGFile($file_id);
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
			return TBGSettings::getUploadsLocalpath() . $this->getOriginalFilename();
		}

		public function doesFileExistOnDisk()
		{
			return file_exists($this->getFullpath());
		}

		public function delete()
		{
			if ($this->doesFileExistOnDisk())
			{
				unlink($this->getFullpath());
			}
			TBGFilesTable::getTable()->doDeleteById($this->getID());
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

	}