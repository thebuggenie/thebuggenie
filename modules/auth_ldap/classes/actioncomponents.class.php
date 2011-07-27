<?php

	/**
	 * action components for the ldap_authentication module
	 */
	class auth_ldapActionComponents extends TBGActionComponent
	{
		public function componentSettings()
		{
			if (!extension_loaded('ldap'))
			{
				$this->noldap = true;
			}
			else
			{
				$this->noldap = false;
			}
		}
	}

