<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * User notification settings table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.3
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * User notification settings table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="user_notificationsettings")
	 * @Entity(class="TBGNotificationSetting")
	 */
	class TBGUserNotificationSettingsTable extends TBGB2DBTable 
	{
		
		const B2DB_TABLE_VERSION = 1;
		
	}
