<?php

	class TBGFile extends TBGIdentifiableClass
	{

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

		public function __construct($id, $row = null)
		{
			if (!is_numeric($id))
			{
				throw new Exception('Please specify a file id');
			}
			if ($row === null)
			{
				$row = TBGFilesTable::getTable()->getByID($id);
			}

			if (!$row instanceof B2DBRow)
			{
				throw new Exception('The specified file id does not exist');
			}

			$this->_itemid = $row->get(TBGFilesTable::ID);
			$this->_content_type = $row->get(TBGFilesTable::CONTENT_TYPE);
			$this->_uploaded_by = $row->get(TBGFilesTable::UID);
			$this->_uploaded_at = $row->get(TBGFilesTable::UPLOADED_AT);
			$this->_real_filename = $row->get(TBGFilesTable::REAL_FILENAME);
			$this->_original_filename = $row->get(TBGFilesTable::ORIGINAL_FILENAME);
			$this->_content = ($row->get(TBGFilesTable::CONTENT)) ? $row->get(TBGFilesTable::CONTENT) : null;
			$this->_description = ($row->get(TBGFilesTable::DESCRIPTION)) ? $row->get(TBGFilesTable::DESCRIPTION) : null;
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