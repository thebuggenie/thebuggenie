<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\entities\SavedSearch;
    use thebuggenie\core\framework;
    use b2db\Core,
        b2db\Criteria,
        b2db\Criterion;

    /**
     * @Table(name="savedsearches")
     * @Entity(class="\thebuggenie\core\entities\SavedSearch")
     */
    class SavedSearches extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;
        const B2DBNAME = 'savedsearches';
        const ID = 'savedsearches.id';
        const SCOPE = 'savedsearches.scope';
        const NAME = 'savedsearches.name';
        const DESCRIPTION = 'savedsearches.description';
        const GROUPBY = 'savedsearches.groupby';
        const GROUPORDER = 'savedsearches.grouporder';
        const ISSUES_PER_PAGE = 'savedsearches.issues_per_page';
        const TEMPLATE_NAME = 'savedsearches.templatename';
        const TEMPLATE_PARAMETER = 'savedsearches.templateparameter';
        const APPLIES_TO_PROJECT = 'savedsearches.applies_to_project';
        const IS_PUBLIC = 'savedsearches.is_public';
        const UID = 'savedsearches.uid';

        /**
         * @param $user_id
         * @param int $project_id
         * @return SavedSearch[][]
         */
        public function getAllSavedSearchesByUserIDAndPossiblyProjectID($user_id, $project_id = 0)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $criteria = new Criteria();
            $criteria->where(self::UID, $user_id);
            $criteria->or(self::UID, 0);
            $query->and($criteria);

            if ($project_id !== 0) {
                $query->where(self::APPLIES_TO_PROJECT, $project_id);
            } else {
                $query->where(self::APPLIES_TO_PROJECT, 0);
            }

            $retarr = array('user' => array(), 'public' => array());

            if ($res = $this->select($query, 'none'))
            {
                foreach ($res as $id => $search)
                {
                    if ($search->getUserID() == 0 && !$search->isPublic()) continue;

                    $retarr[($search->getUserID() != 0) ? 'user' : 'public'][$id] = $search;
                }
            }

            return $retarr;
        }

    }
