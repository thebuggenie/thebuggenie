<?php

    namespace thebuggenie\core\entities\tables;

    use \thebuggenie\core\framework;

    /**
     * B2DB Table, vcs_integration -> VCSIntegrationFilesTable
     *
     * @method static CommitFiles getTable()
     *
     * @Entity(class="\thebuggenie\core\entities\CommitFileDiff")
     * @Table(name="commitfile_diffs")
     */
    class CommitFileDiffs extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;
        const B2DBNAME = 'commitfile_diffs';
        const ID = 'commitfile_diffs.id';
        const SCOPE = 'commitfile_diffs.scope';

    }
