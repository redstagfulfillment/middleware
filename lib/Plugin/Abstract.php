<?php

/**
 * Abstract class for a plugin
 */
abstract class Plugin_Abstract implements Plugin_Interface
{
    /** @var string */
    private $code;
    
    /** @var Middleware */
    private $middleware;

    /** @var null|Middleware_JsonClient */
    private $_client;

    /** @var bool */
    private $_isDebug = FALSE;

    /**
     * @param string $code
     */
    final public function __construct($code)
    {
        $this->code = $code;
    }

    /*
     * Abstract methods which may be overridden by plugins
     */

    /**
     * @param array $query
     * @param array $headers
     * @param string $data
     * @return bool
     */
    public function verifyWebhook($query, $headers, $data)
    {
        return FALSE;
    }

    /**
     * @param $query
     * @param $headers
     * @param $data
     * @return bool
     */
    public function handleWebhook($query, $headers, $data)
    {
        return FALSE;
    }

    /**
     * @param array $request
     * @return void
     */
    public function oauthHandleRedirect($request) {}

    /**
     * @param array $params
     * @return string
     */
    public function oauthGetRedirectUrl($params = array())
    {
        $params = array_merge(
            $params,
            ['plugin' => $this->code],
            ['action' => 'redirect']
        );
        $query = [];
        foreach ($params as $key => $value) {
            $query[] = $key.'/'.$value;
        }
        return $this->_getBaseUrl().'oauth.php/'.implode('/', $query).'/';
    }

    /**
     * Get the button to setup the OAuth connection
     *
     * @param array $connectParams
     * @param array $redirectParams
     * @return string
     */
    public function oauthGetConnectButton($connectParams = array(), $redirectParams = array()) {}

    /**
     * Get the button to disconnect from OAuth
     *
     * @param array $params
     * @return void
     */
    public function oauthDisconnect($params = array()) {}

