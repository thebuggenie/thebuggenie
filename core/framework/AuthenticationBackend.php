<?php

    namespace thebuggenie\core\framework;

    /**
     * Parameter holder class used in the MVC part of the framework for \thebuggenie\core\entities\Action and \thebuggenie\core\entities\ActionComponent
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage mvc
     */

    use thebuggenie\core\entities\tables\Users;
    use thebuggenie\core\entities\User;
    use thebuggenie\core\entities\UserSession;
    use thebuggenie\core\framework\exceptions\ElevatedLoginException;

    /**
     * Parameter holder class used in the MVC part of the framework for \thebuggenie\core\entities\Action and \thebuggenie\core\entities\ActionComponent
     *
     * @package thebuggenie
     * @subpackage mvc
     */
    class AuthenticationBackend implements AuthenticationProviderInterface
    {
        function getAuthenticationMethod()
        {
            return AuthenticationProviderInterface::AUTHENTICATION_TYPE_TOKEN;
        }

        function verifyLogin($username, $password, $is_elevated = false)
        {
        }

        /**
         * Verify username and token against valid tokens for that user
         *
         * @param string $username
         * @param string $token
         * @param bool $is_elevated
         *
         * @return User|null
         * @throws ElevatedLoginException
         */
        function verifyToken($username, $token, $is_elevated = false)
        {
            $user = Users::getTable()->getByUsername($username);

            if (!$user instanceof User)
            {
                Context::logout();
                return;
            }

            if (!$user->verifyUserSession($token, $is_elevated))
            {
                if ($is_elevated)
                {
                    Context::setUser($user);
                    Context::getRouting()->setCurrentRouteName('elevated_login_page');
                    throw new ElevatedLoginException('reenter');
                }

                $user = null;
            }

            return $user;
        }

        function doLogin($username, $token)
        {
        }

        function logout()
        {
            Context::getResponse()->deleteCookie('tbg_username');
            Context::getResponse()->deleteCookie('tbg_session_token');
            Context::getResponse()->deleteCookie('tbg_elevated_session_token');
        }

        /**
         * @param Request $request
         *
         * @return null|User
         */
        function doAutoLogin(Request $request)
        {
        }

        /**
         * @param User $user
         * @param UserSession $token
         * @param bool $session_only
         * @return mixed|void
         */
        function persistTokenSession(User $user, UserSession $token, $session_only)
        {
            if ($session_only)
            {
                Context::getResponse()->setSessionCookie('tbg_username', $user->getUsername());
                Context::getResponse()->setSessionCookie('tbg_session_token', $token->getToken());
            }
            else
            {
                Context::getResponse()->setCookie('tbg_username', $user->getUsername());
                Context::getResponse()->setCookie('tbg_session_token', $token->getToken());
            }
        }

        function persistPasswordSession(User $user, $password, $session_only)
        {
            if ($session_only)
            {
                Context::getResponse()->setSessionCookie('tbg_username', $user->getUsername());
                Context::getResponse()->setSessionCookie('tbg_password', $password);
            }
            else
            {
                Context::getResponse()->setCookie('tbg_username', $user->getUsername());
                Context::getResponse()->setCookie('tbg_password', $password);
            }
        }

    }
