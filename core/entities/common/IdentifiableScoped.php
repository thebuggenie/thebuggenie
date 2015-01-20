<?php

    namespace thebuggenie\core\entities\common;

    use thebuggenie\core\framework;

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
    abstract class IdentifiableScoped extends Identifiable
    {

        /**
         * The related scope
         *
         * @var integer
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Scope")
         */
        protected $_scope;

        /**
         * Set the scope this item is in
         *
         * @param \thebuggenie\core\entities\Scope $scope
         */
        public function setScope($scope)
        {
            $this->_scope = $scope;
        }

        /**
         * Retrieve the scope this item is in
         *
         * @return \thebuggenie\core\entities\Scope
         */
        public function getScope()
        {
            if (!$this->_scope instanceof \thebuggenie\core\entities\Scope)
                $this->_b2dbLazyload('_scope');

            return $this->_scope;
        }

        protected function getCurrentScope()
        {
            return framework\Context::getScope();
        }

        protected function getCurrentScopeID()
        {
            return framework\Context::getScope()->getID();
        }

        protected function _preSave($is_new)
        {
            if ($is_new && $this->_scope === null)
                $this->_scope = $this->getCurrentScope();
        }

        public function toJSON()
        {
            return array('id' => $this->getID());
        }

    }
