<?php

    namespace thebuggenie\core\entities\common;

    /**
     * Releaseable item class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * Releaseable item class
     *
     * @package thebuggenie
     * @subpackage core
     */
    class Releaseable extends Ownable
    {

        /**
         * Whether the item is released (generally available)
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_isreleased;

        /**
         * The items release date
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_release_date = 0;
        
        /**
         * Is the item released?
         * 
         * @return boolean
         */
        public function isReleased()
        {
            return $this->_isreleased;
        }
        
        public function setReleased($released = true)
        {
            $this->_isreleased = (bool) $released;
        }

        /**
         * Set the release date
         *
         * @param integer $release_date
         */
        public function setReleaseDate($release_date = null)
        {
            $this->_release_date = $release_date;
        }

        /**
         * Return whether or not this item has a release date specified
         *
         * @return boolean
         */
        public function hasReleaseDate()
        {
            return (bool) ($this->_release_date > 0);
        }

        /**
         * Returns the release date
         *
         * @return integer
         */
        public function getReleaseDate()
        {
            return $this->_release_date;
        }
        
        /**
         * Returns the release date year
         *
         * @return integer
         */
        public function getReleaseDateYear()
        {
            return date("Y", $this->_release_date);
        }
        
        /**
         * Returns the release date month
         *
         * @return integer
         */
        public function getReleaseDateMonth()
        {
            return date("n", $this->_release_date);
        }

        /**
         * Returns the release date day
         *
         * @return integer
         */
        public function getReleaseDateDay()
        {
            return date("j", $this->_release_date);
        }
        
        /**
         * Returns the release date hour
         *
         * @return integer
         */
        public function getReleaseDateHour()
        {
            return date("h", $this->_release_date);
        }
        
        /**
         * Returns the release date minute
         *
         * @return integer
         */
        public function getReleaseDateMinute()
        {
            return date("i", $this->_release_date);
        }
        
        /**
         * Returns the release date AM/PM value
         *
         * @return integer
         */
        public function getReleaseDateAMPM()
        {
            return date("A", $this->_release_date);
        }
        
    }
