<?php

declare(strict_types=1);

use KamoteLab\Http\Client;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use KamoteLab\Turnstile;

// This test will hit actual clouldflares verify endpoint
#[CoversClass(Turnstile::class)]
#[UsesClass(Client::class)]
final class TurnstileTest extends TestCase
{
    private const string RESPONSE = 'XXXX.DUMMY.TOKEN.XXXX';
    public function testVerifySuccess(): void
    {
        $client = new Turnstile($this->getSecretKey('pass'));
        $response = $client->verify(self::RESPONSE, '1234', '127.0.0.1');
        $this->assertTrue($response['success']);
        $this->assertEmpty($response['error-codes']);
        $response = $client->verify(self::RESPONSE, '1234');
        $this->assertTrue($response['success']);
        $this->assertEmpty($response['error-codes']);
        $response = $client->verify(self::RESPONSE);
        $this->assertTrue($response['success']);
        $this->assertEmpty($response['error-codes']);
        $response = $client->verify(self::RESPONSE, null, '127.0.0.1');
        $this->assertTrue($response['success']);
        $this->assertEmpty($response['error-codes']);
    }

    public function testVerifyFail(): void
    {
        $client = new Turnstile($this->getSecretKey('fail'));
        $response = $client->verify(self::RESPONSE, '1234', '127.0.0.1');
        $this->assertFalse($response['success']);
        $this->assertNotEmpty($response['error-codes']);
        $this->assertSame($response['error-codes'][0], 'invalid-input-response');
        $response = $client->verify(self::RESPONSE, '1234');
        $this->assertFalse($response['success']);
        $this->assertNotEmpty($response['error-codes']);
        $this->assertSame($response['error-codes'][0], 'invalid-input-response');
        $response = $client->verify(self::RESPONSE);
        $this->assertFalse($response['success']);
        $this->assertNotEmpty($response['error-codes']);
        $this->assertSame($response['error-codes'][0], 'invalid-input-response');
        $response = $client->verify(self::RESPONSE, null, '127.0.0.1');
        $this->assertFalse($response['success']);
        $this->assertNotEmpty($response['error-codes']);
    }

    public function testVerifyDuplicate(): void
    {
        $client = new Turnstile($this->getSecretKey('duplicate'));
        $response = $client->verify(self::RESPONSE, '1234', '127.0.0.1');
        $this->assertFalse($response['success']);
        $this->assertNotEmpty($response['error-codes']);
        $this->assertSame($response['error-codes'][0], 'timeout-or-duplicate');
        $response = $client->verify(self::RESPONSE, '1234');
        $this->assertFalse($response['success']);
        $this->assertNotEmpty($response['error-codes']);
        $this->assertSame($response['error-codes'][0], 'timeout-or-duplicate');
        $response = $client->verify(self::RESPONSE);
        $this->assertFalse($response['success']);
        $this->assertNotEmpty($response['error-codes']);
        $this->assertSame($response['error-codes'][0], 'timeout-or-duplicate');
        $response = $client->verify(self::RESPONSE, null, '127.0.0.1');
        $this->assertFalse($response['success']);
        $this->assertNotEmpty($response['error-codes']);
        $this->assertSame($response['error-codes'][0], 'timeout-or-duplicate');
    }

    /**
     * @param string $type
     * @return string
     */
    private function getSecretKey(string $type): string
    {
        $secretKey = [
            'pass' => '1x0000000000000000000000000000000AA',
            'fail' => '2x0000000000000000000000000000000AA',
            'duplicate' => '3x0000000000000000000000000000000AA'
        ];

        return $secretKey[$type];
    }
}