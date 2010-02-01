<?php

/*
    FREE TO USE
    Simple OpenID PHP Class
    Contributed by http://www.fivestores.com/
    
    Some modifications by Eddie Roosenmaallen, eddie@roosenmaallen.com
    Some OpenID 2.0 specifications added by Steve Love (stevelove.org)
    
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

This Class was written to make easy for you to integrate OpenID on your website. 
This is just a client, which checks for user's identity. This Class Requires CURL Module.
It should be easy to use some other HTTP Request Method, but remember, often OpenID servers
are using SSL.
We need to be able to perform SSL Verification on the background to check for valid signature.

HOW TO USE THIS CLASS:
  STEP 1)
    $openid = new SimpleOpenID;
    :: SET IDENTITY ::
        $openid->SetIdentity($_POST['openid_url']);
    :: SET RETURN URL ::
        $openid->SetApprovedURL('http://www.yoursite.com/return.php'); // Script which handles a response from OpenID Server
    :: SET TRUST ROOT ::
        $openid->SetTrustRoot('http://www.yoursite.com/');
    :: FETCH SERVER URL FROM IDENTITY PAGE ::  [Note: It is recomended to cache this (Session, Cookie, Database)]
        $openid->GetOpenIDServer(); // Returns false if server is not found
    :: REDIRECT USER TO OPEN ID SERVER FOR APPROVAL ::
    
    :: (OPTIONAL) SET OPENID SERVER ::
        $openid->SetOpenIDServer($server_url); // If you have cached previously this, you don't have to call GetOpenIDServer and set value this directly
        
    STEP 2)
    Once user gets returned we must validate signature
    :: VALIDATE REQUEST ::
        true|false = $openid->ValidateWithServer();
        
    ERRORS:
        array = $openid->GetError();     // Get latest Error code
    
    FIELDS:
        OpenID allowes you to retreive a profile. To set what fields you'd like to get use (accepts either string or array):
        $openid->SetRequiredFields(array('email','fullname','dob','gender','postcode','country','language','timezone'));
         or
        $openid->SetOptionalFields('postcode');
        
IMPORTANT TIPS:
OPENID as is now, is not trust system. It is a great single-sign on method. If you want to 
store information about OpenID in your database for later use, make sure you handle url identities
properly.
  For example:
    https://steve.myopenid.com/
    https://steve.myopenid.com
    http://steve.myopenid.com/
    http://steve.myopenid.com
    ... are representing one single user. Some OpenIDs can be in format openidserver.com/users/user/ - keep this in mind when storing identities

    To help you store an OpenID in your DB, you can use function:
        $openid_db_safe = $openid->OpenID_Standarize($upenid);
    This may not be comatible with current specs, but it works in current enviroment. Use this function to get openid
    in one format like steve.myopenid.com (without trailing slashes and http/https).
    Use output to insert Identity to database. Don't use this for validation - it may fail.

*/

class SimpleOpenID
{
    protected $openid_url_identity;
    protected $URLs = array();
    protected $error = array();
    protected $fields = array('required' => array(), 'optional' => array());
	protected $openid_version = '2.0';
	protected $openid_ns = "http://specs.openid.net/auth/2.0";
    
    var $arr_ax_types = array(
                      'nickname' => 'http://axschema.org/namePerson/friendly',
                      'email'    => 'http://axschema.org/contact/email',
                      'fullname' => 'http://axschema.org/namePerson',
                      'dob'      => 'http://axschema.org/birthDate',
                      'gender'   => 'http://axschema.org/person/gender',
                      'postcode' => 'http://axschema.org/contact/postalCode/home',
                      'country'  => 'http://axschema.org/contact/country/home',
                      'language' => 'http://axschema.org/pref/language',
                      'timezone' => 'http://axschema.org/pref/timezone',
                      'prefix'   => 'http://axschema.org/namePerson/prefix',
                      'firstname' => 'http://axschema.org/namePerson/first',
                      'lastname'  => 'http://axschema.org/namePerson/last',
                      'suffix'    => 'http://axschema.org/namePerson/suffix'
                    );
    
