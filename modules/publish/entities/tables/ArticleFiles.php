<?php

    namespace thebuggenie\modules\publish\entities\tables;

    use thebuggenie\core\framework,
        thebuggenie\core\entities\tables\ScopedTable;

    /**
     * Articles <-> Files table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Articles <-> Files table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="articlefiles")
     */
    class ArticleFiles extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'articlefiles';
        const ID = 'articlefiles.id';
        const SCOPE = 'articlefiles.scope';
        const UID = 'articlefiles.uid';
        const ATTACHED_AT = 'articlefiles.attached_at';
        const FILE_ID = 'articlefiles.file_id';
        const ARTICLE_ID = 'articlefiles.article_id';

        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addForeignKeyColumn(self::UID, \thebuggenie\core\entities\tables\Users::getTable(), \thebuggenie\core\entities\tables\Users::ID);
            parent::_addForeignKeyColumn(self::ARTICLE_ID, Articles::getTable(), Articles::ID);
            parent::_addForeignKeyColumn(self::FILE_ID, \thebuggenie\core\entities\tables\Files::getTable(), \thebuggenie\core\entities\tables\Files::ID);
            parent::_addInteger(self::ATTACHED_AT, 10);
        }

        public function addByArticleIDandFileID($article_id, $file_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ARTICLE_ID, $article_id);
            $crit->addWhere(self::FILE_ID, $file_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            if ($this->doCount($crit) == 0)
            {
                $crit = $this->getCriteria();
                $crit->addInsert(self::SCOPE, framework\Context::getScope()->getID());
                $crit->addInsert(self::ATTACHED_AT, NOW);
                $crit->addInsert(self::ARTICLE_ID, $article_id);
                $crit->addInsert(self::FILE_ID, $file_id);
                $this->doInsert($crit);
            }
        }

        public function getByArticleID($article_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ARTICLE_ID, $article_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->doSelect($crit);

            $ret_arr = array();

            if ($res)
            {
                while ($row = $res->getNextRow())
                {
                    try
                    {
                        $file = new \thebuggenie\core\entities\File($row->get(\thebuggenie\core\entities\tables\Files::ID), $row);
                        $file->setUploadedAt($row->get(self::ATTACHED_AT));
                        $ret_arr[$row->get(\thebuggenie\core\entities\tables\Files::ID)] = $file;
                    }
                    catch (\Exception $e)
                    {
                        $this->doDeleteById($row->get(self::ID));
                    }
                }
            }

            return $ret_arr;
        }

        public function removeByArticleIDandFileID($article_id, $file_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ARTICLE_ID, $article_id);
            $crit->addWhere(self::FILE_ID, $file_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            if ($res = $this->doSelectOne($crit))
            {
                $this->doDelete($crit);
            }
            return $res;
        }

        public function deleteFilesByArticleID($article_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ARTICLE_ID, $article_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $this->doDelete($crit);
        }

        public function getArticlesByFileID($file_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::FILE_ID, $file_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            $article_ids = array();
            if ($res = $this->doSelect($crit))
            {
                while ($row = $res->getNextRow())
                {
                    $a_id = $row->get(self::ARTICLE_ID);
                    $article_ids[$a_id] = $a_id;
                }
            }
            return $article_ids;
        }

    }
