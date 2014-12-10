# Sift Science PHP Bindings For Partnership API

## Installation
### With Composer
1. Add siftscience/sift-partner-php as a dependency in composer.json.

    ```
    "require": {
        ...
        "siftscience/sift-partner-php" : "1.*"
        ...
    }
    ```

2. Run `composer update`.
3. Now `SiftClient` will be autoloaded into your project.


    ```
    require 'vendor/autoload.php';

    $partner_client = new SiftClient('my_api_key', 'my_partner_id');
    ```

### Manually
1. Download the latest release.
2. Extract into a folder in your project root named "sift-partner-php".
2. Include `SiftClient` in your project like this:

    ```
    require 'sift-partner-php/lib/Services_JSON-1.0.3/JSON.php';
    require 'sift-partner-php/lib/SiftRequest.php';
    require 'sift-partner-php/lib/SiftResponse.php';
    require 'sift-partner-php/lib/SiftClient.php';

    $partner_client = new SiftClient('my_api_key', 'my_partner_id');
    ```

## Usage
### Create a new account
Here's an example that creates a new merchant account.

```
// Note: this will only work once, afterwards you will receive an 
// error as the merchant account with these details has already been created
$partner_client = new SiftClient('my_api_key', 'my_partner_id');
$response = $partner_client->new_account(
  "merchantsite.com", // the url for the merchant's site
  "shopowner@merchantsite.com", // an email belonging to the merchant
  "johndoe@merchantsite.com", // an email used to log in to Sift
  "s0m3l0ngp455w0rd" // password associated with that log in
);

$response->isOk();
```
### Get a list of accounts created by you

```
$partner_client = new SiftClient('my_api_key', 'my_partner_id');
$response = $partner_client->getAccounts();

$response->isOk();
```
### Configure the http notification endpoint and threshold for all of your merchants.

```
// Note: The %s must appear exactly once in your notification url.
$partner_client = new SiftClient('my_api_key', 'my_partner_id');
$response = $partner_client->updateNotificationConfig('http://your.url.endpoint/someting?id=%s', 0.60);  //This sets the threshold to a 60 (0.60*100)
```


## Contributing
Run the tests from the project root with [PHPUnit](http://phpunit.de) like this:

```
phpunit --bootstrap vendor/autoload.php test/SiftClientTest
```


## License
MIT
