<?php

    namespace thebuggenie\core\modules\installation\upgrade_414;

    use thebuggenie\core\entities\common\IdentifiableScoped;

    /**
     * @Table(name="\thebuggenie\core\modules\installation\upgrade_414\FilesTable")
     */
    class File extends IdentifiableScoped
    {

        /**
         * @Column(type="string", length=200)
         */
        protected $_content_type;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_uploaded_at;

        /**
         * @Column(type="string", length=200)
         */
        protected $_real_filename;

        /**
         * @Column(type="string", length=200, name="original_filename")
         */
        protected $_name;

        /**
         * @Column(type="blob")
         */
        protected $_content;

        /**
         * @Column(type="string", length=200)
         */
        protected $_description;

        /**
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\User")
         */
        protected $_uid;

    }
