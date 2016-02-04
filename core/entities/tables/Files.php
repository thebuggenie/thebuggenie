<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Criteria;
    use b2db\Table;
    use thebuggenie\core\framework;

    /**
     * Files table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Files table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="files")
     * @Entity(class="\thebuggenie\core\entities\File")
     */
    class Files extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;
        const B2DBNAME = 'files';
        const ID = 'files.id';
        const SCOPE = 'files.scope';
        const UID = 'files.uid';
        const UPLOADED_AT = 'files.uploaded_at';
        const REAL_FILENAME = 'files.real_filename';
        const ORIGINAL_FILENAME = 'files.original_filename';
        const CONTENT_TYPE = 'files.content_type';
        const CONTENT = 'files.content';
        const DESCRIPTION = 'files.description';

        public function saveFile($real_filename, $original_filename, $content_type, $description = null, $content = null)
        {
            $crit = $this->getCriteria();
            $crit->addInsert(self::UID, framework\Context::getUser()->getID());
            $crit->addInsert(self::REAL_FILENAME, $real_filename);
            $crit->addInsert(self::UPLOADED_AT, NOW);
            $crit->addInsert(self::ORIGINAL_FILENAME, $original_filename);
            $crit->addInsert(self::CONTENT_TYPE, $content_type);
            $crit->addInsert(self::SCOPE, framework\Context::getScope()->getID());
            if ($description !== null)
            {
                $crit->addInsert(self::DESCRIPTION, $description);
            }
            if ($content !== null)
            {
                $crit->addInsert(self::CONTENT, $content);
            }
            $res = $this->doInsert($crit);

            return $res->getInsertID();
        }

        public function _migrateData(Table $old_table)
        {
            switch ($old_table::B2DB_TABLE_VERSION) {
                case 1:
                    foreach ($this->selectAll() as $file) {
                        $file->save();
                    }
            }
        }

        public function getAllContentFiles()
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::CONTENT, null, Criteria::DB_IS_NOT_NULL);
            $crit->addWhere(self::CONTENT, '', Criteria::DB_NOT_EQUALS);

            return $this->select($crit);
        }

        public function getUnattachedFiles()
        {
            $crit = $this->getCriteria();
            $crit->addSelectionColumn(self::ID, 'id');

            $res = $this->doSelect($crit);
            $file_ids = [];
            if ($res) {
                while ($row = $res->getNextRow()) {
                    $file_ids[$row['id']] = $row['id'];
                }
            }

            $file_ids = array_diff($file_ids, IssueFiles::getTable()->getLinkedFileIds($file_ids));

            $event = framework\Event::createNew('core', 'thebuggenie\core\entities\\tables\Files::getUnattachedFiles', $this, ['file_ids' => $file_ids], []);
            $event->trigger();
            if ($event->isProcessed()) {
                foreach ($event->getReturnList() as $linked_file_ids) {
                    $file_ids = array_diff($file_ids, $linked_file_ids);
                }
            }

            return $file_ids;
        }

        public function getByID($id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $row = $this->doSelectById($id, $crit, false);
            return $row;
        }

        public function getByScopeID($scope_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, $scope_id);

            return $this->select($crit);
        }

        public function getSizeByScopeID($scope_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, $scope_id);
            $crit->addSelectionColumn('files.size', 'totalsize', Criteria::DB_SUM);

            $result = $this->doSelectOne($crit);
            return $result['totalsize'];
        }

    }
