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
	final class TBGFactory
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
		static protected $_customtypes = array();
		static protected $_customtypeoptions = array();
		static protected $_priorities = array();
		static protected $_resolutions = array();
		static protected $_reproducabilities = array();
		static protected $_categories = array();
		static protected $_severities = array();
		static protected $_issuetypes = array();
		static protected $_files = array();
		static protected $_issues = array();
		static protected $_comments = array();
		static protected $_modules = array();
		static protected $_milestones = array();
		static protected $_userstates = array();
		static protected $_scopes = array();
		
		/**
		 * Returns a TBGProject
		 *
		 * @param integer $p_id
		 * 
		 * @return TBGProject
		 */
		public static function projectLab($p_id, $row = null)
		{
			if ((int) $p_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_projects[$p_id]))
			{
				try
				{
					if (!($project = TBGCache::get('project_'.$p_id)))
					{
						self::$_projects[$p_id] = new TBGProject($p_id, $row);
						TBGCache::add('project_'.$p_id, self::$_projects[$p_id]);
					}
					else
					{
						TBGLogging::log('using cached project '.$p_id);
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
		 * Returns a TBGEdition
		 *
		 * @param integer $e_id
		 * 
		 * @return TBGEdition
		 */
		public static function editionLab($e_id, $row = null)
		{
			if ((int) $e_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_editions[$e_id]))
			{
				try
				{
					self::$_editions[$e_id] = new TBGEdition($e_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_editions[$e_id];
		}
		
		/**
		 * Returns a TBGBuild
		 *
		 * @param integer $b_id
		 * 
		 * @return TBGBuild
		 */
		public static function buildLab($b_id, $row = null)
		{
			if ((int) $b_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_builds[$b_id]))
			{
				try
				{
					self::$_builds[$b_id] = new TBGBuild($b_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_builds[$b_id];
		}
		
		/**
		 * Returns a TBGComponent
		 *
		 * @param integer $c_id
		 * 
		 * @return TBGComponent
		 */
		public static function componentLab($c_id, $row = null)
		{
			if ((int) $c_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_components[$c_id]))
			{
				try
				{
					self::$_components[$c_id] = new TBGComponent($c_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_components[$c_id];
		}
		
		/**
		 * Returns a TBGUser
		 *
		 * @param integer $u_id
		 * 
		 * @return TBGUser
		 */
		public static function userLab($u_id, $row = null)
		{
			if (is_object($u_id)) throw new Exception('object??');
			if ((int) $u_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_users[$u_id]))
			{
				try
				{
					self::$_users[$u_id] = new TBGUser($u_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_users[$u_id];
		}
		
		/**
		 * Returns a TBGTeam
		 *
		 * @param integer $t_id
		 * 
		 * @return TBGTeam
		 */
		public static function teamLab($t_id, $row = null)
		{
			if ((int) $t_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_teams[$t_id]))
			{
				try
				{
					self::$_teams[$t_id] = new TBGTeam($t_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_teams[$t_id];
		}

		/**
		 * Returns a TBGGroup
		 *
		 * @param integer $g_id
		 * 
		 * @return TBGGroup
		 */
		public static function groupLab($g_id, $row = null)
		{
			if ((int) $g_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_groups[$g_id]))
			{
				try
				{
					self::$_groups[$g_id] = new TBGGroup($g_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_groups[$g_id];
		}
		
		/**
		 * Returns a TBGCustomer
		 *
		 * @param integer $c_id
		 * 
		 * @return TBGCustomer
		 */
		public static function customerLab($c_id, $row = null)
		{
			if ((int) $c_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_customers[$c_id]))
			{
				try
				{
					self::$_customers[$c_id] = new TBGCustomer($c_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_customers[$c_id];
		}
		
		/**
		 * Returns a TBGCustomDatatype
		 *
		 * @param integer $i_id The item id
		 * @param B2DBrow $row[optional] a row to use
		 *
		 * @return TBGCustomDatatype
		 */
		public static function TBGCustomDatatypeLab($i_id, $row = null)
		{
			if ((int) $i_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_customtypes[$i_id]))
			{
				try
				{
					self::$_customtypes[$i_id] = new TBGCustomDatatype($i_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_customtypes[$i_id];
		}

		/**
		 * Returns a TBGCustomDatatypeOption
		 *
		 * @param integer $i_id The item id
		 * @param B2DBrow $row[optional] a row to use
		 *
		 * @return TBGCustomDatatypeOption
		 */
		public static function TBGCustomDatatypeOptionLab($i_id, $row = null)
		{
			if ((int) $i_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_customtypeoptions[$i_id]))
			{
				try
				{
					self::$_customtypeoptions[$i_id] = new TBGCustomDatatypeOption($i_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_customtypeoptions[$i_id];
		}

		/**
		 * Returns a TBGStatus
		 *
		 * @param integer $i_id The item id
		 * @param B2DBrow $row[optional] a row to use
		 *
		 * @return TBGStatus
		 */
		public static function TBGStatusLab($i_id, $row = null)
		{
			if ((int) $i_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_statuses[$i_id]))
			{
				try
				{
					self::$_statuses[$i_id] = new TBGStatus($i_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_statuses[$i_id];
		}

		/**
		 * Returns a TBGPriority
		 *
		 * @param integer $i_id The item id
		 * @param B2DBrow $row[optional] a row to use
		 * 
		 * @return TBGPriority
		 */
		public static function TBGPriorityLab($i_id, $row = null)
		{
			if ((int) $i_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_priorities[$i_id]))
			{
				try
				{
					self::$_priorities[$i_id] = new TBGPriority($i_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_priorities[$i_id];
		}
		
		/**
		 * Returns a TBGSeverity
		 *
		 * @param integer $i_id The item id
		 * @param B2DBrow $row[optional] a row to use
		 * 
		 * @return TBGSeverity
		 */
		public static function TBGSeverityLab($i_id, $row = null)
		{
			if ((int) $i_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_severities[$i_id]))
			{
				try
				{
					self::$_severities[$i_id] = new TBGSeverity($i_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_severities[$i_id];
		}

		/**
		 * Returns a TBGCategory
		 *
		 * @param integer $i_id The item id
		 * @param B2DBrow $row[optional] a row to use
		 * 
		 * @return TBGCategory
		 */
		public static function TBGCategoryLab($i_id, $row = null)
		{
			if ((int) $i_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_categories[$i_id]))
			{
				try
				{
					self::$_categories[$i_id] = new TBGCategory($i_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_categories[$i_id];
		}

		/**
		 * Returns a TBGResolution
		 *
		 * @param integer $i_id The item id
		 * @param B2DBrow $row[optional] a row to use
		 * 
		 * @return TBGResolution
		 */
		public static function TBGResolutionLab($i_id, $row = null)
		{
			if ((int) $i_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_resolutions[$i_id]))
			{
				try
				{
					self::$_resolutions[$i_id] = new TBGResolution($i_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_resolutions[$i_id];
		}

		/**
		 * Returns a TBGReproducability
		 *
		 * @param integer $i_id The item id
		 * @param B2DBrow $row[optional] a row to use
		 * 
		 * @return TBGReproducability
		 */
		public static function TBGReproducabilityLab($i_id, $row = null)
		{
			if ((int) $i_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_reproducabilities[$i_id]))
			{
				try
				{
					self::$_reproducabilities[$i_id] = new TBGReproducability($i_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_reproducabilities[$i_id];
		}
		
		/**
		 * Returns a TBGUserstate
		 *
		 * @param integer $us_id
		 * 
		 * @return TBGUserstate
		 */
		public static function userstateLab($us_id, $row = null)
		{
			if ((int) $us_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_userstates[$us_id]))
			{
				try
				{
					self::$_userstates[$us_id] = new TBGUserstate($us_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_userstates[$us_id];
		}
		
		/**
		 * Returns a TBGIssuetype
		 *
		 * @param integer $i_id
		 * 
		 * @return TBGIssuetype
		 */
		public static function TBGIssuetypeLab($i_id, $row = null)
		{
			if ($i_id instanceof TBGIssuetype)
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
						self::$_issuetypes[$i_id] = new TBGIssuetype($i_id, $row);
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
		 * Returns a TBGFile
		 *
		 * @param integer $id
		 * 
		 * @return TBGFile
		 */
		public static function TBGFileLab($id, $row = null)
		{
			if ((int) $id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_files[$id]))
			{
				try
				{
					self::$_files[$id] = new TBGFile($id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_files[$id];
		}
		
		/**
		 * Returns a TBGIssue
		 *
		 * @param integer $i_id
		 * 
		 * @return TBGIssue
		 */
		public static function TBGIssueLab($i_id, $row = null)
		{
			if ((int) $i_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_issues[$i_id]))
			{
				try
				{
					self::$_issues[$i_id] = new TBGIssue($i_id, $row);
				}					
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_issues[$i_id];
		}

		/**
		 * Returns a TBGComment
		 *
		 * @param integer $c_id
		 * 
		 * @return TBGComment
		 */
		public static function TBGCommentLab($c_id, $row = null)
		{
			if ((int) $c_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_comments[$c_id]))
			{
				try
				{
					self::$_comments[$c_id] = new TBGComment($c_id, $row);
				}					
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_comments[$c_id];
		}

		/**
		 * Returns a TBGMilestone
		 *
		 * @param integer $m_id
		 * 
		 * @return TBGMilestone
		 */
		public static function TBGMilestoneLab($m_id, $row = null)
		{
			if ((int) $m_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_milestones[$m_id]))
			{
				try
				{
					self::$_milestones[$m_id] = new TBGMilestone($m_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_milestones[$m_id];
		}
		
		/**
		 * Returns a TBGScope
		 *
		 * @param integer|string|B2DBrow $s_id
		 * @return TBGScope
		 */
		public static function scopeLab($s_id, $row = null)
		{
			if ((int) $s_id == 0) throw new Exception('Invalid id');
			if (!isset(self::$_scopes[$s_id]))
			{
				try
				{
					self::$_scopes[$s_id] = new TBGScope($s_id, $row);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			return self::$_scopes[$s_id];
		}
		
	}
