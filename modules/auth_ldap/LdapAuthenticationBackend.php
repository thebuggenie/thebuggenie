<?php

    namespace thebuggenie\modules\auth_ldap;

    use thebuggenie\core\entities\UserSession;
    use thebuggenie\core\framework\AuthenticationBackend;
    use thebuggenie\core\entities\Module;
    use thebuggenie\core\entities\User;
    use thebuggenie\core\framework;
    use thebuggenie\core\framework\Request;

    /**
     * LDAP Authentication
     *
     * @author
     * @version 0.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package auth_ldap
     * @subpackage core
     */

    /**
     * LDAP Authentication
     *
     * @package auth_ldap
     * @subpackage core
     *
     * @Table(name="\thebuggenie\core\entities\tables\Modules")
     */
    class LdapAuthenticationBackend extends AuthenticationBackend
    {

        /**
         * @var Auth_ldap
         */
        protected $_module;

        /**
         * @return Auth_ldap
         */
        public function getModule(): Auth_ldap
        {
            return $this->_module;
        }

        /**
         * @param Auth_ldap $module
         */
        public function setModule(Auth_ldap $module)
        {
            $this->_module = $module;
        }

        /**
         * Log-in the user with provided credentials.
         *
         * @param string $username
         *   Username  to log-in with.
         *
         * @param string $password
         *   Password to log-in with.
         *
         *
         * @retval thebuggenie\core\entities\User | null
         *   User object associated with the login. If login has failed, returns
         *   null.
         */
        public function doLogin($username, $password)
        {
            return $this->_loginUser($username, $password, true);
        }


        /**
         * Automatic login, triggered if no credentials were supplied.
         *
         * LDAP authentication auto-login implementation is used in conjunction
         * with HTTP integrated authentication.
         *
         * @retval \thebuggenie\core\entities\User | null
         *   If HTTP integrated authentication is enabled, and appropriate
         *   header is available in the request, runs login and returns user
         *   entity if login was successful. Otherwise returns null.
         *
         * @param framework\Request $request
         * @return User|null
         * @throws \Exception Thrown if HTTP header has not been configured, and HTTP integrated
         *   authentication has been enabled
         */
        public function doAutoLogin(framework\Request $request)
        {
            $user = null;

            if ($this->getModule()->getSetting('integrated_auth'))
            {
                if (!isset($_SERVER[$this->getModule()->getSetting('integrated_auth_header')]))
                {
                    throw new \Exception(framework\Context::geti18n()->__('HTTP integrated authentication is enabled but the HTTP header has not been provided by the web server.'));
                }

                $user = $this->_loginUser($_SERVER[$this->getModule()->getSetting('integrated_auth_header')], "", true);
            }

            return $user;
        }

        /**
         * Verifies username and password login against LDAP server.
         *
         * @param string $username
         *   Username to log-in with. Keep in mind this is DN in case of LDAP.
         *
         * @param string $password
         *   Password to log-in with.
         *
         *
         * @retval bool
         *   Returns true, if username + password combination is valid, false
         *   otherwise.
         */
        protected function _verifyLDAPLogin($username, $password)
        {
            // Assume failure.
            $result = false;

            // Make sure to use separate connection for verifying regular
            // users. Do not reuse the control user connection.
            $connection = @ldap_connect($this->getModule()->getSetting('hostname'));

            if ($connection !== false)
            {
                // Default LDAP protocol version used is 2, ensure we are
                // using version 3 instead.
                ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
                ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);

                // Ignore PHP errors from this function (all PHP ldap_*
                // functions misuse PHP error handling).
                $result = @ldap_bind($connection, $username, $password);
            }

            return $result;
        }

        /**
         * Logs-in the user based on provided username and password, and
         * retrieves the user entity.
         *
         * Initial logins are always verified against the LDAP directory, while
         * subsequent ones are assumed to succeed automatically, since password
         * will be in a hashed format that we cannot use for verification.
         *
         * @param string $username
         *   Username to log-in with. Ignored if using HTTP integrated
         *   authentication. This should be regular TBG username, not the LDAP
         *   one (the LDAP one will be looked-up based on this value).
         *
         * @param string $password
         *   Password to log-in with. Ignored if using HTTP integrated
         *   authentication.
         *
         * @param bool $initial_login
         *   Specify login mode. If initial login is requested, we will test
         *   username and password against the LDAP server, otherwise it is
         *   assumed that login has been performed before successfully.
         *
         * @retval \thebuggenie\core\entities\User | null
         *   User entity if login was successful, null otherwise.
         */
        protected function _loginUser($username, $password, $initial_login)
        {
            // Retrieve LDAP user information.
            $ldap_user_info = $this->getModule()->getLDAPUserInformation($username);

            // If we could not locate user, return null to denote invalid login.
            if (count($ldap_user_info) == 0)
            {
                return null;
            }
            // Bail-out if we locate more than one user, something is wrong with
            // either module settings, or LDAP structure itself.
            elseif (count($ldap_user_info) > 1)
            {
                framework\Logging::log("More than one user in LDAP directory has username '${username}'. Please verify integrity and structure of your LDAP installation.",
                                       'ldap', framework\Logging::LEVEL_FATAL);
                throw new \Exception(framework\Context::geti18n()->__('This user was found multiple times in the directory, please contact your administrator'));
            }

            // Extract user information.
            $ldap_user = $ldap_user_info[0];

            // Perform authentication based on whether we are using integrated
            // authentication or not.
            if ($this->getModule()->getSetting('integrated_auth') == true && $initial_login === true)
            {
                if (!isset($_SERVER[$this->getModule()->getSetting('integrated_auth_header')]) || $_SERVER[$this->getModule()->getSetting('integrated_auth_header')] != $username)
                {
                    throw new \Exception(framework\Context::geti18n()->__('HTTP authentication internal error.'));
                }
            }
            elseif ($initial_login === true)
            {
                $login_result = $this->_verifyLDAPLogin($ldap_user['ldap_username'], $password);

                if ($login_result === false)
                {
                    return null;
                }
            }

            // Create or update the existing user with up-to-date information.
            list($user, $created) = $this->getModule()->createOrUpdateUser($ldap_user);

            return $user;
        }


        function doExplicitLogin(Request $request)
        {
            $username = $request->getRawParameter('username');
            $password = $request->getRawParameter('password');

            return $this->_loginUser($username, $password, true);
        }


    }
