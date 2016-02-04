<?php

    namespace thebuggenie\core\entities\traits;

    /**
     * Trait for looking up files that are not linked
     *
     * @package thebuggenie
     * @subpackage traits
     */
    trait FileLink
    {

        public function getLinkedFileIds()
        {
            $crit = $this->getCriteria();
            $crit->addSelectionColumn(self::FILE_ID, 'file_id');

            $res = $this->doSelect($crit);
            $linked_file_ids = [];

            if ($res) {
                while ($row = $res->getNextRow()) {
                    $linked_file_ids[$row['file_id']] = $row['file_id'];
                }
            }

            return $linked_file_ids;
        }

    }
