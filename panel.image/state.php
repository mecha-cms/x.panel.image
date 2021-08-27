<?php

return [
    'title' => 'Image', // The tab title
    'name' => 'image', // The page property name to store the image URL
    'rect' => [ // List of pre-defined image dimension(s)
        '1280x720' => "1280 \u{00D7} 720",
        '1024x538' => "1024 \u{00D7} 538",
        '300x300' => "300 \u{00D7} 300",
        '150x150' => "150 \u{00D7} 150"
    ],
    'rect-auto' => true, // Allow user to preserve image dimension as-is?
    'size' => [0, 204800] // Allowed minimum and maximum image file size in byte(s) (0 – 200 KB)
];