    function __construct()
	{
        if (!function_exists('curl_exec'))
		{
            die('Error: Class SimpleOpenID requires curl extension to work');
        }
    }
    
    function SetOpenIDServer($a)
	{
        $this->URLs['openid_server'] = $a;
    }
    
    function SetServiceType($a)
	{
        // Hopefully the provider is using OpenID 2.0 but let's check
        // the protocol version in order to handle backwards compatibility.
        // Probably not the best method, but it works for now.
        if (stristr($a, "2.0"))
		{
            $this->openid_ns = "http://specs.openid.net/auth/2.0";
            $this->openid_version = "2.0";
        }
        else if (stristr($a, "1.1"))
		{
            $this->openid_ns = "http://openid.net/signon/1.1";
            $this->openid_version = "1.1";
        }
		else
		{
            $this->openid_ns = "http://openid.net/signon/1.0";
            $this->openid_version = "1.0";
        }
    }
    
    function SetTrustRoot($a)
	{
        $this->URLs['trust_root'] = $a;
    }
    
    function SetCancelURL($a)
	{
        $this->URLs['cancel'] = $a;
    }
    
    function SetApprovedURL($a)
	{
        $this->URLs['approved'] = $a;
    }
    
    function SetRequiredFields($a)
	{
        if (is_array($a))
		{
            $this->fields['required'] = $a;
        }
		else
		{
            $this->fields['required'][] = $a;
        }
    }
    
    function SetOptionalFields($a)
	{
        if (is_array($a))
		{
            $this->fields['optional'] = $a;
        }
		else
		{
            $this->fields['optional'][] = $a;
        }
    }
    
    function SetPapePolicies($a)
	{
        if (is_array($a))
		{
            $this->fields['pape_policies'] = $a;
        }
		else
		{
            $this->fields['pape_policies'][] = $a;
        }
    }
    
    function SetPapeMaxAuthAge($a)
	{
        // Numeric value greater than or equal to zero in seconds
        // How much time should the user be given to authenticate?
        if (preg_match("/^[1-9]+[0-9]*$/",$a))
		{
            $this->fields['pape_max_auth_age'] = $a;
        }
		else
		{
            die('Error: SetPapeMaxAuthAge requires a numeric value greater than zero.');
        }
    }
    
    function SetIdentity($a)
	{     // Set Identity URL
        /* XRI support not ready yet.
        $xriIdentifiers = array('=', '$', '!', '@', '+');
        $xriProxy = 'http://xri.net/';

        // Is this an XRI string?
        // Check for "xri://" prefix or XRI Global Constant Symbols
        if (stripos($a, 'xri://') || in_array($a[0], $xriIdentifiers)){    
            // Attempts to convert an XRI into a URI by removing the "xri://" prefix and
            // appending the remainder to the URI of an XRI proxy such as "http://xri.net"
            if (stripos($a, 'xri://') == 0) {
                if (stripos($a, 'xri://$ip*') == 0) {
                    $a = substr($a, 10);
                } elseif (stripos($a, 'xri://$dns*') == 0) {
                    $a = substr($a, 11);
                } else {
                    $a = substr($a, 6);
                }
            }
                $a = $xriProxy.$a;
        }*/

        if ((stripos($a, 'http://') === false) && (stripos($a, 'https://') === false))
		{
            $a = 'http://'.$a;
        }
        if (stripos($a, 'gmail') || stripos($a, 'google'))
		{
           $a = "https://www.google.com/accounts/o8/id";
        }        
        $this->openid_url_identity = $a;/*var_dump($this->openid_url_identity);die();*/
    }
    
    function GetIdentity()
	{     // Get Identity
        return $this->openid_url_identity;
    }
    
    function GetError()
	{
        $e = $this->error;
        return array('code'=>$e[0],'description'=>$e[1]);
    }

