<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;
	
	/**
	 * B2DB Table, vcs_integration -> VCSIntegrationFilesTable
	 *
	 * @author Philip Kent <kentphilip@gmail.com>
	 * @version 3.2
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage vcs_integration
	 */

	/**
	 * B2DB Table, vcs_integration -> VCSIntegrationFilesTable
	 *
	 * @package thebuggenie
	 * @subpackage vcs_integration
	 *
	 * @Entity(class="TBGVCSIntegrationFile")
	 * @Table(name="vcsintegration_files")
	 */
	class TBGVCSIntegrationFilesTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 2;
		const B2DBNAME = 'vcsintegration_files';
		const ID = 'vcsintegration_files.id';
		const SCOPE = 'vcsintegration_files.scope';
		const COMMIT_ID = 'vcsintegration_files.commit_id';
		const FILE_NAME = 'vcsintegration_files.file_name';
		const ACTION = 'vcsintegration_files.action';

		protected function _setupIndexes()
		{
			$this->_addIndex('commit', self::COMMIT_ID);
		}

		/**
		 * Get all affected files by commit
		 * @param integer $id
		 */
		public function getByCommitID($id, $scope = null)
		{
			$scope = ($scope === null) ? TBGContext::getScope()->getID() : $scope;
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, $scope);
			$crit->addWhere(self::COMMIT_ID, $id);
			
			$res = $this->doSelect($crit);
			
			return $res;
		}

	}

