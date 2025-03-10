# PHP Turnstile Library
![GitHub Actions Workflow Status](https://img.shields.io/github/actions/workflow/status/kamotelab/php-turnstile/test.yml?logo=github&)
![GitHub Release](https://img.shields.io/github/v/release/kamotelab/php-turnstile?logo=packagist)

A lightweight PHP implementation of Cloudflare's Turnstile

## Requirements
* PHP ^8.3
* Any PSR-17, PSR-18 and HTTPlug implementations (ie [Symfony HTTP Client](https://github.com/symfony/http-client))

## Installation
```
 composer require kamotelab/php-turnstile
```

## Example Usage
```
<?php

use KamoteLab\Turnstile;

class YourClass 
{
    private Turnstile $turnstile

    public function __construct(Turnstile $turnstile) {
        $this->turnstile = new Turnstile('{Turnstile Secret Key}');
    }
    
    /*
     * $response (required) = turnstile response from client side render on your site.
     * $idempotencyKey (optional) = use this if you need to retry failed request.
     * $remoteIp (optional) = The visitor’s IP address.
     */
    public function yourMethod(string $response, ?string $idempotencyKey = null, ?string $remoteIp = null) {
        
        try {
            $this->turnstile->verify($response, $idempotencyKey, $remoteIp)
        } catch (Exception $e) {
            // next step in case of exception
        }
        
    }
}

```

## Disclaimer
* This library will not throw any exception when the response code is 4xx or 5xx
* You will have to implement your own error handling when response code is 4xx, 5xx
* You will have to implement your own error handling when error was thrown

