<?php

declare(strict_types=1);

use KamoteLab\Http\Client;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

// This test will hit actual clouldflares verify endpoint
#[CoversClass(Client::class)]
final class ClientTest extends TestCase
{
	private const string RESPONSE = 'XXXX.DUMMY.TOKEN.XXXX';
	
	public function testCreateRequest(): void
	{
		$client = new Client();
		$data = [
			'secret'   => $this->getSecretKey(),
			'response' => self::RESPONSE,
		];
		
		$request = $client->createRequest($data);
		$this->assertInstanceOf(RequestInterface::class, $request);
		$this->assertSame($request->getHeaders(), [
			'Host'         => ['challenges.cloudflare.com'],
			'Content-Type' => ['application/json'],
		]);
	}
	
	public function testSendRequest(): void
	{
		$client = new Client();
		$data = [
			'secret'   => $this->getSecretKey(),
			'response' => self::RESPONSE,
		];
		
		$request = $client->createRequest($data);
		$response = $client->sendRequest($request);
		
		$this->assertInstanceOf(ResponseInterface::class, $response);
	}
	
	/**
	 * @return string
	 */
	private function getSecretKey(): string
	{
		return '1x0000000000000000000000000000000AA';
	}
}