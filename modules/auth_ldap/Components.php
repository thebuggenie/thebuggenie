<?php

    namespace thebuggenie\modules\auth_ldap;

    /**
     * action components for the ldap_authentication module
     */
    class Components extends \thebuggenie\core\framework\ActionComponent
    {

        public function componentSettings()
        {
            if (!extension_loaded('ldap'))
            {
                $this->noldap = true;
            }
            else
            {
                $this->noldap = false;
            }
        }

    }
