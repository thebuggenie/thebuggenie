<?php

    namespace thebuggenie\modules\auth_ldap;

    use thebuggenie\core\framework;

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
    class Auth_ldap extends \thebuggenie\core\entities\Module
    {

        const VERSION = '2.0';

        protected $_name = 'auth_ldap';
        protected $_longname = 'LDAP Authentication';
        protected $_description = 'Allows authentication against a LDAP or Active Directory server';
        protected $_module_config_title = 'LDAP Authentication';
        protected $_module_config_description = 'Configure server connection settings';
        protected $_has_config_settings = true;

        protected function _initialize()
        {

        }

        protected function _install($scope)
        {

        }

        protected function _uninstall()
        {

        }

        public final function getType()
        {
            return parent::MODULE_AUTH;
        }

        protected function _addListeners()
        {
            framework\Event::listen('core', 'thebuggenie\core\modules\configuration\controllers\Main\getAuthenticationMethodForAction', array($this, 'listen_configurationAuthenticationMethod'));
        }

        public function postConfigSettings(framework\Request $request)
        {
            $settings = array('hostname', 'u_type', 'g_type', 'b_dn', 'groups', 'dn_attr', 'u_attr', 'g_attr', 'e_attr', 'f_attr', 'b_attr', 'g_dn', 'control_user', 'control_pass', 'integrated_auth', 'integrated_auth_header');
            foreach ($settings as $setting)
            {
                if (($setting == 'u_type' || $setting == 'g_type' || $setting == 'dn_attr') && $request->getParameter($setting) == '')
                {
                    if ($setting == 'u_type')
                    {
                        $this->saveSetting($setting, 'person');
                    }
                    elseif ($setting == 'g_type')
                    {
                        $this->saveSetting($setting, 'group');
                    }
                    else
                    {
                        $this->saveSetting($setting, 'entrydn');
                    }
                }
                elseif ($setting == 'integrated_auth')
                {
                    $this->saveSetting($setting, (int) $request->getParameter($setting, 0));
                }
                else
                {
                    if ($request->hasParameter($setting))
                    {
                        $this->saveSetting($setting, $request->getParameter($setting));
                    }
                }
            }
        }

        public function connect()
        {
            $host = $this->getSetting('hostname');
            $failed = false;

            $connection = ldap_connect($host);
            ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);

            if ($connection == false): $failed = true;
            endif;

            if ($failed)
            {
                throw new \Exception(framework\Context::geti18n()->__('Failed to connect to server'));
            }

            return $connection;
        }

        public function bind($connection, $lduser = null, $ldpass = null)
        {
            $bind = ldap_bind($connection, $lduser, $ldpass);

            if (!$bind)
            {
                ldap_unbind($connection);
                framework\Logging::log('bind failed: ' . ldap_error($connection), 'ldap', framework\Logging::LEVEL_FATAL);
                throw new \Exception(framework\Context::geti18n()->__('Failed to bind: ') . ldap_error($connection));
            }
        }

        public function escape($string)
        {
            $chars = array('*', '()', ')', chr(0));
            foreach ($chars as $char)
            {
                $string = str_replace($char, '\\' . $char, $string);
            }

            return $string;
        }

        public function doLogin($username, $password, $mode = 1)
        {
            $validgroups = $this->getSetting('groups');
            $base_dn = $this->getSetting('b_dn');
            $dn_attr = $this->escape($this->getSetting('dn_attr'));
            $username_attr = $this->escape($this->getSetting('u_attr'));
            $fullname_attr = $this->escape($this->getSetting('f_attr'));
            $buddyname_attr = $this->escape($this->getSetting('b_attr'));
            $email_attr = $this->escape($this->getSetting('e_attr'));
            $groups_members_attr = $this->escape($this->getSetting('g_attr'));

            $user_class = framework\Context::getModule('auth_ldap')->getSetting('u_type');
            $group_class = framework\Context::getModule('auth_ldap')->getSetting('g_type');

            $email = null;

            $integrated_auth = $this->getSetting('integrated_auth');

            /*
             * Do the LDAP check here.
             *
             * If a connection error or something, throw an exception and log
             *
             * If we can, set $mail and $realname to correct values from LDAP
             * otherwise don't touch those variables.
             *
             * To log do:
             * framework\Logging::log('error goes here', 'ldap', framework\Logging::LEVEL_FATAL);
             */
            try
            {
                /*
                 * First job is to connect to our control user (may be an anonymous bind)
                 * so we can find the user we want to log in as/validate.
                 */
                $connection = $this->connect();

                $control_user = $this->getSetting('control_user');
                $control_password = $this->getSetting('control_pass');

                $this->bind($connection, $control_user, $control_password);

                // Assume bind successful, otherwise we would have had an exception

                /*
                 * Search for a user with the username specified. We search in the base_dn, so we can
                 * find users in multiple parts of the directory, and only return users of a specific
                 * class (default person).
                 *
                 * We want exactly 1 user to be returned. We get the user's full name, email, cn
                 * and dn.
                 */
                $fields = array($fullname_attr, $buddyname_attr, $email_attr, 'cn', $dn_attr);
                $filter = '(&(objectClass=' . $this->escape($user_class) . ')(' . $username_attr . '=' . $this->escape($username) . '))';

                $results = ldap_search($connection, $base_dn, $filter, $fields);

                if (!$results)
                {
                    framework\Logging::log('failed to search for user: ' . ldap_error($connection), 'ldap', framework\Logging::LEVEL_FATAL);
                    throw new \Exception(framework\Context::geti18n()->__('Search failed: ') . ldap_error($connection));
                }

                $data = ldap_get_entries($connection, $results);

                // User does not exist
                if ($data['count'] == 0)
                {
                    framework\Logging::log('could not find user ' . $username . ', class ' . $user_class . ', attribute ' . $username_attr, 'ldap', framework\Logging::LEVEL_FATAL);
                    throw new \Exception(framework\Context::geti18n()->__('User does not exist in the directory'));
                }

                // If we have more than 1 user, something is seriously messed up...
                if ($data['count'] > 1)
                {
                    framework\Logging::log('too many users for ' . $username . ', class ' . $user_class . ', attribute ' . $username_attr, 'ldap', framework\Logging::LEVEL_FATAL);
                    throw new \Exception(framework\Context::geti18n()->__('This user was found multiple times in the directory, please contact your administrator'));
                }

                /*
                 * If groups are specified, perform group restriction tests
                 */
                if ($validgroups != '')
                {
                    /*
                     * We will repeat this for every group, but groups are supplied as a comma-separated list
                     */
                    if (strstr($validgroups, ','))
                    {
                        $groups = explode(',', $validgroups);
                    }
                    else
                    {
                        $groups = array();
                        $groups[] = $validgroups;
                    }

                    // Assumed we are initially banned
                    $allowed = false;

                    foreach ($groups as $group)
                    {
                        // No need to carry on looking if we have access
                        if ($allowed == true): continue;
                        endif;

                        /*
                         * Find the group we are looking for, we search the entire directory as per users (See that stuff)
                         * We want to find 1 group, if we don't get 1, silently ignore this group.
                         */
                        $fields2 = array($groups_members_attr);
                        $filter2 = '(&(objectClass=' . $this->escape($group_class) . ')(cn=' . $this->escape($group) . '))';

                        $results2 = ldap_search($connection, $base_dn, $filter2, $fields2);

                        if (!$results2)
                        {
                            framework\Logging::log('failed to search for user after binding: ' . ldap_error($connection), 'ldap', framework\Logging::LEVEL_FATAL);
                            throw new \Exception(framework\Context::geti18n()->__('Search failed ') . ldap_error($connection));
                        }

                        $data2 = ldap_get_entries($connection, $results2);

                        if ($data2['count'] != 1)
                        {
                            continue;
                        }

                        /*
                         * Look through the group's member list. If we are found, grant access.
                         */
                        foreach ($data2[0][strtolower($groups_members_attr)] as $member)
                        {
                            $member = preg_replace('/(?<=,) +(?=[a-zA-Z])/', '', $member);
                            $user_dn = preg_replace('/(?<=,) +(?=[a-zA-Z])/', '', $data[0][strtolower($dn_attr)][0]);

                            if (!is_numeric($member) && strtolower($member) == strtolower($user_dn))
                            {
                                $allowed = true;
                            }
                        }
                    }

                    if ($allowed == false)
                    {
                        throw new \Exception(framework\Context::getI18n()->__('You are not a member of a group allowed to log in'));
                    }
                }

                /*
                 * Set user's properties.
                 * Realname is obtained from directory, if not found we set it to the username
                 * Email is obtained from directory, if not found we set it to blank
                 */
                if (!array_key_exists(strtolower($fullname_attr), $data[0]))
                {
                    $realname = $username;
                }
                else
                {
                    $realname = $data[0][strtolower($fullname_attr)][0];
                }

                if (!array_key_exists(strtolower($buddyname_attr), $data[0]))
                {
                    $buddyname = $username;
                }
                else
                {
                    $buddyname = $data[0][strtolower($buddyname_attr)][0];
                }

                if (!array_key_exists(strtolower($email_attr), $data[0]))
                {
                    $email = '';
                }
                else
                {
                    $email = $data[0][strtolower($email_attr)][0];
                }

                /*
                 * If we are performing a non integrated authentication login,
                 * now bind to the user and see if the credentials
                 * are valid. We bind using the full DN of the user, so no need for DOMAIN\ stuff
                 * on Windows, and more importantly it fixes other servers.
                 *
                 * If the bind fails (exception), we throw a nicer exception and don't continue.
                 */
                if ($mode == 1 && !$integrated_auth)
                {
                    try
                    {
                        if (!is_array($data[0][strtolower($dn_attr)]))
                        {
                            $dn = $data[0][strtolower($dn_attr)];
                        }
                        else
                        {
                            $dn = $data[0][strtolower($dn_attr)][0];
                        }
                        $bind = $this->bind($connection, $this->escape($dn), html_entity_decode($password));
                    }
                    catch (\Exception $e)
                    {
                        throw new \Exception(framework\Context::geti18n()->__('Your password was not accepted by the server'));
                    }
                }
                /*
                 * Performing a login using the HTTP authentication header REMOTE_USER to identify
                 * the current user. Password will NOT be checked as the web server is handling
                 * authentication which we are trusting.
                 */
                elseif ($mode == 1)
                {
                    if (!isset($_SERVER[$this->getSetting('integrated_auth_header')]) || $_SERVER[$this->getSetting('integrated_auth_header')] != $username)
                    {
                        throw new \Exception(framework\Context::geti18n()->__('HTTP authentication internal error.'));
                    }
                }
            }
            catch (\Exception $e)
            {
                ldap_unbind($connection);
                throw $e;
            }

            try
            {
                /*
                 * Get the user object. If the user exists, update the user's
                 * data from the directory.
                 */
                $user = \thebuggenie\core\entities\User::getByUsername($username);
                if ($user instanceof \thebuggenie\core\entities\User)
                {
                    $user->setBuddyname($buddyname);
                    $user->setRealname($realname);
                    $user->setPassword($user->getJoinedDate() . $username); // update password
                    $user->setEmail($email); // update email address
                    $user->save();
                }
                else
                {
                    /*
                     * If not, and we are performing an initial login, create the user object
                     * if we are validating a log in, kick the user out as the session is invalid.
                     */
                    if ($mode == 1)
                    {
                        // create user
                        $user = new \thebuggenie\core\entities\User();
                        $user->setUsername($username);
                        $user->setRealname('temporary');
                        $user->setBuddyname($username);
                        $user->setEmail('temporary');
                        $user->setEnabled();
                        $user->setActivated();
                        $user->setJoined();
                        $user->setPassword($user->getJoinedDate() . $username);
                        $user->save();
                    }
                    else
                    {
                        throw new \Exception('User does not exist in TBG');
                    }
                }
            }
            catch (\Exception $e)
            {
                ldap_unbind($connection);
                throw $e;
            }

            ldap_unbind($connection);

            /*
             * Set cookies and return user row for general operations.
             */
            framework\Context::getResponse()->setCookie('tbg3_username', $username);
            framework\Context::getResponse()->setCookie('tbg3_password', \thebuggenie\core\entities\User::hashPassword($user->getJoinedDate() . $username, $user->getSalt()));

            return \thebuggenie\core\entities\tables\Users::getTable()->getByUsername($username);
        }

        public function verifyLogin($username)
        {
            return $this->doLogin($username, 'a', 2);
        }

        /*
         * Actions on logout
         */

        public function logout()
        {

        }

        /*
         * Actions on login - if there are no credentials supplied try an autologin
         *
         * Will activate auto-login process if HTTP integrated authentication is enabled.

         * Return:
         * true - succeeded operation but no autologin
         * false - invalid cookies found
         * Row from \thebuggenie\core\entities\tables\Users - succeeded operation, user found
         *
         */

        public function doAutoLogin()
        {
            if ($this->getSetting('integrated_auth'))
            {
                if (isset($_SERVER[$this->getSetting('integrated_auth_header')]))
                {
                    return $this->doLogin($_SERVER[$this->getSetting('integrated_auth_header')], 'a', 1);
                }
                else
                {
                    throw new \Exception(framework\Context::geti18n()->__('HTTP integrated authentication is enabled but the HTTP header has not been provided by the web server.'));
                }
            }
            else
            {
                return true;
            }
        }

        public function listen_configurationAuthenticationMethod(framework\Event $event)
        {
            if (framework\Settings::getAuthenticationBackend() == $this->getName()) {
                $event->setReturnValue(framework\Action::AUTHENTICATION_METHOD_CORE);
            }
        }

    }
