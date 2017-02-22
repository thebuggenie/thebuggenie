<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\entities\common\Identifiable;

    /**
     * @Table(name="\thebuggenie\core\entities\tables\RolePermissions")
     */
    class RolePermission extends Identifiable
    {

        /**
         * @Column(name="module", type="string", length="100")
         */
        protected $_module = 'core';

        /**
         * @Column(name="target_id", type="string", length="250")
         */
        protected $_target_id;

        /**
         * @Column(name="permission", type="string", length="250")
         */
        protected $_permission;

        /**
         * @Column(name="role_id", type="integer", length="10")
         * @Relates(class="\thebuggenie\core\entities\Role")
         */
        protected $_role;

        public function getModule()
        {
            return $this->_module;
        }

        public function setModule($module)
        {
            $this->_module = $module;
        }

        public function getTargetID()
        {
            return $this->_target_id;
        }

        public function setTargetID($target_id)
        {
            $this->_target_id = $target_id;
        }

        public function getPermission()
        {
            return $this->_permission;
        }

        public function setPermission($permission)
        {
            $this->_permission = $permission;
        }

        public function getRole()
        {
            return $this->_role;
        }

        public function setRole($role)
        {
            $this->_role = $role;
        }

        public function hasTargetID()
        {
            return (bool) ($this->_target_id);
        }

        public function getReplacedTargetID(\thebuggenie\core\entities\Project $project)
        {
            return str_replace('%project_key%', $project->getKey(), $this->_target_id);
        }

        /**
         * Returns expanded target ID specific to passed-in project. Some role
         * permissions may consist out of parametrised string, in which case the
         * parameters need to be replaced based on project content.
         *
         * This method will take in as argument a project to which the role
         * permission is applied, perform all the necessary expanding of
         * target_id, and return a valid target_id that can be further used for
         * processing permissions.
         *
         * If no expansion needs to be done, project ID will be returned.
         *
         * @param project \thebuggenie\core\entities\Project Project against for which the extended target ID should be obtained.
         *
         * @return int|string Target ID for which the role permission is applicable.
         */
        public function getExpandedTargetIDForProject($project)
        {
            // If we have explicit target ID, probably need to do some string
            // replacements.
            if ($this->hasTargetID())
            {
                return $this->getReplacedTargetID($project);
            }
            // If this is targeting a specific project, return its ID.
            else if ($project instanceof Project)
            {
                return $project->getID();
            }

            // Otherwise assume global target.
            return 0;
        }

    }

