<?php

return [
    // Make the image field active
    'active' => true,
    // Set custom image field description
    'description' => true,
    // Set custom image folder path as file upload target
    'folder' => null,
    // Set custom image data key for page file
    'key' => 'image',
    // Set custom image file name (without the extension)
    'name' => null,
    // Set this value to `true` to hide the image field
    'skip' => false,
    // Set custom image field title
    'title' => 'Image',
    // Force user to fill out the image field to be able to submit the form
    'vital' => false,
    // These option(s) apply when the `image` extension is active
    'fit' => null, // Set maximum image height and width to make sure that the new width and height will not overflow the maximum width and height
    'height' => null, // Set maximum image height
    'width' => null, // Set maximum image width
    'x' => null // Convert image type by its file extension
];