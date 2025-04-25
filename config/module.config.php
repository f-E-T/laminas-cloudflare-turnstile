<?php

use Fet\LaminasCloudflareTurnstile\TurnstileViewHelper;

return [
    'view_helpers' => [
        'aliases' => [
            'turnstile' => TurnstileViewHelper::class,
        ],
        'invokables' => [
            TurnstileViewHelper::class => TurnstileViewHelper::class,
        ],
    ],
];
