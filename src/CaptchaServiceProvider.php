<?php

namespace Elrayes\Extreme\Captcha;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class CaptchaServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('extreme-captcha', function () {
            return new CaptchaGenerator;
        });
        $this->app->singleton('extreme-captcha-renderer', function () {
            return new CaptchaRenderer;
        });
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/extreme-captcha.php', 'extreme-captcha');

        // Allow publishing the package config to the application
        $this->publishes([
            __DIR__ . '/../config/extreme-captcha.php' => config_path('extreme-captcha.php'),
        ], 'extreme-captcha-config');
        Validator::extend('extreme_captcha', function ($attribute, $value) {
            $sessionKey = config('extreme-captcha.session_key', 'extreme_captcha');
            return session($sessionKey) === $value;
        }, 'Invalid captcha code.');
    }
}
