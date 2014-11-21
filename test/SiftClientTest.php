<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

class SiftClientTest extends PHPUnit_Framework_TestCase {
    private static $API_KEY = 'agreatsuccess';
    private static $PARTNER_ID = 'verynice';
    private $client;
    private $errors;
    private $validConfigNotificationUrl;
    private $validConfigNotificationThreshold;
    private $validSiteUrl;
    private $validSiteEmail;
    private $validAnalystEmail;
    private $validPassword;


    protected function setUp() {
        $this->client = new SiftClient(SiftClientTest::$API_KEY, SiftClientTest::$PARTNER_ID);
        $this->validConfigNotificationThreshold = 0.60;
        $this->validConfigNotificationUrl = 'http://api.partner.com/notify?id=%s';

        $this->validSiteUrl = 'somefakeurl.com';
        $this->validSiteEmail = 'owner@somefakeurl.com';
        $this->validAnalystEmail = 'analyst@somefakeurl.com';
        $this->validPassword = 's0mepA55word';

        $this->errors = array();
        set_error_handler(array($this, "errorHandler"));
        // reset global variable
        Sift::setApiKey(null);
        Sift::setPartnerId(null);
    }
 
    public function errorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
        $this->errors[] = compact("errno", "errstr", "errfile",
            "errline", "errcontext");
    }
 
    public function assertError($errstr, $errno) {
        foreach ($this->errors as $error) {
            if ($error["errstr"] === $errstr
                && $error["errno"] === $errno) {
                return;
            }
        }
        $this->fail("Error with level " . $errno .
            " and message '" . $errstr . "' not found in ", 
            var_export($this->errors, TRUE));
    }

    protected function tearDown() {
        SiftRequest::clearMockResponse();
    }

    public function testConstructor() {
        $this->assertInstanceOf('SiftClient', $this->client);
    }

    private function validCreateNewAccountResponseJson() {
        return json_encode(array(
                  array(
                    "production"=>   array(
                                      "api_keys"=>   array(
                                                        array(
                                                          "id"=>"54125bfee4b0beea0dfebfba",
                                                          "state"=>"ACTIVE",
                                                          "key"=>"492f506b096f0aa9"
                                                        )
                                                    ),
                                       "beacon_keys"=>   array(
                                                            array(                                                     
                                                                "id"=>"54125bfee4b0beea0dfebfbb",
                                                                "state"=>"ACTIVE",
                                                                "key"=>"5edb0c9c38"
                                                            )
                                                        )
                                    ),
                                    "sandbox"=>  array(
                                                  "api_keys"=>   array(
                                                                  array(
                                                                    "id"=>"54125bfee4b0beea0dfebfbd",
                                                                    "state"=>"ACTIVE",
                                                                    "key"=>"1c1155e8d391b161"
                                                                  )
                                                                ),
                                                  "beacon_keys"=>array(
                                                                  array(
                                                                    "id"=>"54125bfee4b0beea0dfebfbe",
                                                                    "state"=>"ACTIVE",
                                                                    "key"=>"44063ef989"
                                                                  )
                                                                )
                                                ),
                                    "account_id"=>"54125bfee4b0beea0dfebfb9"
                    )
               ));
    }
        
        

    private function validGetAccountsListResponseJson() {
        return json_encode(array(
                "data"=> array(
                  array(
                    "account_id"=> "54125bfee4b0beea0dfebfb9",
                    "production"=> array(
                      "api_keys"=> array(
                        array(
                          "id"=> "54125bfee4b0beea0dfebfba",
                          "key"=> "492f506b096f0aa9",
                          "state"=> "ACTIVE"
                        )
                      ),
                      "beacon_keys"=> array(
                        array(
                          "id"=> "54125bfee4b0beea0dfebfbb",
                          "key"=> "5edb0c9c38",
                          "state"=> "ACTIVE"
                        )
                      )
                    ),
                    "sandbox"=> array(
                      "api_keys"=> array(
                        array(
                          "id"=> "54125bfee4b0beea0dfebfbd",
                          "key"=> "1c1155e8d391b161",
                          "state"=> "ACTIVE"
                        )
                      ),
                      "beacon_keys"=> array(
                        array(
                          "id"=> "54125bfee4b0beea0dfebfbe",
                          "key"=> "44063ef989",
                          "state"=> "ACTIVE"
                        )
                      )
                    )
                  ),
                  array(
                    "account_id"=> "541793ece4b0550b2274a8ed",
                    "production"=> array(
                      "api_keys"=> array(
                        array(
                          "id"=> "541793ece4b0550b2274a8ee",
                          "key"=> "c1ab335655fbcd3f",
                          "state"=> "ACTIVE"
                        )
                      ),
                      "beacon_keys"=> array(
                        array(
                          "id"=> "541793ece4b0550b2274a8ef",
                          "key"=> "461b33d204",
                          "state"=> "ACTIVE"
                        )
                      )
                    ),
                    "sandbox"=> array(
                      "api_keys"=> array(
                        array(
                          "id"=> "541793ece4b0550b2274a8f1",
                          "key"=> "31d70b58b6030cde",
                          "state"=> "ACTIVE"
                        )
                      ),
                      "beacon_keys"=> array(
                        array(
                          "id"=> "541793ece4b0550b2274a8f2",
                          "key"=> "7eaa6fa0ea",
                          "state"=> "ACTIVE"
                        )
                      )
                    )
                  )
                ),
                "has_more"=> False,
                "total_results"=> 2,
                "type"=> "partner_account"
              ));
    }

    private function validConfigNotificationUrlResponseJson() {
        return json_encode(array(
                 "http_notification_threshold"=> 0.60,
                 "http_notification_url"=> "http://api.partner.com/notify?id=%s",
               ));
    }

    public function testGlobalApiKeySuccess() {
        $this->setExpectedException(null);
        Sift::setApiKey(SiftClientTest::$API_KEY);
        Sift::setPartnerId(SiftClientTest::$PARTNER_ID);
        new SiftClient();
    }

    public function testEmptyGlobalApiKeyFail() {
        $this->setExpectedException('InvalidArgumentException');
        Sift::setApiKey('');
        Sift::setPartnerId(SiftClientTest::$PARTNER_ID);
        new SiftClient();
    }

    public function testNullGlobalApiKeyFail() {
        $this->setExpectedException('InvalidArgumentException');
        Sift::setApiKey(null);
        Sift::setPartnerId(SiftClientTest::$PARTNER_ID);
        new SiftClient();
    }

    public function testNonStringGlobalApiKeyFail() {
        $this->setExpectedException('InvalidArgumentException');
        Sift::setApiKey(42);
        Sift::setPartnerId(SiftClientTest::$PARTNER_ID);
        new SiftClient();
    }

    public function testGlobalPartnerIdSuccess() {
        $this->setExpectedException(null);
        Sift::setApiKey(SiftClientTest::$API_KEY);
        Sift::setPartnerId(SiftClientTest::$PARTNER_ID);
        new SiftClient();
    }

    public function testEmptyGlobalPartnerIdFail() {
        $this->setExpectedException('InvalidArgumentException');
        Sift::setApiKey(SiftClientTest::$API_KEY);
        Sift::setPartnerId('');
        new SiftClient();
    }

    public function testNullGlobalPartnerIdFail() {
        $this->setExpectedException('InvalidArgumentException');
        Sift::setApiKey(SiftClientTest::$API_KEY);
        Sift::setPartnerId(null);
        new SiftClient();
    }

    public function testNonStringGlobalPartnerIdFail() {
        $this->setExpectedException('InvalidArgumentException');
        Sift::setApiKey(SiftClientTest::$API_KEY);
        Sift::setPartnerId(42);
        new SiftClient();
    }

    public function testEmptyApiKeyFail() {
        $this->setExpectedException('InvalidArgumentException');
        new SiftClient('', SiftClientTest::$PARTNER_ID);    
    }

    public function testNullApiKeyFail() {
        $this->setExpectedException('InvalidArgumentException');
        new SiftClient(null, SiftClientTest::$PARTNER_ID);
    }

    public function testNonStringApiKeyFail() {
        $this->setExpectedException('InvalidArgumentException');
        new SiftClient(42, SiftClientTest::$PARTNER_ID);
    }

    public function testEmptyPartnerIdFail() {
        $this->setExpectedException('InvalidArgumentException');
        new SiftClient(SiftClientTest::$API_KEY,'');
    }

    public function testNullPartnerIdFail() {
        $this->setExpectedException('InvalidArgumentException');
        new SiftClient(SiftClientTest::$API_KEY, null);
    }

    public function testNonStringPartnerIdFail() {
        $this->setExpectedException('InvalidArgumentException');
        new SiftClient(SiftClientTest::$API_KEY,42);
    }

    public function testSuccessfullNewAccountCreation() {
        $mockUrl = 'https://partner.siftscience.com/v3/partners/' . SiftClientTest::$PARTNER_ID . '/accounts';
        $mockResponse = new SiftResponse($this->validCreateNewAccountResponseJson(), 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::POST ,$mockResponse);
        $response = $this->client->newAccount($this->validSiteUrl, $this->validSiteEmail, $this->validAnalystEmail, $this->validPassword);
        $this->assertTrue($response->isOk());
        // had to add index 0 because the response is a list of json objects (i.e.  [{object1}, {object2}])
        $this->assertTrue(array_key_exists('production', $response->body[0]));
        $this->assertTrue(array_key_exists('sandbox', $response->body[0]));
        $this->assertTrue(array_key_exists('account_id', $response->body[0]));
    }

    public function testSuccessfulGetAccounts() {
        $mockUrl = 'https://partner.siftscience.com/v3/partners/' . SiftClientTest::$PARTNER_ID . '/accounts';
        $mockResponse = new SiftResponse($this->validGetAccountsListResponseJson(), 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET ,$mockResponse);
        $response = $this->client->getAccounts();
        $this->assertTrue($response->isOk());
        $this->assertTrue(array_key_exists('has_more', $response->body));
        $this->assertFalse($response->body['has_more']);
        $this->assertTrue(array_key_exists('total_results', $response->body));
        $this->assertEquals($response->body['total_results'], 2);
        $this->assertTrue(array_key_exists('data', $response->body));
        $this->assertEquals(count($response->body['data']), 2);
    }

    public function testSuccessfulUpdateNotificationConfig() {
        $mockUrl = 'https://partner.siftscience.com/v3/accounts/' . SiftClientTest::$PARTNER_ID . '/config';
        $mockResponse = new SiftResponse($this->validConfigNotificationUrlResponseJson(), 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::PUT ,$mockResponse);
        $response = $this->client->updateNotificationConfig($this->validConfigNotificationUrl, $this->validConfigNotificationThreshold);
        $this->assertTrue($response->isOk());
        $this->assertEquals($response->body['http_notification_url'], $this->validConfigNotificationUrl);
        $this->assertEquals($response->body['http_notification_threshold'], $this->validConfigNotificationThreshold);
    }

}
