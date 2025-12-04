<?php

namespace Elrayes\Extreme\Captcha\Facades;

use Illuminate\Support\Facades\Facade;

class ExtremeCaptcha extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'extreme-captcha';
    }
}
