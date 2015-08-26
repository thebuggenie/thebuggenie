<?php

    namespace thebuggenie\modules\auth_ldap\controllers;

    use thebuggenie\core\framework;

    /**
     * actions for the ldap_authentication module
     */
    class Main extends framework\Action
    {

        /**
         * Test the LDAP connection
         *
         * @param \thebuggenie\core\framework\Request $request
         */
        public function runTestConnection(framework\Request $request)
        {
            $validgroups = framework\Context::getModule('auth_ldap')->getSetting('groups');
            $base_dn = framework\Context::getModule('auth_ldap')->getSetting('b_dn');
            $groups_members_attr = framework\Context::getModule('auth_ldap')->getSetting('g_attr');
            $group_class = framework\Context::getModule('auth_ldap')->getSetting('g_type');

            try
            {
                $connection = framework\Context::getModule('auth_ldap')->connect();

                framework\Context::getModule('auth_ldap')->bind($connection, framework\Context::getModule('auth_ldap')->getSetting('control_user'), framework\Context::getModule('auth_ldap')->getSetting('control_pass'));
            }
            catch (\Exception $e)
            {
                framework\Context::setMessage('module_error', framework\Context::getI18n()->__('Failed to connect to server'));
                framework\Context::setMessage('module_error_details', $e->getMessage());
                $this->forward(framework\Context::getRouting()->generate('configure_module', array('config_module' => 'auth_ldap')));
            }

            $nonexisting = array();

            try
            {
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

                    // Check if specified groups exist
                    foreach ($groups as $group)
                    {
                        /*
                         * Find the group we are looking for, we search the entire directory
                         * We want to find 1 group, if we don't get 1, silently ignore this group.
                         */
                        $fields2 = array($groups_members_attr);
                        $filter2 = '(&(cn=' . framework\Context::getModule('auth_ldap')->escape($group) . ')(objectClass=' . framework\Context::getModule('auth_ldap')->escape($group_class) . '))';

                        $results2 = ldap_search($connection, $base_dn, $filter2, $fields2);

                        if (!$results2)
                        {
                            framework\Logging::log('failed to search for user: ' . ldap_error($connection), 'ldap', framework\Logging::LEVEL_FATAL);
                            throw new \Exception(framework\Context::geti18n()->__('Search failed: ') . ldap_error($connection));
                        }

                        $data2 = ldap_get_entries($connection, $results2);

                        if ($data2['count'] != 1)
                        {
                            $nonexisting[] = $group;
                        }
                    }
                }
            }
            catch (\Exception $e)
            {
                ldap_unbind($connection);
                framework\Context::setMessage('module_error', framework\Context::getI18n()->__('Failed to validate groups'));
                framework\Context::setMessage('module_error_details', $e->getMessage());
                $this->forward(framework\Context::getRouting()->generate('configure_module', array('config_module' => 'auth_ldap')));
            }

            if (count($nonexisting) == 0)
            {
                ldap_unbind($connection);

                /*
                 * Test if REMOTE_USER header is being provided by web server if HTTP integrated authentication option is enabled.
                 */
                if (framework\Context::getModule('auth_ldap')->getSetting('integrated_auth'))
                {
                    if (!isset($_SERVER['REMOTE_USER']))
                    {
                        framework\Context::setMessage('module_error', framework\Context::getI18n()->__('HTTP Authentication Header not present'));
                        framework\Context::setMessage('module_error_details', 'HTTP integrated authentication is enabled but the REMOTE_USER header is not being provided to Bug Genie. Please check your web server configuration');
                        $this->forward(framework\Context::getRouting()->generate('configure_module', array('config_module' => 'auth_ldap')));
                    }
                    else
                    {
                        framework\Context::setMessage('module_message', framework\Context::getI18n()->__('Connection test successful. HTTP integrated authentication states your username is "USER"', array('USER' => $_SERVER['REMOTE_USER'])));
                        $this->forward(framework\Context::getRouting()->generate('configure_module', array('config_module' => 'auth_ldap')));
                    }
                }
                else
                {
                    framework\Context::setMessage('module_message', framework\Context::getI18n()->__('Connection test successful'));
                    $this->forward(framework\Context::getRouting()->generate('configure_module', array('config_module' => 'auth_ldap')));
                }
            }
            else
            {
                ldap_unbind($connection);
                framework\Context::setMessage('module_error', framework\Context::getI18n()->__('Some of the groups you specified don\'t exist'));
                framework\Context::setMessage('module_error_details', framework\Context::getI18n()->__('The following groups for the group restriction could not be found: %groups', array('%groups' => implode(', ', $nonexisting))));
                $this->forward(framework\Context::getRouting()->generate('configure_module', array('config_module' => 'auth_ldap')));
            }
        }

        /**
         * Prune users from users table who aren't in LDAP
         *
         * @param \thebuggenie\core\framework\Request $request
         */
        public function runPruneUsers(framework\Request $request)
        {
            $validgroups = framework\Context::getModule('auth_ldap')->getSetting('groups');
            $base_dn = framework\Context::getModule('auth_ldap')->getSetting('b_dn');
            $dn_attr = framework\Context::getModule('auth_ldap')->getSetting('dn_attr');
            $username_attr = framework\Context::getModule('auth_ldap')->getSetting('u_attr');
            $fullname_attr = framework\Context::getModule('auth_ldap')->getSetting('f_attr');
            $email_attr = framework\Context::getModule('auth_ldap')->getSetting('e_attr');
            $groups_members_attr = framework\Context::getModule('auth_ldap')->getSetting('g_attr');

            $user_class = framework\Context::getModule('auth_ldap')->getSetting('u_type');
            $group_class = framework\Context::getModule('auth_ldap')->getSetting('g_type');

            $users = \thebuggenie\core\entities\User::getAll();
            $deletecount = 0;

            try
            {
                $connection = framework\Context::getModule('auth_ldap')->connect();
                framework\Context::getModule('auth_ldap')->bind($connection, framework\Context::getModule('auth_ldap')->getSetting('control_user'), framework\Context::getModule('auth_ldap')->getSetting('control_pass'));

                $default = framework\Settings::getDefaultUserID();

                foreach ($users as $user)
                {
                    if ($user->getID() == $default)
                    {
                        continue;
                    }

                    $username = $user->getUsername();

                    $fields = array($fullname_attr, $email_attr, 'cn', $dn_attr);
                    $filter = '(&(objectClass=' . framework\Context::getModule('auth_ldap')->escape($user_class) . ')(' . $username_attr . '=' . framework\Context::getModule('auth_ldap')->escape($username) . '))';

                    $results = ldap_search($connection, $base_dn, $filter, $fields);

                    if (!$results)
                    {
                        framework\Logging::log('failed to search for user: ' . ldap_error($connection), 'ldap', framework\Logging::LEVEL_FATAL);
                        throw new \Exception(framework\Context::geti18n()->__('Search failed: ') . ldap_error($connection));
                    }

                    $data = ldap_get_entries($connection, $results);

                    /*
                     * If a user is not found, delete it
                     */
                    if ($data['count'] != 1)
                    {
                        $user->delete();
                        $deletecount++;
                        continue;
                    }

                    if ($validgroups != '')
                    {
                        if (strstr($validgroups, ','))
                        {
                            $groups = explode(',', $validgroups);
                        }
                        else
                        {
                            $groups = array();
                            $groups[] = $validgroups;
                        }

                        $allowed = false;

                        foreach ($groups as $group)
                        {
                            $fields2 = array($groups_members_attr);
                            $filter2 = '(&(objectClass=' . framework\Context::getModule('auth_ldap')->escape($group_class) . ')(cn=' . framework\Context::getModule('auth_ldap')->escape($group) . '))';

                            $results2 = ldap_search($connection, $base_dn, $filter2, $fields2);

                            if (!$results2)
                            {
                                framework\Logging::log('failed to search for user: ' . ldap_error($connection), 'ldap', framework\Logging::LEVEL_FATAL);
                                throw new \Exception(framework\Context::geti18n()->__('Search failed: ') . ldap_error($connection));
                            }

                            $data2 = ldap_get_entries($connection, $results2);

                            if ($data2['count'] != 1)
                            {
                                continue;
                            }

                            foreach ($data2[0][$groups_members_attr] as $member)
                            {
                                $member = preg_replace('/(?<=,) +(?=[a-zA-Z])/', '', $member);
                                $user_dn = preg_replace('/(?<=,) +(?=[a-zA-Z])/', '', $data[0][strtolower($dn_attr)][0]);

                                if (!is_numeric($member) && strtolower($member) == strtolower($user_dn))
                                {
                                    $allowed = true;
                                }
                            }
                        }

                        /*
                         * If a user is not allowed access, delete it
                         */
                        if ($allowed == false)
                        {
                            $user->delete();
                            $deletecount++;
                            continue;
                        }
                    }
                }
            }
            catch (\Exception $e)
            {
                ldap_unbind($connection);
                framework\Context::setMessage('module_error', framework\Context::getI18n()->__('Pruning failed'));
                framework\Context::setMessage('module_error_details', $e->getMessage());
                $this->forward(framework\Context::getRouting()->generate('configure_module', array('config_module' => 'auth_ldap')));
            }

            ldap_unbind($connection);
            framework\Context::setMessage('module_message', framework\Context::getI18n()->__('Pruning successful! %del users deleted', array('%del' => $deletecount)));
            $this->forward(framework\Context::getRouting()->generate('configure_module', array('config_module' => 'auth_ldap')));
        }

        /**
         * Import all valid users
         *
         * @param \thebuggenie\core\framework\Request $request
         */
        public function runImportUsers(framework\Request $request)
        {
            $validgroups = framework\Context::getModule('auth_ldap')->getSetting('groups');
            $base_dn = framework\Context::getModule('auth_ldap')->getSetting('b_dn');
            $dn_attr = framework\Context::getModule('auth_ldap')->getSetting('dn_attr');
            $username_attr = framework\Context::getModule('auth_ldap')->getSetting('u_attr');
            $fullname_attr = framework\Context::getModule('auth_ldap')->getSetting('f_attr');
            $buddyname_attr = framework\Context::getModule('auth_ldap')->getSetting('b_attr');
            $email_attr = framework\Context::getModule('auth_ldap')->getSetting('e_attr');
            $groups_members_attr = framework\Context::getModule('auth_ldap')->getSetting('g_attr');

            $user_class = framework\Context::getModule('auth_ldap')->getSetting('u_type');
            $group_class = framework\Context::getModule('auth_ldap')->getSetting('g_type');

            $users = array();
            $importcount = 0;
            $updatecount = 0;

            try
            {
                /*
                 * Connect and bind to the control user
                 */
                $connection = framework\Context::getModule('auth_ldap')->connect();
                framework\Context::getModule('auth_ldap')->bind($connection, framework\Context::getModule('auth_ldap')->getSetting('control_user'), framework\Context::getModule('auth_ldap')->getSetting('control_pass'));

                /*
                 * Get a list of all users of a certain objectClass
                 */
                $fields = array($fullname_attr, $buddyname_attr, $username_attr, $email_attr, 'cn', $dn_attr);
                $filter = '(objectClass=' . framework\Context::getModule('auth_ldap')->escape($user_class) . ')';

                $results = ldap_search($connection, $base_dn, $filter, $fields);
                if (!$results)
                {
                    framework\Logging::log('failed to search for users: ' . ldap_error($connection), 'ldap', framework\Logging::LEVEL_FATAL);
                    throw new \Exception(framework\Context::geti18n()->__('Search failed: ') . ldap_error($connection));
                }

                $data = ldap_get_entries($connection, $results);

                /*
                 * For every user that exists, process it.
                 */
                for ($i = 0; $i != $data['count']; $i++)
                {
                    $user_dn = $data[$i][strtolower($dn_attr)][0];

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
                             * Find the group we are looking for, we search the entire directory
                             * We want to find 1 group, if we don't get 1, silently ignore this group.
                             */
                            $fields2 = array($groups_members_attr);
                            $filter2 = '(&(cn=' . framework\Context::getModule('auth_ldap')->escape($group) . ')(objectClass=' . framework\Context::getModule('auth_ldap')->escape($group_class) . '))';

                            $results2 = ldap_search($connection, $base_dn, $filter2, $fields2);

                            if (!$results2)
                            {
                                framework\Logging::log('failed to search for user: ' . ldap_error($connection), 'ldap', framework\Logging::LEVEL_FATAL);
                                throw new \Exception(framework\Context::geti18n()->__('Search failed: ') . ldap_error($connection));
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
                                $user_dn = preg_replace('/(?<=,) +(?=[a-zA-Z])/', '', $user_dn);
                                if (!is_numeric($member) && strtolower($member) == strtolower($user_dn))
                                {
                                    $allowed = true;
                                }
                            }
                        }

                        if ($allowed == false)
                        {
                            continue;
                        }
                    }

                    $users[$i] = array();

                    /*
                     * Set user's properties.
                     * Realname is obtained from directory, if not found we set it to the username
                     * Email is obtained from directory, if not found we set it to blank
                     */
                    if (!array_key_exists(strtolower($fullname_attr), $data[$i]))
                    {
                        $users[$i]['realname'] = $data[$i]['cn'][0];
                    }
                    else
                    {
                        $users[$i]['realname'] = $data[$i][strtolower($fullname_attr)][0];
                    }

                    if (!array_key_exists(strtolower($buddyname_attr), $data[$i]))
                    {
                        $users[$i]['buddyname'] = $data[$i]['cn'][0];
                    }
                    else
                    {
                        $users[$i]['buddyname'] = $data[$i][strtolower($buddyname_attr)][0];
                    }

                    if (!array_key_exists(strtolower($email_attr), $data[$i]))
                    {
                        $users[$i]['email'] = '';
                    }
                    else
                    {
                        $users[$i]['email'] = $data[$i][strtolower($email_attr)][0];
                    }
                    $users[$i]['username'] = $data[$i][strtolower($username_attr)][0];
                }
            }
            catch (\Exception $e)
            {
                framework\Context::setMessage('module_error', framework\Context::getI18n()->__('Import failed'));
                framework\Context::setMessage('module_error_details', $e->getMessage());
                $this->forward(framework\Context::getRouting()->generate('configure_module', array('config_module' => 'auth_ldap')));
            }

            /*
             * For every user that was found, either create a new user object, or update
             * the existing one. This will update the created and updated counts as appropriate.
             */
            foreach ($users as $ldapuser)
            {
                $username = $ldapuser['username'];
                $email = $ldapuser['email'];
                $realname = $ldapuser['realname'];
                $buddyname = $ldapuser['buddyname'];

                try
                {
                    $user = \thebuggenie\core\entities\User::getByUsername($username);
                    if ($user instanceof \thebuggenie\core\entities\User)
                    {
                        $user->setRealname($realname);
                        $user->setEmail($email); // update email address
                        $user->save();
                        $updatecount++;
                    }
                    else
                    {
                        // create user
                        $user = new \thebuggenie\core\entities\User();
                        $user->setUsername($username);
                        $user->setRealname($realname);
                        $user->setBuddyname($buddyname);
                        $user->setEmail($email);
                        $user->setEnabled();
                        $user->setActivated();
                        $user->setPassword($user->getJoinedDate() . $username);
                        $user->setJoined();
                        $user->save();
                        $importcount++;
                    }
                }
                catch (\Exception $e)
                {
                    ldap_unbind($connection);
                    framework\Context::setMessage('module_error', framework\Context::getI18n()->__('Import failed'));
                    framework\Context::setMessage('module_error_details', $e->getMessage());
                    $this->forward(framework\Context::getRouting()->generate('configure_module', array('config_module' => 'auth_ldap')));
                }
            }

            ldap_unbind($connection);
            framework\Context::setMessage('module_message', framework\Context::getI18n()->__('Import successful! %imp users imported, %upd users updated from LDAP', array('%imp' => $importcount, '%upd' => $updatecount)));
            $this->forward(framework\Context::getRouting()->generate('configure_module', array('config_module' => 'auth_ldap')));
        }

    }
