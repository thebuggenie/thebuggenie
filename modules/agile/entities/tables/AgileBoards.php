<?php

    namespace thebuggenie\modules\agile\entities\tables;

    use b2db\Criteria;
    use b2db\Update;
    use thebuggenie\core\entities\tables\ScopedTable;

    /**
     * Agile boards table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Agile boards table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @method static \thebuggenie\core\entities\tables\AgileBoards getTable() Retrieves an instance of this table
     * @method \thebuggenie\modules\agile\entities\AgileBoard selectById(integer $id) Retrieves an agile board
     *
     * @Table(name="agileboards")
     * @Entity(class="\thebuggenie\modules\agile\entities\AgileBoard")
     */
    class AgileBoards extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;

        const SCOPE = 'agileboards.scope';

        public function getAvailableProjectBoards($user_id, $project_id)
        {
            $query = $this->getQuery();
            $query->where('agileboards.project_id', $project_id);

            $criteria = new Criteria();
            $criteria->where('agileboards.user_id', $user_id);
            $criteria->or('agileboards.is_private', false);

            $query->and($criteria);

            return $this->select($query);
        }

        protected function migrateData(\b2db\Table $old_table)
        {
            if ($old_table instanceof \thebuggenie\core\modules\installation\upgrade_413\AgileBoardsTable)
            {
                $update = new Update();
                $update->add('agileboards.issue_field_values', serialize(array()));

                $this->rawUpdate($update);
            }
        }

    }
