<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\entities\common\IdentifiableScoped,
        thebuggenie\modules\publish\entities\tables\ArticleFiles;

    /**
     * @Table(name="\thebuggenie\core\entities\tables\Files")
     */
    class File extends IdentifiableScoped
    {

        /**
         * @Column(type="string", length=200)
         */
        protected $_content_type;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_uploaded_at;

        /**
         * @Column(type="string", length=200)
         */
        protected $_real_filename;

        /**
         * @Column(type="string", length=200, name="original_filename")
         */
        protected $_name;

        /**
         * @Column(type="blob")
         */
        protected $_content;

        /**
         * @Column(type="string", length=200)
         */
        protected $_description;

        /**
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\User")
         */
        protected $_uid;

        public static function getByIssueID($issue_id)
        {
            return tables\IssueFiles::getTable()->getByIssueID($issue_id);
        }

        public static function countByIssueID($issue_id)
        {
            return tables\IssueFiles::getTable()->countByIssueID($issue_id);
        }

        public static function getByArticleID($article_id)
        {
            return ArticleFiles::getTable()->getByArticleID($article_id);
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
            return $this->_b2dbLazyload('_uid');
        }

        public function setUploadedBy($uploaded_by)
        {
            $this->_uid = $uploaded_by;
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
            return $this->_name;
        }

        public function setOriginalFilename($original_filename)
        {
            $this->_name = $original_filename;
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
            return \thebuggenie\core\framework\Settings::getUploadsLocalpath() . $this->getRealFilename();
        }

        public function doesFileExistOnDisk()
        {
            return file_exists($this->getFullpath());
        }

        protected function _preDelete()
        {
            if ($this->doesFileExistOnDisk())
            {
                unlink($this->getFullpath());
            }
        }

        public function getSize()
        {
            return ($this->doesFileExistOnDisk()) ? filesize($this->getFullpath()) : 0;
        }

        public function getReadableFilesize()
        {
            $size = $this->getSize();
            if ($size > 1024 * 1024)
            {
                return round(($size * 100 / (1024 * 1024)) / 100, 2) . 'MB';
            }
            else
            {
                return round(($size * 100 / 1024) / 100, 2) . 'KB';
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
            if (\thebuggenie\core\framework\Settings::getUploadStorage() == 'files')
            {
                rename($this->getFullpath(), \thebuggenie\core\framework\Settings::getUploadsLocalpath() . $target_path);
            }
            $this->setRealFilename($target_path);
            $this->save();
        }

        public function hasAccess()
        {
            $issue_ids = tables\IssueFiles::getTable()->getIssuesByFileID($this->getID());

            foreach ($issue_ids as $issue_id)
            {
                $issue = new \thebuggenie\core\entities\Issue($issue_id);
                if ($issue->hasAccess())
                    return true;
            }

            $event = \thebuggenie\core\framework\Event::createNew('core', 'thebuggenie\core\entities\File::hasAccess', $this);
            $event->setReturnValue(false);
            $event->triggerUntilProcessed();

            return $event->getReturnValue();
        }

    }
