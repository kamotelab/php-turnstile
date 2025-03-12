<?php

declare(strict_types=1);

/**
 *
 * @category Library
 * @package  PHP Turnstile
 * @author   Yan Santos
 * @license  https://opensource.org/license/mit
 * @link     https://github.com/kamotelab/php-turnstile
 */

namespace KamoteLab\Http;

use JsonException;
use KamoteLab\Http\ClientInterface as TurnstileClientInterface;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

class Client implements TurnstileClientInterface
{
	private ClientInterface $client;
	private RequestFactoryInterface $requestFactory;
	private StreamFactoryInterface $streamFactory;
	
	/**
	 * @param ClientInterface|null $client
	 * @param RequestFactoryInterface|null $requestFactory
	 * @param StreamFactoryInterface|null $streamFactory
	 */
	public function __construct(
		?ClientInterface         $client = null,
		?RequestFactoryInterface $requestFactory = null,
		?StreamFactoryInterface  $streamFactory = null
	)
	{
		$this->client = $client ?? Psr18ClientDiscovery::find();
		$this->requestFactory = $requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();
		$this->streamFactory = $streamFactory ?? Psr17FactoryDiscovery::findStreamFactory();
	}
	
	/**
	 * @param array $data
	 *
	 * @return RequestInterface
	 *
	 * @throws JsonException
	 */
	public function createRequest(array $data): RequestInterface
	{
		$jsonPayload = json_encode($data, JSON_THROW_ON_ERROR);
		
		return $this->requestFactory
			->createRequest('POST', self::TURNSTILE_VERIFY_URL)
			->withHeader('Content-Type', 'application/json')
			->withBody($this->streamFactory->createStream($jsonPayload));
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
