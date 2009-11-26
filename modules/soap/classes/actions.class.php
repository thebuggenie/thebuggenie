<?php

	class soapActions extends BUGSaction
	{
		
		public function preExecute(BUGSrequest $request, $action)
		{
			$this->getResponse()->setContentType('application/xml');
			$this->getResponse()->setDecoration(BUGSresponse::DECORATE_NONE);
			if ($action == 'getWSDL') return;
			BUGSlogging::log('turning off wsdl cache');
			ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
			BUGSlogging::log('initiating soap server');
			$this->server = new SoapServer(BUGScontext::getIncludePath() . "modules/soap/templates/thebuggenie.wsdl");
			BUGScontext::loadLibrary('soap');
			$this->server->addFunction('getIssue');
		}
		
		public function doSomething()
		{
			return 'fu';
		}
		
		public function runGetWSDL(BUGSrequest $request)
		{
			$this->getResponse()->setTemplate('thebuggenie.wsdl');
		}
		
		public function runSoapHandler(BUGSrequest $request)
		{
			$this->server->handle();
			exit();
		}
		
	}

?>