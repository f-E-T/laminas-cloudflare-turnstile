<?php

namespace Fet\LaminasCloudflareTurnstile;

use Fet\LaminasCloudflareTurnstile\TurnstileService;
use Laminas\Captcha\AbstractAdapter;

class TurnstileCaptcha extends AbstractAdapter
{
    const CAPTCHA_ERROR = 'captchaError';

    /** @var array<string> $messageTemplates */
    protected array $messageTemplates = [
        self::CAPTCHA_ERROR => 'Turnstile Captcha Error',
    ];

    protected $options;

    public function __construct($options = null, protected ?TurnstileService $turnstileService = null)
    {
        parent::__construct($options);

        $this->options = $options;
    }

    public function generate(): string
    {
        return '';
    }

    public function isValid($value, $context = null): bool
    {
        $token = $context[$value] ?? '';
        $valid = $this->getTurnstileService()->validate($token);

        if (!$valid) {
            $this->error(self::CAPTCHA_ERROR);

            return false;
        }

        return true;
    }

    public function getHelperName(): string
    {
        return 'turnstile';
    }

    public function getTurnstileService(): TurnstileService
    {
        if ($this->turnstileService === null) {
            $this->turnstileService = new TurnstileService($this->options);
        }

        return $this->turnstileService;
    }
}
