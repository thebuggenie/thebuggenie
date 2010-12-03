<?php

	/**
	 * B2DB class that all TBGTable class extends, implementing scope access
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage mvc
	 */

	/**
	 * B2DB class that all TBGTable class extends, implementing scope access
	 *
	 * @package thebuggenie
	 * @subpackage mvc
	 */
	class TBGB2DBTable extends B2DBTable
	{
		
		/**
		 * Return a row for the specified id in the current scope, if defined
		 * 
		 * @param integer $id
		 * 
		 * @return B2DBRow
		 */
		public function getByID($id)
		{
			if (defined('static::SCOPE'))
			{
				$crit = $this->getCriteria();
				$crit->addWhere(static::SCOPE, TBGContext::getScope()->getID());
				$row = $this->doSelectById($id, $crit);
			}
			else
			{
				$row = $this->doSelectById($id);
			}
			return $row;
		}
		
	}