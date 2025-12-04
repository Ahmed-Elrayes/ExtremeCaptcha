<?php

namespace Elrayes\Extreme\Captcha;

class CaptchaRenderer
{
    /**
     * Renders a CAPTCHA image based on the provided code and configuration settings.
     *
     * The function generates a CAPTCHA with noise (pixels and arcs), applies
     * wave distortions, and renders the characters in a randomized manner.
     * It produces a base64-encoded PNG image suitable for embedding in HTML.
     *
     * Configuration options used for rendering include:
     * - Image dimensions: width and height.
     * - Font settings: file path and size.
     * - Color settings: background color and text color.
     * - Noise settings: amount of noise pixels and arcs.
     * - Character transformations: maximum rotation angle, wave amplitude,
     *   wave frequency, and text positioning.
     *
     * All characters are positioned with calculated spacing and transformed
     * based on random angles and distortions to enhance CAPTCHA security.
     *
     * @param string $code The character string to render in the CAPTCHA image.
     * @return string A base64-encoded PNG image data URI.
     * @throws \InvalidArgumentException If the image width or height is less than or equal to zero.
     */
    public function render(string $code): string
    {
        $w = config('extreme-captcha.width', 200);
        $h = config('extreme-captcha.height', 70);
        // Default to a bundled font in resources/fonts; can be overridden via config
        $font = config('extreme-captcha.font') ?? __DIR__ . '/../resources/fonts/captcha1.ttf';
        if (!file_exists($font)) {
            throw new \RuntimeException("Captcha font not found at: {$font}");
        }
        $fontSize = config('extreme-captcha.font_size', 32);
        $bgColor = config('extreme-captcha.background_color', [255, 255, 255]);
        $textColor = config('extreme-captcha.text_color', [0, 0, 0]);
        $noisePixels = config('extreme-captcha.noise_pixels', 400);
        $noiseArcs = config('extreme-captcha.noise_arcs', 6);
        $maxCharAngle = config('extreme-captcha.char_angle', 20);
        $waveAmplitude = config('extreme-captcha.wave_amplitude', 5);
        $waveFrequency = config('extreme-captcha.wave_frequency', 0.3);
        $charSpacing = config('extreme-captcha.char_spacing', 5); // extra spacing between chars

        if ($w <= 0 || $h <= 0) {
            throw new \InvalidArgumentException("Captcha width and height must be greater than 0.");
        }

        $img = imagecreatetruecolor($w, $h);
        $bg = imagecolorallocate($img, $bgColor[0], $bgColor[1], $bgColor[2]);
        imagefilledrectangle($img, 0, 0, $w, $h, $bg);

        // Noise pixels
        for ($i = 0; $i < $noisePixels; $i++) {
            $c = imagecolorallocate($img, rand(0, 120), rand(0, 120), rand(0, 120));
            imagesetpixel($img, rand(0, $w - 1), rand(0, $h - 1), $c);
        }

        // Noise arcs
        for ($i = 0; $i < $noiseArcs; $i++) {
            $c = imagecolorallocate($img, rand(0, 255), rand(0, 255), rand(0, 255));
            imagearc($img, rand(0, $w), rand(0, $h), rand(80, 280), rand(10, 150), 0, 360, $c);
        }

        // Per-character rendering with wave distortion
        $len = strlen($code);
        $yBaseline = ($h + $fontSize) / 2;

        // Calculate total width including spacing
        $totalWidth = 0;
        $charBoxes = [];
        for ($i = 0; $i < $len; $i++) {
            $angle = rand(-$maxCharAngle, $maxCharAngle);
            $bbox = imagettfbbox($fontSize, $angle, $font, $code[$i]);
            $charWidth = abs($bbox[2] - $bbox[0]) + $charSpacing;
            $charBoxes[] = ['angle' => $angle, 'width' => $charWidth];
            $totalWidth += $charWidth;
        }

        $x = ($w - $totalWidth) / 2;

        foreach ($charBoxes as $i => $charBox) {
            $angle = $charBox['angle'];
            $char = $code[$i];

            // Bounding box for this character
            $bbox = imagettfbbox($fontSize, $angle, $font, $char);
            $charHeight = abs($bbox[7] - $bbox[1]); // vertical height
            $yOffset = ($h - $charHeight) / 2; // center vertically

            // Wave effect: sine-based vertical offset
            $yWave = $yOffset + sin($i * $waveFrequency) * $waveAmplitude;
            $y = $yWave + rand(-2, 2); // slight jitter

            // Text color variation
            $color = imagecolorallocate(
                $img,
                max(0, min(255, $textColor[0] + rand(-30, 30))),
                max(0, min(255, $textColor[1] + rand(-30, 30))),
                max(0, min(255, $textColor[2] + rand(-30, 30)))
            );

            imagettftext($img, $fontSize, $angle, $x, $y + $fontSize, $color, $font, $char);
            $x += $charBox['width'];
        }

        // Use memory stream instead of ob_start
        $stream = fopen('php://memory', 'r+');
        imagepng($img, $stream);
        rewind($stream);
        $imageData = stream_get_contents($stream);
        fclose($stream);
        imagedestroy($img);

        return 'data:image/png;base64,' . base64_encode($imageData);
    }
}
