<?php namespace _\lot\x\panel\image\page;

function fields($_) {
    extract($GLOBALS, \EXTR_SKIP);
    $has_image_extension = !empty($state->x->image);
    $title = $state->x->{'panel.image'}->title ?? 'Image';
    $key = $state->x->{'panel.image'}->name ?? 'image';
    $image = (new \Page($_['f']))->{$key};
    $resize_options = (array) ($state->x->{'panel.image'}->rect ?? []);
    if (!empty($state->x->{'panel.image'}->{'rect-auto'})) {
        $resize_options = ["" => 'None'] + $resize_options;
    }
    $js = <<<JS
<script>
let blob = document.forms.edit['image[blob]'],
    name = document.forms.edit['image[name]'];
if (blob && name) {
    blob.addEventListener('change', function() {
        name.value = this.value.split(/[\\\\/]/).pop();
    }, false);
}
</script>
JS;
    $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['image'] = [
        'title' => $title,
        'lot' => [
            'fields' => [
                'type' => 'fields',
                'lot' => [
                    'view' => [
                        'title' => 'Image',
                        'type' => 'field',
                        'content' => '<img alt="' . \basename($image) . '" src="' . $image . '?v=' . \filemtime($_['f']) . '" loading="lazy"><input name="image[link]" type="hidden" value="' . $image . '">',
                        'hidden' => 's' === $_['task'] || !$image,
                        'stack' => 9.9
                    ],
                    'image' => 'g' === $_['task'] && $image ? [
                        'title' => "",
                        'type' => 'items',
                        'name' => 'image',
                        'lot' => [
                            'let' => [
                                'title' => 'Delete',
                                'value' => 1
                            ]
                        ],
                        'tags' => ['mt:0'],
                        'stack' => 10
                    ] : [
                        'title' => 'File',
                        // Unless it’s prefixed by `blob`, `data`, `file` or `page`,
                        // this field data will not be stored to a file automatically
                        'type' => 'blob',
                        'name' => 'image[blob]',
                        'stack' => 10
                    ],
                    'name' => [
                        'description' => 'Set name of the image. Leave empty to use the sanitized version of the original file name.',
                        'type' => 'text',
                        'pattern' => "^([_.]?[a-z\\d]+([_.-][a-z\\d]+)*)?\\.(gif|jpe?g|png)$",
                        // Unless it’s prefixed by `blob`, `data`, `file` or `page`,
                        // this field data will not be stored to a file automatically
                        'name' => 'image[name]',
                        'alt' => 'foo-bar.jpg',
                        'hidden' => 'g' === $_['task'] && $image,
                        'stack' => 20
                    ],
                    'rect' => [
                        'title' => 'Dimension',
                        // Unless it’s prefixed by `blob`, `data`, `file` or `page`,
                        // this field data will not be stored to a file automatically
                        'name' => 'image[rect]',
                        'description' => $has_image_extension ? 'Set maximum width and height of the image.' : 'This feature requires you to install the <a href="https://github.com/mecha-cms/x.image" target="_blank">image</a> extension.',
                        'type' => 'combo',
                        'active' => $has_image_extension,
                        'sort' => false,
                        'lot' => $resize_options,
                        'value' => \Session::get(\dechex(\crc32('panel.image.rect'))),
                        'hidden' => 'g' === $_['task'] && $image,
                        'stack' => 30
                    ]
                ],
                'stack' => 10
            ],
            'script' => [
                '0' => false, // Remove node name, so it will leave only the content
                'type' => 'content',
                'content' => $js,
                'hidden' => 'g' === $_['task'] && $image,
                'stack' => 40
            ]
        ],
        'stack' => 11
    ];
    return $_;
}

