<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\entities\common\Keyable;

    /**
     * Generic datatype class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage main
     */

    /**
     * Generic datatype class
     *
     * @package thebuggenie
     * @subpackage main
     *
     * @Table(name="\thebuggenie\core\entities\tables\ListTypes")
     */
    abstract class DatatypeBase extends Keyable
    {

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * Item type
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_itemtype = null;

        /**
         * Extra data for that data type (if any)
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_itemdata = null;

        /**
         * Sort order of this item
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_sort_order = null;

        /**
         * Return the items name
         *
         * @return string
         */
        public function getName()
        {
            return $this->_name;
        }

        /**
         * Set the edition name
         *
         * @param string $name
         */
        public function setName($name)
        {
            $this->_name = $name;
            $this->_generateKey();
        }

        /**
         * Returns the itemdata associated with the datatype (if any)
         *
         * @return string
         */
        public function getItemdata()
        {
            return $this->_itemdata;
        }

        /**
         * Set the itemdata
         *
         * @param string $itemdata
         */
        public function setItemdata($itemdata)
        {
            $this->_itemdata = $itemdata;
        }

        /**
         * Invoked when trying to print the object
         *
         * @return string
         */
        public function __toString()
        {
            return $this->_name;
        }

        public function getItemtype()
        {
            return $this->_itemtype;
        }

        public function setItemtype($itemtype)
        {
            $this->_itemtype = $itemtype;
        }

        public static function getAvailableFields($builtin_only = false)
        {
            $builtin_types = array('shortname', 'description', 'reproduction_steps', 'status', 'category', 'resolution', 'priority', 'reproducability', 'percent_complete', 'severity', 'owned_by', 'assignee', 'edition', 'build', 'component', 'estimated_time', 'spent_time', 'milestone', 'user_pain', 'votes');

            if ($builtin_only) return $builtin_types;

            $customtypes = CustomDatatype::getAll();
            $types = array_merge($builtin_types, array_keys($customtypes));

            return $types;
        }

        public function getPermissionsKey()
        {
            return 'set_datatype_' . $this->_itemtype;
        }

        public function canUserSet(\thebuggenie\core\entities\User $user)
        {
            $retval = $user->hasPermission($this->getPermissionsKey(), $this->getID(), 'core');
            $retval = ($retval === null) ? $user->hasPermission($this->getPermissionsKey(), 0, 'core') : $retval;

            return ($retval !== null) ? $retval : \thebuggenie\core\framework\Settings::isPermissive();
        }

        public function setOrder($order)
        {
            $this->_sort_order = $order;
        }

        public function getOrder()
        {
            return (int) $this->_sort_order;
        }

        public function toJSON()
        {
            return array('id' => $this->getID(), 'itemdata' => $this->getItemdata(), 'itemtype' => $this->_itemtype, 'name' => $this->getName(), 'key' => $this->getKey());
        }

        abstract function isBuiltin();

    }
