<?php

return [
    'width' => 200,
    'height' => 70,
    'font' => __DIR__ . '/../resources/fonts/captcha1.ttf', // package-relative path
    'length' => 5,
    'font_size' => 32,
    'noise_pixels' => 400,
    'noise_arcs' => 6,
    'background_color' => [255, 255, 255],
    'text_color' => [0, 0, 0],
    'char_angle' => 20,           // max rotation per character
    'wave_amplitude' => 5,        // max vertical wave offset
    'wave_frequency' => 0.3,      // number of waves across text
    'case_sensitive' => true,    // use upper+lower or just upper
    'exclude_confusing' => true,  // exclude 0,O,1,l
    'char_spacing' => 6, // space between each character
    'session_key' => 'extreme_captcha',
];
