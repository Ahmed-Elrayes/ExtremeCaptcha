<?php

namespace Elrayes\Extreme\Captcha;

/**
 *
 */
class CaptchaGenerator
{
    /**
     * Generates a random captcha code and stores it in the session.
     *
     * The generated code is a six-character string created by shuffling a set of
     * predefined alphanumeric characters (excluding potentially confusing ones)
     * and selecting the first six characters. The code is stored in the session
     * under the key 'extreme_captcha' and rendered using the application's captcha renderer.
     *
     * @return array The rendered captcha based on the generated code.
     */
    public function generate(): array
    {
        $code = $this->generateCode();
        $sessionKey = config('extreme-captcha.session_key', 'extreme_captcha');
        session([$sessionKey => $code]);
        return [
            'code' => $code,
            'image' => app('extreme-captcha-renderer')->render($code),
        ];
    }

    /**
     * Generates a random code based on the configuration settings.
     *
     * The code length, sensitivity to character case, and exclusion of confusing
     * characters can be configured through the application's configuration files.
     *
     * @return string The generated random code.
     */
    public function generateCode(): string
    {
        $length = config('extreme-captcha.length', 5);
        $caseSensitive = config('extreme-captcha.case_sensitive', false);
        $excludeConfusing = config('extreme-captcha.exclude_confusing', true);

        $chars = $caseSensitive
            ? 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789'
            : 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        if (!$excludeConfusing) {
            $chars = $caseSensitive
                ? 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
                : 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        }

        $code = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[rand(0, $max)];
        }

        return $code;
    }

}
