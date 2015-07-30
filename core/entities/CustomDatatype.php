<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\framework;

    /**
     * @Table(name="\thebuggenie\core\entities\tables\CustomFields")
     */
    class CustomDatatype extends DatatypeBase
    {

        const DROPDOWN_CHOICE_TEXT = 1;
        const INPUT_TEXT = 2;
        const INPUT_TEXTAREA_MAIN = 3;
        const INPUT_TEXTAREA_SMALL = 4;
        const RADIO_CHOICE = 5;
        const RELEASES_CHOICE = 8;
        const COMPONENTS_CHOICE = 10;
        const EDITIONS_CHOICE = 12;
        const STATUS_CHOICE = 13;
        const USER_CHOICE = 14;
        const TEAM_CHOICE = 15;
        const CALCULATED_FIELD = 18;
        const DATE_PICKER = 19;
        const MILESTONE_CHOICE = 20;
        const CLIENT_CHOICE = 21;

        protected static $_types = null;

        /**
         * This custom types options (if any)
         *
         * @var array
         * @Relates(class="\thebuggenie\core\entities\CustomDatatypeOption", collection=true, foreign_column="customfield_id", orderby="sort_order")
         */
        protected $_options = null;

        /**
         * The custom types description
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_description = null;

        /**
         * The custom types instructions
         *
         * @var string
         * @Column(type="text")
         */
        protected $_instructions = null;

        /**
         * Returns all custom types available
         *
         * @return array
         */
        public static function getAll()
        {
            if (self::$_types === null)
            {
                self::$_types = tables\CustomFields::getTable()->getAll();
            }
            return self::$_types;
        }

        public static function getByFieldType($type)
        {
            $return_fields = array();
            foreach (self::getAll() as $field)
            {
                if ($field->getType() == $type) $return_fields[$field->getID()] = $field;
            }

            return $return_fields;
        }

        public static function getAllExceptTypes($types)
        {
            $return_fields = array();
            foreach (self::getAll() as $field)
            {
                if (!in_array($field->getType(), $types)) $return_fields[$field->getID()] = $field;
            }

            return $return_fields;
        }

        public static function getFieldTypes()
        {
            $i18n = framework\Context::getI18n();
            $types = array();
            $types[self::DROPDOWN_CHOICE_TEXT] = $i18n->__('Dropdown list with custom text choices');
            $types[self::INPUT_TEXT] = $i18n->__('Single line text input');
            $types[self::INPUT_TEXTAREA_MAIN] = $i18n->__('Textarea in issue main area');
            $types[self::INPUT_TEXTAREA_SMALL] = $i18n->__('Textarea (small) in issue details list');
            $types[self::RADIO_CHOICE] = $i18n->__('Radio choices');
            $types[self::RELEASES_CHOICE] = $i18n->__('Dropdown list of available releases');
            $types[self::COMPONENTS_CHOICE] = $i18n->__('Dropdown list of available components');
            $types[self::EDITIONS_CHOICE] = $i18n->__('Dropdown list of available editions');
            $types[self::MILESTONE_CHOICE] = $i18n->__('Dropdown list of available milestones');
            $types[self::STATUS_CHOICE] = $i18n->__('Dropdown list of all statuses');
            $types[self::CALCULATED_FIELD] = $i18n->__('Calculated field');
            $types[self::DATE_PICKER] = $i18n->__('Date picker');
            $types[self::USER_CHOICE] = $i18n->__('Find and pick a user');
            $types[self::TEAM_CHOICE] = $i18n->__('Find and pick a team');
            $types[self::CLIENT_CHOICE] = $i18n->__('Find and pick a client');

            return $types;

        }

        protected function _preSave($is_new)
        {
            parent::_preSave($is_new);
            if ($is_new)
            {
                $this->_generateKey();
                if (array_key_exists($this->_key, self::getAll()))
                {
                    throw new \Exception(framework\Context::getI18n()->__('This field key already exists'));
                }
            }
        }

        protected function _preDelete()
        {
            tables\CustomFieldOptions::getTable()->deleteCustomFieldOptions($this->getID());
            tables\IssueFields::getTable()->deleteByIssueFieldKey($this->getKey());
        }

        public static function doesKeyExist($key)
        {
            return array_key_exists($key, self::getAll());
        }

        /**
         * Get a custom type by its key
         *
         * @param string $key
         *
         * @return \thebuggenie\core\entities\CustomDatatype
         */
        public static function getByKey($key)
        {
            foreach (self::getAll() as $field)
            {
                if ($field->getKey() == $key) return $field;
            }
            return null;
        }

        public static function getCustomChoiceFieldsAsArray()
        {
            return array(self::DROPDOWN_CHOICE_TEXT,
                        self::RADIO_CHOICE,
                        self::CALCULATED_FIELD
            );
        }

        public static function getChoiceFieldsAsArray()
        {
            return array(self::DROPDOWN_CHOICE_TEXT, self::RADIO_CHOICE, self::RELEASES_CHOICE, self::COMPONENTS_CHOICE,
                        self::EDITIONS_CHOICE, self::STATUS_CHOICE, self::USER_CHOICE, self::TEAM_CHOICE, self::CLIENT_CHOICE, self::MILESTONE_CHOICE);
        }

        public static function getInternalChoiceFieldsAsArray()
        {
            return array(self::RELEASES_CHOICE, self::COMPONENTS_CHOICE, self::MILESTONE_CHOICE,
                        self::EDITIONS_CHOICE, self::STATUS_CHOICE, self::USER_CHOICE, self::TEAM_CHOICE, self::CLIENT_CHOICE);
        }

        /**
         * Constructor
         *
         * @param B2DBrow $row [optional] A B2DBrow to use
         */
        public function _construct(\b2db\Row $row, $foreign_key = null)
        {
            $this->_description = $this->_description ?: $this->_name;
        }

        protected function _populateOptions()
        {
            if ($this->_options === null)
            {
                if ($this->hasCustomOptions()) {
                    $this->_b2dbLazyload('_options');
                } else {
                    switch ($this->getType()) {
                        case self::RELEASES_CHOICE:
                            $this->_options = (framework\Context::isProjectContext()) ? framework\Context::getCurrentProject()->getBuilds() : \thebuggenie\core\entities\tables\Builds::getTable()->selectAll();
                            break;
                        case self::COMPONENTS_CHOICE:
                            $this->_options = (framework\Context::isProjectContext()) ? framework\Context::getCurrentProject()->getComponents() : \thebuggenie\core\entities\tables\Components::getTable()->selectAll();
                            break;
                        case self::EDITIONS_CHOICE:
                            $this->_options = (framework\Context::isProjectContext()) ? framework\Context::getCurrentProject()->getEditions() : \thebuggenie\core\entities\tables\Editions::getTable()->selectAll();
                            break;
                        case self::MILESTONE_CHOICE:
                            $this->_options = (framework\Context::isProjectContext()) ? framework\Context::getCurrentProject()->getMilestonesForIssues() : \thebuggenie\core\entities\tables\Milestones::getTable()->selectAll();
                            break;
                        case self::STATUS_CHOICE:
                            $this->_options = \thebuggenie\core\entities\Status::getAll();
                            break;
                    }
                }
            }
        }

        public function getOptions()
        {
            $this->_populateOptions();
            return $this->_options;
        }

        public function createNewOption($name, $value, $itemdata = null)
        {
            if ($this->getType() == self::CALCULATED_FIELD) {
                // Only allow one option/formula for the calculated field
                $opts = $this->getOptions();
                foreach ($opts as $option) {
                    $option->delete();
                }
            }

            $option = new CustomDatatypeOption();
            $option->setName($name);
            $option->setKey($this->getKey());
            $option->setValue($value);
            $option->setItemdata($itemdata);
            $option->setCustomdatatype($this->_id);
            $option->save();

            // In order to set permissions correctly the item type has to be the same
            // as the option id not the item field. set the opton id with the newly generated
            // option ID and save again
            $option->setItemtype($option->getID());
            $option->save();
            $this->_options = null;
            return $option;
        }

        /**
         * Return this custom types key
         *
         * @return string
         */
        public function getKey()
        {
            return $this->_key;
        }

        public function getType()
        {
            return $this->_itemtype;
        }

        public function setType($type)
        {
            $this->_itemtype = $type;
        }

        /**
         * Return the description for this custom type
         *
         * @return string
         */
        public function getTypeDescription()
        {
            $types = self::getFieldTypes();
            return $types[$this->_itemtype];
        }

        public function hasCustomOptions()
        {
            return (bool) in_array($this->getType(), self::getCustomChoiceFieldsAsArray());
        }

        public function hasPredefinedOptions()
        {
            return (bool) in_array($this->getType(), self::getChoiceFieldsAsArray());
        }

        /**
         * Get the custom types description
         */
        public function getDescription()
        {
            return $this->_description;
        }

        /**
         * Set the custom types description
         *
         * @param string $description
         */
        public function setDescription($description)
        {
            $this->_description = $description;
        }

        /**
         * Get the custom types instructions
         */
        public function getInstructions()
        {
            return $this->_instructions;
        }

        /**
         * Set the custom types instructions
         *
         * @param string $instructions
         */
        public function setInstructions($instructions)
        {
            $this->_instructions = $instructions;
        }

        /**
         * Whether or not this custom type has any instructions
         *
         * @return boolean
         */
        public function hasInstructions()
        {
            return (bool) $this->_instructions;
        }

        /**
         * Set the custom type name
         *
         * @param string $name
         */
        public function setName($name)
        {
            $this->_name = $name;
        }

        /**
         * Whether or not this custom data type is visible for this issue type
         *
         * @param integer $issuetype_id
         *
         * @return bool
         */
        public function isVisibleForIssuetype($issuetype_id)
        {
            return true;
        }

        /**
         * Whether or not this custom data type is searchable from the Issues filter
         *
         * @return bool
         */
        public function isSearchable()
        {
            switch ($this->getType()) {
                case self::CALCULATED_FIELD:
                    return false;
            }
            return true;
        }

        /**
         * Whether or not this custom data type is editable from the Issues detail page
         *
         * @return bool
         */
        public function isEditable()
        {
            switch ($this->getType()) {
                case self::CALCULATED_FIELD:
                    return false;
            }
            return true;
        }

        public function isBuiltin()
        {
            return false;
        }

    }

