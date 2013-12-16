<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Notifications table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Notifications table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="notifications")
	 * @Entity(class="TBGNotification")
	 */
	class TBGNotificationsTable extends TBGB2DBTable 
	{
		
		const B2DB_TABLE_VERSION = 2;
		const B2DBNAME = 'notifications';
		const ID = 'notifications.id';
		const SCOPE = 'notifications.scope';
		const MODULE_NAME = 'notifications.module_name';
		const NOTIFICATION_TYPE = 'notifications.notification_type';
		const TARGET_ID = 'notifications.target_id';
		const TRIGGERED_BY_UID = 'notifications.uid';
		const USER_ID = 'notifications.user_id';
		const IS_READ = 'notifications.is_read';

		public function getCountsByUserID($user_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::USER_ID, $user_id);
			$crit->addWhere(self::IS_READ, false);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$unread_count = $this->count($crit);

			$crit = $this->getCriteria();
			$crit->addWhere(self::USER_ID, $user_id);
			$crit->addWhere(self::IS_READ, true);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$read_count = $this->count($crit);
			
			return array($unread_count, $read_count);
		}
		
		public function markUserNotificationsReadByTypesAndId($types, $id, $user_id)
		{
			if (!is_array($types)) $types = array($types);
			
			$crit = $this->getCriteria();
			$crit->addWhere(self::USER_ID, $user_id);
			if (is_array($id))
			{
				$crit->addWhere(self::TARGET_ID, $id, Criteria::DB_IN);
			}
			else
			{
				$crit->addWhere(self::TARGET_ID, $id);
			}
			$crit->addWhere(self::NOTIFICATION_TYPE, $types, Criteria::DB_IN);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addUpdate(self::IS_READ, true);
			$this->doUpdate($crit);
		}
		
	}
