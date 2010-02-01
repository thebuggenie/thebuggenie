<?php

	/**
	 * actions for the openid module
	 */
	class openidActions extends TBGAction
	{

		const SERVICES_YADIS_MATCH_ALL = 101;
		const SERVICES_YADIS_MATCH_ANY = 102;

		/**
		 * An instance of a Services_Yadis_XMLParser subclass.
		 * 
		 * @var Services_Yadis_XMLParser
		 */
		protected static $_Services_Yadis_defaultParser = null;

		protected static $_Services_Yadis_xml_extensions = array('dom' => 'Services_Yadis_dom', 'domxml' => 'Services_Yadis_domxml');

		protected static $_Services_Yadis_ns_map = array('xrds' => 'xri://$xrds', 'xrd' => 'xri://$xrd*($v*2.0)');

		protected static $_Services_Yadis_Max_Priority = null;
		
		/**
		 * Set a default parser to override the extension-driven selection of
		 * available parser classes.  This is helpful in a test environment or
		 * one in which multiple parsers can be used but one is more
		 * desirable.
		 *
		 * @param Services_Yadis_XMLParser $parser An instance of a Services_Yadis_XMLParser subclass
		 */
		public static function Services_Yadis_setDefaultParser($parser)
		{
			self::$_Services_Yadis_defaultParser = $parser;
		}

		public static function Services_Yadis_Max_Priority()
		{
			if (self::$_Services_Yadis_Max_Priority === null)
			{
				self::$_Services_Yadis_Max_Priority = pow(2, 30);
			}
			return self::$_Services_Yadis_Max_Priority;
		}

		public static function getYadisXMLExtensions()
		{
			return self::$_Services_Yadis_xml_extensions;
		}

		public static function getYadisNSmap()
		{
			return self::$_Services_Yadis_ns_map;
		}

		/**
		 * Returns an instance of a Services_Yadis_XMLParser subclass based on
		 * the availability of PHP extensions for XML parsing.  If
		 * Services_Yadis_setDefaultParser has been called, the parser used in
		 * that call will be returned instead.
		 */
		public static function Services_Yadis_getXMLParser()
		{
			if (self::$_Services_Yadis_defaultParser) {
				return self::$_Services_Yadis_defaultParser;
			}

			$p = null;

			// Return a wrapper for the resident implementation, if any.
			foreach (self::$_Services_Yadis_xml_extensions as $name => $cls)
			{
				if (extension_loaded($name) || @dl($name . '.so'))
				{
					// First create a dummy variable because PHP doesn't let
					// you return things by reference unless they're
					// variables
					$p = new $cls();
					return $p;
				}
			}

			return null;
		}

		/**
		 * @access private
		 */
		public static function Services_Yadis_array_scramble($arr)
		{
			$result = array();

			while (count($arr)) {
				$index = array_rand($arr, 1);
				$result[] = $arr[$index];
				unset($arr[$index]);
			}

			return $result;
		}

		public function runLogin(TBGRequest $request)
		{
			//error_reporting(E_ALL | ~E_NOTICE);
			if ($request->isMethod(TBGRequest::POST))
			{
				$this->getResponse()->setDecoration(TBGResponse::DECORATE_NONE);
				$this->getResponse()->clearHeaders();
				$this->getResponse()->cleanBuffer();
			}
			$this->error = null;
			if ($request->getParameter('openid_action') == "login")
			{ // Get identity from user and redirect browser to OpenID Server
				$openid = new SimpleOpenID;
				$openid->SetIdentity($request->getParameter('openid_url'));
				$openid->SetTrustRoot('http://87.238.44.84/'/*TBGContext::getURLhost()*/);
				$openid->SetRequiredFields(array('email'));
				$openid->SetOptionalFields(array('dob','gender','postcode','country','language','timezone'));
				if ($openid->GetOpenIDServer())
				{
					$openid->SetApprovedURL('http://87.238.44.84'.TBGContext::getRouting()->generate('openid_login', array()));  	// Send Response from OpenID server to this script
					$openid->Redirect(); 	// This will redirect user to OpenID Server
				}
				else
				{
					$error = $openid->GetError();
					echo "ERROR CODE: " . $error['code'] . "<br>";
					echo "ERROR DESCRIPTION: " . $error['description'] . "<br>";
				}
				exit;
			}
			elseif ($request->getParameter('openid_mode') == 'id_res')
			{ 	// Perform HTTP Request to OpenID server to validate key
				$openid = new SimpleOpenID;
				$openid->SetIdentity($_GET['openid_identity']);
				$openid_validation_result = $openid->ValidateWithServer();
				if ($openid_validation_result == true)
				{ 		// OK HERE KEY IS VALID
					echo "VALID";
				}
				elseif ($openid->IsError() == true)
				{		// ON THE WAY, WE GOT SOME ERROR
					$error = $openid->GetError();
					echo "ERROR CODE: " . $error['code'] . "<br>";
					echo "ERROR DESCRIPTION: " . $error['description'] . "<br>";
				} 
				else
				{		// Signature Verification Failed
					echo "INVALID AUTHORIZATION";
				}
			}
			elseif ($request->getParameter('openid_mode') == 'cancel')
			{ // User Canceled your Request
				echo "USER CANCELED REQUEST";
			}
			elseif ($request->getParameter('openid_mode') == 'error')
			{ // User Canceled your Request
				$this->error = true;
				$this->error_description = $request->getParameter('openid_error');
			}
		}

	}
