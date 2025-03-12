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

namespace KamoteLab;

use JsonException;
use KamoteLab\Http\ClientInterface;
use Psr\Http\Client\ClientExceptionInterface;

class Turnstile implements TurnstileInterface
{
	private ClientInterface $client;
	
	public function __construct(
		private string  $secretKey,
		ClientInterface $client = null
	)
	{
		$this->client = $client ?? new Http\Client();
	}
	
	/**
	 * Verify the Turnstile response token with the API.
	 *
	 * @param string $response
	 * @param string|null $idempotencyKey
	 * @param string|null $remoteIp
	 *
	 * @return array
	 *
	 * @throws JsonException
	 * @throws ClientExceptionInterface
	 */
	public function verify(string $response, ?string $idempotencyKey = null, ?string $remoteIp = null): array
	{
		$data = array_filter([
			'secret'          => $this->secretKey,
			'response'        => $response,
			'remoteip'        => $remoteIp,
			'idempotency_key' => $idempotencyKey,
		]);
		
		$request = $this->client->createRequest($data);
		$response = $this->client->sendRequest($request);
		
		return json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR);
	}
}
