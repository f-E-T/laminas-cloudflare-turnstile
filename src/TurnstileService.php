<?php

namespace Fet\LaminasCloudflareTurnstile;

use GuzzleHttp\Client as GuzzleClient;
use RuntimeException;

class TurnstileService
{
    const URL = 'https://challenges.cloudflare.com';

    /** @param array<string, mixed> $options */
    public function __construct(
        protected array $options,
        protected ?GuzzleClient $client = null,
    ) {}

    public function validate(string $token): bool
    {
        try {
            $response = $this->getClient()->request('POST', '/turnstile/v0/siteverify', [
                'form_params' => [
                    'secret' => $this->options['secret_key'],
                    'response' => $token,
                ]
            ]);
        } catch (RuntimeException $e) {
            return false;
        }

        $result = (array) json_decode($response->getBody()->getContents(), true);

        return $response->getStatusCode() === 200 && ($result['success'] === true ?: false);
    }

    protected function getClient(): GuzzleClient
    {
        if ($this->client === null) {
            $this->client = new GuzzleClient([
                'base_uri' => self::URL,
                'timeout'  => 2.0,
            ]);
        }

        return $this->client;
    }

    /** @return array<string, mixed> */
    public function getOptions(): array
    {
        return $this->options;
    }
}
