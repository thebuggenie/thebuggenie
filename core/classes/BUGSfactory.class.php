<?php

	/**
	 * Static factory class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * Static factory class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	final class BUGSfactory
	{
		static protected $_projects = array();
		static protected $_editions = array();
		static protected $_builds = array();
		static protected $_components = array();
		static protected $_users = array();
		static protected $_teams = array();
		static protected $_groups = array();
		static protected $_customers = array();
		static protected $_statuses = array();
		static protected $_priorities = array();
		static protected $_resolutions = array();
		static protected $_reproducabilities = array();
		static protected $_categories = array();
		static protected $_severities = array();
		static protected $_issuetypes = array();
		static protected $_tasks = array();
		static protected $_issues = array();
		static protected $_modules = array();
		static protected $_milestones = array();
		static protected $_userstates = array();
		static protected $_scopes = array();
		
		/**
		 * Returns a BUGSproject
		 *
		 * @param integer $p_id
		 * 
		 * @return BUGSproject
		 */
		public static function projectLab($p_id, $row = null)
		{
			if ((int) $p_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_projects[$p_id]))
			{
				try
				{
					if (!($project = BUGScache::get('project_'.$p_id)))
					{
						self::$_projects[$p_id] = new BUGSproject($p_id, $row);
						BUGScache::add('project_'.$p_id, self::$_projects[$p_id]);
					}
					else
					{
						BUGSlogging::log('using cached project '.$p_id);
						self::$_projects[$p_id] = $project;
					}
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_projects[$p_id];
		}
		
		/**
		 * Returns a BUGSedition
		 *
		 * @param integer $e_id
		 * 
		 * @return BUGSedition
		 */
		public static function editionLab($e_id, $row = null)
		{
			if ((int) $e_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_editions[$e_id]))
			{
				try
				{
					self::$_editions[$e_id] = new BUGSedition($e_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_editions[$e_id];
		}
		
		/**
		 * Returns a BUGSbuild
		 *
		 * @param integer $b_id
		 * 
		 * @return BUGSbuild
		 */
		public static function buildLab($b_id, $row = null)
		{
			if ((int) $b_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_builds[$b_id]))
			{
				try
				{
					self::$_builds[$b_id] = new BUGSbuild($b_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_builds[$b_id];
		}
		
		/**
		 * Returns a BUGScomponent
		 *
		 * @param integer $c_id
		 * 
		 * @return BUGScomponent
		 */
		public static function componentLab($c_id, $row = null)
		{
			if ((int) $c_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_components[$c_id]))
			{
				try
				{
					self::$_components[$c_id] = new BUGScomponent($c_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_components[$c_id];
		}
		
		/**
		 * Returns a BUGSuser
		 *
		 * @param integer $u_id
		 * 
		 * @return BUGSuser
		 */
		public static function userLab($u_id, $row = null)
		{
			if (is_object($u_id)) throw new Exception('object??');
			if ((int) $u_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_users[$u_id]))
			{
				try
				{
					self::$_users[$u_id] = new BUGSuser($u_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_users[$u_id];
		}
		
		/**
		 * Returns a BUGSteam
		 *
		 * @param integer $t_id
		 * 
		 * @return BUGSteam
		 */
		public static function teamLab($t_id, $row = null)
		{
			if ((int) $t_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_teams[$t_id]))
			{
				try
				{
					self::$_teams[$t_id] = new BUGSteam($t_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_teams[$t_id];
		}

		/**
		 * Returns a BUGSgroup
		 *
		 * @param integer $g_id
		 * 
		 * @return BUGSgroup
		 */
		public static function groupLab($g_id, $row = null)
		{
			if ((int) $g_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_groups[$g_id]))
			{
				try
				{
					self::$_groups[$g_id] = new BUGSgroup($g_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_groups[$g_id];
		}
		
		/**
		 * Returns a BUGScustomer
		 *
		 * @param integer $c_id
		 * 
		 * @return BUGScustomer
		 */
		public static function customerLab($c_id, $row = null)
		{
			if ((int) $c_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_customers[$c_id]))
			{
				try
				{
					self::$_customers[$c_id] = new BUGScustomer($c_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_customers[$c_id];
		}
		
		/**
		 * Returns a BUGSstatus
		 *
		 * @param integer $i_id The item id
		 * @param B2DBrow $row[optional] a row to use
		 * 
		 * @return BUGSstatus
		 */
		public static function BUGSstatusLab($i_id, $row = null)
		{
			if ((int) $i_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_statuses[$i_id]))
			{
				try
				{
					self::$_statuses[$i_id] = new BUGSstatus($i_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_statuses[$i_id];
		}
		
		/**
		 * Returns a BUGSpriority
		 *
		 * @param integer $i_id The item id
		 * @param B2DBrow $row[optional] a row to use
		 * 
		 * @return BUGSpriority
		 */
		public static function BUGSpriorityLab($i_id, $row = null)
		{
			if ((int) $i_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_priorities[$i_id]))
			{
				try
				{
					self::$_priorities[$i_id] = new BUGSpriority($i_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_priorities[$i_id];
		}
		
		/**
		 * Returns a BUGSseverity
		 *
		 * @param integer $i_id The item id
		 * @param B2DBrow $row[optional] a row to use
		 * 
		 * @return BUGSseverity
		 */
		public static function BUGSseverityLab($i_id, $row = null)
		{
			if ((int) $i_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_severities[$i_id]))
			{
				try
				{
					self::$_severities[$i_id] = new BUGSseverity($i_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_severities[$i_id];
		}

		/**
		 * Returns a BUGScategory
		 *
		 * @param integer $i_id The item id
		 * @param B2DBrow $row[optional] a row to use
		 * 
		 * @return BUGScategory
		 */
		public static function BUGScategoryLab($i_id, $row = null)
		{
			if ((int) $i_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_categories[$i_id]))
			{
				try
				{
					self::$_categories[$i_id] = new BUGScategory($i_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_categories[$i_id];
		}

		/**
		 * Returns a BUGSresolution
		 *
		 * @param integer $i_id The item id
		 * @param B2DBrow $row[optional] a row to use
		 * 
		 * @return BUGSresolution
		 */
		public static function BUGSresolutionLab($i_id, $row = null)
		{
			if ((int) $i_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_resolutions[$i_id]))
			{
				try
				{
					self::$_resolutions[$i_id] = new BUGSresolution($i_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_resolutions[$i_id];
		}

		/**
		 * Returns a BUGSreproducability
		 *
		 * @param integer $i_id The item id
		 * @param B2DBrow $row[optional] a row to use
		 * 
		 * @return BUGSreproducability
		 */
		public static function BUGSreproducabilityLab($i_id, $row = null)
		{
			if ((int) $i_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_reproducabilities[$i_id]))
			{
				try
				{
					self::$_reproducabilities[$i_id] = new BUGSreproducability($i_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_reproducabilities[$i_id];
		}
		
		/**
		 * Returns a BUGSuserstate
		 *
		 * @param integer $us_id
		 * 
		 * @return BUGSuserstate
		 */
		public static function userstateLab($us_id, $row = null)
		{
			if ((int) $us_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_userstates[$us_id]))
			{
				try
				{
					self::$_userstates[$us_id] = new BUGSuserstate($us_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_userstates[$us_id];
		}
		
		/**
		 * Returns a BUGSissuetype
		 *
		 * @param integer $i_id
		 * 
		 * @return BUGSissuetype
		 */
		public static function BUGSissuetypeLab($i_id, $row = null)
		{
			if ($i_id instanceof BUGSissuetype)
			{
				throw new Exception('waat? no control!');
			}
			else
			{
				if ((int) $i_id == 0) throw new Exception('Invalid id');
				if (!isset(self::$_issuetypes[$i_id]))
				{
					try
					{
						self::$_issuetypes[$i_id] = new BUGSissuetype($i_id, $row);
					}
					catch (Exception $e)
					{
						throw $e;
					}
				}
				return self::$_issuetypes[$i_id];
			}
		}

		/**
		 * Returns a BUGStask
		 *
		 * @param integer $t_id
		 * 
		 * @return BUGStask
		 */
		public static function taskLab($t_id, $row = null)
		{
			if ((int) $i_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_tasks[$t_id]))
			{
				try
				{
					self::$_tasks[$t_id] = new BUGStask($t_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_tasks[$t_id];
		}
		
		/**
		 * Returns a BUGSissue
		 *
		 * @param integer $i_id
		 * 
		 * @return BUGSissue
		 */
		public static function BUGSissueLab($i_id, $row = null)
		{
			if ((int) $i_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_issues[$i_id]))
			{
				try
				{
					self::$_issues[$i_id] = new BUGSissue($i_id, $row);
				}					
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_issues[$i_id];
		}
		
		/**
		 * Returns a BUGSmilestone
		 *
		 * @param integer $m_id
		 * 
		 * @return BUGSmilestone
		 */
		public static function milestoneLab($m_id, $row = null)
		{
			if ((int) $m_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_milestones[$m_id]))
			{
				try
				{
					self::$_milestones[$m_id] = new BUGSmilestone($m_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_milestones[$m_id];
		}
		
		/**
		 * Returns a BUGSscope
		 *
		 * @param integer|string|B2DBrow $s_id
		 * @return BUGSscope
		 */
		public static function scopeLab($s_id, $row = null)
		{
			if ((int) $s_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_scopes[$s_id]))
			{
				try
				{
					self::$_scopes[$s_id] = new BUGSscope($s_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_scopes[$s_id];
		}
		
	}
