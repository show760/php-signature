# PHP-Signature

A simple library to validate and generate signature in PHP

## Installation

Use composer to manage your dependencies and download this library 

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/show760/php-signature.git"
        }
    ],
    "require": {
        "show760/php-signature": "^1.0.0"
    }
}
```

## Example

### HMAC Signature

The following example would help you to generate the `Signature` in the request header.

```php
require __DIR__.'/vendor/autoload.php';

use GuzzleHttp\Client;
use Token\ApiSignature;

$key = '<your key';
$secret = '<your secret>';
$apiSignature = new ApiSignature($key, $secret);

$params = [
    'foo' => 'bar'
];
$payload = json_encode($params);
$requestTimestamp = times();
$signature = $apiSignature->generateSignature($payload, $requestTimestamp);

// Send request
$client = new Client(['base_uri' => THE_SERVER_HOST]);
$client->request('POST', '/api-path', [
    'headers' => [
        'X-Id' => $key,
        'X-Timestamp' => $requestTimestamp,
        'X-Signature' => $signature,
    ]       
    'json' => $params
]);
```

## Tests

```
composer install
./vendor/bin/phpunit
```

## License

Please see [MIT License File](./LICENSE) for more information.
