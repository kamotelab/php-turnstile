<?php

declare(strict_types=1);

/**
 *
 * @category Library
 * @package  PHP Turnstile
 * @author   Yan Santos
 * @license  https://opensource.org/license/mit
 * @link     https://github.com/kamotelab/turnstile
 */

namespace KamoteLab\Http;

use KamoteLab\Http\ClientInterface as TurnstileInterface;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

class Client implements TurnstileInterface
{
    private ClientInterface $client;
    private RequestFactoryInterface $requestFactory;
    private StreamFactoryInterface $streamFactory;

    public function __construct() {
        $this->client = Psr18ClientDiscovery::find();
        $this->requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory = Psr17FactoryDiscovery::findStreamFactory();
    }

    /**
     * @param array $data
     *
     * @return RequestInterface
     */
    public function createRequest(array $data): RequestInterface
    {
        return $this->requestFactory
            ->createRequest('POST', self::TURNSTILE_VERIFY_URL)
            ->withHeader('Content-Type', 'application/json')
            ->withBody(
                $this->streamFactory->createStream(
                    json_encode($data)
                ),
            );
    }

    /**
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws ClientExceptionInterface
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->client->sendRequest($request);
    }
}
