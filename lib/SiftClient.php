<?php

class SiftClient {
    const API_ENDPOINT = 'https://partner.siftscience.com/v';

    // Must be kept in sync with composer.json
    const API_VERSION = '3';
    const DEFAULT_TIMEOUT = 2;


    private $apiKey;
    private $partnerId;
    private $timeout;

    /**
     * SiftClient constructor
     *
     * @param   $apiKey The SiftScience API key associated with your account. If Sift::$apiKey has been set you can instantiate the client without an $apiKey,
     *          If Sift::$apiKey has not been set, this parameter is required and must not be null or an empty string.
     */
    function  __construct($apiKey = null, $partnerId = null, $timeout = self::DEFAULT_TIMEOUT) {
        if (!$apiKey) {
            $apiKey = Sift::$apiKey;
        }
        if (!$partnerId) {
            $partnerId = Sift::$partnerId;
        }
        $this->validateArgument($apiKey, 'api key', 'string');
        $this->apiKey = $apiKey;

        $this->validateArgument($partnerId, 'partner id', 'string');
        $this->partnerId = $partnerId;

        $this->timeout = $timeout;
    }

    /**
     * Creates a new merchant account under the given partner.
     * == Parameters:
     * siteUrl
     * the url of the merchant site
     *
     * siteEmail
     * an email address for the merchant
     *
     * analystEmail
     * an email address which will be used to log in at the Sift Console
     *
     * password
     * password (at least 10 chars) to be used to sign into the Console
     *
     * When successful, returns a including the new account id and credentials.
     * When an error occurs, The exception is raised.
    */
    public function newAccount($siteUrl, $siteEmail, $analystEmail, $password) {
        $this->validateArgument($siteUrl, 'site url', 'string');
        $this->validateArgument($siteEmail, 'site email', 'string');
        $this->validateArgument($analystEmail, 'analyst email', 'string');
        $this->validateArgument($password, 'password', 'string');

        $body = Array();

        $body['site_url'] = $siteUrl;
        $body['site_email'] = $siteEmail;
        $body['analyst_email'] = $analystEmail;
        $body['password'] = $password;

        try {
            $request = new SiftRequest(self::accountsUrl(), SiftRequest::POST, $body, $this->apiKey, $this->timeout);
            return $request->send();
        } catch (Exception $e) {
            return null;
        }
    }

    public function getAccounts() {
        try {
            $request = new SiftRequest(self::accountsUrl(), SiftRequest::GET, null, $this->apiKey, $this->timeout);
            return $request->send();
        } catch (Exception $e) {
            return null;
        }
    }

    /** Updates the configuration which controls http notifications for all merchant
     * accounts under this partner.
     *
     * == Parameters
     * httpNotificationUrl
     * A url to send POST notifications to.  The value of the notification_url will be a url containing the string '%s' exactly once.
     *
     * httpNotificationThreshold
     *  The notification threshold should be a double between 0.0 and 1.0
     */
    public function updateNotificationConfig($httpNotificationUrl = null, $httpNotificationThreshold = null) {
        $this->validateArgument($httpNotificationUrl, 'notification url', 'string');
        $this->validateArgument($httpNotificationThreshold, 'notification threshold', 'double');

        $body = Array();

        $body['http_notification_url'] = $httpNotificationUrl;
        $body['http_notification_threshold'] = $httpNotificationThreshold;

        try {
            $request = new SiftRequest(self::notificationConfigUrl(), SiftRequest::PUT, $body, $this->apiKey, $this->timeout);
            return $request->send();
        } catch (Exception $e) {
            return null;
        }
    }

    private function validateArgument($arg, $name, $type) {
        // Validate type
        if (gettype($arg) != $type)
            throw new InvalidArgumentException("${name} must be a ${type}.");

        // Check if empty
        if (empty($arg))
            throw new InvalidArgumentException("${name} cannot be empty.");
    }

    private function accountsUrl() {
        return self::urlPrefix() . '/partners/' . $this->partnerId . '/accounts';
    }

    private function notificationConfigUrl() {
        return self::urlPrefix() . '/accounts/' . $this->partnerId . '/config';
    }

    private static function urlPrefix() {
        return self::API_ENDPOINT . self::API_VERSION;
    }
}
