<?php 

	/**
	 * actions for the main module
	 */
	class mainActions extends TBGAction
	{

		/**
		 * The currently selected project in actions where there is one
		 *
		 * @access protected
		 * @property TBGProject $selected_project
		 */

		/**
		 * View an issue
		 * 
		 * @param TBGRequest $request
		 */
		public function runViewIssue(TBGRequest $request)
		{
			//TBGEvent::listen('core', 'viewissue', array($this, 'listenViewIssuePostError'));
			TBGLogging::log('Loading issue');
			$selected_project = null;
			
			if ($project_key = $request->getParameter('project_key'))
			{
				try
				{
					$selected_project = TBGProject::getByKey($project_key);
					TBGContext::setCurrentProject($selected_project);
					$this->selected_project = $selected_project;
				}
				catch (Exception $e) {}
			}
			if ($issue_no = TBGContext::getRequest()->getParameter('issue_no'))
			{
				$issue = TBGIssue::getIssueFromLink($issue_no);
				if (!$selected_project instanceof TBGProject || $issue->getProjectID() != $selected_project->getID())
				{
					$issue = null;
				}
			}
			TBGLogging::log('done (Loading issue)');
			$this->getResponse()->setPage('viewissue');
			if ($issue instanceof TBGIssue && !$issue->hasAccess())
			{
				$issue = null;
			}
			$message = TBGContext::getMessageAndClear('issue_saved');
			
			if ($request->isMethod(TBGRequest::POST) && $issue instanceof TBGIssue && $request->hasParameter('issue_action'))
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
									TBGContext::setMessage('issue_saved', true);
									$this->forward(TBGContext::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
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
							$this->forward(TBGContext::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
						}
						break;
				}
			}
			elseif ($message == true)
			{
				$this->issue_saved = true;
			}
			$this->theIssue = $issue;
			$event = TBGEvent::createNew('core', 'viewissue', $issue)->trigger();
			$this->listenViewIssuePostError($event);
		}
		
		/**
		 * Frontpage
		 *  
		 * @param TBGRequest $request
		 */
		public function runIndex(TBGRequest $request)
		{
			if (TBGSettings::isSingleProjectTracker())
			{
				if (($projects = TBGProject::getAll()) && $project = array_shift($projects))
				{
					$this->forward(TBGContext::getRouting()->generate('project_dashboard', array('project_key' => $project->getKey())));
				}
			}
			$this->forward403unless(TBGContext::getUser()->hasPageAccess('home'));
			$this->getResponse()->setProjectMenuStripHidden();
			$this->links = TBGContext::getMainLinks();
		}

		/**
		 * Developer dashboard
		 *  
		 * @param TBGRequest $request
		 */
		public function runDashboard(TBGRequest $request)
		{
			$this->forward403unless(!TBGContext::getUser()->isThisGuest() && TBGContext::getUser()->hasPageAccess('dashboard'));
			$this->getResponse()->setProjectMenuStripHidden();
		}
		
		/**
		 * About page
		 *  
		 * @param TBGRequest $request
		 */
		public function runAbout(TBGRequest $request)
		{
			B2DB::getTable('TBGIssueEstimates')->create();
			B2DB::getTable('TBGIssueSpentTimes')->create();
			$this->forward403unless(TBGContext::getUser()->hasPageAccess('about'));
			$this->getResponse()->setProjectMenuStripHidden();
		}
		
		/**
		 * 404 not found page
		 * 
		 * @param TBGRequest $request
		 */
		public function runNotFound(TBGRequest $request)
		{
			$this->getResponse()->setHttpStatus(404);
			$message = null;
		}
		
		/**
		 * Logs the user out
		 * 
		 * @param TBGRequest $request
		 */
		public function runLogout(TBGRequest $request)
		{
			if (TBGContext::getUser() instanceof TBGUser)
			{
				TBGLogging::log('Setting user logout state');
				TBGContext::getUser()->setState(TBGSettings::get('offlinestate'));
			}
			TBGContext::logout();
			$this->forward(TBGContext::getRouting()->generate(TBGSettings::getLogoutReturnRoute()));
		}
		
		/**
		 * Login page
		 *  
		 * @param TBGRequest $request
		 */
		public function runLogin(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();
			$this->getResponse()->setPage('login');
			$this->getResponse()->setProjectMenuStripHidden();
			try
			{
				if (TBGContext::getRequest()->getMethod() == TBGRequest::POST)
				{
					if (TBGContext::getRequest()->hasParameter('b2_username') && TBGContext::getRequest()->hasParameter('b2_password'))
					{
						$username = TBGContext::getRequest()->getParameter('b2_username');
						$password = TBGContext::getRequest()->getParameter('b2_password');
						$user = TBGUser::loginCheck($username, $password, true);
						$this->getResponse()->setCookie('b2_username', $username);
						$this->getResponse()->setCookie('b2_password', TBGUser::hashPassword($password));
						if (TBGContext::getRequest()->hasParameter('return_to')) 
						{
							$this->forward(TBGContext::getRequest()->getParameter('return_to'));
						}
						else
						{
							$this->forward(TBGContext::getRouting()->generate(TBGSettings::get('returnfromlogin')));
						}
					}
					else
					{
						throw new Exception($i18n->__('Please enter a username and password'));
					}
				}
				elseif (!TBGContext::getUser()->isAuthenticated() && TBGSettings::get('requirelogin'))
				{
					$this->login_error = $i18n->__('You need to log in to access this site');
				}
				elseif (!TBGContext::getUser()->isAuthenticated())
				{
					$this->login_error = $i18n->__('Please log in');
				}
				elseif (TBGContext::hasMessage('forward'))
				{
					$this->login_error = TBGContext::getMessageAndClear('forward');
				}
			}
			catch (Exception $e)
			{
				$this->login_error = $e->getMessage();
			}
		}
		
		/**
		 * Registration logic part 1 - check if username is free
		 *  
		 * @param TBGRequest $request
		 */
		public function runRegister1(TBGRequest $request)
		{
			$this->getResponse()->setPage('login');
			try
			{
				if (TBGContext::getRequest()->getMethod() == TBGRequest::POST)
				{
					$username = TBGContext::getRequest()->getParameter('desired_username');
					if (!empty($username))
					{
						$exists = B2DB::getTable('TBGUsersTable')->getByUsername($username);
						
						if ($exists)
						{
							throw new Exception(TBGContext::getI18n()->__('This username is in use'));
						}
						else
						{
							TBGContext::setMessage('prereg_success', $username);
							$this->forward('login');

						}
					}
					else
					{
						throw new Exception(TBGContext::getI18n()->__('Please enter a username'));
					}
				}
			}
			catch (Exception $e)
			{
				TBGContext::setMessage('prereg_error', $e->getMessage());
				$this->forward('login');
			}
		}

		/**
		 * Registration logic part 2 - add user data
		 *  
		 * @param TBGRequest $request
		 */
		public function runRegister2(TBGRequest $request)
		{
			$this->getResponse()->setPage('login');
			try
			{
				if (TBGContext::getRequest()->getMethod() == TBGRequest::POST)
				{
					$username = TBGContext::getRequest()->getParameter('username');
					$buddyname = TBGContext::getRequest()->getParameter('buddyname');
					$email = TBGContext::getRequest()->getParameter('email_address');
					$confirmemail = TBGContext::getRequest()->getParameter('email_confirm');
					$security = TBGContext::getRequest()->getParameter('verification_no');
					$realname = TBGContext::getRequest()->getParameter('realname');
					
					if (!empty($buddyname) && !empty($email) && !empty($confirmemail) && !empty($security))
					{
						if ($email != $confirmemail)
						{
							throw new Exception(TBGContext::getI18n()->__('The email address must be valid, and must be typed twice.'));
						}

						if ($security != $_SESSION['activation_number'])
						{
							throw new Exception(TBGContext::getI18n()->__('To prevent automatic sign-ups, enter the verification number shown below.'));
						}

						$email_ok = false;
						$valid_domain = false;

						if ((!(stristr($email, "@") === false)) && (strripos($email, ".") > strripos($email, "@")))
						{
							$email_ok = true;
						}
						
						if ($email_ok && TBGSettings::get('limit_registration') != '')
						{

							$allowed_domains = explode(',', TBGSettings::get('limit_registration'));
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
							throw new Exception(TBGContext::getI18n()->__('Email adresses from this domain can not be used.'));
						}
						
						if($email_ok == false)
						{
							throw new Exception(TBGContext::getI18n()->__('The email address must be valid, and must be typed twice.'));
						}
						
						if ($security != $_SESSION['activation_number'])
						{
							throw new Exception(TBGContext::getI18n()->__('To prevent automatic sign-ups, enter the verification number shown below.'));
						}

						$password = TBGUser::createPassword();
						$user = TBGUser::createNew($username, $realname, $buddyname, TBGContext::getScope()->getID(), false, true, TBGUser::hashPassword($password), $email, true);

						if ($user->isActivated())
						{
							TBGContext::setMessage('postreg_password', $password);
						}
						TBGContext::setMessage('postreg_success', true);
						$this->forward('login');
					}
					else
					{
						throw new Exception(TBGContext::getI18n()->__('You need to fill out all fields correctly.'));
					}
				}
			}
			catch (Exception $e)
			{
				TBGContext::setMessage('prereg_success', $username);
				TBGContext::setMessage('postreg_error', $e->getMessage());
				$this->forward('login');
			}
		}

		/**
		 * Activate newly registered account
		 *  
		 * @param TBGRequest $request
		 */
		public function runActivate(TBGRequest $request)
		{
			$this->getResponse()->setPage('../../login');
			
			$row = B2DB::getTable('TBGUsersTable')->getByUsername($request->getParameter('user'));
			if ($row)
			{
				if ($row->get(TBGUsersTable::PASSWD) != $request->getParameter('key'))
				{
					TBGContext::setMessage('account_activate', true);
					TBGContext::setMessage('activate_failure', true);
				}
				else
				{
					$user = new TBGUser($row->get(TBGUsersTable::ID), $row);
					$user->setValidated(1);
					TBGContext::setMessage('account_activate', true);
					TBGContext::setMessage('activate_success', true);
				}
			}
			else
			{
				TBGContext::setMessage('account_activate', true);
				TBGContext::setMessage('activate_failure', true);
			}
			$this->forward('../../login');
		}

		/**
		 * "My account" page
		 *  
		 * @param TBGRequest $request
		 */
		public function runMyAccount(TBGRequest $request)
		{
			$this->forward403unless(TBGContext::getUser()->hasPageAccess('account'));
			if ($request->isMethod(TBGRequest::POST) && $request->hasParameter('mode'))
			{
				switch ($request->getParameter('mode'))
				{
					case 'information':
						TBGContext::getUser()->setBuddyname($request->getParameter('buddyname'));
						TBGContext::getUser()->setRealname($request->getParameter('realname'));
						TBGContext::getUser()->setHomepage($request->getParameter('homepage'));
						TBGContext::getUser()->setEmailPrivate((bool) $request->getParameter('email_private'));

						if (TBGContext::getUser()->getEmail() != $request->getParameter('email'))
						{
							if (TBGEvent::createNew('core', 'changeEmail', TBGContext::getUser(), array('email' => $request->getParameter('email')))->triggerUntilProcessed()->isProcessed() == false)
							{
								TBGContext::getUser()->setEmail($request->getParameter('email'));
							}
						}

						TBGContext::getUser()->save();

						return $this->renderJSON(array('failed' => false, 'title' => TBGContext::getI18n()->__('Account information saved'), 'content' => ''));
						break;
					case 'settings':
						TBGContext::getUser()->setUsesGravatar((bool) $request->getParameter('use_gravatar'));
						TBGContext::getUser()->setTimezone($request->getParameter('timezone'));

						TBGContext::getUser()->save();

						return $this->renderJSON(array('failed' => false, 'title' => TBGContext::getI18n()->__('Profile settings saved'), 'content' => ''));
						break;
					case 'module':
						foreach (TBGContext::getModules() as $module_name => $module)
						{
							if ($request->getParameter('target_module') == $module_name && $module->hasAccountSettings())
							{
								if ($module->postAccountSettings($request))
								{
									return $this->renderJSON(array('failed' => false, 'title' => TBGContext::getI18n()->__('Settings saved'), 'content' => ''));
								}
								else
								{
									return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('An error occured')));
								}
							}
						}
						break;
				}
			}
			$this->getResponse()->setPage('account');
			$this->getResponse()->setProjectMenuStripHidden();
		}

		/**
		 * Change password ajax action
		 *
		 * @param TBGRequest $request
		 */
		public function runAccountChangePassword(TBGRequest $request)
		{
			$this->forward403unless(TBGContext::getUser()->hasPageAccess('account'));
			if ($request->isMethod(TBGRequest::POST))
			{
				if (!$request->hasParameter('current_password') || !$request->getParameter('current_password'))
				{
					return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('Please enter your current password')));
				}
				if (!$request->hasParameter('new_password_1') || !$request->getParameter('new_password_1'))
				{
					return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('Please enter a new password')));
				}
				if (!$request->hasParameter('new_password_2') || !$request->getParameter('new_password_2'))
				{
					return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('Please enter the new password twice')));
				}
				if (!TBGContext::getUser()->hasPassword($request->getParameter('current_password')))
				{
					return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('Please enter your current password')));
				}
				if ($request->getParameter('new_password_1') != $request->getParameter('new_password_2'))
				{
					return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('Please enter the new password twice')));
				}
				TBGContext::getUser()->changePassword($request->getParameter('new_password_1'));
				TBGContext::getUser()->save();
				$this->getResponse()->setCookie('b2_password', TBGContext::getUser()->getPasswordHash());
				return $this->renderJSON(array('failed' => false, 'title' => TBGContext::getI18n()->__('Your new password was changed')));
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
			$this->selected_pain_bug_type = null;
			$this->selected_pain_likelihood = null;
			$this->selected_pain_effect = null;
			$selected_customdatatype = array();
			foreach (TBGCustomDatatype::getAll() as $customdatatype)
			{
				$selected_customdatatype[$customdatatype->getKey()] = null;
			}
			$this->selected_customdatatype = $selected_customdatatype;
			$this->issuetypes = array();
			$this->issuetype_id = null;
			$this->issue = null;
			$this->categories = TBGCategory::getAll();
			$this->severities = TBGSeverity::getAll();
			$this->priorities = TBGPriority::getAll();
			$this->reproducabilities = TBGReproducability::getAll();
			$this->resolutions = TBGResolution::getAll();
			$this->statuses = TBGStatus::getAll();
			$this->projects = TBGProject::getAll();
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
			$this->selected_pain_bug_type = null;
			$this->selected_pain_likelihood = null;
			$this->selected_pain_effect = null;
			$selected_customdatatype = array();
			foreach (TBGCustomDatatype::getAll() as $customdatatype)
			{
				$selected_customdatatype[$customdatatype->getKey()] = null;
			}
			$this->selected_customdatatype = $selected_customdatatype;
		}

		protected function _loadSelectedProjectAndIssueTypeFromRequestForReportIssueAction(TBGRequest $request)
		{
			if ($project_key = $request->getParameter('project_key'))
			{
				try
				{
					$this->selected_project = TBGProject::getByKey($project_key);
				}
				catch (Exception $e) {}
			}
			elseif ($project_id = $request->getParameter('project_id'))
			{
				try
				{
					$this->selected_project = TBGFactory::projectLab($project_id);
				}
				catch (Exception $e) {}
			}
			if ($this->selected_project instanceof TBGProject)
			{
				TBGContext::setCurrentProject($this->selected_project);
			}
			if ($this->selected_project instanceof TBGProject)
			{
				$this->issuetypes = TBGIssuetype::getAllApplicableToProject($this->selected_project->getID());
			}
			else
			{
				$this->issuetypes = TBGIssuetype::getAll();
			}

			$this->issuetype_id = $request->getParameter('issuetype_id');
			if ($this->issuetype_id)
			{
				try
				{
					$this->selected_issuetype = TBGFactory::TBGIssuetypeLab($this->issuetype_id);
				}
				catch (Exception $e) {}
			}
		}

		protected function _postIssueValidation(TBGRequest $request, &$errors, &$permission_errors)
		{
			$i18n = TBGContext::getI18n();
			if (!$this->selected_project instanceof TBGProject) $errors['project'] = $i18n->__('You have to select a valid project');
			if (!$this->selected_issuetype instanceof TBGIssuetype) $errors['issuetype'] = $i18n->__('You have to select a valid issue type');
			if (empty($errors))
			{
				$fields_array = $this->selected_project->getReportableFieldsArray($this->issuetype_id);

				$this->title = $request->getParameter('title');
				$this->selected_description = $request->getParameter('description', null, false);
				$this->selected_reproduction_steps = $request->getParameter('reproduction_steps', null, false);

				if ($edition_id = (int) $request->getParameter('edition_id'))
				{
					$this->selected_edition = TBGFactory::editionLab($edition_id);
				}
				if ($build_id = (int) $request->getParameter('build_id'))
				{
					$this->selected_build = TBGFactory::buildLab($build_id);
				}
				if ($component_id = (int) $request->getParameter('component_id'))
				{
					$this->selected_component = TBGFactory::componentLab($component_id);
				}

				if (trim($this->title) == '' || $this->title == $this->default_title) $errors['title'] = true; //$i18n->__('You have to specify a title');

				if (isset($fields_array['editions']))
				{
					if ($edition_id && !in_array($edition_id, array_keys($fields_array['editions']['values'])))
						$errors['editions'] = true; // $i18n->__('The edition you specified is invalid');
				}

				if (isset($fields_array['builds']))
				{
					if ($build_id && !in_array($build_id, array_keys($fields_array['builds']['values'])))
						$errors['builds'] = true; //$i18n->__('The release you specified is invalid');
				}

				if (isset($fields_array['component']))
				{
					if ($component_id && !in_array($component_id, array_keys($fields_array['component']['components'])))
						$errors['components'] = true; //$i18n->__('The component you specified is invalid');
				}

				if ($category_id = (int) $request->getParameter('category_id'))
				{
					$this->selected_category = TBGFactory::TBGCategoryLab($category_id);
				}

				if ($status_id = (int) $request->getParameter('status_id'))
				{
					$this->selected_status = TBGFactory::TBGStatusLab($status_id);
				}

				if ($reproducability_id = (int) $request->getParameter('reproducability_id'))
				{
					$this->selected_reproducability = TBGFactory::TBGReproducabilityLab($reproducability_id);
				}

				if ($resolution_id = (int) $request->getParameter('resolution_id'))
				{
					$this->selected_resolution = TBGFactory::TBGResolutionLab($resolution_id);
				}

				if ($severity_id = (int) $request->getParameter('severity_id'))
				{
					$this->selected_severity = TBGFactory::TBGSeverityLab($severity_id);
				}

				if ($priority_id = (int) $request->getParameter('priority_id'))
				{
					$this->selected_priority = TBGFactory::TBGPriorityLab($priority_id);
				}

				if ($request->getParameter('estimated_time'))
				{
					$this->selected_estimated_time = $request->getParameter('estimated_time');
				}

				if ($request->getParameter('elapsed_time'))
				{
					$this->selected_elapsed_time = $request->getParameter('elapsed_time');
				}

				if (is_numeric($request->getParameter('percent_complete')))
				{
					$this->selected_percent_complete = (int) $request->getParameter('percent_complete');
				}

				if ($pain_bug_type_id = (int) $request->getParameter('pain_bug_type_id'))
				{
					$this->selected_pain_bug_type = $pain_bug_type_id;
				}

				if ($pain_likelihood_id = (int) $request->getParameter('pain_likelihood_id'))
				{
					$this->selected_pain_likelihood = $pain_likelihood_id;
				}

				if ($pain_effect_id = (int) $request->getParameter('pain_effect_id'))
				{
					$this->selected_pain_effect = $pain_effect_id;
				}

				$selected_customdatatype = array();
				foreach (TBGCustomDatatype::getAll() as $customdatatype)
				{
					$selected_customdatatype[$customdatatype->getKey()] = null;
					$customdatatype_id = $customdatatype->getKey() . '_id';
					if ($request->hasParameter($customdatatype_id))
					{
						$$customdatatype_id = $request->getParameter($customdatatype_id);
						$selected_customdatatype[$customdatatype->getKey()] = TBGCustomDatatypeOption::getByValueAndKey($$customdatatype_id, $customdatatype->getKey());
					}
				}
				$this->selected_customdatatype = $selected_customdatatype;

				foreach ($fields_array as $field => $info)
				{
					if ($field == 'user_pain')
					{
						if ($info['required'])
						{
							if (!($this->selected_pain_bug_type != 0 && $this->selected_pain_likelihood != 0 && $this->selected_pain_effect != 0))
							{
								$errors['user_pain'] = true;
							}
						}
					}
					elseif ($info['required'])
					{
						$var_name = "selected_{$field}";
						if ((in_array($field, TBGDatatype::getAvailableFields(true)) && ($this->$var_name === null || $this->$var_name === 0)) || (!in_array($field, TBGDatatype::getAvailableFields(true)) && !in_array($field, array('pain_bug_type', 'pain_likelihood', 'pain_effect')) && (array_key_exists($field, $selected_customdatatype) && $selected_customdatatype[$field] === null)))
						{
							$errors[$field] = true;
						}
					}
					else
					{
						if (in_array($field, TBGDatatype::getAvailableFields(true)))
						{
							if (!$this->selected_project->fieldPermissionCheck($field, true))
							{
								$permission_errors[$field] = true;
							}
						}
						elseif (!$this->selected_project->fieldPermissionCheck($field, true, true))
						{
							$permission_errors[$field] = true;
						}
					}
				}

			}
			return !(bool) (count($errors) + count($permission_errors));
		}

		protected function _postIssue()
		{
			$fields_array = $this->selected_project->getReportableFieldsArray($this->issuetype_id);
			$issue = TBGIssue::createNew($this->title, $this->issuetype_id, $this->selected_project->getID(), null, false);
			if (isset($fields_array['description'])) $issue->setDescription($this->selected_description);
			if (isset($fields_array['reproduction_steps'])) $issue->setReproductionSteps($this->selected_reproduction_steps);
			if (isset($fields_array['category']) && $this->selected_category instanceof TBGDatatype) $issue->setCategory($this->selected_category->getID());
			if (isset($fields_array['status']) && $this->selected_status instanceof TBGDatatype) $issue->setStatus($this->selected_status->getID());
			if (isset($fields_array['reproducability']) && $this->selected_reproducability instanceof TBGDatatype) $issue->setReproducability($this->selected_reproducability->getID());
			if (isset($fields_array['resolution']) && $this->selected_resolution instanceof TBGDatatype) $issue->setResolution($this->selected_resolution->getID());
			if (isset($fields_array['severity']) && $this->selected_severity instanceof TBGDatatype) $issue->setSeverity($this->selected_severity->getID());
			if (isset($fields_array['priority']) && $this->selected_priority instanceof TBGDatatype) $issue->setPriority($this->selected_priority->getID());
			if (isset($fields_array['estimated_time'])) $issue->setEstimatedTime($this->selected_estimated_time);
			if (isset($fields_array['elapsed_time'])) $issue->setSpentTime($this->selected_elapsed_time);
			if (isset($fields_array['percent_complete'])) $issue->setPercentCompleted($this->selected_percent_complete);
			if (isset($fields_array['pain_bug_type'])) $issue->setPainBugType($this->selected_pain_bug_type);
			if (isset($fields_array['pain_likelihood'])) $issue->setPainLikelihood($this->selected_pain_likelihood);
			if (isset($fields_array['pain_effect'])) $issue->setPainEffect($this->selected_pain_effect);
			foreach (TBGCustomDatatype::getAll() as $customdatatype)
			{
				if (isset($fields_array[$customdatatype->getKey()]) && $this->selected_customdatatype[$customdatatype->getKey()] instanceof TBGCustomDatatypeOption)
				{
					$selected_option = $this->selected_customdatatype[$customdatatype->getKey()];
					$issue->setCustomField($customdatatype->getKey(), $selected_option->getValue());
				}
			}
			$issue->save(false, true);
			if (isset($fields_array['edition']) && $this->selected_edition instanceof TBGEdition) $issue->addAffectedEdition($this->selected_edition);
			if (isset($fields_array['build']) && $this->selected_build instanceof TBGBuild) $issue->addAffectedBuild($this->selected_build);
			if (isset($fields_array['component']) && $this->selected_component instanceof TBGComponent) $issue->addAffectedComponent($this->selected_component);
			TBGEvent::createNew('core', 'TBGIssue::createNew', $issue)->trigger();

			return $issue;
		}
		
		/**
		 * "Report issue" page
		 *  
		 * @param TBGRequest $request
		 */
		public function runReportIssue(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();
			$this->_setupReportIssueProperties();
			$errors = array();
			$permission_errors = array();
			$this->getResponse()->setPage('reportissue');
			$this->default_title = $i18n->__('Enter a short, but descriptive summary of the issue here');
			$this->default_estimated_time = $i18n->__('Enter an estimate here');
			$this->default_elapsed_time = $i18n->__('Enter time spent here');

			$this->_loadSelectedProjectAndIssueTypeFromRequestForReportIssueAction($request);

			if ($request->isMethod(TBGRequest::POST))
			{
				if ($this->_postIssueValidation($request, $errors, $permission_errors))
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
							$this->forward(TBGContext::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), 303);
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
			$this->permission_errors = $permission_errors;
		}
		
		/**
		 * Retrieves the fields which are valid for that product and issue type combination
		 *  
		 * @param TBGRequest $request
		 */
		public function runReportIssueGetFields(TBGRequest $request)
		{
			if ($project_id = $request->getParameter('project_id'))
			{
				try
				{
					$selected_project = TBGFactory::projectLab($project_id);
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
			$available_fields = TBGDatatypeBase::getAvailableFields();
			$available_fields[] = 'pain_bug_type';
			$available_fields[] = 'pain_likelihood';
			$available_fields[] = 'pain_effect';
			return $this->renderJSON(array('available_fields' => $available_fields, 'fields' => $fields_array));
		}

		/**
		 * Retrieves the fields which are valid for that product and issue type combination
		 *  
		 * @param TBGRequest $request
		 */
		public function runToggleFavouriteIssue(TBGRequest $request)
		{
			if ($issue_id = $request->getParameter('issue_id'))
			{
				try
				{
					$issue = TBGFactory::TBGIssueLab($issue_id);
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
			
			if (TBGContext::getUser()->isIssueStarred($issue_id))
			{
				$retval = !TBGContext::getUser()->removeStarredIssue($issue_id);
			}
			else
			{
				$retval = TBGContext::getUser()->addStarredIssue($issue_id);
			}
			return $this->renderText(json_encode(array('starred' => $retval)));
		}
		
		/**
		 * Sets an issue field to a specified value
		 * 
		 * @param TBGRequest $request
		 */
		public function runIssueSetField(TBGRequest $request)
		{
			if ($issue_id = $request->getParameter('issue_id'))
			{
				try
				{
					$issue = TBGFactory::TBGIssueLab($issue_id);
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

			TBGContext::loadLibrary('common');
			
			switch ($request->getParameter('field'))
			{
				case 'description':
					$issue->setDescription($request->getRawParameter('value'));
					if ($issue->isDescriptionChanged())
					{
						return $this->renderJSON(array('changed' => true, 'field' => array('id' => (int) ($issue->getDescription() != ''), 'name' => tbg_parse_text($issue->getDescription())), 'description' => tbg_parse_text($issue->getDescription())));
					}
					else
					{
						return $this->renderJSON(array('changed' => false, 'field' => array('id' => (int) ($issue->getDescription() != ''), 'name' => tbg_parse_text($issue->getDescription())), 'description' => tbg_parse_text($issue->getDescripton())));
					}
					break;
				case 'reproduction_steps':
					$issue->setReproductionSteps($request->getRawParameter('value'));
					if ($issue->isReproduction_StepsChanged())
					{
						return $this->renderJSON(array('changed' => true, 'field' => array('id' => (int) ($issue->getReproductionSteps() != ''), 'name' => tbg_parse_text($issue->getReproductionSteps())), 'reproduction_steps' => tbg_parse_text($issue->getReproductionSteps())));
					}
					else
					{
						return $this->renderJSON(array('changed' => false, 'field' => array('id' => (int) ($issue->getReproductionSteps() != ''), 'name' => tbg_parse_text($issue->getReproductionSteps())), 'reproduction_steps' => tbg_parse_text($issue->getReproductionSteps())));
					}
					break;
				case 'title':
					if ($request->getParameter('value') == '')
					{
						return $this->renderJSON(array('changed' => false, 'failed' => true, 'error' => TBGContext::getI18n()->__('You have to provide a title')));
					}
					else
					{
						$issue->setTitle($request->getParameter('value'));
						if ($issue->isTitleChanged())
						{
							return $this->renderJSON(array('changed' => true, 'field' => array('id' => 1, 'name' => strip_tags($issue->getTitle())), 'title' => strip_tags($issue->getTitle())));
						}
						else
						{
							return $this->renderJSON(array('changed' => false, 'field' => array('id' => 1, 'name' => strip_tags($issue->getTitle())), 'title' => strip_tags($issue->getTitle())));
						}
					}
					break;
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
					if ($request->getParameter('estimated_time') != TBGContext::getI18n()->__('Enter your estimate here') && $request->getParameter('estimated_time'))
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
							if (in_array($request->getParameter('identifiable_type'), array(TBGIdentifiableClass::TYPE_USER, TBGIdentifiableClass::TYPE_TEAM)))
							{
								switch ($request->getParameter('identifiable_type'))
								{
									case TBGIdentifiableClass::TYPE_USER:
										$identified = TBGFactory::userLab($request->getParameter('value'));
										break;
									case TBGIdentifiableClass::TYPE_TEAM:
										$identified = TBGFactory::teamLab($request->getParameter('value'));
										break;
								}
								if ($identified instanceof TBGIdentifiableClass)
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
							$identified = TBGFactory::userLab($request->getParameter('value'));
							if ($identified instanceof TBGIdentifiableClass)
							{
								$issue->setPostedBy($identified);
							}
						}
						if ($request->getParameter('field') == 'owned_by')
							return $this->renderJSON(array('changed' => $issue->isOwnedByChanged(), 'field' => (($issue->isOwned()) ? array('id' => $issue->getOwnerID(), 'name' => (($issue->getOwnerType() == TBGIdentifiableClass::TYPE_USER) ? $this->getComponentHTML('main/userdropdown', array('user' => $issue->getOwner())) : $this->getComponentHTML('main/teamdropdown', array('team' => $issue->getOwner())))) : array('id' => 0))));
						if ($request->getParameter('field') == 'posted_by')
							return $this->renderJSON(array('changed' => $issue->isPostedByChanged(), 'field' => array('id' => $issue->getPostedByID(), 'name' => $this->getComponentHTML('main/userdropdown', array('user' => $issue->getPostedBy())))));
						if ($request->getParameter('field') == 'assigned_to')
							return $this->renderJSON(array('changed' => $issue->isAssignedToChanged(), 'field' => (($issue->isAssigned()) ? array('id' => $issue->getAssigneeID(), 'name' => (($issue->getAssigneeType() == TBGIdentifiableClass::TYPE_USER) ? $this->getComponentHTML('main/userdropdown', array('user' => $issue->getAssignee())) : $this->getComponentHTML('main/teamdropdown', array('team' => $issue->getAssignee())))) : array('id' => 0))));
					}
					break;
				case 'spent_time':
					if ($request->getParameter('spent_time') != TBGContext::getI18n()->__('Enter time spent here') && $request->getParameter('spent_time'))
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
				case 'pain_bug_type':
				case 'pain_likelihood':
				case 'pain_effect':
					try
					{
						if ($request->hasParameter('category_id') && $request->getParameter('field') == 'category')
						{
							$category_id = $request->getParameter('category_id');
							if ($category_id == 0 || ($category_id !== 0 && ($category = TBGFactory::TBGCategoryLab($category_id)) instanceof TBGCategory))
							{
								$issue->setCategory($category_id);
								if (!$issue->isCategoryChanged()) return $this->renderJSON(array('changed' => false));
								return ($category_id == 0) ? $this->renderJSON(array('changed' => true, 'field' => array('id' => 0))) : $this->renderJSON(array('changed' => true, 'field' => array('id' => $category_id, 'name' => $category->getName()))); 
							}
						}
						elseif ($request->hasParameter('resolution_id') && $request->getParameter('field') == 'resolution')
						{
							$resolution_id = $request->getParameter('resolution_id');
							if ($resolution_id == 0 || ($resolution_id !== 0 && ($resolution = TBGFactory::TBGResolutionLab($resolution_id)) instanceof TBGResolution))
							{
								$issue->setResolution($resolution_id);
								if (!$issue->isResolutionChanged()) return $this->renderJSON(array('changed' => false));
								return ($resolution_id == 0) ? $this->renderJSON(array('changed' => true, 'field' => array('id' => 0))) : $this->renderJSON(array('changed' => true, 'field' => array('id' => $resolution_id, 'name' => $resolution->getName()))); 
							}
						}
						elseif ($request->hasParameter('issuetype_id') && $request->getParameter('field') == 'issuetype')
						{
							$issuetype_id = $request->getParameter('issuetype_id');
							if ($issuetype_id == 0 || ($issuetype_id !== 0 && ($issuetype = TBGFactory::TBGIssuetypeLab($issuetype_id)) instanceof TBGIssuetype))
							{
								$issue->setIssuetype($issuetype_id);
								if (!$issue->isIssuetypeChanged()) return $this->renderJSON(array('changed' => false));
								$visible_fields = ($issuetype_id != 0) ? $issue->getProject()->getVisibleFieldsArray($issuetype_id) : array();
								return ($issuetype_id == 0) ? $this->renderJSON(array('changed' => true, 'field' => array('id' => 0), 'visible_fields' => $visible_fields)) : $this->renderJSON(array('changed' => true, 'field' => array('id' => $issuetype_id, 'name' => $issuetype->getName(), 'src' => htmlspecialchars(TBGSettings::getURLsubdir() . 'themes/' . TBGSettings::getThemeName() . '/' . $issuetype->getIcon() . '_small.png')), 'visible_fields' => $visible_fields)); 
							}
						}
						elseif ($request->hasParameter('severity_id') && $request->getParameter('field') == 'severity')
						{
							$severity_id = $request->getParameter('severity_id');
							if ($severity_id == 0 || ($severity_id !== 0 && ($severity = TBGFactory::TBGSeverityLab($severity_id)) instanceof TBGSeverity))
							{
								$issue->setSeverity($severity_id);
								if (!$issue->isSeverityChanged()) return $this->renderJSON(array('changed' => false));
								return ($severity_id == 0) ? $this->renderJSON(array('changed' => true, 'field' => array('id' => 0))) : $this->renderJSON(array('changed' => true, 'field' => array('id' => $severity_id, 'name' => $severity->getName()))); 
							}
						}
						elseif ($request->hasParameter('reproducability_id') && $request->getParameter('field') == 'reproducability')
						{
							$reproducability_id = $request->getParameter('reproducability_id');
							if ($reproducability_id == 0 || ($reproducability_id !== 0 && ($reproducability = TBGFactory::TBGReproducabilityLab($reproducability_id)) instanceof TBGReproducability))
							{
								$issue->setReproducability($reproducability_id);
								if (!$issue->isReproducabilityChanged()) return $this->renderJSON(array('changed' => false));
								return ($reproducability_id == 0) ? $this->renderJSON(array('changed' => true, 'field' => array('id' => 0))) : $this->renderJSON(array('changed' => true, 'field' => array('id' => $reproducability_id, 'name' => $reproducability->getName()))); 
							}
						}
						elseif ($request->hasParameter('priority_id') && $request->getParameter('field') == 'priority')
						{
							$priority_id = $request->getParameter('priority_id');
							if ($priority_id == 0 || ($priority_id !== 0 && ($priority = TBGFactory::TBGPriorityLab($priority_id)) instanceof TBGPriority))
							{
								$issue->setPriority($priority_id);
								if (!$issue->isPriorityChanged()) return $this->renderJSON(array('changed' => false));
								return ($priority_id == 0) ? $this->renderJSON(array('changed' => true, 'field' => array('id' => 0))) : $this->renderJSON(array('changed' => true, 'field' => array('id' => $priority_id, 'name' => $priority->getName()))); 
							}
						}
						elseif ($request->hasParameter('status_id') && $request->getParameter('field') == 'status')
						{
							$status_id = $request->getParameter('status_id');
							if ($status_id == 0 || ($status_id !== 0 && ($status = TBGFactory::TBGStatusLab($status_id)) instanceof TBGStatus))
							{
								$issue->setStatus($status_id);
								if (!$issue->isStatusChanged()) return $this->renderJSON(array('changed' => false));
								return ($status_id == 0) ? $this->renderJSON(array('changed' => true, 'field' => array('id' => 0))) : $this->renderJSON(array('changed' => true, 'field' => array('id' => $status_id, 'name' => $status->getName(), 'color' => $status->getItemdata()))); 
							}
						}
						elseif ($request->hasParameter('milestone_id') && $request->getParameter('field') == 'milestone')
						{
							$milestone_id = $request->getParameter('milestone_id');
							if ($milestone_id == 0 || ($milestone_id !== 0 && ($milestone = TBGFactory::TBGMilestoneLab($milestone_id)) instanceof TBGMilestone))
							{
								$issue->setMilestone($milestone_id);
								if (!$issue->isMilestoneChanged()) return $this->renderJSON(array('changed' => false));
								return ($milestone_id == 0) ? $this->renderJSON(array('changed' => true, 'field' => array('id' => 0))) : $this->renderJSON(array('changed' => true, 'field' => array('id' => $milestone_id, 'name' => $milestone->getName()))); 
							}
						}
						elseif ($request->hasParameter('pain_bug_type_id') && $request->getParameter('field') == 'pain_bug_type')
						{
							$pain_bug_type_id = $request->getParameter('pain_bug_type_id');
							if ($pain_bug_type_id == 0 || ($pain_bug_type_id !== 0 && (in_array($pain_bug_type_id, array_keys(TBGIssue::getPainTypesOrLabel('bug_type'))))))
							{
								$issue->setPainBugType($pain_bug_type_id);
								if (!$issue->isPainBugTypeChanged()) return $this->renderJSON(array('changed' => false, 'field' => array('id' => 0), 'user_pain' => $issue->getUserPain(), 'user_pain_diff_text' => $issue->getUserPainDiffText()));
								return ($pain_bug_type_id == 0) ? $this->renderJSON(array('changed' => true, 'field' => array('id' => 0), 'user_pain' => $issue->getUserPain(), 'user_pain_diff_text' => $issue->getUserPainDiffText())) : $this->renderJSON(array('changed' => true, 'field' => array('id' => $pain_bug_type_id, 'name' => $issue->getPainBugTypeLabel()), 'user_pain' => $issue->getUserPain(), 'user_pain_diff_text' => $issue->getUserPainDiffText()));
							}
						}
						elseif ($request->hasParameter('pain_likelihood_id') && $request->getParameter('field') == 'pain_likelihood')
						{
							$pain_likelihood_id = $request->getParameter('pain_likelihood_id');
							if ($pain_likelihood_id == 0 || ($pain_likelihood_id !== 0 && (in_array($pain_likelihood_id, array_keys(TBGIssue::getPainTypesOrLabel('likelihood'))))))
							{
								$issue->setPainLikelihood($pain_likelihood_id);
								if (!$issue->isPainLikelihoodChanged()) return $this->renderJSON(array('changed' => false, 'field' => array('id' => 0), 'user_pain' => $issue->getUserPain(), 'user_pain_diff_text' => $issue->getUserPainDiffText()));
								return ($pain_likelihood_id == 0) ? $this->renderJSON(array('changed' => true, 'field' => array('id' => 0), 'user_pain' => $issue->getUserPain(), 'user_pain_diff_text' => $issue->getUserPainDiffText())) : $this->renderJSON(array('changed' => true, 'field' => array('id' => $pain_likelihood_id, 'name' => $issue->getPainLikelihoodLabel()), 'user_pain' => $issue->getUserPain(), 'user_pain_diff_text' => $issue->getUserPainDiffText()));
							}
						}
						elseif ($request->hasParameter('pain_effect_id') && $request->getParameter('field') == 'pain_effect')
						{
							$pain_effect_id = $request->getParameter('pain_effect_id');
							if ($pain_effect_id == 0 || ($pain_effect_id !== 0 && (in_array($pain_effect_id, array_keys(TBGIssue::getPainTypesOrLabel('effect'))))))
							{
								$issue->setPainEffect($pain_effect_id);
								if (!$issue->isPainEffectChanged()) return $this->renderJSON(array('changed' => false, 'field' => array('id' => 0), 'user_pain' => $issue->getUserPain(), 'user_pain_diff_text' => $issue->getUserPainDiffText()));
								return ($pain_effect_id == 0) ? $this->renderJSON(array('changed' => true, 'field' => array('id' => 0), 'user_pain' => $issue->getUserPain(), 'user_pain_diff_text' => $issue->getUserPainDiffText())) : $this->renderJSON(array('changed' => true, 'field' => array('id' => $pain_effect_id, 'name' => $issue->getPainEffectLabel()), 'user_pain' => $issue->getUserPain(), 'user_pain_diff_text' => $issue->getUserPainDiffText()));
							}
						}
					}
					catch (Exception $e)
					{
						$this->getResponse()->setHttpStatus(400);
						return $this->renderJSON(array('error' => $e->getMessage()));
					}
					$this->getResponse()->setHttpStatus(400);
					return $this->renderJSON(array('error' => TBGContext::getI18n()->__('No valid field value specified')));
					break;
				default:
					if ($customdatatype = TBGCustomDatatype::getByKey($request->getParameter('field')))
					{
						$key = $customdatatype->getKey();
						if ($request->hasParameter("{$key}_value"))
						{
							$customdatatypeoption_value = $request->getParameter("{$key}_value");
							if ($customdatatypeoption_value && ($customdatatypeoption = TBGCustomDatatypeOption::getByValueAndKey($customdatatypeoption_value, $key)) instanceof TBGCustomDatatypeOption)
							{
								$issue->setCustomField($key, $customdatatypeoption->getValue());
								$changed_methodname = "isCustomfield{$key}Changed";
								if (!$issue->$changed_methodname()) return $this->renderJSON(array('changed' => false));
								return ($customdatatypeoption_value == '') ? $this->renderJSON(array('changed' => true, 'field' => array('id' => 0))) : $this->renderJSON(array('changed' => true, 'field' => array('value' => $customdatatypeoption_value, 'name' => $customdatatypeoption->getName())));
							}
						}
						$this->getResponse()->setHttpStatus(400);
						return $this->renderJSON(array('error' => TBGContext::getI18n()->__('No valid field value specified')));
					}
					break;
			}
			
			$this->getResponse()->setHttpStatus(400);
			return $this->renderJSON(array('error' => TBGContext::getI18n()->__('No valid field specified (%field%)', array('%field%' => $request->getParameter('field')))));
		}

		/**
		 * Reverts an issue field back to the original value
		 * 
		 * @param TBGRequest $request
		 */
		public function runIssueRevertField(TBGRequest $request)
		{
			if ($issue_id = $request->getParameter('issue_id'))
			{
				try
				{
					$issue = TBGFactory::TBGIssueLab($issue_id);
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
				case 'description':
					$issue->revertDescription();
					$field = array('id' => (int) ($issue->getDescription() != ''), 'name' => tbg_parse_text($issue->getDescription()), 'form_value' => $issue->getDescription());
					break;
				case 'reproduction_steps':
					$issue->revertReproduction_Steps();
					$field = array('id' => (int) ($issue->getReproductionSteps() != ''), 'name' => tbg_parse_text($issue->getReproductionSteps()), 'form_value' => $issue->getReproductionSteps());
					break;
				case 'title':
					$issue->revertTitle();
					$field = array('id' => 1, 'name' => strip_tags($issue->getTitle()));
					break;
				case 'category':
					$issue->revertCategory();
					$field = ($issue->getCategory() instanceof TBGCategory) ? array('id' => $issue->getCategory()->getID(), 'name' => $issue->getCategory()->getName()) : array('id' => 0);
					break;
				case 'resolution':
					$issue->revertResolution();
					$field = ($issue->getResolution() instanceof TBGResolution) ? array('id' => $issue->getResolution()->getID(), 'name' => $issue->getResolution()->getName()) : array('id' => 0);
					break;
				case 'severity':
					$issue->revertSeverity();
					$field = ($issue->getSeverity() instanceof TBGSeverity) ? array('id' => $issue->getSeverity()->getID(), 'name' => $issue->getSeverity()->getName()) : array('id' => 0);
					break;
				case 'reproducability':
					$issue->revertReproducability();
					$field = ($issue->getReproducability() instanceof TBGReproducability) ? array('id' => $issue->getReproducability()->getID(), 'name' => $issue->getReproducability()->getName()) : array('id' => 0);
					break;
				case 'priority':
					$issue->revertPriority();
					$field = ($issue->getPriority() instanceof TBGPriority) ? array('id' => $issue->getPriority()->getID(), 'name' => $issue->getPriority()->getName()) : array('id' => 0);
					break;
				case 'percent':
					$issue->revertPercentCompleted();
					return $this->renderJSON(array('ok' => true, 'percent' => $issue->getPercentCompleted()));
					break;
				case 'status':
					$issue->revertStatus();
					$field = ($issue->getStatus() instanceof TBGStatus) ? array('id' => $issue->getStatus()->getID(), 'name' => $issue->getStatus()->getName(), 'color' => $issue->getStatus()->getColor()) : array('id' => 0);
					break;
				case 'pain_bug_type':
					$issue->revertPainBugType();
					$field = ($issue->hasPainBugType()) ? array('id' => $issue->getPainBugType(), 'name' => $issue->getPainBugTypeLabel(), 'user_pain' => $issue->getUserPain()) : array('id' => 0, 'user_pain' => $issue->getUserPain());
					break;
				case 'pain_likelihood':
					$issue->revertPainLikelihood();
					$field = ($issue->hasPainLikelihood()) ? array('id' => $issue->getPainLikelihood(), 'name' => $issue->getPainLikelihoodLabel(), 'user_pain' => $issue->getUserPain()) : array('id' => 0, 'user_pain' => $issue->getUserPain());
					break;
				case 'pain_effect':
					$issue->revertPainEffect();
					$field = ($issue->hasPainEffect()) ? array('id' => $issue->getPainEffect(), 'name' => $issue->getPainEffectLabel(), 'user_pain' => $issue->getUserPain()) : array('id' => 0, 'user_pain' => $issue->getUserPain());
					break;
				case 'issuetype':
					$issue->revertIssuetype();
					$field = ($issue->getIssuetype() instanceof TBGIssuetype) ? array('id' => $issue->getIssuetype()->getID(), 'name' => $issue->getIssuetype()->getName(), 'src' => htmlspecialchars(TBGSettings::getURLsubdir() . 'themes/' . TBGSettings::getThemeName() . '/' . $issue->getIssuetype()->getIcon() . '_small.png')) : array('id' => 0);
					$visible_fields = ($issue->getIssuetype() instanceof TBGIssuetype) ? $issue->getProject()->getVisibleFieldsArray($issue->getIssuetype()->getID()) : array();
					return $this->renderJSON(array('ok' => true, 'field' => $field, 'visible_fields' => $visible_fields));
					break;
				case 'milestone':
					$issue->revertMilestone();
					$field = ($issue->getMilestone() instanceof TBGMilestone) ? array('id' => $issue->getMilestone()->getID(), 'name' => $issue->getMilestone()->getName()) : array('id' => 0);
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
					return $this->renderJSON(array('changed' => $issue->isOwnedByChanged(), 'field' => (($issue->isOwned()) ? array('id' => $issue->getOwnerID(), 'name' => (($issue->getOwnerType() == TBGIdentifiableClass::TYPE_USER) ? $this->getComponentHTML('main/userdropdown', array('user' => $issue->getOwner())) : $this->getComponentHTML('main/teamdropdown', array('team' => $issue->getOwner())))) : array('id' => 0))));
					break;
				case 'assigned_to':
					$issue->revertAssignedTo();
					return $this->renderJSON(array('changed' => $issue->isAssignedToChanged(), 'field' => (($issue->isAssigned()) ? array('id' => $issue->getAssigneeID(), 'name' => (($issue->getAssigneeType() == TBGIdentifiableClass::TYPE_USER) ? $this->getComponentHTML('main/userdropdown', array('user' => $issue->getAssignee())) : $this->getComponentHTML('main/teamdropdown', array('team' => $issue->getAssignee())))) : array('id' => 0))));
					break;
				case 'posted_by':
					$issue->revertPostedBy();
					return $this->renderJSON(array('changed' => $issue->isPostedByChanged(), 'field' => array('id' => $issue->getPostedByID(), 'name' => $this->getComponentHTML('main/userdropdown', array('user' => $issue->getPostedBy())))));
					break;
				default:
					if ($customdatatype = TBGCustomDatatype::getByKey($request->getParameter('field')))
					{
						$key = $customdatatype->getKey();
						$revert_methodname = "revertCustomfield{$key}";
						$issue->$revert_methodname();
						$field = ($issue->getCustomField($key) instanceof TBGCustomDatatypeOption) ? array('value' => $issue->getCustomField($key)->getValue(), 'name' => $issue->getCustomField($key)->getName()) : array('id' => 0);
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
				return $this->renderJSON(array('error' => TBGContext::getI18n()->__('No valid field specified (%field%)', array('%field%' => $request->getParameter('field')))));
			}
		}
		
		/**
		 * Marks this issue as being worked on by the current user
		 * 
		 * @param TBGRequest $request
		 */
		public function runIssueStartWorking(TBGRequest $request)
		{
			if ($issue_id = $request->getParameter('issue_id'))
			{
				try
				{
					$issue = TBGFactory::TBGIssueLab($issue_id);
				}
				catch (Exception $e)
				{
					return $this->return404(TBGContext::getI18n()->__('This issue does not exist'));
				}
			}
			else
			{
				return $this->return404(TBGContext::getI18n()->__('This issue does not exist'));
			}
			$issue->startWorkingOnIssue(TBGContext::getUser());
			$issue->save();
			$this->forward(TBGContext::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
		}
		
		/**
		 * Marks this issue as being completed work on by the current user
		 * 
		 * @param TBGRequest $request
		 */
		public function runIssueStopWorking(TBGRequest $request)
		{
			if ($issue_id = $request->getParameter('issue_id'))
			{
				try
				{
					$issue = TBGFactory::TBGIssueLab($issue_id);
				}
				catch (Exception $e)
				{
					return $this->return404(TBGContext::getI18n()->__('This issue does not exist'));
				}
			}
			else
			{
				return $this->return404(TBGContext::getI18n()->__('This issue does not exist'));
			}
			
			if ($request->hasParameter('did') && $request->getParameter('did') == 'nothing')
			{
				$issue->clearUserWorkingOnIssue();
				$issue->save();
				$this->forward(TBGContext::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
			}
			elseif ($request->hasParameter('perform_action') && $request->getParameter('perform_action') == 'grab')
			{
				$issue->clearUserWorkingOnIssue();
				$issue->startWorkingOnIssue(TBGContext::getUser());
				$issue->save();
				$this->forward(TBGContext::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
			}
			else
			{
				$issue->stopWorkingOnIssue();
				$issue->save();
				$this->forward(TBGContext::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
			}
		}

		/**
		 * Reopen the issue
		 * 
		 * @param TBGRequest $request
		 */
		public function runReopenIssue(TBGRequest $request)
		{
			if ($issue_id = $request->getParameter('issue_id'))
			{
				try
				{
					$issue = TBGFactory::TBGIssueLab($issue_id);
				}
				catch (Exception $e)
				{
					return $this->return404(TBGContext::getI18n()->__('This issue does not exist'));
				}
			}
			else
			{
				return $this->return404(TBGContext::getI18n()->__('This issue does not exist'));
			}
			$issue->open();
			$issue->save();
			$this->forward(TBGContext::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
		}
		
		/**
		 * Close the issue
		 * 
		 * @param TBGRequest $request
		 */
		public function runCloseIssue(TBGRequest $request)
		{
			if ($issue_id = $request->getParameter('issue_id'))
			{
				try
				{
					$issue = TBGFactory::TBGIssueLab($issue_id);
				}
				catch (Exception $e)
				{
					return $this->return404(TBGContext::getI18n()->__('This issue does not exist'));
				}
			}
			else
			{
				return $this->return404(TBGContext::getI18n()->__('This issue does not exist'));
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
				$issue->addSystemComment(TBGContext::getI18n()->__('Issue closed'), $request->getParameter('close_comment'), TBGContext::getUser()->getID());
			}
			$issue->close();
			$issue->save();
			$this->forward(TBGContext::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
		}
		
		/**
		 * Find users and show selection links
		 * 
		 * @param TBGRequest $request The request object
		 */		
		public function runFindIdentifiable(TBGRequest $request)
		{
			$this->forward403unless($request->isMethod(TBGRequest::POST));
			$this->users = array();
			
			if ($find_identifiable_by = $request->getParameter('find_identifiable_by'))
			{
				$this->users = TBGUser::findUsers($find_identifiable_by, 10);
				if ($request->getParameter('include_teams'))
				{
					$this->teams = TBGTeam::findTeams($find_identifiable_by);
				}
			}
			return $this->renderComponent('identifiableselectorresults', array('users' => $this->users, 'callback' => $request->getParameter('callback')));
		}
		
		/**
		 * Hides an infobox with a specific key
		 * 
		 * @param TBGRequest $request The request object
		 */		
		public function runHideInfobox(TBGRequest $request)
		{
			TBGSettings::hideInfoBox($request->getParameter('key'));
			return $this->renderJSON(array('hidden' => true));
		}

		public function runGetUploadStatus(TBGRequest $request)
		{
			$id = $request->getParameter('upload_id', 0);

			TBGLogging::log('requesting status for upload with id ' . $id);
			$status = TBGContext::getRequest()->getUploadStatus($id);
			TBGLogging::log('status was: ' . (int) $status['finished']. ', pct: '. (int) $status['percent']);
			if (array_key_exists('file_id', $status) && $request->getParameter('mode') == 'issue')
			{
				$status['content_uploader'] = $this->getComponentHTML('main/attachedfile', array('base_id' => 'uploaded_files', 'mode' => 'issue', 'issue_id' => $request->getParameter('issue_id'), 'file_id' => $status['file_id']));
				$status['content_inline'] = $this->getComponentHTML('main/attachedfile', array('base_id' => 'viewissue_files', 'mode' => 'issue', 'issue_id' => $request->getParameter('issue_id'), 'file_id' => $status['file_id']));
				$issue = TBGFactory::TBGIssueLab($request->getParameter('issue_id'));
				$status['attachmentcount'] = count($issue->getFiles()) + count($issue->getLinks());
			}
			
			return $this->renderJSON($status);
		}

		public function runUpload(TBGRequest $request)
		{
			if (!$request->getParameter('APC_UPLOAD_PROGRESS'))
			{
				$request->setParameter('APC_UPLOAD_PROGRESS', $request->getParameter('upload_id'));
			}
			$this->getResponse()->setDecoration(TBGResponse::DECORATE_NONE);

			$canupload = false;

			if ($request->getParameter('mode') == 'issue')
			{
				$issue = TBGFactory::TBGIssueLab($request->getParameter('issue_id'));
				$canupload = (bool) ($issue instanceof TBGIssue && $issue->hasAccess() && $issue->canAttachFiles());
			}
			
			$event = TBGEvent::createNew('core', 'main_upload', $request->getParameter('mode'))->triggerUntilProcessed();
			if ($event->isProcessed())
			{
				$canupload = $event->getReturnValue();
			}

			if ($canupload)
			{
				try
				{
					$file_id = TBGContext::getRequest()->handleUpload('uploader_file', $issue);
					if ($file_id)
					{
						if ($request->getParameter('mode') == 'issue')
						{
							$issue->attachFile($file_id);
							if ($request->getParameter('comment') != '')
							{
								TBGComment::createNew('', TBGContext::getI18n()->__('The file %link_to_file% was uploaded with the following comment: %comment%', array('%comment%' => "\n  " . str_replace("\n", "\n  ", $request->getParameter('comment')), '%link_to_file%' => "[[TBG:@showfile?id={$file_id}|{$request->getParameter('uploader_file_description')}]]")), TBGContext::getUser()->getID(), $issue->getID(), TBGComment::TYPE_ISSUE);
							}
							else
							{
								TBGComment::createNew('', TBGContext::getI18n()->__('The file %link_to_file% was uploaded.', array('%link_to_file%' => "[[TBG:@showfile?id={$file_id}|{$request->getParameter('uploader_file_description')}]]")), TBGContext::getUser()->getID(), $issue->getID(), TBGComment::TYPE_ISSUE, 'core', true, true);								
							}
						}
						return $this->renderText('ok');
					}
					$this->error = TBGContext::getI18n()->__('An unhandled error occured with the upload');
				}
				catch (Exception $e)
				{
					$this->getResponse()->setHttpStatus(401);
					$this->error = $e->getMessage();
				}
			}
			else
			{
				$this->getResponse()->setHttpStatus(401);
				$this->error = TBGContext::getI18n()->__('You are not allowed to attach files here');
			}
			TBGLogging::log('marking upload ' . $request->getParameter('APC_UPLOAD_PROGRESS') . ' as completed with error ' . $this->error);
			$request->markUploadAsFinishedWithError($request->getParameter('APC_UPLOAD_PROGRESS'), $this->error);
			return $this->renderText($request->getParameter('APC_UPLOAD_PROGRESS').': '.$this->error);
		}

		public function runDetachFile(TBGrequest $request)
		{
			switch ($request->getParameter('mode'))
			{
				case 'issue':
					$issue = TBGFactory::TBGIssueLab($request->getParameter('issue_id'));
					if ($issue->canRemoveAttachments() && (int) $request->getParameter('file_id', 0))
					{
						B2DB::getTable('TBGIssueFilesTable')->removeFileFromIssue($issue->getID(), (int) $request->getParameter('file_id'));
						return $this->renderJSON(array('failed' => false, 'file_id' => $request->getParameter('file_id'), 'attachmentcount' => (count($issue->getFiles()) + count($issue->getLinks())), 'message' => TBGContext::getI18n()->__('The attachment has been removed')));
					}
					return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('You can not remove items from this issue')));
					break;
			}
			return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('Invalid mode')));
		}

		public function runGetFile(TBGRequest $request)
		{
			$file = B2DB::getTable('TBGFilesTable')->doSelectById((int) $request->getParameter('id'));
			if ($file instanceof B2DBRow)
			{
				$this->getResponse()->cleanBuffer();
				$this->getResponse()->clearHeaders();
				$this->getResponse()->setDecoration(TBGResponse::DECORATE_NONE);
				$this->getResponse()->addHeader('Content-disposition: '.(($request->getParameter('mode') == 'download') ? 'attachment' : 'inline').'; filename='.$file->get(TBGFilesTable::ORIGINAL_FILENAME));
				$this->getResponse()->addHeader('Content-type: '.$file->get(TBGFilesTable::CONTENT_TYPE));
				$this->getResponse()->renderHeaders();
				if (TBGSettings::getUploadStorage() == 'files')
				{
					echo fpassthru(fopen(TBGSettings::getUploadsLocalpath().$file->get(TBGFilesTable::REAL_FILENAME), 'r'));
				}
				else
				{
					echo $file->get(TBGFilesTable::CONTENT);
				}
				//die();
				return true;
			}
			$this->return404(TBGContext::getI18n()->__('This file does not exist'));
		}

		public function runAttachLinkToIssue(TBGRequest $request)
		{
			$issue = TBGFactory::TBGIssueLab($request->getParameter('issue_id'));
			if ($issue instanceof TBGIssue && $issue->canAttachLinks())
			{
				if ($request->getParameter('link_url') != '')
				{
					$link_id = $issue->attachLink($request->getParameter('link_url'), $request->getParameter('description'));
					return $this->renderJSON(array('failed' => false, 'message' => TBGContext::getI18n()->__('Link attached!'), 'attachmentcount' => (count($issue->getFiles()) + count($issue->getLinks())), 'content' => $this->getTemplateHTML('main/attachedlink', array('issue' => $issue, 'link_id' => $link_id, 'link' => array('description' => $request->getParameter('description'), 'url' => $request->getParameter('link_url'))))));
				}
				return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('You have to provide a link URL, otherwise we have nowhere to link to!')));
			}
			return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('You can not attach links to this issue')));
		}

		public function runRemoveLinkFromIssue(TBGRequest $request)
		{
			$issue = TBGFactory::TBGIssueLab($request->getParameter('issue_id'));
			if ($issue instanceof TBGIssue && $issue->canRemoveAttachments())
			{
				if ($request->getParameter('link_id') != 0)
				{
					$issue->removeLink($request->getParameter('link_id'));
					return $this->renderJSON(array('failed' => false, 'attachmentcount' => (count($issue->getFiles()) + count($issue->getLinks())), 'message' => TBGContext::getI18n()->__('Link removed!')));
				}
				return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('You have to provide a valid link id')));
			}
			return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('You can not remove items from this issue')));
		}

		public function runAttachLink(TBGRequest $request)
		{
			$link_id = TBGLinksTable::getTable()->addLink($request->getParameter('target_type'), $request->getParameter('target_id'), $request->getParameter('link_url'), $request->getRawParameter('description'));
			return $this->renderJSON(array('failed' => false, 'message' => TBGContext::getI18n()->__('Link added!'), 'content' => $this->getTemplateHTML('main/menulink', array('link_id' => $link_id, 'link' => array('target_type' => $request->getParameter('target_type'), 'target_id' => $request->getParameter('target_id'), 'description' => $request->getRawParameter('description'), 'url' => $request->getParameter('link_url'))))));
		}

		public function runRemoveLink(TBGRequest $request)
		{
			if ($request->getParameter('link_id') != 0)
			{
				TBGLinksTable::getTable()->removeByTargetTypeTargetIDandLinkID($request->getParameter('target_type'), $request->getParameter('target_id'), $request->getParameter('link_id'));
				return $this->renderJSON(array('failed' => false, 'message' => TBGContext::getI18n()->__('Link removed!')));
			}
			return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('You have to provide a valid link id')));
		}
		
		public function runDeleteComment(TBGRequest $request)
		{
			$comment = TBGFactory::TBGCommentLab($request->getParameter('comment_id'));
			if ($comment instanceof TBGcomment)
			{							
				if (!$comment->canUserDeleteComment())
				{
					return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('You are not allowed to do this')));
				}
				else
				{
					unset($comment);
					TBGComment::deleteComment($request->getParameter('comment_id'));
					return $this->renderJSON(array('title' => TBGContext::getI18n()->__('Comment deleted!')));
				}
			}
			else
			{
				return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('Comment ID is invalid')));
			}
		}
		
		public function runUpdateComment(TBGRequest $request)
		{
			TBGContext::loadLibrary('ui');
			$comment = TBGFactory::TBGCommentLab($request->getParameter('comment_id'));
			if ($comment instanceof TBGcomment)
			{							
				if (!$comment->canUserEditComment())
				{
					return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('You are not allowed to do this')));
				}
				else
				{
					if ($request->getParameter('comment_body') == '')
					{
						return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('The comment must have some content')));
					}
					
					$comment->setContent($request->getParameter('comment_body'));
					
					if ($request->getParameter('comment_title') == '')
					{
						$comment->setTitle(TBGContext::getI18n()->__('Untitled comment'));
					}
					else
					{
						$comment->setTitle($request->getParameter('comment_title'));
					}
					
					$comment->setIsPublic($request->getParameter('comment_visibility'));
					$comment->setUpdatedBy(TBGContext::getUser()->getID());
					$body = tbg_parse_text($comment->getContent());
					
					return $this->renderJSON(array('title' => TBGContext::getI18n()->__('Comment edited!'), 'comment_title' => $comment->getTitle(), 'comment_body' => $body));
				}
			}
			else
			{
				return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('Comment ID is invalid')));
			}
		}

		public function listenIssueSaveAddComment(TBGEvent $event)
		{
			$this->comment_lines = $event->getParameter('comment_lines');
			$this->comment = $event->getParameter('comment');
		}

		public function listenViewIssuePostError(TBGEvent $event)
		{
			if (TBGContext::hasMessage('comment_error'))
			{
				$this->comment_error = true;
				$this->error = TBGContext::getMessageAndClear('comment_error');
				$this->comment_error_title = TBGContext::getMessageAndClear('comment_error_title');
				$this->comment_error_body = TBGContext::getMessageAndClear('comment_error_body');
			}
		}
		
		public function runAddComment(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();
			$comment = null;
			$project = TBGFactory::ProjectLab($request->getParameter('project_id'));
			$project_key = ($project instanceof TBGProject) ? $project->getKey() : false;
			try
			{
				if ($project instanceof TBGProject)
				{
					if (!TBGContext::getUser()->canPostComments())
					{
						throw new Exception($i18n->__('You are not allowed to do this'));
					}
					else
					{
						if ($request->getParameter('comment_title') == '')
						{
							$title = $i18n->__('Untitled comment');
						}
						else
						{
							$title = $request->getParameter('comment_title');
						}

						if ($request->getParameter('comment_body') == '')
						{
							throw new Exception($i18n->__('The comment must have some content'));
						}

						if (!$request->isAjaxCall())
						{
							$this->comment_lines = array();
							$this->comment = '';
							TBGEvent::listen('core', 'TBGIssue::save', array($this, 'listenIssueSaveAddComment'));
							$issue = TBGFactory::TBGIssueLab($request->getParameter('comment_applies_id'));
							$issue->save(false);
						}

						$comment_body = $this->comment . "\n\n" . $request->getParameter('comment_body');

						$comment = TBGComment::createNew($title, $comment_body, TBGContext::getUser()->getID(), $request->getParameter('comment_applies_id'), $request->getParameter('comment_applies_type'), $request->getParameter('comment_module'), $request->getParameter('comment_visibility'), 0, false);

						if ($request->getParameter('comment_applies_type') == 1 && $request->getParameter('comment_module') == 'core')
						{
							$comment_html = $this->getTemplateHTML('main/comment', array('aComment' => $comment, 'theIssue' => TBGFactory::TBGIssueLab($request->getParameter('comment_applies_id'))));
						}
						else
						{
							$comment_html = 'OH NO!';
						}
					}
				}
				else
				{
					throw new Exception($i18n->__('Comment ID is invalid'));
				}
			}
			catch (Exception $e)
			{
				if ($request->isAjaxCall())
				{
					return $this->renderJSON(array('failed' => true, 'error' => $e->getMessage()));
				}
				else
				{
					TBGContext::setMessage('comment_error', $e->getMessage());
					TBGContext::setMessage('comment_error_body', $request->getParameter('comment_body'));
					TBGContext::setMessage('comment_error_title', $request->getParameter('comment_title'));
					TBGContext::setMessage('comment_error_visibility', $request->getParameter('comment_visibility'));
				}
			}
			if ($request->isAjaxCall())
			{
				return $this->renderJSON(array('title' => $i18n->__('Comment added!'), 'comment_data' => $comment_html, 'commentcount' => TBGComment::countComments($request->getParameter('comment_applies_id'), $request->getParameter('comment_applies_type'), $request->getParameter('comment_module'))));
			}
//			var_dump($comment);die();
			if ($comment instanceof TBGComment)
			{
				$this->forward($request->getParameter('forward_url') . "#comment_{$request->getParameter('comment_applies_type')}_{$request->getParameter('comment_applies_id')}_{$comment->getID()}");
			}
			else
			{
				$this->forward($request->getParameter('forward_url'));
			}
		}

		public function runGetBackdropPartial(TBGRequest $request)
		{
			try
			{
				switch ($request->getParameter('key'))
				{
					case 'close_issue':
						$issue = TBGFactory::TBGIssueLab($request->getParameter('issue_id'));
						return $this->renderJSON(array('content' => $this->getComponentHTML('main/closeissue', array('issue' => $issue))));
						break;
				}
			}
			catch (Exception $e)
			{
				return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('An error occured: %error_message%', array('%error_message%' => $e->getMessage()))));
			}
			return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('Invalid template or parameter')));
		}

	}