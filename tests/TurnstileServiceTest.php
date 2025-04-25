<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\HandlerStack;
use Laminas\Http\PhpEnvironment\Request;
use Mockery as m;
use Fet\LaminasCloudflareTurnstile\TurnstileService;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request as GuzzleRequest;

class TurnstileServiceTest extends TestCase
{
    public function testValidateValidToken()
    {
        $mock = new MockHandler([
            new Response(
                200,
                [],
                '
                    {
                        "success": true,
                        "error-codes": [],
                        "challenge_ts": "2022-10-06T00:07:23.274Z",
                        "hostname": "example.com"
                    }
                '
            ),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $guzzleClient = new GuzzleClient([
            'base_uri' => 'https://www.example.com',
            'handler' => $handlerStack
        ]);

        $options = [
            'secret_key' => 'secret'
        ];

        $request = m::mock(Request::class);
        $turnstileService = new TurnstileService($options, $guzzleClient, $request);

        $this->assertTrue($turnstileService->validate('valid-token'));
        $this->assertEquals('https://challenges.cloudflare.com', $turnstileService::URL);
        $this->assertSame($options, $turnstileService->getOptions());

        $lastRequest = $mock->getLastRequest();
        $formParams = [];
        parse_str($lastRequest->getBody(), $formParams);

        $this->assertEquals('https', $lastRequest->getUri()->getScheme());
        $this->assertEquals('www.example.com', $lastRequest->getUri()->getHost());
        $this->assertEquals('POST', $lastRequest->getMethod());
        $this->assertEquals('/turnstile/v0/siteverify', $lastRequest->getRequestTarget());

        $this->assertEquals('secret', $formParams['secret']);
        $this->assertEquals('valid-token', $formParams['response']);
    }

    public function testValidateInvalidToken()
    {
        $mock = new MockHandler([
            new Response(
                200,
                [],
                '
                    {
                        "success": false,
                        "error-codes": ["invalid-token"],
                        "messages": []
                    }
                '
            ),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $guzzleClient = new GuzzleClient(['handler' => $handlerStack]);

        $options = ['secret_key' => 'secret'];

        $turnstileService = new TurnstileService($options, $guzzleClient);

        $this->assertFalse($turnstileService->validate('invalid-token'));
    }

    public function testValidateException()
    {
        $mock = new MockHandler([
            new RequestException('Server Error', new GuzzleRequest('GET', 'test'))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $guzzleClient = new GuzzleClient(['handler' => $handlerStack]);

        $options = ['secret_key' => 'secret'];

        $turnstileService = new TurnstileService($options, $guzzleClient);

        $this->assertFalse($turnstileService->validate('valid-token'));
    }
}
