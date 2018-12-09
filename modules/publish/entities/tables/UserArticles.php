<?php

    namespace thebuggenie\modules\publish\entities\tables;

    use b2db\Insertion;
    use thebuggenie\core\framework,
        thebuggenie\core\entities\tables\ScopedTable;

    /**
     * User articles table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * User articles table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="userarticles")
     */
    class UserArticles extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'userarticles';
        const ID = 'userarticles.id';
        const SCOPE = 'userarticles.scope';
        const ARTICLE = 'userarticles.article';
        const UID = 'userarticles.uid';

        protected function _initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addForeignKeyColumn(self::ARTICLE, Articles::getTable(), Articles::ID);
            parent::addForeignKeyColumn(self::UID, \thebuggenie\core\entities\tables\Users::getTable(), \thebuggenie\core\entities\tables\Users::ID);
        }

        public function _setupIndexes()
        {
            $this->_addIndex('uid_scope', array(self::UID, self::SCOPE));
        }

        public function getUserIDsByArticleID($article_id)
        {
            $uids = array();
            $query = $this->getQuery();

            $query->where(self::ARTICLE, $article_id);

            if ($res = $this->rawSelect($query))
            {
                while ($row = $res->getNextRow())
                {
                    $uid = $row->get(self::UID);
                    $uids[$uid] = $uid;
                }
            }

            return $uids;
        }

        public function copyStarrers($from_article_id, $to_article_id)
        {
            $old_watchers = $this->getUserIDsByIssueID($from_article_id);
            $new_watchers = $this->getUserIDsByIssueID($to_article_id);

            if (count($old_watchers))
            {
                $insertion = new Insertion();
                $insertion->add(self::ARTICLE, $to_article_id);
                $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
                foreach ($old_watchers as $uid)
                {
                    if (!in_array($uid, $new_watchers))
                    {
                        $insertion->add(self::UID, $uid);
                        $this->rawInsert($insertion);
                    }
                }
            }
        }

        public function getUserStarredArticles($user_id)
        {
            $query = $this->getQuery();
            $query->where(self::UID, $user_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->join(tables\Articles::getTable(), Articles::ID, self::ARTICLE);
            $query->where(Articles::DELETED, 0);

            $res = $this->select($query);
            return $res;
        }

        public function addStarredArticle($user_id, $article_id)
        {
            $insertion = new Insertion();
            $insertion->add(self::ARTICLE, $article_id);
            $insertion->add(self::UID, $user_id);
            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());

            $this->rawInsert($insertion);
        }

        public function removeStarredArticle($user_id, $article_id)
        {
            $query = $this->getQuery();
            $query->where(self::ARTICLE, $article_id);
            $query->where(self::UID, $user_id);

            $this->rawDelete($query);
            return true;
        }

        public function hasStarredArticle($user_id, $article_id)
        {
            $query = $this->getQuery();
            $query->where(self::ARTICLE, $article_id);
            $query->where(self::UID, $user_id);

            return $this->count($query);
        }

    }
