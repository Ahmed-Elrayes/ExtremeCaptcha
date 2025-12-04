Elrayes Extreme Captcha for Laravel

An advanced noisy CAPTCHA generator for Laravel featuring curved lines, heavy dots, wave distortion, a custom validation rule, and a convenient facade.

Repository: https://github.com/Ahmed-Elrayes/ExtremeCaptcha

Requirements
- PHP >= 8.1
- ext-gd
- Laravel 10+

Installation
1) Require the package via Composer:
```bash
composer require elrayes/extreme-captcha
```

2) Auto-discovery
- Service Provider: `Elrayes\\Extreme\\Captcha\\CaptchaServiceProvider`
- Facade alias: `ExtremeCaptcha`

3) Publish the configuration (optional):
```bash
php artisan vendor:publish --tag=extreme-captcha-config
```

Quick start
- Controller example:
```php
$captcha = app('extreme-captcha')->generate(); // returns ['code' => 'ABCDE', 'image' => 'data:image/png;base64,...']
return view('auth.register', compact('captcha'));
```

- Blade view:
```bladehtml
<img src="{{ $captcha['image'] }}" alt="captcha" />
```

- Validate user input against the generated captcha using the custom rule `extreme_captcha`:
```php
$request->validate([
    'captcha' => ['required', 'extreme_captcha'],
]);
```

- Using the facade:
```php
use ExtremeCaptcha;
$captcha = ExtremeCaptcha::generate();
```

Configuration
All options are publishable to `config/extreme-captcha.php`:
- `width`, `height`
- `font`, `font_size`
- `noise_pixels`, `noise_arcs`
- `background_color`, `text_color`
- `char_angle`, `wave_amplitude`, `wave_frequency`, `char_spacing`
- `case_sensitive`, `exclude_confusing`
- `session_key`

Troubleshooting
- Make sure the GD extension is enabled: `ext-gd`.
- If you customize the font path, ensure the file is readable by PHP.

Links
- Source & docs: https://github.com/Ahmed-Elrayes/ExtremeCaptcha
- Issues: https://github.com/Ahmed-Elrayes/ExtremeCaptcha/issues

License
This package is open-sourced software licensed under the MIT license. See `LICENSE`.

Credits
- Author: Ahmed Elrayes
