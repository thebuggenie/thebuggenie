<?php

	class soapActions extends TBGAction
	{
		
		public function preExecute(TBGRequest $request, $action)
		{
			$this->getResponse()->setContentType('application/xml');
			$this->getResponse()->setDecoration(TBGResponse::DECORATE_NONE);
			if ($action == 'getWSDL') return;
			TBGLogging::log('turning off wsdl cache');
			ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
			TBGLogging::log('initiating soap server');
			$this->server = new SoapServer(TBGContext::getIncludePath() . "modules/soap/templates/thebuggenie.wsdl");
			TBGContext::loadLibrary('soap');
			$this->server->addFunction('getIssue');
		}
		
		public function doSomething()
		{
			return 'fu';
		}
		
		public function runGetWSDL(TBGRequest $request)
		{
			$this->getResponse()->setTemplate('thebuggenie.wsdl');
		}
		
		public function runSoapHandler(TBGRequest $request)
		{
			$this->server->handle();
			exit();
		}
		
	}

