<?php

    namespace thebuggenie\core\framework;

    use \thebuggenie\core\entities\File;

    /**
     * Request class, used for retrieving request information
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage mvc
     */

    /**
     * Request class, used for retrieving request information
     *
     * @package thebuggenie
     * @subpackage mvc
     */
    class Request implements \ArrayAccess
    {

        const POST = 'post';
        const GET = 'get';
        const PUT = 'put';
        const DELETE = 'delete';

        protected $_request_parameters = array();
        protected $_post_parameters = array();
        protected $_get_parameters = array();
        protected $_files = array();
        protected $_cookies = array();
        protected $_querystring = null;

        protected $_hasfiles = false;

        protected $_is_ajax_call = false;

        /**
         * Handles an uploaded file, stores it to the correct folder, adds an entry
         * to the database and returns a \thebuggenie\core\entities\File object.
         *
         * @param string $key The request parameter the file was sent as
         *
         * @return File The File object
         * @throws \Exception
         */
        public function handleUpload($key)
        {
            $apc_exists = self::CanGetUploadStatus();
            if ($apc_exists && !array_key_exists($this->getParameter('APC_UPLOAD_PROGRESS'), $_SESSION['__upload_status'])) {
                $_SESSION['__upload_status'][$this->getParameter('APC_UPLOAD_PROGRESS')] = array(
                    'id'       => $this->getParameter('APC_UPLOAD_PROGRESS'),
                    'finished' => false,
                    'percent'  => 0,
                    'total'    => 0,
                    'complete' => 0
                );
            }
            try
            {
                if ($this->getUploadedFile($key) !== null)
                {
                    $thefile = $this->getUploadedFile($key);
                    if (Settings::isUploadsEnabled())
                    {
                        Logging::log('Uploads enabled');
                        if ($thefile['error'] == UPLOAD_ERR_OK)
                        {
                            Logging::log('No upload errors');
                            if (filesize($thefile['tmp_name']) > Settings::getUploadsEffectiveMaxSize(true))
                            {
                                throw new \Exception(Context::getI18n()->__('You cannot upload files bigger than %max_size MB', array('%max_size' => Settings::getUploadsEffectiveMaxSize())));
                            }
                            Logging::log('Upload filesize ok');
                            $extension = mb_substr(basename($thefile['name']), mb_strrpos(basename($thefile['name']), '.'));
                            if ($extension == '')
                            {
                                Logging::log('OOps, could not determine upload filetype', 'main', Logging::LEVEL_WARNING_RISK);
                                //throw new \Exception(Context::getI18n()->__('Could not determine filetype'));
                            }
                            else
                            {
                                Logging::log('Checking uploaded file extension');
                                $extension = mb_substr($extension, 1);
                                $upload_extensions = Settings::getUploadsExtensionsList();
                                if (Settings::getUploadsRestrictionMode() == 'blacklist')
                                {
                                    Logging::log('... using blacklist');
                                    foreach ($upload_extensions as $an_ext)
                                    {
                                        if (mb_strtolower(trim($extension)) == mb_strtolower(trim($an_ext)))
                                        {
                                            Logging::log('Upload extension not ok');
                                            throw new \Exception(Context::getI18n()->__('This filetype is not allowed'));
                                        }
                                    }
                                    Logging::log('Upload extension ok');
                                }
                                else
                                {
                                    Logging::log('... using whitelist');
                                    $is_ok = false;
                                    foreach ($upload_extensions as $an_ext)
                                    {
                                        if (mb_strtolower(trim($extension)) == mb_strtolower(trim($an_ext)))
                                        {
                                            Logging::log('Upload extension ok');
                                            $is_ok = true;
                                            break;
                                        }
                                    }
                                    if (!$is_ok)
                                    {
                                        Logging::log('Upload extension not ok');
                                        throw new \Exception(Context::getI18n()->__('This filetype is not allowed'));
                                    }
                                }
                                /*if (in_array(mb_strtolower(trim($extension)), array('php', 'asp')))
                                {
                                    Logging::log('Upload extension is php or asp');
                                    throw new \Exception(Context::getI18n()->__('This filetype is not allowed'));
                                }*/
                            }
                            if (is_uploaded_file($thefile['tmp_name']))
                            {
                                Logging::log('Uploaded file is uploaded');

                                $new_filename = Context::getUser()->getID() . '_' . NOW . '_' . basename($thefile['name']);
                                if (Settings::getUploadStorage() == 'files')
                                {
                                    $files_dir = Settings::getUploadsLocalpath();
                                    $filename = $files_dir.$new_filename;
                                }
                                else
                                {
                                    $filename = $thefile['tmp_name'];
                                }
                                Logging::log('Moving uploaded file to '.$filename);
                                if (Settings::getUploadStorage() == 'files' && !move_uploaded_file($thefile['tmp_name'], $filename))
                                {
                                    Logging::log('Moving uploaded file failed!');
                                    throw new \Exception(Context::getI18n()->__('An error occured when saving the file'));
                                }
                                else
                                {
                                    Logging::log('Upload complete and ok, storing upload status and returning filename '.$new_filename);
                                    $content_type = File::getMimeType($filename);
                                    $file = new File();
                                    $file->setRealFilename($new_filename);
                                    $file->setOriginalFilename(basename($thefile['name']));
                                    $file->setContentType($content_type);
                                    $file->setDescription($this->getParameter($key.'_description'));
                                    $file->setUploadedBy(Context::getUser());
                                    if (Settings::getUploadStorage() == 'database')
                                    {
                                        $file->setContent(file_get_contents($filename));
                                    }
                                    $file->save();
                                    if ($apc_exists)
                                    {
                                        $_SESSION['__upload_status'][$this->getParameter('APC_UPLOAD_PROGRESS')] = array(
                                            'id'       => $this->getParameter('APC_UPLOAD_PROGRESS'),
                                            'finished' => true,
                                            'percent'  => 100,
                                            'total'    => 0,
                                            'complete' => 0,
                                            'file_id'  => $file->getID()
                                        );
                                    }
                                    return $file;
                                }
                            }
                            else
                            {
                                Logging::log('Uploaded file was not uploaded correctly');
                                throw new \Exception(Context::getI18n()->__('The file was not uploaded correctly'));
                            }
                        }
                        else
                        {
                            Logging::log('Upload error: '.$thefile['error']);
                            switch ($thefile['error'])
                            {
                                case UPLOAD_ERR_INI_SIZE:
                                case UPLOAD_ERR_FORM_SIZE:
                                    throw new \Exception(Context::getI18n()->__('You cannot upload files bigger than %max_size MB', array('%max_size' => Settings::getUploadsEffectiveMaxSize())));
                                case UPLOAD_ERR_PARTIAL:
                                    throw new \Exception(Context::getI18n()->__('The upload was interrupted, please try again'));
                                case UPLOAD_ERR_NO_FILE:
                                    throw new \Exception(Context::getI18n()->__('No file was uploaded'));
                                default:
                                    throw new \Exception(Context::getI18n()->__('An unhandled error occured') . ': ' . $thefile['error']);
                            }
                        }
                    }
                    else
                    {
                        Logging::log('Uploads not enabled');
                        throw new \Exception(Context::getI18n()->__('Uploads are not enabled'));
                    }
                }
                Logging::log('Could not find uploaded file' . $key);
                throw new \Exception(Context::getI18n()->__('Could not find the uploaded file. Please make sure that it is not too big.'));
            }
            catch (\Exception $e)
            {
                Logging::log('Upload exception: '.$e->getMessage());
                if ($apc_exists)
                {
                    $_SESSION['__upload_status'][$this->getParameter('APC_UPLOAD_PROGRESS')]['error'] = $e->getMessage();
                    $_SESSION['__upload_status'][$this->getParameter('APC_UPLOAD_PROGRESS')]['finished'] = true;
                    $_SESSION['__upload_status'][$this->getParameter('APC_UPLOAD_PROGRESS')]['percent'] = 100;
                }
                throw $e;
            }
        }

        public static function CanGetUploadStatus()
        {
            if (!extension_loaded('apc'))
                return false;

            if (!function_exists('apc_fetch'))
                return false;

            return ini_get('apc.enabled') && ini_get('apc.rfc1867');
        }

        public function markUploadAsFinishedWithError($id, $error)
        {
            $_SESSION['__upload_status'][$id] = array(
                'id'       => $id,
                'finished' => true,
                'percent'  => 100,
                'total'    => 0,
                'complete' => 0,
                'error'    => $error
            );
        }

        public function getUploadStatus($id)
        {
            Logging::log('sanitizing id');
            // sanitize the ID value
            $id = preg_replace('/[^a-z0-9]/i', '', $id);
            if (mb_strlen($id) == 0)
            {
                Logging::log('oops, invalid id '. $id);
                return;
            }

            // ensure the uploaded status data exists in the session
            if (!array_key_exists($id, $_SESSION['__upload_status']))
            {
                Logging::log('upload with this id ' .$id. ' is not in progress yet');
                $_SESSION['__upload_status'][$id] = array(
                    'id'       => $id,
                    'finished' => false,
                    'percent'  => 0,
                    'total'    => 0,
                    'complete' => 0
                );
            }

            // retrieve the data from the session so it can be updated and returned
            $ret = $_SESSION['__upload_status'][$id];

            // if we can't retrieve the status or the upload has finished just return
            if (!self::CanGetUploadStatus() || $ret['finished'])
            {
                Logging::log('upload either finished or we cant track it');
//                $ret['finished'] = true;
//                $ret['percent'] = 100;
//                $ret['complete'] = 100;
                return $ret;
            }

            // retrieve the upload data from APC
            $status = apc_fetch('upload_' . $id);

            // false is returned if the data isn't found
            if ($status) {
                $ret['finished'] = (bool) $status['done'];
                $ret['total']    = $status['total'];
                $ret['complete'] = $status['current'];
                if (array_key_exists('file_id', $ret))
                {
                    $status['file_id'] = $ret['file_id'];
                }
                elseif (array_key_exists('error', $ret))
                {
                    $status['failed'] = true;
                    $status['error'] = $ret['error'];
                }

                // calculate the completed percentage
                if ($ret['total'] > 0)
                    $ret['percent'] = $ret['complete'] / $ret['total'] * 100;

                // write the changed data back to the session
                $_SESSION['__upload_status'][$id] = $ret;
            }

            return $ret;
        }

        /**
         * Sanitizes a given parameter and returns it
         *
         * @param mixed $params
         *
         * @return mixed
         */
        protected function __sanitize_params($params)
        {
            if (is_array($params))
            {
                foreach ($params as $key => $param)
                {
                    if (is_string($param))
                    {
                        $params[$key] = $this->__sanitize_string($param);
                    }
                    elseif (is_array($param))
                    {
                        $params[$key] = $this->__sanitize_params($param);
                    }
                }
            }
            elseif (is_string($params))
            {
                $params = $this->__sanitize_string($params);
            }
            return $params;
        }

        /**
         * Sets up the request object and initializes and assigns the correct
         * variables
         */
        public function __construct()
        {
            if (get_magic_quotes_gpc())
            {
                $strip_gpc = function(&$value)
                {
                    $value = stripslashes($value);
                };
                foreach (array($_GET, $_POST, $_COOKIE, $_REQUEST) as $inputarray)
                {
                    array_walk_recursive($inputarray, $strip_gpc);
                }
            }

            foreach ($_COOKIE as $key => $value)
            {
                $this->_cookies[$key] = $value;
            }
            foreach ($_POST as $key => $value)
            {
                $this->_post_parameters[$key] = $value;
                $this->_request_parameters[$key] = $value;
            }
            foreach ($_GET as $key => $value)
            {
                $this->_get_parameters[$key] = $value;
                $this->_request_parameters[$key] = $value;
            }
            foreach ($_FILES as $key => $file)
            {
                $this->_files[$key] = $file;
                $this->_hasfiles = true;
            }
            $this->_is_ajax_call = (array_key_exists("HTTP_X_REQUESTED_WITH", $_SERVER) && mb_strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == 'xmlhttprequest');

            if (isset($_SESSION) && !array_key_exists('__upload_status', $_SESSION))
            {
                $_SESSION['__upload_status'] = array();
            }

            $this->_querystring = array_key_exists('QUERY_STRING', $_SERVER) ? $_SERVER['QUERY_STRING'] : '';

        }

        public function hasFileUploads()
        {
            return (bool) $this->_hasfiles;
        }

        public function getUploadedFile($key)
        {
            if (isset($this->_files[$key]))
            {
                return $this->_files[$key];
            }
            return null;
        }

        public function getUploadedFiles()
        {
            return $this->_files;
        }

        /**
         * Get all parameters from the request
         *
         * @return array
         */
        public function getParameters()
        {
            return array_diff_key($this->_request_parameters, array('url' => null));;
        }

        /**
         * Get a parameter from the request
         *
         * @param string $key The parameter you want to retrieve
         * @param mixed $default_value The value to return if it doesn't exist
         * @param boolean $sanitized Whether to sanitize strings or not
         *
         * @return mixed
         */
        public function getParameter($key, $default_value = null, $sanitized = true)
        {
            if (isset($this->_request_parameters[$key]))
            {
                if ($sanitized && is_string($this->_request_parameters[$key]))
                {
                    return $this->__sanitize_string($this->_request_parameters[$key]);
                }
                elseif ($sanitized)
                {
                    return $this->__sanitize_params($this->_request_parameters[$key]);
                }
                else
                {
                    return $this->_request_parameters[$key];
                }
            }
            else
            {
                return $default_value;
            }
        }

        /**
         * Retrieve an unsanitized request parameter
         *
         * @see getParameter
         *
         * @param string $key The parameter you want to retrieve
         * @param mixed $default_value [optional] The value to return if it doesn't exist
         *
         * @return mixed
         */
        public function getRawParameter($key, $default_value = null)
        {
            return $this->getParameter($key, $default_value, false);
        }

        /**
         * Retrieve a cookie
         *
         * @param string $key The cookie to retrieve
         * @param mixed $default_value The value to return if it doesn't exist
         *
         * @return mixed
         */
        public function getCookie($key, $default_value = null)
        {
            return (isset($this->_cookies[$key])) ? $this->_cookies[$key] : $default_value;
        }

        /**
         * Check to see if a request parameter is set
         *
         * @param string $key The parameter to check for
         *
         * @return boolean
         */
        public function hasParameter($key)
        {
            return array_key_exists($key, $this->_request_parameters);
        }

        /**
         * Check to see if a cookie is set
         *
         * @param string $key The cookie to check for
         *
         * @return boolean
         */
        public function hasCookie($key)
        {
            return (bool) ($this->getCookie($key) !== null);
        }

        /**
         * Set a request parameter
         *
         * @param string $key The parameter to set
         * @param mixed $value The value to set it too
         */
        public function setParameter($key, $value)
        {
            $this->_request_parameters[$key] = $value;
        }

        /**
         * Get the current request method
         *
         * @return integer
         */
        public function getMethod()
        {
            switch (mb_strtolower($_SERVER['REQUEST_METHOD']))
            {
                case 'get':
                    return self::GET;
                    break;
                case 'post':
                    return self::POST;
                    break;
                case 'delete':
                    return self::DELETE;
                    break;
                case 'put':
                    return self::PUT;
                    break;
            }
        }

        /**
         * Check if the current request method is $method
         *
         * @param $method
         *
         * @return boolean
         */
        public function isMethod($method)
        {
            return ($this->getMethod() == $method) ? true : false;
        }

        public function isPost()
        {
            return $this->isMethod(self::POST);
        }

        public function isPut()
        {
            return ($this->isMethod(self::PUT) || ($this->isPost() && mb_strtolower($this['_method'] == 'put')));
        }

        public function isDelete()
        {
            return ($this->isMethod(self::DELETE) || ($this->isPost() && mb_strtolower($this['_method'] == 'delete')));
        }

        /**
         * Check if the current request is an ajax call
         *
         * @return boolean
         */
        public function isAjaxCall()
        {
            return $this->_is_ajax_call;
        }

        /**
         * Sanitize a string
         *
         * @param string $string The string to sanitize
         *
         * @return string the sanitized string
         */
        protected function __sanitize_string($string)
        {
            try
            {
                $charset = (class_exists('\thebuggenie\core\framework\Context')) ? Context::getI18n()->getCharset() : 'utf-8';
            }
            catch (\Exception $e)
            {
                $charset = 'utf-8';
            }
            return htmlspecialchars($string, ENT_QUOTES, $charset);
        }

        /**
         * Wrapper around __sanitize_string method
         *
         * @param string $string The string to sanitize
         *
         * @return string the sanitized string
         */
        public function sanitize_input($string)
        {
            return $this->__sanitize_string($string);
        }

        public function getRequestedFormat()
        {
            return $this->getParameter('format', 'html');
        }

        public function offsetExists($offset)
        {
            return $this->hasParameter($offset);
        }

        public function offsetGet($offset)
        {
            return $this->getParameter($offset);
        }

        public function offsetSet($offset, $value)
        {
            $this->setParameter($offset, $value);
        }

        public function offsetUnset($offset)
        {
            $this->setParameter($offset, null);
        }

        public function getQueryString()
        {
            return $this->_querystring;
        }

    }
