<?php

use Fet\LaminasCloudflareTurnstile\TurnstileViewHelper;
use PHPUnit\Framework\TestCase;
use Mockery as m;
use Laminas\Form\ElementInterface;

class TurnstileViewHelperTest extends TestCase
{
    public function testRenderScripts()
    {
        $viewHelper = new TurnstileViewHelper(['site_key' => 'site-key']);
        $script = '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script><div class="cf-turnstile" data-sitekey="site-key"></div><input type="hidden" name="captcha" value="cf-turnstile-response">';

        $element = m::mock(ElementInterface::class);
        $element
            ->shouldReceive('getCaptcha->getTurnstileService->getOptions')
            ->andReturn(['site_key' => 'site-key']);


        $this->assertEquals($script, $viewHelper($element));
    }
}
