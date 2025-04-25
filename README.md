# Introduction
The `fet/laminas-cloudflare-turnstile` package provides a Cloudflare turnstile widget integration for Laminas forms.

# Installation
To install this package, use Composer:

```bash
composer require fet/laminas-cloudflare-turnstile
```

Enable the `Fet\\LaminasCloudflareTurnstile` module in your `config/application.config.php` file.

```php
return [
    'modules' => [
        // ... other modules ...
        'Fet\\LaminasCloudflareTurnstile',
    ],
];
```

# Usage
You can add the turnstile adapter via the `captcha` option to your form and render the element in your view.

## Form
```php
$form = new \Laminas\Form\Form('my-form');
$form->add([
    'name' => 'captcha',
    'type' => \Laminas\Form\Element\Captcha::class,
    'options' => [
        'captcha' => new \Fet\LaminasCloudflareTurnstile\TurnstileCaptcha([
            'site_key' => getenv('CLOUDFLARE_TURNSTILE_SITE_KEY'),
            'secret_key' => getenv('CLOUDFLARE_TURNSTILE_SECRET_KEY'),
        ]),
    ]
]);
```

## View
```html
<?= $this->form($this->form); ?>

<!-- or -->
<?= $this->formElement($this->form->get('captcha')); ?>
<?= $this->formElementErrors($this->form->get('captcha')); ?>
```

# Tests
Run the tests with:

```bash
composer test
```