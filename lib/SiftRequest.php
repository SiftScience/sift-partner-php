<?php

class SiftRequest {
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';

    private static $mock = null;

    private $url;
    private $method;
    private $properties;
    private $timeout;
    private $apiKey;

    /**
     * SiftRequest constructor
     *
     * @param $url Url of the HTTP request
     * @param $method Method of the HTTP request
     * @param $properties Parameters to send along with the request
     * @param $timeout HTTP request timeout
     */
    function __construct($url, $method, $properties, $apiKey, $timeout) {
        $this->url = $url;
        $this->method = $method;
        $this->properties = $properties;
        $this->timeout = $timeout;
        $this->apiKey = $apiKey;
    }

    /**
     * Send the HTTP request via cURL
     *
     * @return SiftResponse
     */
    public function send() {
        $curlUrl = $this->url;

        // Mock the request if self::$mock exists
        if (self::$mock) {
            if (self::$mock['url'] == $curlUrl && self::$mock['method'] == $this->method) {
                return self::$mock['response'];
            }
            return null;
        }

        // Open and configure curl connection
        $ch = curl_init();
        

        $headers = array(
            'Authorization: Basic ' . $this->apiKey,
            'User-Agent: SiftScience/v' . SiftClient::API_VERSION . ' sift-partner-php/' . Sift::VERSION);

        //GET Specific setup.  The check for null is in the case that we don't add any parameters.
        if ($this->method == self::GET) {
                if ($this->properties !== null) {
                    $propertiesString = http_build_query($this->properties);
                    $curlUrl .= '?' . $propertiesString;
                }
        }
        else {
            // POST specific setup
            if ($this->method == self::POST) {
                curl_setopt($ch, CURLOPT_POST, 1);
            }
            // PUT specific setup
            else if ($this->method == self::PUT) {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            }

            // shared by POST and PUT setup
            if (function_exists('json_encode')) {
                $jsonString = json_encode($this->properties);
            } else {
                require_once(dirname(__FILE__) . '/Services_JSON-1.0.3/JSON.php');
                $json = new Services_JSON();
                $jsonString = $json->encodeUnsafe($this->properties);
            }
            
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonString);

            /**
             * add 'Content-Type: application/json',
             *      'Content-Length: ' . strlen($jsonString),
             */
            array_push($headers, 'Content-Type: application/json');
            array_push($headers, 'Content-Length: ' . strlen($jsonString));
           

        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $curlUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        // Set the header
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        

        // Send the request using curl and parse result
        $result = curl_exec($ch);
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Close the curl connection
        curl_close($ch);

        return new SiftResponse($result, $httpStatusCode, $this);
    }

    // for unittesting purposes
    public static function setMockResponse($url, $method, $response) {
        self::$mock = array(
            'url' => $url,
            'method' => $method,
            'response' => $response
        );
    }

    public static function clearMockResponse() {
        self::$mock = null;
    }
}
