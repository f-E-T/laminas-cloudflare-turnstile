<?php

namespace Fet\LaminasCloudflareTurnstile;

use Laminas\Form\View\Helper\FormInput;
use Laminas\Form\ElementInterface;

class TurnstileViewHelper extends FormInput
{
    const URL = 'https://challenges.cloudflare.com/turnstile/v0/api.js';

    /** @var array<string, mixed> $options */
    protected array $options;

    public function __invoke(?ElementInterface $element = null)
    {
        /** @var \Laminas\Form\Element\Captcha $element */
        $captcha = $element->getCaptcha();
        /** @var \Fet\LaminasCloudflareTurnstile\TurnstileCaptcha $captcha */
        $this->options = $captcha->getTurnstileService()->getOptions();

        return $this->addWidget() . $this->renderWidget() . '<input type="hidden" name="captcha" value="cf-turnstile-response">';
    }

    public function addWidget(): string
    {
        $pattern = '<script src="%s" async defer></script>';

        return sprintf($pattern, self::URL);
    }

    public function renderWidget(): string
    {
        $pattern = '<div class="cf-turnstile" data-sitekey="%s"></div>';
        $siteKey = is_string($this->options['site_key']) ? $this->options['site_key'] : '';

        return sprintf($pattern, $siteKey);
    }
}
