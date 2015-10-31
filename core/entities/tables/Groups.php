<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;

    /**
     * Groups table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Groups table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="groups")
     * @Entity(class="\thebuggenie\core\entities\Group")
     */
    class Groups extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'groups';
        const ID = 'groups.id';
        const NAME = 'groups.name';
        const SCOPE = 'groups.scope';

        public function getAll($scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, $scope);
            
            $res = $this->select($crit);
            
            return $res;
        }

        public function doesGroupNameExist($group_name)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::NAME, $group_name);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            return (bool) $this->doCount($crit);
        }
        
    }
