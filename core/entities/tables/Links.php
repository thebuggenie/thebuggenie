<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Insertion;
    use b2db\Update;
    use thebuggenie\core\framework,
        b2db\Criteria;

    /**
     * Links table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Links table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="links")
     */
    class Links extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'links';
        const ID = 'links.id';
        const UID = 'links.uid';
        const URL = 'links.url';
        const LINK_ORDER = 'links.link_order';
        const DESCRIPTION = 'links.description';
        const TARGET_TYPE = 'links.target_type';
        const TARGET_ID = 'links.target_id';
        const SCOPE = 'links.scope';

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addVarchar(self::URL, 300);
            parent::addInteger(self::LINK_ORDER, 3);
            parent::addVarchar(self::TARGET_TYPE, 30);
            parent::addInteger(self::TARGET_ID, 10);
            parent::addVarchar(self::DESCRIPTION, 100, '');
            parent::addForeignKeyColumn(self::UID, Users::getTable(), Users::ID);
        }
        
        public function addLink($target_type, $target_id = 0, $url = null, $description = null, $link_order = null, $scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            if ($link_order === null)
            {
                $query = $this->getQuery();
                $query->addSelectionColumn(self::LINK_ORDER, 'max_order', \b2db\Query::DB_MAX, '', '+1');
                $query->where(self::TARGET_TYPE, $target_type);
                $query->where(self::TARGET_ID, $target_id);
                $query->where(self::SCOPE, $scope);
    
                $row = $this->rawSelectOne($query);
                $link_order = ($row->get('max_order')) ? $row->get('max_order') : 1;
            }

            $insertion = new Insertion();
            $insertion->add(self::TARGET_TYPE, $target_type);
            $insertion->add(self::TARGET_ID, $target_id);
            $insertion->add(self::URL, $url);
            $insertion->add(self::DESCRIPTION, $description);
            $insertion->add(self::LINK_ORDER, $link_order);
            $insertion->add(self::UID, (framework\Context::getUser() instanceof \thebuggenie\core\entities\User) ? framework\Context::getUser()->getID() : 0);
            $insertion->add(self::SCOPE, $scope);
            $res = $this->rawInsert($insertion);

            framework\Context::getCache()->clearCacheKeys(array(framework\Cache::KEY_MAIN_MENU_LINKS));

            return $res->getInsertID();
        }
        
        public function getLinks($target_type, $target_id = 0)
        {
            $links = array();
            $query = $this->getQuery();
            $query->where(self::TARGET_TYPE, $target_type);
            $query->where(self::TARGET_ID, $target_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->addOrderBy(self::LINK_ORDER, \b2db\QueryColumnSort::SORT_ASC);
            if ($res = $this->rawSelect($query, 'none'))
            {
                while ($row = $res->getNextRow())
                {
                    $links[] = array('id' => $row->get(self::ID), 'target_type' => $row->get(self::TARGET_TYPE), 'target_id' => $row->get(self::TARGET_ID), 'url' => $row->get(self::URL), 'description' => $row->get(self::DESCRIPTION));
                }
            }
            return $links;
        }
        
        public function addLinkToIssue($issue_id, $url, $description = null)
        {
            return $this->addLink('issue', $issue_id, $url, $description);
        }
        
        public function getMainLinks()
        {
            return $this->getLinks('main_menu');
        }
        
        public function getByIssueID($issue_id)
        {
            return $this->getLinks('issue', $issue_id);
        }
        
        public function removeByTargetTypeTargetIDandLinkID($target_type, $target_id, $link_id = null)
        {
            $query = $this->getQuery();
            $query->where(self::TARGET_TYPE, $target_type);
            $query->where(self::TARGET_ID, $target_id);
            if ($link_id !== null)
            {
                $query->where(self::ID, $link_id);
            }
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawDelete($query);

            framework\Context::getCache()->clearCacheKeys(array(framework\Cache::KEY_MAIN_MENU_LINKS));
            
            return true;
        }

        public function removeByIssueIDandLinkID($issue_id, $link_id)
        {
            return $this->removeByTargetTypeTargetIDandLinkID('issue', $issue_id, $link_id);
        }
        
        public function addMainMenuLink($url = null, $description = null, $link_order = null, $scope = null)
        {
            return $this->addLink('main_menu', 0, $url, $description, $link_order, $scope);
        }

        public function saveLinkOrder($links)
        {
            foreach ($links as $key => $link_id)
            {
                $update = new Update();
                $update->add(self::LINK_ORDER, $key + 1);
                $this->rawUpdateById($update, $link_id);
            }
            framework\Context::getCache()->clearCacheKeys(array(framework\Cache::KEY_MAIN_MENU_LINKS));
        }

        public function loadFixtures(\thebuggenie\core\entities\Scope $scope)
        {
            $scope_id = $scope->getID();
            
            $this->addMainMenuLink('https://thebuggenie.com', 'The Bug Genie homepage', 1, $scope_id);
            $this->addMainMenuLink(null, null, 2, $scope_id);
            $this->addMainMenuLink('https://issues.thebuggenie.com', 'Online issue tracker', 4, $scope_id);
            $this->addMainMenuLink('', "''This is the issue tracker for The Bug Genie''", 5, $scope_id);
        }

        protected function setupIndexes()
        {
            $this->addIndex('targettype_targetid_scope', array(self::TARGET_TYPE, self::TARGET_ID, self::SCOPE));
        }

    }
