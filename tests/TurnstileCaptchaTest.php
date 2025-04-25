<?php

use Fet\LaminasCloudflareTurnstile\TurnstileCaptcha;
use PHPUnit\Framework\TestCase;
use Mockery as m;
use Fet\LaminasCloudflareTurnstile\TurnstileService;

class TurnstileCaptchaTest extends TestCase
{
    public function testValidity()
    {
        $this->assertTrue($this->getTurnstileCaptcha()->isValid('captcha', ['captcha' => 'token']));
        $this->assertFalse($this->getTurnstileCaptcha(false)->isValid('captcha', ['captcha' => 'token']));
        $this->assertEquals('', $this->getTurnstileCaptcha()->generate());
    }

    public function testViewHelper()
    {
        $config = require __DIR__ . '/../config/module.config.php';
        $viewHelperName = $this->getTurnstileCaptcha()->getHelperName();

        $this->assertArrayHasKey($viewHelperName, $config['view_helpers']['aliases']);
    }

    public function testErrors()
    {
        $turnstile = $this->getTurnstileCaptcha(false);

        $turnstile->isValid('captcha', ['captcha' => 'token']);

        $this->assertEquals('Turnstile Captcha Error', $turnstile->getOption('messages')['captchaError']);
    }

    protected function getTurnstileCaptcha($valid = true)
    {
        $turnstileService = m::mock(TurnstileService::class);

        $turnstileService
            ->shouldReceive('validate')
            ->with('token')
            ->andReturn($valid);

        return new TurnstileCaptcha(null, $turnstileService);
    }
}
