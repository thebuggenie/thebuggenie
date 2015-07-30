<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;
    use b2db\Table;

    /**
     * B2DB class that all  class extends, implementing scope access
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage mvc
     */

    /**
     * B2DB class that all  class extends, implementing scope access
     *
     * @package thebuggenie
     * @subpackage mvc
     */
    class ScopedTable extends Table
    {
        
        /**
         * Return a row for the specified id in the current scope, if defined
         * 
         * @param integer $id
         * 
         * @return \b2db\Row
         */
        public function getByID($id)
        {
            if (defined('static::SCOPE'))
            {
                $crit = $this->getCriteria();
                $crit->addWhere(static::SCOPE, $this->getCurrentScopeID());
                $row = $this->doSelectById($id, $crit);
            }
            else
            {
                $row = $this->doSelectById($id);
            }
            return $row;
        }

        public function selectAll()
        {
            if (defined('static::SCOPE'))
            {
                $crit = $this->getCriteria();
                $crit->addWhere(static::SCOPE, $this->getCurrentScopeID());
                $results = $this->select($crit);
            }
            else
            {
                $results = parent::selectAll();
            }
            return $results;
        }

        protected function _setup($b2db_name, $id_column)
        {
            parent::_setup($b2db_name, $id_column);
            parent::_addForeignKeyColumn(static::SCOPE, \thebuggenie\core\entities\tables\Scopes::getTable(), \thebuggenie\core\entities\tables\Scopes::ID);
        }

        public function deleteFromScope($scope)
        {
            $crit = $this->getCriteria();
            if (defined('static::SCOPE'))
            {
                $crit->addWhere(static::SCOPE, $scope);
            }
            $res = $this->doDelete($crit);
            return $res;
        }

        protected function getCurrentScope()
        {
            return framework\Context::getScope();
        }

        protected function getCurrentScopeID()
        {
            return framework\Context::getScope()->getID();
        }

    }