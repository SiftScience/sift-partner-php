<?php

class SiftResponse {
    public $body;
    public $httpStatusCode;
    public $errorMessage;
    public $request;
    public $result;

    private $errorIssues;
    private $error;

    public function __construct($result, $httpStatusCode, $request) {
        if (function_exists('json_decode')) {
            $this->body = json_decode($result, true);
        } else {
            require_once(dirname(__FILE__) . '/Services_JSON-1.0.3/JSON.php');
            $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
            $this->body = $json->decode($result);
        }
        $this->httpStatusCode = $httpStatusCode;

        // If there is an error we want to build out an error string.
        if ($this->httpStatusCode !== 200) {
            $this->error = $this->body['error'];

            $this->errorMessage = $this->error . ': ' . $this->body['description'];

            if(array_key_exists('issues', $this->body)) {
                $this->errorIssues = $this->body['issues'];

                foreach($this->errorIssues as &$issue) {
                    $this->errorMessage .= (array_search($issue, $this->errorIssues) . ': ' . $issue);
                }
                unset($issue);
            }
        }

        if (array_key_exists('request', $this->body)) {
            $this->request = $this->body['request'];
        }
        else {
            $this->request = null;
        }

        $this->request = $request;
        $this->result = $result;
    }

    public function isOk() {
        return $this->httpStatusCode === 200;
    }
}