function requests($_) {
    // Not a `POST` request, abort!
    if ('POST' !== $_SERVER['REQUEST_METHOD']) {
        return $_;
    }
    // Store current image rect value to session, so that when you do open a new page editor,
    // then the image rect field value will be set to the previous image rect value automatically
    if ($rect = $_['form']['image']['rect'] ?? "") {
        \Session::set(\dechex(\crc32('panel.image.rect')), $rect);
    }
    // Abort by previous hook’s return value if any
    if (!empty($_['alert']['error'])) {
        return $_;
    }
    extract($GLOBALS, \EXTR_SKIP);
    $image = $_['form']['image'] ?? [];
    $key = $state->x->{'panel.image'}->name ?? 'image';
    $link = null; // Prepare page’s `image` data
    $sizes = (array) ($state->x->{'panel.image'}->size ?? []);
    // Delete or update
    if (!empty($image['link'])) {
        // Delete
        if (!empty($image['let'])) {
            $path = \strtr($image['link'], [
                $url . "" => "",
                '../' => "", // Prevent directory traversal attack
                '/' => \DS
            ]);
            if (\is_file($f = \ROOT . $path)) {
                // Just to be sure
                if (false === \strpos(',gif,jpeg,jpg,png,', ',' . \pathinfo($f, \PATHINFO_EXTENSION) . ',')) {
                    $_['alert']['error'][] = ['Could not delete %s because it is likely not an image.', '<code>' . \basename($path) . '</code>'];
                } else if (0 !== strpos(mime_content_type($f), 'image/')) {
                    $_['alert']['error'][] = ['Could not delete %s because it is likely not an image.', '<code>' . \basename($path) . '</code>'];
                } else {
                    \unlink($f);
                    $link = false;
                    unset($_['form']['page'][$key]);
                    $_['alert']['success'][] = ['%s %s successfully deleted.', ['Image', '<code>' . \strtr($f, [\ROOT => '.']) . '</code>']];
                }
            }
        // Update
        } else {
            $_['form']['page'][$key] = $link = $image['link'];
        }
    // Upload
    } else if (!empty($image['blob']['name'])) {
        $x = \pathinfo($image['blob']['name'] = $name = \basename(\To::file($image['name'] ?? $image['blob']['name'])), \PATHINFO_EXTENSION);
        $folder = \LOT . \DS . 'asset' . \DS . $x . (1 === $user['status'] ? "" : \DS . $user->key);
        $f = '<code>' . \strtr($folder . \DS . $name, [\ROOT => '.']) . '</code>'; // File name preview
        // Check for image file extension
        if (false === \strpos(',gif,jpeg,jpg,png,', ',' . $x . ',')) {
            $_['alert']['error'][] = ['Please upload an image file.'];
        // Check for image file type
        } else if (0 !== \strpos($image['blob']['type'], 'image/')) {
            $_['alert']['error'][] = ['Please upload an image file.'];
        // Check for image file size
        } else if ($image['blob']['size'] < ($i = $sizes[0] ?? 0)) {
            $_['alert']['error'][] = ['Minimum file size allowed to upload is %s.', '<code>' . \File::sizer($i) . '</code>'];
        } else if ($image['blob']['size'] > ($i = $sizes[1] ?? 204800)) {
            $_['alert']['error'][] = ['Maximum file size allowed to upload is %s.', '<code>' . \File::sizer($i) . '</code>'];
        } else {
            // Upload…
            $response = \File::push($image['blob'], $folder);
            if (false === $response) {
                $_['alert']['info'][] = ['%s %s already exists.', ['Image', $f]];
                $_['form']['page'][$key] = $link = \To::URL($folder . \DS . $name);
            // Check for error code
            } else if (\is_int($response)) {
                $_['alert']['error'][] = 'Failed to upload with error code: ' . $response;
            } else {
                // Resize image
                if (isset($state->x->image) && !empty($image['rect']) && \preg_match('/^(\d+)x(\d+)$/', $image['rect'], $m)) {
                    $blob = new \Image($folder . \DS . $name);
                    $blob->crop((int) $m[1], (int) $m[2]);
                    $blob->let(); // Delete current image
                    $blob->store($blob->path); // Save as current image with the updated size
                }
                $_['alert']['success'][] = ['%s %s successfully uploaded.', ['Image', $f]];
                $_['form']['page'][$key] = $link = \To::URL($response);
            }
            // Remove temporary form data
            unset($_['form']['image'], $_POST['image']);
        }
    }
    // Use the uploaded image URL as the page’s `image` property
    if (isset($link)) {
        $data = \From::page(\file_get_contents($_['f']), true);
        if (false !== $link) {
            $data[$key] = $link;
        } else {
            unset($data[$key]);
        }
        \file_put_contents($_['f'], \To::page($data));
    }
    return $_;
}

\Hook::set('_', __NAMESPACE__ . "\\fields");

\Hook::set([
    'do.page.get',
    'do.page.set'
], __NAMESPACE__ . "\\requests", 20); // Make sure to run this hook after the default page create/update event
