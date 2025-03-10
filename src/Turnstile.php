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
use KamoteLab\Http\Client;
use KamoteLab\Http\ClientInterface;
use Psr\Http\Client\ClientExceptionInterface;

readonly class Turnstile implements TurnstileInterface
{
    private ClientInterface $client;

    /**
     * @param string $secretKey
     */
    public function __construct(private string $secretKey)
    {
        $this->client = new Client();
    }

    /**
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
        $data = [
            'secret' => $this->secretKey,
            'response' => $response,
        ];

        $data = $this->addOptionalDataIfPresent($data, $remoteIp, $idempotencyKey);

        $request = $this->client->createRequest($data);
        $response = $this->client->sendRequest($request);

        $responseBody = (string) $response->getBody();

        return json_decode($responseBody, true, 512, JSON_THROW_ON_ERROR);

    }

    /**
     * @param array $data
     * @param string|null $remoteIp
     * @param string|null $idempotencyKey
     *
     * @return array
     */
    private function addOptionalDataIfPresent(array $data, ?string $remoteIp = null, ?string $idempotencyKey = null): array
    {
        $optional = ['remoteIp' => $remoteIp, 'idempotencyKey' => $idempotencyKey];
        foreach ($optional as $key => $value) {
            if (!empty($value)) {
                $data[$key] = $value;
            }
        }

        return $data;
    }
}
