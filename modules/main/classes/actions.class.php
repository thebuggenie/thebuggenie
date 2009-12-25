<?php 

	/**
	 * actions for the main module
	 */
	class mainActions extends BUGSaction
	{

		/**
		 * The currently selected project in actions where there is one
		 *
		 * @access protected
		 * @property BUGSproject $selected_project
		 */

		/**
		 * View an issue
		 * 
		 * @param BUGSrequest $request
		 */
		public function runViewIssue(BUGSrequest $request)
		{
			BUGSlogging::log('Loading issue');
			$selected_project = null;
			
			if ($project_key = $request->getParameter('project_key'))
			{
				try
				{
					$selected_project = BUGSproject::getByKey($project_key);
					BUGScontext::setCurrentProject($selected_project);
					$this->selected_project = $selected_project;
				}
				catch (Exception $e) {}
			}
			if ($issue_no = BUGScontext::getRequest()->getParameter('issue_no'))
			{
				$issue = BUGSissue::getIssueFromLink($issue_no);
				if (!$selected_project instanceof BUGSproject || $issue->getProjectID() != $selected_project->getID())
				{
					$issue = null;
				}
			}
			BUGSlogging::log('done (Loading issue)');
			$this->getResponse()->setPage('viewissue');
			$message = BUGScontext::getMessageAndClear('issue_saved');
			
			if ($request->isMethod(BUGSrequest::POST) && $issue instanceof BUGSissue && $request->hasParameter('issue_action'))
			{
				switch ($request->getParameter('issue_action'))
				{
					case 'save':
						if ($issue->hasUnsavedChanges())
						{
							if (!$issue->hasMergeErrors())
							{
								try
								{
									$issue->save();
									BUGScontext::setMessage('issue_saved', true);
									$this->forward(BUGScontext::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
								}
								catch (Exception $e)
								{
									$this->error = $e->getMessage();
								}
							}
							else
							{
								$this->issue_unsaved = true;
							}
						}
						else
						{
							$this->forward(BUGScontext::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
						}
						break;
				}
			}
			elseif ($message == true)
			{
				$this->issue_saved = true;
			}
			$this->theIssue = $issue;
		}
		
		/**
		 * Frontpage
		 *  
		 * @param BUGSrequest $request
		 */
		public function runIndex(BUGSrequest $request)
		{
			if (BUGSsettings::isSingleProjectTracker())
			{
				if (($projects = BUGSproject::getAll()) && $project = array_shift($projects))
				{
					$this->forward(BUGScontext::getRouting()->generate('project_dashboard', array('project_key' => $project->getKey())));
				}
			}
			$this->getResponse()->setPage('index');
			$this->getResponse()->setProjectMenuStripHidden();
			$this->showleftbar = false;
			if (!BUGSuser::isThisGuest() && (BUGScontext::getUser()->showFollowUps() || BUGScontext::getUser()->showAssigned()))
			{
				$this->showleftbar = true;
			}
			if (BUGSsettings::showLoginBox() && BUGSuser::isThisGuest())
			{
				$this->showleftbar = true;
			}
			if (BUGScontext::isHookedInto('core', 'index_left_top') || BUGScontext::isHookedInto('core', 'index_left_middle') || BUGScontext::isHookedInto('core', 'index_left_bottom')) 
			{
				$this->showleftbar = true;
			}
			if ($this->showleftbar)
			{
				$this->links = BUGScontext::getMainLinks();
			}
		}

		/**
		 * Developer dashboard
		 *  
		 * @param BUGSrequest $request
		 */
		public function runDashboard(BUGSrequest $request)
		{
			if (BUGScontext::getUser()->isThisGuest())
			{
				$this->forward403();
			}
			$this->getResponse()->setProjectMenuStripHidden();
		}
		
		/**
		 * About page
		 *  
		 * @param BUGSrequest $request
		 */
		public function runAbout(BUGSrequest $request)
		{
			$this->getResponse()->setProjectMenuStripHidden();
		}
		
		/**
		 * 404 not found page
		 * 
		 * @param BUGSrequest $request
		 */
		public function runNotFound(BUGSrequest $request)
		{
			$this->getResponse()->setHttpStatus(404);
			$message = null;
		}
		
		/**
		 * Logs the user out
		 * 
		 * @param BUGSrequest $request
		 */
		public function runLogout(BUGSrequest $request)
		{
			if (BUGScontext::getUser() instanceof BUGSuser)
			{
				BUGSlogging::log('Setting user logout state');
				BUGScontext::getUser()->setState(BUGSsettings::get('offlinestate'));
			}
			BUGScontext::logout();
			$this->forward(BUGScontext::getRouting()->generate(BUGSsettings::getLogoutReturnRoute()));
		}
		
		/**
		 * Login page
		 *  
		 * @param BUGSrequest $request
		 */
		public function runLogin(BUGSrequest $request)
		{
			$this->getResponse()->setPage('login');
			$this->getResponse()->setProjectMenuStripHidden();
			try
			{
				if (BUGScontext::getRequest()->getMethod() == BUGSrequest::POST)
				{
					if (BUGScontext::getRequest()->hasParameter('b2_username') && BUGScontext::getRequest()->hasParameter('b2_password'))
					{
						$username = BUGScontext::getRequest()->getParameter('b2_username');
						$password = BUGScontext::getRequest()->getParameter('b2_password');
						$user = BUGSuser::loginCheck($username, md5($password), true);
						$this->getResponse()->setCookie('b2_username', $username);
						$this->getResponse()->setCookie('b2_password', md5($password));
						if (BUGScontext::getRequest()->hasParameter('return_to')) 
						{
							$this->forward(BUGScontext::getRequest()->getParameter('return_to'));
						}
						else
						{
							$this->forward(BUGScontext::getRouting()->generate(BUGSsettings::get('returnfromlogin')));
						}
					}
					else
					{
						throw new Exception(__('Please enter a username and password'));
					}
				}
				elseif (!BUGScontext::getUser()->isAuthenticated() && BUGSsettings::get('requirelogin'))
				{
					$this->login_error = __('You need to log in to access this site');
				}
				elseif (!BUGScontext::getUser()->isAuthenticated())
				{
					$this->login_error = __('Please log in');
				}
				elseif (BUGScontext::hasMessage('forward'))
				{
					$this->login_error = BUGScontext::getMessageAndClear('forward');
				}
			}
			catch (Exception $e)
			{
				$this->login_error = $e->getMessage();
			}
			
			/*
			elseif (BUGScontext::getRequest()->getParameter('switch_user'))
			{
				if (BUGScontext::getRequest()->getParameter('new_user'))
				{
					$BUGS_user = BUGScontext::loginCheck($_SESSION['b2_username'], $_SESSION['b2_password']);
					if ($BUGS_user->getUID() != 0)
					{
						$crit = new B2DBCriteria();
						$crit->addWhere(B2tUsers::UNAME, BUGScontext::getRequest()->getParameter('new_user'));
						$crit->addSelectionColumn(B2tUsers::ID);
						$row = B2DB::getTable('B2tUsers')->doSelectOne($crit);
						if ($row instanceof B2DBRow)
						{
							$newUser = new BUGSuser($row->get(B2tUsers::ID));
							BUGScontext::setScope(1);
							BUGScontext::cacheAllPermissions();
							if ((BUGScontext::getUser()->hasPermission("b2saveconfig", 14, "core") && $newUser->getScope()->getID() != BUGScontext::getScope()->getID()) || BUGScontext::getUser()->hasPermission("b2saveconfig", 2, "core"))
							{
								$pre_uname = $BUGS_user->getUname();
								$pre_pwd = $BUGS_user->getMD5Password();
								$BUGS_user = BUGScontext::loginCheck($newUser->getUname(), $newUser->getMD5Password());
								if ($BUGS_user->getLoginError() == '')
								{
									setcookie("b2_username_preswitch", $pre_uname, $_SERVER["REQUEST_TIME"] + 432000);
									setcookie("b2_password_preswitch", $pre_pwd, $_SERVER["REQUEST_TIME"] + 432000);
									setcookie("b2_uname", $newUser->getUname(), $_SERVER["REQUEST_TIME"] + 432000);
									setcookie("b2_upwd", $newUser->getMD5Password(), $_SERVER["REQUEST_TIME"] + 432000);
									bugs_moveTo(BUGSsettings::get('returnfromlogin'));
								}
								else
								{
									$_SESSION['login_error'] = $BUGS_user->getLoginError();
									bugs_moveTo('login.php');
								}
								exit;
							}
						} 
					}
					else
					{
						$BUGS_user->setLoginError('Please enter a username and password');
					}
				}
				else
				{
					$BUGS_user = BUGScontext::loginCheck($_COOKIE['b2_username_preswitch'], $_COOKIE['b2_password_preswitch']);
					setcookie("b2_uname", $BUGS_user->getUname(), $_SERVER["REQUEST_TIME"] + 432000);
					setcookie("b2_upwd", $BUGS_user->getMD5Password(), $_SERVER["REQUEST_TIME"] + 432000);
					setcookie("b2_username_preswitch", '', $_SERVER["REQUEST_TIME"] - 432000);
					setcookie("b2_password_preswitch", '', $_SERVER["REQUEST_TIME"] - 432000);
					bugs_moveTo(BUGSsettings::get('returnfromlogin'));
					exit;
				}
			}
			else
			{
				$BUGS_user = new BUGSuser();
				$BUGS_user->setLoginError('Please enter a username and password');
			}
			
				
			if ($BUGS_user->hasLoginError()) 
			{ 
			   $_SESSION['login_error'] = $BUGS_user->getLoginError(); 
			} 
			else 
			{ 
			   $_SESSION['login_error'] = ''; 
			}
			bugs_moveTo("login.php");
		*/
		}
		
		/**
		 * Registration logic part 1 - check if username is free
		 *  
		 * @param BUGSrequest $request
		 */
		public function runRegister1(BUGSrequest $request)
		{
			$this->getResponse()->setPage('login');
			try
			{
				if (BUGScontext::getRequest()->getMethod() == BUGSrequest::POST)
				{
					$username = BUGScontext::getRequest()->getParameter('desired_username');
					if (!empty($username))
					{
						$exists = B2DB::getTable('B2tUsers')->getByUsername($username);
						
						if ($exists)
						{
							throw new Exception(__('This username is in use'));
						}
						else
						{
							bugscontext::setMessage('prereg_success', $username);
							$this->forward('login');

						}
					}
					else
					{
						throw new Exception(__('Please enter a username'));
					}
				}
			}
			catch (Exception $e)
			{
				bugscontext::setMessage('prereg_error', $e->getMessage());
				$this->forward('login');
			}
		}

		/**
		 * Registration logic part 2 - add user data
		 *  
		 * @param BUGSrequest $request
		 */
		public function runRegister2(BUGSrequest $request)
		{
			$this->getResponse()->setPage('login');
			try
			{
				if (BUGScontext::getRequest()->getMethod() == BUGSrequest::POST)
				{
					$username = BUGScontext::getRequest()->getParameter('username');
					$buddyname = BUGScontext::getRequest()->getParameter('buddyname');
					$email = BUGScontext::getRequest()->getParameter('email_address');
					$confirmemail = BUGScontext::getRequest()->getParameter('email_confirm');
					$security = BUGScontext::getRequest()->getParameter('verification_no');
					$realname = BUGScontext::getRequest()->getParameter('realname');
					
					if (!empty($buddyname) && !empty($email) && !empty($confirmemail) && !empty($security))
					{
						if ($email != $confirmemail)
						{
							throw new Exception(__('The email address must be valid, and must be typed twice.'));
						}

						if ($security != $_SESSION['activation_number'])
						{
							throw new Exception(__('To prevent automatic sign-ups, enter the verification number shown below.'));
						}

						$email_ok = false;
						$valid_domain = false;

						if ((!(stristr($email, "@") === false)) && (strripos($email, ".") > strripos($email, "@")))
						{
							$email_ok = true;
						}
						
						if ($email_ok && BUGSsettings::get('limit_registration') != '')
						{

							$allowed_domains = explode(',', BUGSsettings::get('limit_registration'));
							if (count($allowed_domains) > 0)
							{
								foreach ($allowed_domains as $allowed_domain)
								{
									$allowed_domain = '@' . trim($allowed_domain);
									if (strpos($email, $allowed_domain) !== false ) //strpos checks if $to
									{
										$valid_domain = true;
										break;
									}
								}
							}
							else
							{
								$valid_domain = true;
							}
						}
						else
						{
							$valid_domain = true;
						}
						
						if ($valid_domain == false)
						{
							throw new Exception(__('Email adresses from this domain can not be used.'));
						}
						
						if($email_ok == false)
						{
							throw new Exception(__('The email address must be valid, and must be typed twice.'));
						}
						
						if ($security != $_SESSION['activation_number'])
						{
							throw new Exception(__('To prevent automatic sign-ups, enter the verification number shown below.'));
						}

						/* FIXME send email */

						$user = BUGSuser::createNew($username, $realname, $buddyname, BUGScontext::getScope()->getID(), false, true, md5(bugs_createpassword()), $email, true);

						bugscontext::setMessage('postreg_success', true);
						$this->forward('login');
					}
					else
					{
						throw new Exception(__('You need to fill out all fields correctly.'));
					}
				}
			}
			catch (Exception $e)
			{
				bugscontext::setMessage('prereg_success', $username);
				bugscontext::setMessage('postreg_error', $e->getMessage());
				$this->forward('login');
			}
		}

		/**
		 * Activate newly registered account
		 *  
		 * @param BUGSrequest $request
		 */
		public function runActivate(BUGSrequest $request)
		{
			$this->getResponse()->setPage('../../login');
			
			$row = B2DB::getTable('B2tUsers')->getByUsername($request->getParameter('user'));
			if ($row)
			{
				if ($row->get(B2tUsers::PASSWD) != $request->getParameter('key'))
				{
					bugscontext::setMessage('account_activate', true);
					bugscontext::setMessage('activate_failure', true);
				}
				else
				{
					$user = new BUGSUser($row->get(B2tUsers::ID), $row);
					$user->setValidated(1);
					bugscontext::setMessage('account_activate', true);
					bugscontext::setMessage('activate_success', true);
				}
			}
			else
			{
				bugscontext::setMessage('account_activate', true);
				bugscontext::setMessage('activate_failure', true);
			}
			$this->forward('../../login');
		}

		/**
		 * "My account" page
		 *  
		 * @param BUGSrequest $request
		 */
		public function runMyAccount(BUGSrequest $request)
		{
			$this->forward403unless(BUGScontext::getUser()->hasPageAccess('account'));
			if ($request->isMethod(BUGSrequest::POST) && $request->hasParameter('mode'))
			{
				switch ($request->getParameter('mode'))
				{
					case 'information':
						BUGScontext::getUser()->setBuddyname($request->getParameter('buddyname'));
						BUGScontext::getUser()->setRealname($request->getParameter('realname'));
						BUGScontext::getUser()->setHomepage($request->getParameter('homepage'));
						BUGScontext::getUser()->setEmailPrivate((bool) $request->getParameter('email_private'));

						if (BUGScontext::getUser()->getEmail() != $request->getParameter('email'))
						{
							if (BUGScontext::trigger('core', 'changeEmail', array('email' => $request->getParameter('email')), true) !== true)
							{
								BUGScontext::getUser()->setEmail($request->getParameter('email'));
							}
						}

						BUGScontext::getUser()->save();

						return $this->renderJSON(array('failed' => false, 'title' => BUGScontext::getI18n()->__('Account information saved'), 'content' => ''));
						break;
					case 'settings':
						BUGScontext::getUser()->setUsesGravatar((bool) $request->getParameter('use_gravatar'));
						BUGScontext::getUser()->setTimezone($request->getParameter('timezone'));

						BUGScontext::getUser()->save();

						return $this->renderJSON(array('failed' => false, 'title' => BUGScontext::getI18n()->__('Profile settings saved'), 'content' => ''));
						break;
				}
			}
			$this->getResponse()->setPage('account');
			$this->getResponse()->setProjectMenuStripHidden();
		}

		/**
		 * Change password ajax action
		 *
		 * @param BUGSrequest $request
		 */
		public function runAccountChangePassword(BUGSrequest $request)
		{
			$this->forward403unless(BUGScontext::getUser()->hasPageAccess('account'));
			if ($request->isMethod(BUGSrequest::POST))
			{
				if (!$request->hasParameter('current_password') || !$request->getParameter('current_password'))
				{
					return $this->renderJSON(array('failed' => true, 'error' => BUGScontext::getI18n()->__('Please enter your current password')));
				}
				if (!$request->hasParameter('new_password_1') || !$request->getParameter('new_password_1'))
				{
					return $this->renderJSON(array('failed' => true, 'error' => BUGScontext::getI18n()->__('Please enter a new password')));
				}
				if (!$request->hasParameter('new_password_2') || !$request->getParameter('new_password_2'))
				{
					return $this->renderJSON(array('failed' => true, 'error' => BUGScontext::getI18n()->__('Please enter the new password twice')));
				}
				if (!BUGScontext::getUser()->hasPassword($request->getParameter('current_password')))
				{
					return $this->renderJSON(array('failed' => true, 'error' => BUGScontext::getI18n()->__('Please enter your current password')));
				}
				if ($request->getParameter('new_password_1') != $request->getParameter('new_password_2'))
				{
					return $this->renderJSON(array('failed' => true, 'error' => BUGScontext::getI18n()->__('Please enter the new password twice')));
				}
				BUGScontext::getUser()->changePassword($request->getParameter('new_password_1'));
				BUGScontext::getUser()->save();
				$this->getResponse()->setCookie('b2_password', BUGScontext::getUser()->getPasswordMD5());
				return $this->renderJSON(array('failed' => false, 'title' => BUGScontext::getI18n()->__('Your new password was changed')));
			}
		}

		protected function _setupReportIssueProperties()
		{
			$this->selected_project = null;
			$this->selected_issuetype = null;
			$this->selected_edition = null;
			$this->selected_build = null;
			$this->selected_component = null;
			$this->selected_category = null;
			$this->selected_status = null;
			$this->selected_resolution = null;
			$this->selected_priority = null;
			$this->selected_reproducability = null;
			$this->selected_severity = null;
			$this->selected_estimated_time = null;
			$this->selected_elapsed_time = null;
			$this->selected_percent_complete = null;
			$selected_customdatatype = array();
			foreach (BUGScustomdatatype::getAll() as $customdatatype)
			{
				$selected_customdatatype[$customdatatype->getKey()] = null;
			}
			$this->selected_customdatatype = $selected_customdatatype;
			$this->issuetypes = array();
			$this->issuetype_id = null;
			$this->issue = null;
			$this->categories = BUGScategory::getAll();
			$this->severities = BUGSseverity::getAll();
			$this->priorities = BUGSpriority::getAll();
			$this->reproducabilities = BUGSreproducability::getAll();
			$this->resolutions = BUGSresolution::getAll();
			$this->statuses = BUGSstatus::getAll();
			$this->projects = BUGSproject::getAll();
		}

		protected function _clearReportIssueProperties()
		{
			$this->title = null;
			$this->description = null;
			$this->reproduction_steps = null;
			$this->selected_category = null;
			$this->selected_status = null;
			$this->selected_reproducability = null;
			$this->selected_resolution = null;
			$this->selected_severity = null;
			$this->selected_priority = null;
			$this->selected_edition = null;
			$this->selected_build = null;
			$this->selected_component = null;
			$this->selected_estimated_time = null;
			$this->selected_elapsed_time = null;
			$this->selected_percent_complete = null;
		}

		protected function _loadSelectedProjectAndIssueTypeFromRequestForReportIssueAction(BUGSrequest $request)
		{
			if ($project_key = $request->getParameter('project_key'))
			{
				try
				{
					$this->selected_project = BUGSproject::getByKey($project_key);
				}
				catch (Exception $e) {}
			}
			elseif ($project_id = $request->getParameter('project_id'))
			{
				try
				{
					$this->selected_project = BUGSfactory::projectLab($project_id);
				}
				catch (Exception $e) {}
			}
			if ($this->selected_project instanceof BUGSproject)
			{
				BUGScontext::setCurrentProject($this->selected_project);
			}
			if ($this->selected_project instanceof BUGSproject)
			{
				$this->issuetypes = BUGSissuetype::getAllApplicableToProject($this->selected_project->getID());
			}
			else
			{
				$this->issuetypes = BUGSissuetype::getAll();
			}

			$this->issuetype_id = $request->getParameter('issuetype_id');
			if ($this->issuetype_id)
			{
				try
				{
					$this->selected_issuetype = BUGSfactory::BUGSissuetypeLab($this->issuetype_id);
				}
				catch (Exception $e) {}
			}
		}

		protected function _postIssueValidation(BUGSrequest $request, &$errors)
		{
			$i18n = BUGScontext::getI18n();
			if (!$this->selected_project instanceof BUGSproject) $errors['project'] = $i18n->__('You have to select a valid project');
			if (!$this->selected_issuetype instanceof BUGSissuetype) $errors['issuetype'] = $i18n->__('You have to select a valid issue type');
			if (empty($errors))
			{
				$fields_array = $this->selected_project->getReportableFieldsArray($this->issuetype_id);

				$this->title = $request->getParameter('title');
				$this->description = $request->getParameter('description', null, false);
				$this->reproduction_steps = $request->getParameter('reproduction_steps', null, false);

				if ($edition_id = (int) $request->getParameter('edition_id'))
				{
					$this->selected_edition = BUGSfactory::editionLab($edition_id);
				}
				if ($build_id = (int) $request->getParameter('build_id'))
				{
					$this->selected_build = BUGSfactory::buildLab($build_id);
				}
				if ($component_id = (int) $request->getParameter('component_id'))
				{
					$this->selected_component = BUGSfactory::componentLab($component_id);
				}

				if (trim($this->title) == '') $errors['title'] = $i18n->__('You have to specify a title');
				if ($this->title == $this->default_title) $errors['title'] = $i18n->__('You have to specify a title');

				if (isset($fields_array['description']) && $fields_array['description']['required'] && trim($this->description) == '')
					$errors['description'] = $i18n->__('You have to enter something in the "%description%" field', array('%description%' => $i18n->__('Description')));
				if (isset($fields_array['reproduction_steps']) && $fields_array['reproduction_steps']['required'] && trim($this->reproduction_steps) == '')
					$errors['reproduction_steps'] = $i18n->__('You have to enter something in the "%reproduction_steps%" field', array('%reproduction_steps%' => $i18n->__('Reproduction steps')));

				if (isset($fields_array['edition']))
				{
					if ($fields_array['edition']['required'] && !$edition_id)
						$errors['edition'] = $i18n->__('You have to specify an edition');
					if ($edition_id && !in_array($edition_id, array_keys($fields_array['edition']['editions'])))
						$errors['edition'] = $i18n->__('The edition you specified is invalid');
				}

				if (isset($fields_array['build']))
				{
					if ($fields_array['build']['required'] && !$build_id)
						$errors['build'] = $i18n->__('You have to specify a release');
					if ($build_id && !in_array($build_id, array_keys($fields_array['build']['builds'])))
						$errors['build'] = $i18n->__('The release you specified is invalid');
				}

				if (isset($fields_array['component']))
				{
					if ($fields_array['component']['required'] && !$component_id)
						$errors['component'] = $i18n->__('You have to specify a component');
					if ($component_id && !in_array($component_id, array_keys($fields_array['component']['components'])))
						$errors['component'] = $i18n->__('The component you specified is invalid');
				}

				if ($category_id = (int) $request->getParameter('category_id'))
				{
					$this->selected_category = BUGSfactory::BUGScategoryLab($category_id);
					if ($this->selected_category === null)
						$errors['category'] = $i18n->__('You have specified an invalid category');
				}
				if (isset($fields_array['category']) && $fields_array['category']['required'] && $this->selected_category === null)
					$errors['category'] = $i18n->__('You have to specify a category');

				if ($status_id = (int) $request->getParameter('status_id'))
				{
					$this->selected_status = BUGSfactory::BUGSstatusLab($status_id);
					if ($this->selected_status === null)
						$errors['status'] = $i18n->__('You have specified an invalid status');
				}
				if (isset($fields_array['status']) && $fields_array['status']['required'] && $this->selected_status === null)
					$errors['status'] = $i18n->__('You have to specify a status');

				if ($reproducability_id = (int) $request->getParameter('reproducability_id'))
				{
					$this->selected_reproducability = BUGSfactory::BUGSreproducabilityLab($reproducability_id);
					if ($this->selected_reproducability === null)
						$errors['reproducability'] = $i18n->__('You have specified an invalid reproducability');
				}
				if (isset($fields_array['reproducability']) && $fields_array['reproducability']['required'] && $this->selected_reproducability === null)
					$errors['reproducability'] = $i18n->__('You have to specify a reproducability');

				if ($resolution_id = (int) $request->getParameter('resolution_id'))
				{
					$this->selected_resolution = BUGSfactory::BUGSresolutionLab($resolution_id);
					if ($this->selected_resolution === null)
						$errors['resolution'] = $i18n->__('You have specified an invalid resolution');
				}
				if (isset($fields_array['resolution']) && $fields_array['resolution']['required'] && $this->selected_resolution === null)
					$errors['resolution'] = $i18n->__('You have to specify a resolution');

				if ($severity_id = (int) $request->getParameter('severity_id'))
				{
					$this->selected_severity = BUGSfactory::BUGSseverityLab($severity_id);
					if ($this->selected_severity === null)
						$errors['severity'] = $i18n->__('You have specified an invalid severity');
				}
				if (isset($fields_array['severity']) && $fields_array['severity']['required'] && $this->selected_severity === null)
					$errors['severity'] = $i18n->__('You have to specify a severity');

				if ($priority_id = (int) $request->getParameter('priority_id'))
				{
					$this->selected_priority = BUGSfactory::BUGSpriorityLab($priority_id);
					if ($this->selected_priority === null)
						$errors['priority'] = $i18n->__('You have specified an invalid priority');
				}
				if (isset($fields_array['priority']) && $fields_array['priority']['required'] && $this->selected_priority === null)
					$errors['priority'] = $i18n->__('You have to specify a priority');

				if ($request->getParameter('estimated_time'))
				{
					$this->selected_estimated_time = $request->getParameter('estimated_time');
				}
				if (isset($fields_array['estimated_time']) && $fields_array['estimated_time']['required'] && $this->selected_estimated_time === null)
					$errors['estimated_time'] = $i18n->__('You have to provide an estimate');

				if ($request->getParameter('elapsed_time'))
				{
					$this->selected_elapsed_time = $request->getParameter('elapsed_time');
				}
				if (isset($fields_array['elapsed_time']) && $fields_array['elapsed_time']['required'] && $this->selected_elapsed_time === null)
					$errors['elapsed_time'] = $i18n->__('You have to provide an estimate');

				if (is_numeric($request->getParameter('percent_complete')))
				{
					$this->selected_percent_complete = (int) $request->getParameter('percent_complete');
				}
				if (isset($fields_array['percent_complete']) && $fields_array['percent_complete']['required'] && $this->selected_percent_complete === null)
					$errors['percent_complete'] = $i18n->__('You have to set percent completed');

				$selected_customdatatype = array();
				foreach (BUGScustomdatatype::getAll() as $customdatatype)
				{
					$selected_customdatatype[$customdatatype->getKey()] = null;
					$customdatatype_id = $customdatatype->getKey() . '_id';
					if ($$customdatatype_id = $request->getParameter($customdatatype_id))
					{
						$selected_customdatatype[$customdatatype->getKey()] = BUGScustomdatatypeoption::getByValueAndKey($$customdatatype_id, $customdatatype->getKey());
						if ($selected_customdatatype[$customdatatype->getKey()] === null)
							$errors[$customdatatype->getKey()] = $i18n->__('Invalid field: %custom_datatype_field_description%', array($customdatatype->getDescription()));
					}
					if (isset($fields_array[$customdatatype->getKey()]) && $fields_array[$customdatatype->getKey()]['required'] && $selected_customdatatype[$customdatatype->getKey()] === null)
						$errors[$customdatatype->getKey()] = $i18n->__('Required field: %custom_datatype_field_description%', array($customdatatype->getDescription()));
				}
				$this->selected_customdatatype = $selected_customdatatype;
			}
			return !(bool) count($errors);
		}

		protected function _postIssue()
		{
			$fields_array = $this->selected_project->getReportableFieldsArray($this->issuetype_id);
			$issue = BUGSissue::createNew($this->title, $this->issuetype_id, $this->selected_project->getID());
			if (isset($fields_array['description'])) $issue->setDescription($this->description);
			if (isset($fields_array['reproduction_steps'])) $issue->setReproductionSteps($this->reproduction_steps);
			if (isset($fields_array['category']) && $this->selected_category instanceof BUGSdatatype) $issue->setCategory($this->selected_category->getID());
			if (isset($fields_array['status']) && $this->selected_status instanceof BUGSdatatype) $issue->setStatus($this->selected_status->getID());
			if (isset($fields_array['reproducability']) && $this->selected_reproducability instanceof BUGSdatatype) $issue->setReproducability($this->selected_reproducability->getID());
			if (isset($fields_array['resolution']) && $this->selected_resolution instanceof BUGSdatatype) $issue->setResolution($this->selected_resolution->getID());
			if (isset($fields_array['severity']) && $this->selected_severity instanceof BUGSdatatype) $issue->setSeverity($this->selected_severity->getID());
			if (isset($fields_array['priority']) && $this->selected_priority instanceof BUGSdatatype) $issue->setPriority($this->selected_priority->getID());
			if (isset($fields_array['estimated_time'])) $issue->setEstimatedTime($this->selected_estimated_time);
			if (isset($fields_array['elapsed_time'])) $issue->setSpentTime($this->selected_elapsed_time);
			if (isset($fields_array['percent_complete'])) $issue->setPercentCompleted($this->selected_percent_complete);
			foreach (BUGScustomdatatype::getAll() as $customdatatype)
			{
				if (isset($fields_array[$customdatatype->getKey()]) && $this->selected_customdatatype[$customdatatype->getKey()] instanceof BUGScustomdatatypeoption)
				{
					$selected_option = $this->selected_customdatatype[$customdatatype->getKey()];
					$issue->setCustomField($customdatatype->getKey(), $selected_option->getValue());
				}
			}
			$issue->save();
			if (isset($fields_array['edition']) && $this->selected_edition instanceof BUGSedition) $issue->addAffectedEdition($this->selected_edition);
			if (isset($fields_array['build']) && $this->selected_build instanceof BUGSbuild) $issue->addAffectedBuild($this->selected_build);
			if (isset($fields_array['component']) && $this->selected_component instanceof BUGScomponent) $issue->addAffectedComponent($this->selected_component);

			return $issue;
		}
		
		/**
		 * "Report issue" page
		 *  
		 * @param BUGSrequest $request
		 */
		public function runReportIssue(BUGSrequest $request)
		{
			$i18n = BUGScontext::getI18n();
			$this->_setupReportIssueProperties();
			$errors = array();
			$this->getResponse()->setPage('reportissue');
			$this->default_title = $i18n->__('Enter a short, but descriptive summary of the issue here');
			$this->default_estimated_time = $i18n->__('Enter an estimate here');
			$this->default_elapsed_time = $i18n->__('Enter time spent here');

			$this->_loadSelectedProjectAndIssueTypeFromRequestForReportIssueAction($request);

			if ($request->isMethod(BUGSrequest::POST))
			{
				if ($this->_postIssueValidation($request, $errors))
				{
					try
					{
						$issue = $this->_postIssue();
						if ($request->getParameter('return_format') == 'scrum')
						{
							return $this->renderJSON(array('failed' => false, 'story_id' => $issue->getID(), 'content' => $this->getComponentHTML('project/scrumcard', array('issue' => $issue))));
						}
						if ($this->selected_issuetype->getRedirectAfterReporting())
						{
							$this->forward(BUGScontext::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), 303);
						}
						else
						{
							$this->_clearReportIssueProperties();
							$this->issue = $issue;
						}
					}
					catch (Exception $e)
					{
						if ($request->getParameter('return_format') == 'scrum')
						{
							return $this->renderJSON(array('failed' => true, 'error' => $e->getMessage()));
						}
						$errors[] = $e->getMessage();
					}
				}
			}
			if ($request->getParameter('return_format') == 'scrum')
			{
				return $this->renderJSON(array('failed' => true, 'error' => join(', ', $errors)));
			}
			$this->errors = $errors;
		}
		
		/**
		 * Retrieves the fields which are valid for that product and issue type combination
		 *  
		 * @param BUGSrequest $request
		 */
		public function runReportIssueGetFields(BUGSrequest $request)
		{
			if ($project_id = $request->getParameter('project_id'))
			{
				try
				{
					$selected_project = BUGSfactory::projectLab($project_id);
				}
				catch (Exception $e)
				{
					return $this->renderText('fail');
				}
			}
			else
			{
				return $this->renderText('no project');
			}
			
			$fields_array = $selected_project->getReportableFieldsArray($request->getParameter('issuetype_id'));
			return $this->renderJSON(array('available_fields' => BUGSdatatypebase::getAvailableFields(), 'fields' => $fields_array));
		}

		/**
		 * Retrieves the fields which are valid for that product and issue type combination
		 *  
		 * @param BUGSrequest $request
		 */
		public function runToggleFavouriteIssue(BUGSrequest $request)
		{
			if ($issue_id = $request->getParameter('issue_id'))
			{
				try
				{
					$issue = BUGSfactory::BUGSissueLab($issue_id);
				}
				catch (Exception $e)
				{
					return $this->renderText('fail');
				}
			}
			else
			{
				return $this->renderText('no issue');
			}
			
			if (BUGScontext::getUser()->isIssueStarred($issue_id))
			{
				$retval = !BUGScontext::getUser()->removeStarredIssue($issue_id);
			}
			else
			{
				$retval = BUGScontext::getUser()->addStarredIssue($issue_id);
			}
			$this->getResponse()->setContentType('application/json');
			return $this->renderText(json_encode(array('starred' => $retval)));
		}
		
		/**
		 * Sets an issue field to a specified value
		 * 
		 * @param BUGSrequest $request
		 */
		public function runIssueSetField(BUGSrequest $request)
		{
			if ($issue_id = $request->getParameter('issue_id'))
			{
				try
				{
					$issue = BUGSfactory::BUGSissueLab($issue_id);
				}
				catch (Exception $e)
				{
					$this->getResponse()->setHttpStatus(400);
					return $this->renderText('fail');
				}
			}
			else
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderText('no issue');
			}
			
			switch ($request->getParameter('field'))
			{
				case 'percent':
					$issue->setPercentCompleted($request->getParameter('percent'));
					if ($issue->isPercentCompletedChanged())
					{
						return $this->renderJSON(array('changed' => true, 'percent' => $issue->getPercentCompleted()));
					}
					else
					{
						return $this->renderJSON(array('changed' => false, 'percent' => $issue->getPercentCompleted()));
					}
					break;
				case 'estimated_time':
					if ($request->getParameter('estimated_time') != BUGScontext::getI18n()->__('Enter your estimate here') && $request->getParameter('estimated_time'))
					{
						$issue->setEstimatedTime($request->getParameter('estimated_time'));
					}
					elseif ($request->hasParameter('value'))
					{
						$issue->setEstimatedTime($request->getParameter('value'));
					}
					else
					{
						$issue->setEstimatedMonths($request->getParameter('estimated_time_months'));
						$issue->setEstimatedWeeks($request->getParameter('estimated_time_weeks'));
						$issue->setEstimatedDays($request->getParameter('estimated_time_days'));
						$issue->setEstimatedHours($request->getParameter('estimated_time_hours'));
						$issue->setEstimatedPoints($request->getParameter('estimated_time_points'));
					}
					return $this->renderJSON(array('changed' => $issue->isEstimatedTimeChanged(), 'field' => (($issue->hasEstimatedTime()) ? array('id' => 1, 'name' => $issue->getFormattedTime($issue->getEstimatedTime())) : array('id' => 0)), 'values' => $issue->getEstimatedTime()));
					break;
				case 'owned_by':
				case 'posted_by':
				case 'assigned_to':
					if ($request->hasParameter('value'))
					{
						if ($request->hasParameter('identifiable_type'))
						{
							if (in_array($request->getParameter('identifiable_type'), array(BUGSidentifiableclass::TYPE_USER, BUGSidentifiableclass::TYPE_TEAM)))
							{
								switch ($request->getParameter('identifiable_type'))
								{
									case BUGSidentifiableclass::TYPE_USER:
										$identified = BUGSfactory::userLab($request->getParameter('value'));
										break;
									case BUGSidentifiableclass::TYPE_TEAM:
										$identified = BUGSfactory::teamLab($request->getParameter('value'));
										break;
								}
								if ($identified instanceof BUGSidentifiableclass)
								{
									if ($request->getParameter('field') == 'owned_by') $issue->setOwner($identified);
									elseif ($request->getParameter('field') == 'assigned_to') $issue->setAssignee($identified);
								}
							}
							else
							{
								if ($request->getParameter('field') == 'owned_by') $issue->unsetOwner();
								elseif ($request->getParameter('field') == 'assigned_to') $issue->unsetAssignee();
							}
						}
						elseif ($request->getParameter('field') == 'posted_by')
						{
							$identified = BUGSfactory::userLab($request->getParameter('value'));
							if ($identified instanceof BUGSidentifiableclass)
							{
								$issue->setPostedBy($identified);
							}
						}
						if ($request->getParameter('field') == 'owned_by')
							return $this->renderJSON(array('changed' => $issue->isOwnedByChanged(), 'field' => (($issue->isOwned()) ? array('id' => $issue->getOwnerID(), 'name' => (($issue->getOwnerType() == BUGSidentifiableclass::TYPE_USER) ? $this->getComponentHTML('main/userdropdown', array('user' => $issue->getOwner())) : $this->getComponentHTML('main/teamdropdown', array('team' => $issue->getOwner())))) : array('id' => 0))));
						if ($request->getParameter('field') == 'posted_by')
							return $this->renderJSON(array('changed' => $issue->isPostedByChanged(), 'field' => array('id' => $issue->getPostedByID(), 'name' => $this->getComponentHTML('main/userdropdown', array('user' => $issue->getPostedBy())))));
						if ($request->getParameter('field') == 'assigned_to')
							return $this->renderJSON(array('changed' => $issue->isAssignedToChanged(), 'field' => (($issue->isAssigned()) ? array('id' => $issue->getAssigneeID(), 'name' => (($issue->getAssigneeType() == BUGSidentifiableclass::TYPE_USER) ? $this->getComponentHTML('main/userdropdown', array('user' => $issue->getAssignee())) : $this->getComponentHTML('main/teamdropdown', array('team' => $issue->getAssignee())))) : array('id' => 0))));
					}
					break;
				case 'spent_time':
					if ($request->getParameter('spent_time') != BUGScontext::getI18n()->__('Enter time spent here') && $request->getParameter('spent_time'))
					{
						if ($request->hasParameter('spent_time_added_text'))
						{
							$issue->addSpentTime($request->getParameter('spent_time'));
						}
						else
						{
							$issue->setSpentTime($request->getParameter('spent_time'));
						}
					}
					elseif ($request->hasParameter('value'))
					{
						$issue->setSpentTime($request->getParameter('value'));
					}
					else
					{
						if ($request->hasParameter('spent_time_added_input'))
						{
							$issue->addSpentMonths($request->getParameter('spent_time_months'));
							$issue->addSpentWeeks($request->getParameter('spent_time_weeks'));
							$issue->addSpentDays($request->getParameter('spent_time_days'));
							$issue->addSpentHours($request->getParameter('spent_time_hours'));
							$issue->addSpentPoints($request->getParameter('spent_time_points'));
						}
						else
						{
							$issue->setSpentMonths($request->getParameter('spent_time_months'));
							$issue->setSpentWeeks($request->getParameter('spent_time_weeks'));
							$issue->setSpentDays($request->getParameter('spent_time_days'));
							$issue->setSpentHours($request->getParameter('spent_time_hours'));
							$issue->setSpentPoints($request->getParameter('spent_time_points'));
						}
					}
					return $this->renderJSON(array('changed' => $issue->isSpentTimeChanged(), 'field' => (($issue->hasSpentTime()) ? array('id' => 1, 'name' => $issue->getFormattedTime($issue->getSpentTime())) : array('id' => 0)), 'values' => $issue->getSpentTime()));
					break;
				case 'category':
				case 'resolution':
				case 'severity':
				case 'reproducability':
				case 'priority':
				case 'milestone':
				case 'issuetype':
				case 'status':
					try
					{
						if ($request->hasParameter('category_id') && $request->getParameter('field') == 'category')
						{
							$category_id = $request->getParameter('category_id');
							if ($category_id == 0 || ($category_id !== 0 && ($category = BUGSfactory::BUGScategoryLab($category_id)) instanceof BUGScategory))
							{
								$issue->setCategory($category_id);
								if (!$issue->isCategoryChanged()) return $this->renderJSON(array('changed' => false));
								return ($category_id == 0) ? $this->renderJSON(array('changed' => true, 'field' => array('id' => 0))) : $this->renderJSON(array('changed' => true, 'field' => array('id' => $category_id, 'name' => $category->getName()))); 
							}
						}
						elseif ($request->hasParameter('resolution_id') && $request->getParameter('field') == 'resolution')
						{
							$resolution_id = $request->getParameter('resolution_id');
							if ($resolution_id == 0 || ($resolution_id !== 0 && ($resolution = BUGSfactory::BUGSresolutionLab($resolution_id)) instanceof BUGSresolution))
							{
								$issue->setResolution($resolution_id);
								if (!$issue->isResolutionChanged()) return $this->renderJSON(array('changed' => false));
								return ($resolution_id == 0) ? $this->renderJSON(array('changed' => true, 'field' => array('id' => 0))) : $this->renderJSON(array('changed' => true, 'field' => array('id' => $resolution_id, 'name' => $resolution->getName()))); 
							}
						}
						elseif ($request->hasParameter('issuetype_id') && $request->getParameter('field') == 'issuetype')
						{
							$issuetype_id = $request->getParameter('issuetype_id');
							if ($issuetype_id == 0 || ($issuetype_id !== 0 && ($issuetype = BUGSfactory::BUGSissuetypeLab($issuetype_id)) instanceof BUGSissuetype))
							{
								$issue->setIssuetype($issuetype_id);
								if (!$issue->isIssuetypeChanged()) return $this->renderJSON(array('changed' => false));
								$visible_fields = ($issuetype_id != 0) ? $issue->getProject()->getVisibleFieldsArray($issuetype_id) : array();
								return ($issuetype_id == 0) ? $this->renderJSON(array('changed' => true, 'field' => array('id' => 0), 'visible_fields' => $visible_fields)) : $this->renderJSON(array('changed' => true, 'field' => array('id' => $issuetype_id, 'name' => $issuetype->getName(), 'src' => htmlspecialchars(BUGSsettings::getURLsubdir() . 'themes/' . BUGSsettings::getThemeName() . '/' . $issuetype->getIcon() . '_small.png')), 'visible_fields' => $visible_fields)); 
							}
						}
						elseif ($request->hasParameter('severity_id') && $request->getParameter('field') == 'severity')
						{
							$severity_id = $request->getParameter('severity_id');
							if ($severity_id == 0 || ($severity_id !== 0 && ($severity = BUGSfactory::BUGSseverityLab($severity_id)) instanceof BUGSseverity))
							{
								$issue->setSeverity($severity_id);
								if (!$issue->isSeverityChanged()) return $this->renderJSON(array('changed' => false));
								return ($severity_id == 0) ? $this->renderJSON(array('changed' => true, 'field' => array('id' => 0))) : $this->renderJSON(array('changed' => true, 'field' => array('id' => $severity_id, 'name' => $severity->getName()))); 
							}
						}
						elseif ($request->hasParameter('reproducability_id') && $request->getParameter('field') == 'reproducability')
						{
							$reproducability_id = $request->getParameter('reproducability_id');
							if ($reproducability_id == 0 || ($reproducability_id !== 0 && ($reproducability = BUGSfactory::BUGSreproducabilityLab($reproducability_id)) instanceof BUGSreproducability))
							{
								$issue->setReproducability($reproducability_id);
								if (!$issue->isReproducabilityChanged()) return $this->renderJSON(array('changed' => false));
								return ($reproducability_id == 0) ? $this->renderJSON(array('changed' => true, 'field' => array('id' => 0))) : $this->renderJSON(array('changed' => true, 'field' => array('id' => $reproducability_id, 'name' => $reproducability->getName()))); 
							}
						}
						elseif ($request->hasParameter('priority_id') && $request->getParameter('field') == 'priority')
						{
							$priority_id = $request->getParameter('priority_id');
							if ($priority_id == 0 || ($priority_id !== 0 && ($priority = BUGSfactory::BUGSpriorityLab($priority_id)) instanceof BUGSpriority))
							{
								$issue->setPriority($priority_id);
								if (!$issue->isPriorityChanged()) return $this->renderJSON(array('changed' => false));
								return ($priority_id == 0) ? $this->renderJSON(array('changed' => true, 'field' => array('id' => 0))) : $this->renderJSON(array('changed' => true, 'field' => array('id' => $priority_id, 'name' => $priority->getName()))); 
							}
						}
						elseif ($request->hasParameter('status_id') && $request->getParameter('field') == 'status')
						{
							$status_id = $request->getParameter('status_id');
							if ($status_id == 0 || ($status_id !== 0 && ($status = BUGSfactory::BUGSstatusLab($status_id)) instanceof BUGSstatus))
							{
								$issue->setStatus($status_id);
								if (!$issue->isStatusChanged()) return $this->renderJSON(array('changed' => false));
								return ($status_id == 0) ? $this->renderJSON(array('changed' => true, 'field' => array('id' => 0))) : $this->renderJSON(array('changed' => true, 'field' => array('id' => $status_id, 'name' => $status->getName(), 'color' => $status->getItemdata()))); 
							}
						}
						elseif ($request->hasParameter('milestone_id') && $request->getParameter('field') == 'milestone')
						{
							$milestone_id = $request->getParameter('milestone_id');
							if ($milestone_id == 0 || ($milestone_id !== 0 && ($milestone = BUGSfactory::milestoneLab($milestone_id)) instanceof BUGSmilestone))
							{
								$issue->setMilestone($milestone_id);
								if (!$issue->isMilestoneChanged()) return $this->renderJSON(array('changed' => false));
								return ($milestone_id == 0) ? $this->renderJSON(array('changed' => true, 'field' => array('id' => 0))) : $this->renderJSON(array('changed' => true, 'field' => array('id' => $milestone_id, 'name' => $milestone->getName()))); 
							}
						}
					}
					catch (Exception $e)
					{
						$this->getResponse()->setHttpStatus(400);
						return $this->renderJSON(array('error' => $e->getMessage()));
					}
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => BUGScontext::getI18n()->__('No valid field value specified')));
					break;
				default:
					if ($customdatatype = BUGScustomdatatype::getByKey($request->getParameter('field')))
					{
						$key = $customdatatype->getKey();
						if ($request->hasParameter("{$key}_value"))
						{
							$customdatatypeoption_value = $request->getParameter("{$key}_value");
							if ($customdatatypeoption_value && ($customdatatypeoption = BUGScustomdatatypeoption::getByValueAndKey($customdatatypeoption_value, $key)) instanceof BUGScustomdatatypeoption)
							{
								$issue->setCustomField($key, $customdatatypeoption->getValue());
								$changed_methodname = "isCustomfield{$key}Changed";
								if (!$issue->$changed_methodname()) return $this->renderJSON(array('changed' => false));
								return ($customdatatypeoption_value == '') ? $this->renderJSON(array('changed' => true, 'field' => array('id' => 0))) : $this->renderJSON(array('changed' => true, 'field' => array('value' => $customdatatypeoption_value, 'name' => $customdatatypeoption->getName())));
							}
						}
						$this->getResponse()->setHttpStatus(400);
						return $this->renderJSON(array('error' => BUGScontext::getI18n()->__('No valid field value specified')));
					}
					break;
			}
			
			$this->getResponse()->setHttpStatus(400);
			return $this->renderJSON(array('error' => BUGScontext::getI18n()->__('No valid field specified (%field%)', array('%field%' => $request->getParameter('field')))));
		}

		/**
		 * Reverts an issue field back to the original value
		 * 
		 * @param BUGSrequest $request
		 */
		public function runIssueRevertField(BUGSrequest $request)
		{
			if ($issue_id = $request->getParameter('issue_id'))
			{
				try
				{
					$issue = BUGSfactory::BUGSissueLab($issue_id);
				}
				catch (Exception $e)
				{
					$this->getResponse()->setHttpStatus(400);
					return $this->renderText('fail');
				}
			}
			else
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderText('no issue');
			}
			
			$field = null;
			
			switch ($request->getParameter('field'))
			{
				case 'category':
					$issue->revertCategory();
					$field = ($issue->getCategory() instanceof BUGScategory) ? array('id' => $issue->getCategory()->getID(), 'name' => $issue->getCategory()->getName()) : array('id' => 0);
					break;
				case 'resolution':
					$issue->revertResolution();
					$field = ($issue->getResolution() instanceof BUGSresolution) ? array('id' => $issue->getResolution()->getID(), 'name' => $issue->getResolution()->getName()) : array('id' => 0);
					break;
				case 'severity':
					$issue->revertSeverity();
					$field = ($issue->getSeverity() instanceof BUGSseverity) ? array('id' => $issue->getSeverity()->getID(), 'name' => $issue->getSeverity()->getName()) : array('id' => 0);
					break;
				case 'reproducability':
					$issue->revertReproducability();
					$field = ($issue->getReproducability() instanceof BUGSreproducability) ? array('id' => $issue->getReproducability()->getID(), 'name' => $issue->getReproducability()->getName()) : array('id' => 0);
					break;
				case 'priority':
					$issue->revertPriority();
					$field = ($issue->getPriority() instanceof BUGSpriority) ? array('id' => $issue->getPriority()->getID(), 'name' => $issue->getPriority()->getName()) : array('id' => 0);
					break;
				case 'percent':
					$issue->revertPercentCompleted();
					return $this->renderJSON(array('ok' => true, 'percent' => $issue->getPercentCompleted()));
					break;
				case 'status':
					$issue->revertStatus();
					$field = ($issue->getStatus() instanceof BUGSstatus) ? array('id' => $issue->getStatus()->getID(), 'name' => $issue->getStatus()->getName(), 'color' => $issue->getStatus()->getColor()) : array('id' => 0);
					break;
				case 'issuetype':
					$issue->revertIssuetype();
					$field = ($issue->getIssuetype() instanceof BUGSissuetype) ? array('id' => $issue->getIssuetype()->getID(), 'name' => $issue->getIssuetype()->getName(), 'src' => htmlspecialchars(BUGSsettings::getURLsubdir() . 'themes/' . BUGSsettings::getThemeName() . '/' . $issue->getIssuetype()->getIcon() . '_small.png')) : array('id' => 0);
					$visible_fields = ($issue->getIssuetype() instanceof BUGSissuetype) ? $issue->getProject()->getVisibleFieldsArray($issue->getIssuetype()->getID()) : array();
					return $this->renderJSON(array('ok' => true, 'field' => $field, 'visible_fields' => $visible_fields));
					break;
				case 'milestone':
					$issue->revertMilestone();
					$field = ($issue->getMilestone() instanceof BUGSmilestone) ? array('id' => $issue->getMilestone()->getID(), 'name' => $issue->getMilestone()->getName()) : array('id' => 0);
					break;
				case 'estimated_time':
					$issue->revertEstimatedTime();
					return $this->renderJSON(array('ok' => true, 'field' => (($issue->hasEstimatedTime()) ? array('id' => 1, 'name' => $issue->getFormattedTime($issue->getEstimatedTime())) : array('id' => 0)), 'values' => $issue->getEstimatedTime()));
					break;
				case 'spent_time':
					$issue->revertSpentTime();
					return $this->renderJSON(array('ok' => true, 'field' => (($issue->hasSpentTime()) ? array('id' => 1, 'name' => $issue->getFormattedTime($issue->getSpentTime())) : array('id' => 0)), 'values' => $issue->getSpentTime()));
					break;
				case 'owned_by':
					$issue->revertOwnedBy();
					return $this->renderJSON(array('changed' => $issue->isOwnedByChanged(), 'field' => (($issue->isOwned()) ? array('id' => $issue->getOwnerID(), 'name' => (($issue->getOwnerType() == BUGSidentifiableclass::TYPE_USER) ? $this->getComponentHTML('main/userdropdown', array('user' => $issue->getOwner())) : $this->getComponentHTML('main/teamdropdown', array('team' => $issue->getOwner())))) : array('id' => 0))));
					break;
				case 'assigned_to':
					$issue->revertAssignedTo();
					return $this->renderJSON(array('changed' => $issue->isAssignedToChanged(), 'field' => (($issue->isAssigned()) ? array('id' => $issue->getAssigneeID(), 'name' => (($issue->getAssigneeType() == BUGSidentifiableclass::TYPE_USER) ? $this->getComponentHTML('main/userdropdown', array('user' => $issue->getAssignee())) : $this->getComponentHTML('main/teamdropdown', array('team' => $issue->getAssignee())))) : array('id' => 0))));
					break;
				case 'posted_by':
					$issue->revertPostedBy();
					return $this->renderJSON(array('changed' => $issue->isPostedByChanged(), 'field' => array('id' => $issue->getPostedByID(), 'name' => $this->getComponentHTML('main/userdropdown', array('user' => $issue->getPostedBy())))));
					break;
				default:
					if ($customdatatype = BUGScustomdatatype::getByKey($request->getParameter('field')))
					{
						$key = $customdatatype->getKey();
						$revert_methodname = "revertCustomfield{$key}";
						$issue->$revert_methodname();
						$field = ($issue->getCustomField($key) instanceof BUGScustomdatatypeoption) ? array('value' => $issue->getCustomField($key)->getValue(), 'name' => $issue->getCustomField($key)->getName()) : array('id' => 0);
					}
					break;
			}
			
			if ($field !== null)
			{
				return $this->renderJSON(array('ok' => true, 'field' => $field));
			}
			else
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => BUGScontext::getI18n()->__('No valid field specified (%field%)', array('%field%' => $request->getParameter('field')))));
			}
		}
		
		/**
		 * Marks this issue as being worked on by the current user
		 * 
		 * @param BUGSrequest $request
		 */
		public function runIssueStartWorking(BUGSrequest $request)
		{
			if ($issue_id = $request->getParameter('issue_id'))
			{
				try
				{
					$issue = BUGSfactory::BUGSissueLab($issue_id);
				}
				catch (Exception $e)
				{
					return $this->return404(BUGScontext::getI18n()->__('This issue does not exist'));
				}
			}
			else
			{
				return $this->return404(BUGScontext::getI18n()->__('This issue does not exist'));
			}
			$issue->startWorkingOnIssue(BUGScontext::getUser());
			$issue->save();
			$this->forward(BUGScontext::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
		}
		
		/**
		 * Marks this issue as being completed work on by the current user
		 * 
		 * @param BUGSrequest $request
		 */
		public function runIssueStopWorking(BUGSrequest $request)
		{
			if ($issue_id = $request->getParameter('issue_id'))
			{
				try
				{
					$issue = BUGSfactory::BUGSissueLab($issue_id);
				}
				catch (Exception $e)
				{
					return $this->return404(BUGScontext::getI18n()->__('This issue does not exist'));
				}
			}
			else
			{
				return $this->return404(BUGScontext::getI18n()->__('This issue does not exist'));
			}
			
			if ($request->hasParameter('did') && $request->getParameter('did') == 'nothing')
			{
				$issue->clearUserWorkingOnIssue();
				$issue->save();
				$this->forward(BUGScontext::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
			}
			elseif ($request->hasParameter('perform_action') && $request->getParameter('perform_action') == 'grab')
			{
				$issue->clearUserWorkingOnIssue();
				$issue->startWorkingOnIssue(BUGScontext::getUser());
				$issue->save();
				$this->forward(BUGScontext::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
			}
			else
			{
				$issue->stopWorkingOnIssue();
				$issue->save();
				$this->forward(BUGScontext::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
			}
		}

		/**
		 * Reopen the issue
		 * 
		 * @param BUGSrequest $request
		 */
		public function runReopenIssue(BUGSrequest $request)
		{
			if ($issue_id = $request->getParameter('issue_id'))
			{
				try
				{
					$issue = BUGSfactory::BUGSissueLab($issue_id);
				}
				catch (Exception $e)
				{
					return $this->return404(BUGScontext::getI18n()->__('This issue does not exist'));
				}
			}
			else
			{
				return $this->return404(BUGScontext::getI18n()->__('This issue does not exist'));
			}
			$issue->open();
			$issue->save();
			$this->forward(BUGScontext::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
		}
		
		/**
		 * Close the issue
		 * 
		 * @param BUGSrequest $request
		 */
		public function runCloseIssue(BUGSrequest $request)
		{
			if ($issue_id = $request->getParameter('issue_id'))
			{
				try
				{
					$issue = BUGSfactory::BUGSissueLab($issue_id);
				}
				catch (Exception $e)
				{
					return $this->return404(BUGScontext::getI18n()->__('This issue does not exist'));
				}
			}
			else
			{
				return $this->return404(BUGScontext::getI18n()->__('This issue does not exist'));
			}
			if ($request->hasParameter('set_status'))
			{
				$issue->setStatus($request->getParameter('status_id'));
			}
			if ($request->hasParameter('set_resolution'))
			{
				$issue->setResolution($request->getParameter('resolution_id'));
			}
			if (trim($request->getParameter('close_comment')) != '')
			{
				$issue->addSystemComment(BUGScontext::getI18n()->__('Issue closed'), $request->getParameter('close_comment'), BUGScontext::getUser()->getID());
			}
			$issue->close();
			$issue->save();
			$this->forward(BUGScontext::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
		}
		
		/**
		 * Find users and show selection links
		 * 
		 * @param BUGSrequest $request The request object
		 */		
		public function runFindIdentifiable(BUGSrequest $request)
		{
			$this->forward403unless($request->isMethod(BUGSrequest::POST));
			$this->users = array();
			
			if ($find_identifiable_by = $request->getParameter('find_identifiable_by'))
			{
				$this->users = BUGSuser::findUsers($find_identifiable_by, 10);
				if ($request->getParameter('include_teams'))
				{
					$this->teams = BUGSteam::findTeams($find_identifiable_by);
				}
			}
			return $this->renderComponent('identifiableselectorresults', array('users' => $this->users, 'callback' => $request->getParameter('callback')));
		}
		
		/**
		 * Hides an infobox with a specific key
		 * 
		 * @param BUGSrequest $request The request object
		 */		
		public function runHideInfobox(BUGSrequest $request)
		{
			BUGSsettings::hideInfoBox($request->getParameter('key'));
			return $this->renderJSON(array('hidden' => true));
		}
		
	}

?>
