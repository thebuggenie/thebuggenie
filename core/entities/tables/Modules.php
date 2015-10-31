<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;

/**
     * Modules table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Modules table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @method Modules getTable() Retrieves an instance of this table
     * @method \thebuggenie\core\entities\Module selectById(integer $id) Retrieves a module
     *
     * @Table(name="modules")
     * @Entity(class="\thebuggenie\core\entities\Module")
     */
    class Modules extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'modules';
        const ID = 'modules.id';
        const MODULE_NAME = 'modules.name';
        const MODULE_LONGNAME = 'modules.module_longname';
        const ENABLED = 'modules.enabled';
        const VERSION = 'modules.version';
        const CLASSNAME = 'modules.classname';
        const SCOPE = 'modules.scope';

        public function getAll()
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $modules = array();

            if ($res = $this->doSelect($crit))
            {
                while ($row = $res->getNextRow())
                {
                    $module_name = $row->get(self::MODULE_NAME);
                    $classname = "\\thebuggenie\\modules\\{$module_name}\\".ucfirst($module_name);
                    $modules[$module_name] = new $classname($row->get(self::ID), $row);
                }
            }

            return $modules;
        }

        public function getAllNames()
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addSelectionColumn(self::MODULE_NAME);
            $names = array();

            if ($res = $this->doSelect($crit))
            {
                while ($row = $res->getNextRow())
                {
                    $names[$row->get(self::MODULE_NAME)] = true;
                }
            }

            return $names;
        }

        public function disableModuleByID($module_id)
        {
            $crit = $this->getCriteria();
            $crit->addUpdate(self::ENABLED, 0);
            return $this->doUpdateById($crit, $module_id);
        }

        public function setModuleVersion($module_key, $version)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::MODULE_NAME, $module_key);
            $crit->addUpdate(self::VERSION, $version);
            return $this->doUpdate($crit);
        }

        public function removeModuleByID($module_id)
        {
            return $this->doDeleteById($module_id);
        }

        public function disableModuleByName($module_name)
        {
            $crit = $this->getCriteria();
            $crit->addUpdate(self::ENABLED, 0);
            $crit->addWhere(self::MODULE_NAME, $module_name);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            return $this->doUpdate($crit);
        }

        public function installModule($identifier, $scope)
        {
            $classname = "\\thebuggenie\\modules\\".$identifier."\\".ucfirst($identifier);
            if (!class_exists("\\thebuggenie\\modules\\".$identifier."\\".ucfirst($identifier)))
            {
                throw new \Exception('Can not load new instance of type \\thebuggenie\\modules\\'.$identifier."\\".ucfirst($identifier) . ', is not loaded');
            }

            $crit = $this->getCriteria();
            $crit->addWhere(self::MODULE_NAME, $identifier);
            $crit->addWhere(self::SCOPE, $scope);
            if (!$res = $this->doSelectOne($crit))
            {
                $crit = $this->getCriteria();
                $crit->addInsert(self::ENABLED, true);
                $crit->addInsert(self::MODULE_NAME, $identifier);
                $crit->addInsert(self::VERSION, $classname::VERSION);
                $crit->addInsert(self::SCOPE, $scope);
                $module_id = $this->doInsert($crit)->getInsertID();
            }
            else
            {
                $module_id = $res->get(self::ID);
            }

            $module = new $classname($module_id);
            return $module;
        }

        public function getModulesForScope($scope_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, $scope_id);

            $return_array = array();
            if ($res = $this->doSelect($crit))
            {
                while ($row = $res->getNextRow())
                {
                    $return_array[$row->get(self::MODULE_NAME)] = (bool) $row->get(self::ENABLED);
                }
            }

            return $return_array;
        }

        public function getModuleForScope($module_name, $scope_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::MODULE_NAME, $module_name);
            $crit->addWhere(self::SCOPE, $scope_id);

            $module = null;
            if ($row = $this->doSelectOne($crit))
            {
                $classname = $row->get(self::CLASSNAME);
                $module = new $classname($row->get(self::ID), $row);
            }

            return $module;
        }

    }
