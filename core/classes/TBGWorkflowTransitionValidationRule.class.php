<?php

	/**
	 * Workflow transition validation rule class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * Workflow transition validation rule class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class TBGWorkflowTransitionValidationRule extends TBGIdentifiableClass
	{
		
		const RULE_MAX_ASSIGNED_ISSUES = 'max_assigned_issues';
		const RULE_STATUS_VALID = 'valid_status';
		const RULE_RESOLUTION_VALID = 'valid_resolution';
		const RULE_REPRODUCABILITY_VALID = 'valid_reproducability';
		const RULE_PRIORITY_VALID = 'valid_priority';

		static protected $_b2dbtablename = 'TBGWorkflowTransitionValidationRulesTable';
		
		protected $_rule = null;

		protected $_rule_value = null;
		
		protected $_pre_or_post;

		/**
		 * The connected transition
		 *
		 * @var TBGWorkflowTransition
		 * @Class TBGWorkflowTransition
		 */
		protected $_transition_id = null;

		/**
		 * The associated workflow object
		 *
		 * @var TBGWorkflow
		 * @Class TBGWorkflow
		 */
		protected $_workflow_id = null;

		/**
		 * Return the workflow
		 *
		 * @return TBGWorkflow
		 */
		public function getWorkflow()
		{
			return $this->_getPopulatedObjectFromProperty('_workflow_id');
		}

		public function setWorkflow(TBGWorkflow $workflow)
		{
			$this->_workflow_id = $workflow;
		}

		public function setTransition(TBGWorkflowTransition $transition)
		{
			$this->_transition_id = $transition;
		}
		
		public function getTransition()
		{
			return $this->_getPopulatedObjectFromProperty('_transition_id');
		}

		public function setPost()
		{
			$this->_pre_or_post = 'post';
		}
		
		public function setPre()
		{
			$this->_pre_or_post = 'pre';
		}
		
		public function isPreOrPost()
		{
			return $this->_pre_or_post;
		}
		
		public function isPre()
		{
			return (bool) ($this->_pre_or_post == 'pre');
		}
		
		public function isPost()
		{
			return (bool) ($this->_pre_or_post == 'post');
		}

		public function setRule($rule)
		{
			$this->_rule = $rule;
		}
		
		public function getRule()
		{
			return $this->_rule;
		}
		
		public function setRuleValue($rule_value)
		{
			$this->_rule_value = $rule_value;
		}
		
		public function getRuleValue()
		{
			return $this->_rule_value;
		}
		
		public function getRuleValueAsJoinedString()
		{
			if ($this->_rule == self::RULE_STATUS_VALID)
			{
				$fieldname = 'TBGStatus';
			}
			elseif ($this->_rule == self::RULE_RESOLUTION_VALID)
			{
				$fieldname = 'TBGResolution';
			}
			elseif ($this->_rule == self::RULE_REPRODUCABILITY_VALID)
			{
				$fieldname = 'TBGReproducability';
			}
			elseif ($this->_rule == self::RULE_PRIORITY_VALID)
			{
				$fieldname = 'TBGPriority';
			}
			$values = explode(',', $this->getRuleValue());
			$return_values = array();
			foreach ($values as $value)
			{
				try
				{
					$return_values[] = TBGContext::factory()->$fieldname((int) $value)->getName();
				}
				catch (Exception $e) {}
			}
			return join(' / ', $return_values);
		}
		
		public function isValueValid($value)
		{
			if ($this->_rule == self::RULE_STATUS_VALID)
			{
				$fieldname = 'TBGStatus';
			}
			elseif ($this->_rule == self::RULE_RESOLUTION_VALID)
			{
				$fieldname = 'TBGResolution';
			}
			elseif ($this->_rule == self::RULE_REPRODUCABILITY_VALID)
			{
				$fieldname = 'TBGReproducability';
			}
			elseif ($this->_rule == self::RULE_PRIORITY_VALID)
			{
				$fieldname = 'TBGPriority';
			}
			switch ($this->_rule)
			{
				case self::RULE_STATUS_VALID:
				case self::RULE_RESOLUTION_VALID:
				case self::RULE_REPRODUCABILITY_VALID:
				case self::RULE_PRIORITY_VALID:
					$value = ($value instanceof $fieldname) ? $value->getID() : $value;
					return ($this->getRuleValue()) ? in_array($value, explode(',', $this->getRuleValue())) : (bool) $value;
					break;
			}
			return true;
		}
		
		public function isValid($input)
		{
			switch ($this->_rule)
			{
				case self::RULE_MAX_ASSIGNED_ISSUES:
					$num_issues = (int) $this->getRuleValue();
					return ($num_issues) ? (bool) (count(TBGContext::getUser()->getUserAssignedIssues()) < $num_issues) : true;
					break;
				case self::RULE_STATUS_VALID:
				case self::RULE_PRIORITY_VALID:
				case self::RULE_RESOLUTION_VALID:
				case self::RULE_REPRODUCABILITY_VALID:
					$valid_items = explode(',', $this->getRuleValue());
					$valid = false;
					foreach ($valid_items as $item)
					{
						if ($this->_rule == self::RULE_STATUS_VALID)
						{
							$fieldname = 'Status';
							$fieldname_small = 'status';
						}
						elseif ($this->_rule == self::RULE_RESOLUTION_VALID)
						{
							$fieldname = 'Resolution';
							$fieldname_small = 'resolution';
						}
						elseif ($this->_rule == self::RULE_REPRODUCABILITY_VALID)
						{
							$fieldname = 'Reproducability';
							$fieldname_small = 'reproducability';
						}
						elseif ($this->_rule == self::RULE_PRIORITY_VALID)
						{
							$fieldname = 'Priority';
							$fieldname_small = 'priority';
						}
						
						if ($input instanceof TBGIssue)
						{
							$type = "TBG{$fieldname}";
							$getter = "get{$fieldname}";
							if (TBGContext::factory()->$type((int) $item)->getID() == $issue->$getter()->getID())
							{
								$valid = true;
								break;
							}
						}
						elseif ($input instanceof TBGRequest)
						{
							if ($input->getParameter("{$fieldname_small}_id") == $item)
							{
								$valid = true;
								break;
							}
						}
					}
					return $valid;
					break;
			}
		}

	}
