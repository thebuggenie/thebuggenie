<?php

    namespace thebuggenie\core\entities\traits;

    use thebuggenie\core\entities\Comment;

    /**
     * Trait for things that can have comments
     *
     * @package thebuggenie
     * @subpackage traits
     */
    trait Commentable
    {

        /**
         * An array of \thebuggenie\core\entities\Comments
         *
         * @var array
         * @Relates(class="\thebuggenie\core\entities\Comment", collection=true, foreign_column="target_id")
         */
        protected $_comments;

        protected $_num_comments;

        /**
         * Retrieve all comments for this issue
         *
         * @return Comment[]
         */
        public function getComments()
        {
            $this->_populateComments();
            return $this->_comments;
        }

        /**
         * Populate comments array
         */
        protected function _populateComments()
        {
            if ($this->_comments === null)
            {
                $this->_b2dbLazyload('_comments');
            }
        }

        /**
         * Return the number of comments
         *
         * @return integer
         */
        public function getCommentCount()
        {
            if ($this->_num_comments === null)
            {
                if ($this->_comments !== null)
                    $this->_num_comments = count($this->_comments);
                else
                    $this->_num_comments = $this->_b2dbLazycount('_comments');
            }

            return $this->_num_comments;
        }

        public function countComments()
        {
            return $this->getCommentCount();
        }

    }