    /**
     * @param string $accessToken
     * @return mixed
     * @throws Exception
     */
    public function oauthSetTokenData($accessToken)
    {
        return $this->setState('oauth_access_token', $accessToken);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function oauthGetTokenData()
    {
        return $this->getState('oauth_access_token');
    }

    /**
     * @return void
     * @throws Exception
     */
    public function oauthValidateConfig() {}

    /**
     * @return mixed
     * @throws Exception
     */
    public function oauthTest() {}

    /*
     * Available helper methods which CANNOT be overridden by plugins
     */

    /**
     * Respond to webhook early to avoid timeouts
     */
    final public function yieldWebhook()
    {
        $this->middleware->yieldWebhook();
    }

    /**
     * Wrapper for "call" method
     *
     * @param string $method
     * @param array $args
     * @return mixed
     * @throws Exception
     */
    final public function call($method, $args = array())
    {
        return $this->_getClient()->call($method, $args, TRUE);
    }

    /**
     * @param array|string $data
     * @param int|string|array|stdClass|null $value
     * @return mixed
     * @throws Exception
     */
    final public function setState($data, $value = NULL)
    {
        if (is_string($data)) {
            $data = $this->code.'_'.$data;
        } elseif (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$this->code.'_'.$k] = $v;
                unset($data[$k]);
            }
        }
        return $this->call('state.set', array('data' => $data, 'value' => $value));
    }

    /**
     * @param array|string $keys
     * @return array|string
     * @throws Exception
     */
    final public function getState($keys)
    {
        if (is_string($keys)) {
            $keys = $this->code.'_'.$keys;
        } elseif (is_array($keys)) {
            $keys = array_map(function($key){ return $this->code.'_'.$key; }, $keys);
        }
        $data = $this->call('state.get', array($keys));
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $_k = preg_replace("/^{$this->code}_/", '', $k);
                $data[$_k] = $data[$k];
                unset($data[$k]);
            }
        }
        return $this->call('state.get', array($keys));
    }

    /**
     * Retrieve config value
     *
     * @param string $path
     * @return null|string
     * @throws Exception
     */
    final public function getConfig($path)
    {
        return $this->middleware->getConfig("plugin/{$this->code}/$path");
    }

    /**
     * Retrieve plugin information
     *
     * @param string $path
     * @return null|string|Varien_Simplexml_Element[]
     * @throws Exception
     */
    final public function getPluginInfo($path)
    {
        return $this->middleware->getPluginInfo("{$this->code}/$path");
    }

    /**
     * Log messages
     *
     * @param string  $message
     * @param integer $level
     * @param string  $file
     * @return void
     */
    final public function log($message, $level = NULL, $file = NULL)
    {
        $this->middleware->log($message, $file);
    }

    /**
     * Write exception to log
     *
     * @param Exception $e
     * @return void
     */
    final public function logException(Exception $e)
    {
        $this->middleware->logException($e);
    }

    /**
     * Retrieve OAuth url
     *
     * @param array $params
     * @return string
     */
    final public function oauthGetUrl($params = array())
    {
        $params = array_merge(
            $params,
            ['plugin' => $this->code]
        );
        return $this->_getBaseUrl().'oauth.php?'.http_build_query($params, '', '&');
    }

    /**
     * Retrieve callback url
     *
     * @param string $method
     * @return string
     * @throws Exception
     */
    final public function getCallbackUrl($method)
    {
        $params = [
            'plugin' => $this->code,
            'method' => $method,
            'secret_key' => $this->middleware->getConfig('middleware/api/secret_key'),
        ];
        return $this->_getBaseUrl().'rpc.php?'.http_build_query($params, '', '&');
    }

    /**
     * @param string $key
     * @return array|null|string
     */
    final protected function loadCache($key)
    {
        return $this->middleware->loadCache($key);
    }

    /**
     * @param string $key
     * @param string $data
     * @param bool|int $lifeTime
     * @throws Exception
     */
    final protected function saveCache($key, $data, $lifeTime = FALSE)
    {
        $this->middleware->saveCache($key, $data, $lifeTime);
    }

    /**
     * Remove Cache matching key
     * @param $key
     */
    final protected function removeCache($key)
    {
        $this->middleware->removeCache($key);
    }

    /*
     * DO NOT USE METHODS DECLARED BELOW THIS LINE
     */

    /**
     * @param Middleware $middleware
     */
    final public function _setMiddleware(Middleware $middleware)
    {
        $this->middleware = $middleware;
    }

    /**
     * @param null|bool $isDebug
     * @return bool
     */
    final public function _isDebug($isDebug = NULL)
    {
        $result = $this->_isDebug;
        if ( ! is_null($isDebug)) {
            $this->_isDebug = (bool) $isDebug;
        }
        return $result;
    }

    /**
     * Retrieve instance of the JSON client
     *
     * @return Middleware_JsonClient
     * @throws Exception
     */
    final private function _getClient()
    {
        if ( ! $this->_client) {
            $this->_client = new Middleware_JsonClient(
                array(
                    'base_url'  => $this->middleware->getConfig('middleware/api/base_url'),
                    'login'     => $this->middleware->getConfig('middleware/api/login'),
                    'password'  => $this->middleware->getConfig('middleware/api/password'),
                    'debug'     => $this->_isDebug(),
                ), array(
                    'timeout'   => 20,
                    'useragent' => 'Middleware ('.$this->code.')',
                    'keepalive' => TRUE,
                ),
                $this->middleware);
        }
        return $this->_client;
    }

    /**
     * Retrieve base url
     *
     * @return string Example: "http://example.com/"
     * @throws Exception
     */
    final private function _getBaseUrl()
    {
        $baseUrl = trim($this->middleware->getConfig('middleware/system/base_url'));
        $baseUrl .= substr($baseUrl, -1) != '/' ? '/' : '';
        return $baseUrl;
    }
}
