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

namespace KamoteLab;

use JsonException;
use Psr\Http\Client\ClientExceptionInterface;

interface TurnstileInterface
{
    /**
     * @param string $secretKey
     */
    public function __construct(string $secretKey);

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
    public function verify(string $response, ?string $idempotencyKey = null, ?string $remoteIp = null): array;
}
