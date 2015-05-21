<?php

/**
 * Interface for a plugin
 */
interface Plugin_Interface
{

    /*
     * Abstract methods which may be overridden by plugins
     */

    /**
     * @param array $query
     * @param array $headers
     * @param string $data
     * @return bool
     */
    function verifyWebhook($query, $headers, $data);

    /**
     * @param $query
     * @param $headers
     * @param $data
     * @return bool
     */
    function handleWebhook($query, $headers, $data);

    /**
     * @param array $request
     * @return void
     */
    function oauthHandleRedirect($request);

    /**
     * @param array $params
     * @return string
     */
    function oauthGetRedirectUrl($params = array());

    /**
     * @param array $connectParams
     * @param array $redirectParams
     * @return string
     */
    function oauthGetConnectButton($connectParams = array(), $redirectParams = array());

    /**
     * @param array $params
     * @return void
     */
    function oauthDisconnect($params = array());

    /**
     * @param string $accessToken
     * @return mixed
     */
    function oauthSetTokenData($accessToken);

    /**
     * @return string
     */
    function oauthGetTokenData();

    /**
     * @return void
     * @throws Exception
     */
    function oauthValidateConfig();

    /**
     * @return mixed
     * @throws Exception
     */
    function oauthTest();

    /*
     * Available helper methods which CANNOT be overridden by plugins
     */

    function yieldWebhook();

    /**
     * Wrapper for "call" method
     *
     * @param string $method
     * @param array  $args
     * @return mixed
     */
    function call($method, $args = array());

    /**
     * @param array|string $data
     * @param int|string|array|stdClass|null $value
     * @return mixed
     */
    function setState($data, $value = NULL);

    /**
     * @param array|string $keys
     * @return array|string
     */
    function getState($keys);

    /**
     * Retrieve config value
     *
     * @param string $path
     * @return null|string
     */
    function getConfig($path);

    /**
     * Retrieve plugin information value
     *
     * @param string $path
     * @return null|string|Varien_Simplexml_Element[]
     */
    function getPluginInfo($path);

    /**
     * Log messages
     *
     * @param string  $message
     * @param integer $level
     * @param string  $file
     * @return void
     */
    function log($message, $level = NULL, $file = NULL);

    /**
     * Write exception to log
     *
     * @param Exception $e
     * @return void
     */
    function logException(Exception $e);

    /**
     * Retrieve OAuth url
     *
     * @param array $params
     * @return string
     */
    function oauthGetUrl($params = array());

    /**
     * Retrieve callback url
     *
     * @param string $method
     * @return string
     */
    function getCallbackUrl($method);
}
