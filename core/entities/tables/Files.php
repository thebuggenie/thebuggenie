<?php

    namespace thebuggenie\core\entities\tables;

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

        const B2DB_TABLE_VERSION = 1;
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

        public function getByID($id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $row = $this->doSelectById($id, $crit, false);
            return $row;
        }

    }
