<?php

    namespace thebuggenie\core\modules\installation\upgrade_419;

    use thebuggenie\core\entities\common\Identifiable;

    /**
     * Notification setting class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.3
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage main
     */

    /**
     * Notification setting class
     *
     * @package thebuggenie
     * @subpackage main
     *
     * @Table(name="\thebuggenie\core\modules\installation\upgrade_419\NotificationSettingsTable")
     */
    class NotificationSetting extends Identifiable
    {

        /**
         * The module name
         *
         * @var string
         * @Column(type="string", length=50)
         */
        protected $_module_name;

        /**
         * The setting name
         *
         * @var string
         * @Column(type="string", length=50)
         */
        protected $_name;

        /**
         * Setting value
         *
         * @var string
         * @Column(type="string", length=100)
         */
        protected $_value = '';

        /**
         * Who the notification is for
         *
         * @var \thebuggenie\core\entities\User
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\User")
         */
        protected $_user_id;

    }