    function ErrorStore($code, $desc = null)
	{
        $errs['OPENID_NOSERVERSFOUND'] = 'Cannot find OpenID Server TAG on Identity page.';
        if ($desc == null)
		{
            $desc = $errs[$code];
        }
        $this->error = array($code,$desc);
    }

    function IsError()
	{
        if (count($this->error) > 0)
		{
            return true;
        }
		else
		{
            return false;
        }
    }
    
    function splitResponse($response)
	{
        $r = array();
        $response = explode("\n", $response);
        foreach ($response as $line)
		{
            $line = trim($line);
            if ($line != "")
			{
                list($key, $value) = explode(":", $line, 2);
                $r[trim($key)] = trim($value);
            }
        }
		return $r;
    }
    
    function OpenID_Standarize($openid_identity = null)
	{
        if ($openid_identity === null)
		{
			$openid_identity = $this->openid_url_identity;
		}

        $u = parse_url(strtolower(trim($openid_identity)));
        
        if (!isset($u['path']) || ($u['path'] == '/'))
		{
            $u['path'] = '';
        }
        
		if (substr($u['path'],-1,1) == '/')
		{
            $u['path'] = substr($u['path'], 0, strlen($u['path'])-1);
        }
        if (isset($u['query']))
		{ // If there is a query string, then use identity as is
            return $u['host'] . $u['path'] . '?' . $u['query'];
        }
		else
		{
            return $u['host'] . $u['path'];
        }
    }
    
    function array2url($arr)
	{ // converts associated array to URL Query String
        if (!is_array($arr))
		{
            return false;
        }
        $query = '';
        foreach ($arr as $key => $value)
		{
            $query .= $key . "=" . $value . "&";
        }
        return $query;
    }

