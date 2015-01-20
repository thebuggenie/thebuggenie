<?php

    namespace thebuggenie\core\entities\common;

    use b2db\Saveable;

    /**
     * An identifiable class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * An identifiable class
     *
     * @package thebuggenie
     * @subpackage core
     */
    abstract class Identifiable extends Saveable
    {

        /**
         * The id for this item, usually identified by a record in the database
         *
         * @var integer
         * @Id
         * @Column(type="integer", not_null=true, auto_increment=1, length=10, unsigned=true)
         */
        protected $_id;

        /**
         * Return the items id
         *
         * @return integer
         */
        public function getID()
        {
            return (int) $this->_id;
        }

        /**
         * Set the items id
         *
         * @param integer $id
         */
        public function setID($id)
        {
            $this->_id = (int) $id;
        }

    }
