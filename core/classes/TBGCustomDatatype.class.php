<?php

	/**
	 * @Table(name="TBGCustomFieldsTable")
	 */
	class TBGCustomDatatype extends TBGDatatypeBase
	{
		
		const DROPDOWN_CHOICE_TEXT = 1;
		const INPUT_TEXT = 2;
		const INPUT_TEXTAREA_MAIN = 3;
		const INPUT_TEXTAREA_SMALL = 4;
		const RADIO_CHOICE = 5;
		const CHECKBOX_CHOICES = 6;
		const RELEASES_LIST = 7;
		const RELEASES_CHOICE = 8;
		const COMPONENTS_LIST = 9;
		const COMPONENTS_CHOICE = 10;
		const EDITIONS_LIST = 11;
		const EDITIONS_CHOICE = 12;
		const STATUS_CHOICE = 13;
		const USER_CHOICE = 14;
		const TEAM_CHOICE = 15;
		const USER_OR_TEAM_CHOICE = 17;
		const CALCULATED_FIELD = 18;

		protected static $_types = null;

		/**
		 * This custom types options (if any)
		 *
		 * @var array
		 * @Relates(class="TBGCustomDatatypeOption", collection=true, foreign_column="customfield_id", orderby="sort_order")
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
				self::$_types = TBGCustomFieldsTable::getTable()->getAll();
			}
			return self::$_types;
		}

		public static function getFieldTypes()
		{
			$i18n = TBGContext::getI18n();
			$types = array();
			$types[self::DROPDOWN_CHOICE_TEXT] = $i18n->__('Dropdown list with custom text choices');
			/*$types[self::DROPDOWN_CHOICE_TEXT_COLORED] = $i18n->__('Dropdown list with custom colored text choices');
			$types[self::DROPDOWN_CHOICE_TEXT_COLOR] = $i18n->__('Dropdown list with custom color and text choices');
			$types[self::DROPDOWN_CHOICE_TEXT_ICON] = $i18n->__('Dropdown list with custom text choices and icons');*/
			$types[self::INPUT_TEXT] = $i18n->__('Single line text input');
			$types[self::INPUT_TEXTAREA_MAIN] = $i18n->__('Textarea in issue main area');
			$types[self::INPUT_TEXTAREA_SMALL] = $i18n->__('Textarea (small) in issue details list');
			$types[self::RADIO_CHOICE] = $i18n->__('Radio choices');
			// $types[self::CHECKBOX_CHOICES] = $i18n->__('Checkbox choices');
			// $types[self::RELEASES_LIST] = $i18n->__('Add one or more releases from the list of available releases');
			$types[self::RELEASES_CHOICE] = $i18n->__('Select a release from the list of available releases');
			// $types[self::COMPONENTS_LIST] = $i18n->__('Add one or more components from the list of available components');
			$types[self::COMPONENTS_CHOICE] = $i18n->__('Select a component from the list of available components');
			// $types[self::EDITIONS_LIST] = $i18n->__('Add one or more editions from the list of available editions');
			$types[self::EDITIONS_CHOICE] = $i18n->__('Select a edition from the list of available editions');
			$types[self::STATUS_CHOICE] = $i18n->__('Dropdown list with statuses');
			$types[self::CALCULATED_FIELD] = $i18n->__('Calculated Field');
			// $types[self::USER_CHOICE] = $i18n->__('Find and pick a user');
			// $types[self::TEAM_CHOICE] = $i18n->__('Find and pick a team');
			// $types[self::USER_OR_TEAM_CHOICE] = $i18n->__('Find and pick a user or a team');

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
					throw new Exception(TBGContext::getI18n()->__('This field key already exists'));
				}
			}
		}
		
		/**
		 * Delete a custom type by id
		 *
		 * @param integer $id
		 */
		protected function _preDelete()
		{
			TBGCustomFieldOptionsTable::getTable()->deleteCustomFieldOptions($this->getID());
			\b2db\Core::getTable('TBGIssueFieldsTable')->deleteByIssueFieldKey($key);
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
		 * @return TBGCustomDatatype
		 */
		public static function getByKey($key)
		{
			$row = \b2db\Core::getTable('TBGCustomFieldsTable')->getByKey($key);
			if ($row)
			{
				return TBGContext::factory()->TBGCustomDatatype($row->get(TBGCustomFieldsTable::ID), $row);
			}
			return null;
		}

		public static function getCustomChoiceFieldsAsArray()
		{
			return array(self::CHECKBOX_CHOICES,
						self::DROPDOWN_CHOICE_TEXT,
						self::RADIO_CHOICE,
						self::CALCULATED_FIELD
            );
		}

		public static function getChoiceFieldsAsArray()
		{
			return array(self::CHECKBOX_CHOICES, self::DROPDOWN_CHOICE_TEXT, self::RADIO_CHOICE, self::RELEASES_CHOICE,
			             self::COMPONENTS_CHOICE, self::EDITIONS_CHOICE, self::STATUS_CHOICE, self::USER_CHOICE,
			             self::TEAM_CHOICE, self::USER_OR_TEAM_CHOICE);
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
				$this->_b2dbLazyload('_options');
			}
		}

		public function getOptions()
		{
			if ($this->hasCustomOptions())
			{
				$this->_populateOptions();
				return $this->_options;
			}
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

			$option = new TBGCustomDatatypeOption();
			$option->setName($name);
			$option->setItemtype($this->_itemtype);
			$option->setKey($this->getKey());
			$option->setValue($value);
			$option->setItemdata($itemdata);
			$option->setCustomdatatype($this->_id);
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

	}