    function FSOCK_Request($url, $method="GET", $params = "")
	{
        $fp = fsockopen("ssl://www.myopenid.com", 443, $errno, $errstr, 3); // Connection timeout is 3 seconds
        if (!$fp)
		{
            $this->ErrorStore('OPENID_SOCKETERROR', $errstr);
               return false;
        } 
		else
		{
            $request = $method . " /server HTTP/1.0\r\n";
            $request .= "User-Agent: Simple OpenID PHP Class (http://www.phpclasses.org/simple_openid)\r\n";
            $request .= "Connection: close\r\n\r\n";
               fwrite($fp, $request);
               stream_set_timeout($fp, 4); // Connection response timeout is 4 seconds
               $res = fread($fp, 2000);
               $info = stream_get_meta_data($fp);
               fclose($fp);
        
               if ($info['timed_out']) 
				{
				   $this->ErrorStore('OPENID_SOCKETTIMEOUT');
               } 
			   else
				{
                  return $res;
               }
        }
    }
    function CURL_Request($url, $method="GET", $params = "")
	{ // Remember, SSL MUST BE SUPPORTED
            if (is_array($params))
			{
				$params = $this->array2url($params);
			}
            $curl = curl_init($url . ($method == "GET" && $params != "" ? "?" . $params : ""));
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_HTTPGET, ($method == "GET"));
            curl_setopt($curl, CURLOPT_POST, ($method == "POST"));
            if ($method == "POST") curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl);
            
            if (curl_errno($curl) == 0)
			{
                $response;
            }
			else
			{
                $this->ErrorStore('OPENID_CURL', curl_error($curl));
            }
            return $response;
    }
    
    function HTML2OpenIDServer($content)
	{
        $ret = array();
        
        // Get details of their OpenID server and (optional) delegate
        preg_match_all('/<link[^>]*rel=[\'"]openid.server[\'"][^>]*href=[\'"]([^\'"]+)[\'"][^>]*\/?>/i', $content, $matches1);
        preg_match_all('/<link[^>]*rel=[\'"]openid2.provider[\'"][^>]*href=[\'"]([^\'"]+)[\'"][^>]*\/?>/i', $content, $matches2);
        preg_match_all('/<link[^>]*href=\'"([^\'"]+)[\'"][^>]*rel=[\'"]openid.server[\'"][^>]*\/?>/i', $content, $matches3);
        preg_match_all('/<link[^>]*href=\'"([^\'"]+)[\'"][^>]*rel=[\'"]openid2.provider[\'"][^>]*\/?>/i', $content, $matches4);
        $servers = array_merge($matches1[1], $matches2[1], $matches3[1], $matches4[1]);
        
        preg_match_all('/<link[^>]*rel=[\'"]openid.delegate[\'"][^>]*href=[\'"]([^\'"]+)[\'"][^>]*\/?>/i', $content, $matches1);
        preg_match_all('/<link[^>]*rel=[\'"]openid2.local_id[\'"][^>]*href=[\'"]([^\'"]+)[\'"][^>]*\/?>/i', $content, $matches2);
        preg_match_all('/<link[^>]*href=[\'"]([^\'"]+)[\'"][^>]*rel=[\'"]openid.delegate[\'"][^>]*\/?>/i', $content, $matches3);
        preg_match_all('/<link[^>]*href=[\'"]([^\'"]+)[\'"][^>]*rel=[\'"]openid2.local_id[\'"][^>]*\/?>/i', $content, $matches4);
        
        $delegates = array_merge($matches1[1], $matches2[1], $matches3[1], $matches4[1]);
        
        $ret = array($servers, $delegates);
        return $ret;
    }
    
    function GetOpenIDServer()
	{
        
        //Try Yadis Protocol discovery first
        $http_response = array();
        $fetcher = Services_Yadis_Yadis::getHTTPFetcher();
        $yadis_object = Services_Yadis_Yadis::discover($this->openid_url_identity, $http_response, $fetcher);
        
        // Yadis object is returned if discovery is successful
        if ($yadis_object != null)
		{
            $service_list = $yadis_object->services();
            $types = $service_list[0]->getTypes();
            $servers = $service_list[0]->getURIs();
			//var_dump($yadis_object);die();
            $delegates = $service_list[0]->getElements('openid:Delegate');
        }
		else
		{ // Else try HTML discovery
            $response = $this->CURL_Request($this->openid_url_identity);
            list($servers, $delegates) = $this->HTML2OpenIDServer($response);
        }
        if (count($servers) == 0)
		{
            $this->ErrorStore('OPENID_NOSERVERSFOUND');
            return false;
        }
        if (isset($types[0]) && ($types[0] != ""))
		{
            $this->SetServiceType($types[0]);
        }
        if (isset($delegates[0]) && ($delegates[0] != ""))
		{
            $this->SetIdentity($delegates[0]);
        }
        $this->SetOpenIDServer($servers[0]);
        return $servers[0];
    }
    
    function GetRedirectURL()
	{
        $params = array();
        
        $params['openid.return_to'] = urlencode($this->URLs['approved']);
        $params['openid.identity']  = urlencode($this->openid_url_identity);
        
        if ($this->openid_version == "2.0")
		{
            
            $params['openid.ns']         = urlencode($this->openid_ns);
            $params['openid.claimed_id'] = urlencode("http://specs.openid.net/auth/2.0/identifier_select");
            $params['openid.identity']   = urlencode("http://specs.openid.net/auth/2.0/identifier_select");
            $params['openid.realm']      = urlencode($this->URLs['trust_root']);
            
        }
		else
		{
            $params['openid.trust_root'] = urlencode($this->URLs['trust_root']);
        }
        
        $params['openid.mode'] = 'checkid_setup';
        
        // User Info Request: Setup
        if (isset($this->fields['required']) || isset($this->fields['optional']))
		{
            $params['openid.ns.ax']   = "http://openid.net/srv/ax/1.0";
            $params['openid.ax.mode'] = "fetch_request";
            $params['openid.ns.sreg'] = "http://openid.net/extensions/sreg/1.1";
        }
        
        // MyOpenID.com is using an outdated AX schema URI
        if (stristr($this->URLs['openid_server'], 'myopenid.com'))
		{
            $this->arr_ax_types = preg_replace("/axschema.org/","schema.openid.net",$this->arr_ax_types);
        }
        
        // User Info Request: Required data
        if (isset($this->fields['required']) && (count($this->fields['required']) > 0))
		{
            // Set required params for Attribute Exchange (AX) protocol
            $params['openid.ax.required']   = implode(',',$this->fields['required']);;
            foreach ($this->fields['required'] as $field)
			{
				if (array_key_exists($field,$this->arr_ax_types))
				{
					$params["openid.ax.type.$field"] = urlencode($this->arr_ax_types[$field]);
				}
            }
            // Set required params for Simple Registration (SREG) protocol
            $params['openid.sreg.required'] = implode(',',$this->fields['required']);
        }
        
        // User Info Request: Optional data
        if (isset($this->fields['optional']) && (count($this->fields['optional']) > 0))
		{
            // Set optional params for Attribute Exchange (AX) protocol
            $params['openid.ax.if_available'] = implode(',',$this->fields['optional']);
            foreach ($this->fields['optional'] as $field)
			{
				if (array_key_exists($field,$this->arr_ax_types))
				{
					$params["openid.ax.type.$field"] = urlencode($this->arr_ax_types[$field]);
				}
            }
            // Set optional params for Simple Registration (SREG) protocol
            $params['openid.sreg.optional'] = implode(',',$this->fields['optional']);
        }
        
        // Add PAPE params if exists
        if (isset($this->fields['pape_policies']) && (count($this->fields['pape_policies']) > 0))
		{
            $params['openid.ns.pape'] = "http://specs.openid.net/extensions/pape/1.0";
            $params['openid.pape.preferred_auth_policies'] = urlencode(implode(' ',$this->fields['pape_policies']));
            if ($this->fields['pape_max_auth_age'])
			{
                $params['openid.pape.max_auth_age'] = $this->fields['pape_max_auth_age'];
            }
        }
        
        $urlJoiner = (strstr($this->URLs['openid_server'], "?")) ? "&" : "?";
        
        return $this->URLs['openid_server'] . $urlJoiner . $this->array2url($params);
    }

    public function Redirect()
	{
		TBGContext::getResponse()->headerRedirect($this->GetRedirectURL());
        /*if (headers_sent())
		{ // Use JavaScript to redirect if content has been previously sent (not recommended, but safe)
            echo '<script language="JavaScript" type="text/javascript">window.location=\'';
            echo $redirect_to;
            echo '\';</script>';
        }
		else
		{    // Default Header Redirect
            header('Location: ' . $redirect_to);
        }*/
    }
    
    function ValidateWithServer()
	{
    
        $params = array();
        
        // Find keys that include dots and set them aside
        preg_match_all("/([\w]+[\.])/",$_GET['openid_signed'],$arr_periods);
        $arr_periods = array_unique(array_shift($arr_periods));
        
        // Duplicate the dot keys, but replace the dot with an underscore
        $arr_underscores = preg_replace("/\./","_",$arr_periods);
        
        $arr_getSignedKeys    = explode(",",str_replace($arr_periods, $arr_underscores, $_GET['openid_signed']));
        
        // Send only required parameters to confirm validity
        foreach ($arr_getSignedKeys as $key)
		{
            $paramKey = str_replace($arr_underscores, $arr_periods, $key);
            $params["openid.$paramKey"] = urlencode($_GET["openid_$key"]);
        }
        if ($this->openid_version != "2.0")
		{
            $params['openid.assoc_handle'] = urlencode($_GET['openid_assoc_handle']);
            $params['openid.signed']       = urlencode($_GET['openid_signed']);
        }
        $params['openid.sig']  = urlencode($_GET['openid_sig']);
        $params['openid.mode'] = "check_authentication";
        
        $openid_server = $this->GetOpenIDServer();
        if ($openid_server == false)
		{
            return false;
        }
        $response = $this->CURL_Request($openid_server,'POST',$params);
        $data = $this->splitResponse($response);
        
		if ($data['is_valid'] == "true")
		{
            return true;
        }
		else
		{
            return false;
        }
    }
}