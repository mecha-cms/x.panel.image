<?php namespace x\panel__image;

function form($_) {
    extract($GLOBALS, \EXTR_SKIP);
    $has_image_extension = isset($state->x->image);
    $c = $state->x->{'panel.image'};
    $key = $c->name ?? 'image';
    // Not a `POST` request, abort!
    if ('post' !== $_['form']['type']) {
        // Or, check if current task is a delete task
        if ('l' === $_['task'] && \is_file($f = $_['f'])) {
            $image = \From::page(\file_get_contents($f), true)[$key] ?? null;
            if ($image && \is_file($ff = \ROOT . $image)) {
                // Deleting the page will automatically delete the image as well. This situation may not be expected
                // as the author could have used this image in other page files. So I decided to comment-out this line
                // until someone has a better idea. FYI, the file delete action is done via `GET` request, so the
                // checked state of the “Delete” field in the form will not give any difference.
                // The safest way to delete the image and the page for now is to check the “Delete” field and then hit
                // the “Update” button. After the image delete is done, press the “Delete” button to delete the page.
                /*
                if (\unlink($ff)) {
                    $_['alert']['info'][$ff] = ['%s %s successfully deleted.', ['Image', '<code>' . \x\panel\from\path($ff) . '</code>']];
                }
                */
            }
        }
        return $_;
    }
    // Store current image rect value to session, so that when you open a new page editor,
    // the image rect field value will be set to the previous image rect value automatically
    if ($rect = $_['form']['lot']['image']['rect'] ?? "") {
        \Session::set(\dechex(\crc32('panel.image.rect')), $rect);
    }
    // Abort by previous hook’s return value if any
    if (!empty($_['alert']['error'])) {
        return $_;
    }
    $image = $_['form']['lot']['image'] ?? [];
    $link = null; // Prepare page’s `image` data
    $sizes = (array) ($c->size ?? []);
    // Delete or update
    if (!empty($image['link'])) {
        // Delete
        if (!empty($image['let'])) {
            $path = \strtr($image['link'], [
                '../' => "", // Prevent directory traversal attack
                '/' => \DS
            ]);
            if (\is_file($f = \ROOT . $path)) {
                // Just to be sure
                if (false === \strpos(',gif,jpeg,jpg,png,', ',' . \pathinfo($f, \PATHINFO_EXTENSION) . ',')) {
                    $_['alert']['error'][$f] = ['Could not delete %s because it is likely not an image.', '<code>' . \x\panel\from\path($f) . '</code>'];
                } else if (0 !== \strpos(\mime_content_type($f), 'image/')) {
                    $_['alert']['error'][$f] = ['Could not delete %s because it is likely not an image.', '<code>' . \x\panel\from\path($f) . '</code>'];
                } else {
                    \unlink($f);
                    $link = false;
                    unset($_['form']['lot']['page'][$key]);
                    $_['alert']['success'][$f] = ['%s %s successfully deleted.', ['Image', '<code>' . \x\panel\from\path($f) . '</code>']];
                }
            }
        // Update
        } else {
            $_['form']['lot']['page'][$key] = $link = $image['link'];
        }
    // Upload
    } else if (!empty($image['blob']['name'])) {
        $x = \pathinfo($image['blob']['name'] = $name = \basename(\To::file($image['name'] ?? $image['blob']['name'])), \PATHINFO_EXTENSION);
        $folder = \LOT . \DS . 'asset' . \DS . $x . (1 === $user->status ? "" : \DS . $user->user);
        $n = '<code>' . \x\panel\from\path($f = $folder . \DS . $name) . '</code>'; // File name preview
        // Check for image file extension
        if (false === \strpos(',gif,jpeg,jpg,png,', ',' . $x . ',')) {
            $_['alert']['error'][$f] = ['Please upload an image file.'];
        // Check for image file type
        } else if (0 !== \strpos($image['blob']['type'], 'image/')) {
            $_['alert']['error'][$f] = ['Please upload an image file.'];
        // Check for image file size
        } else if ($image['blob']['size'] < ($i = $sizes[0] ?? 0)) {
            $_['alert']['error'][$f] = ['Minimum file size allowed to upload is %s.', '<code>' . \File::sizer($i) . '</code>'];
        } else if ($image['blob']['size'] > ($i = $sizes[1] ?? 204800)) {
            $_['alert']['error'][$f] = ['Maximum file size allowed to upload is %s.', '<code>' . \File::sizer($i) . '</code>'];
        } else {
            // Upload…
            $response = \File::push($image['blob'], $folder);
            if (false === $response) {
                $_['alert']['info'][$f] = ['%s %s already exists.', ['Image', $n]];
                $_['form']['lot']['page'][$key] = $link = \To::URL($f);
            // Check for error code
            } else if (\is_int($response)) {
                $_['alert']['error'][$f] = 'Failed to upload with error code: ' . $response;
            } else {
                // Resize image
                if ($has_image_extension && !empty($image['rect']) && \preg_match('/^(\d+)x(\d+)$/', $image['rect'], $m)) {
                    $blob = new \Image($f);
                    $blob->crop((int) $m[1], (int) $m[2]);
                    $blob->let(); // Delete current image
                    $blob->store($blob->path); // Save as current image with the updated size
                }
                $_['alert']['success'][$f] = ['%s %s successfully uploaded.', ['Image', $n]];
                $_['form']['lot']['page'][$key] = $link = \To::URL($response);
            }
            // Remove temporary form data
            unset($_['form']['lot']['image']);
        }
    }
    // Use the uploaded image URL as the page’s `image` property
    if (isset($link)) {
        $data = \From::page(\file_get_contents($_['f']), true);
        if (false !== $link) {
            $data[$key] = \URL::short($link, false); // Relative to the root folder
        } else {
            unset($data[$key]);
        }
        \file_put_contents($_['f'], \To::page($data));
    }
    return $_;
}

function tab($_) {
    extract($GLOBALS, \EXTR_SKIP);
    $has_image_extension = isset($state->x->image);
    $c = $state->x->{'panel.image'} ?? [];
    $title = $c->title ?? 'Image';
    $key = $c->name ?? 'image';
    $image = \is_file($f = $_['f']) ? (\From::page(\file_get_contents($f), true)[$key] ?? null) : null;
    $resize_options = (array) ($c->rect ?? []);
    if (!empty($c->{'rect-auto'})) {
        $resize_options = ["" => 'None'] + $resize_options;
    }
    $js = <<<JS
(doc => {
    let blob = doc.forms.set['image[blob]'],
        name = doc.forms.set['image[name]'];
    if (blob && name) {
        blob.addEventListener('change', function() {
            name.value = this.value.split(/[\\\\/]/).pop().toLowerCase().replace(/^(.*?)[.]([^.]*?)$/g, (m0, m1, m2) => {
                return m1.replace(/[^a-z\\d]+/g, '-') + '.' + m2.replace(/[^a-z\\d]+/g, "");
            });
        });
    }
})(document);
JS;
    $_['asset']['script']['panel.image'] = [
        'content' => $js,
        'id' => false,
        'stack' => 20
    ];
    $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['image'] = [
        'title' => $title,
        'lot' => [
            'fields' => [
                'type' => 'fields',
                'lot' => [
                    'view' => [
                        'title' => 'Image',
                        'description' => '<a href="' . $url . $image . '" target="_blank">.' . $image . '</a>',
                        'type' => 'field',
                        'content' => '<img alt="' . \basename($image) . '" src="' . $url . $image . '?v=' . \filemtime($_['f']) . '" loading="lazy"><input name="image[link]" type="hidden" value="' . $image . '">',
                        'skip' => 's' === $_['task'] || !$image,
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
                        'hint' => 'foo-bar.jpg',
                        'skip' => 'g' === $_['task'] && $image,
                        'stack' => 20
                    ],
                    'rect' => [
                        'title' => 'Dimension',
                        // Unless it’s prefixed by `blob`, `data`, `file` or `page`,
                        // this field data will not be stored to a file automatically
                        'name' => 'image[rect]',
                        'description' => $has_image_extension ? 'Set maximum width and height of the image.' : 'This feature requires you to install the <a href="https://github.com/mecha-cms/x.image" target="_blank">image</a> extension.',
                        'type' => 'option',
                        'active' => $has_image_extension,
                        'sort' => false,
                        'lot' => $resize_options,
                        'value' => \Session::get(\dechex(\crc32('panel.image.rect'))),
                        'skip' => 'g' === $_['task'] && $image,
                        'stack' => 30
                    ]
                ],
                'stack' => 10
            ]
        ],
        'stack' => 11
    ];
    return $_;
}

// Make sure to run this hook after the default page create/update event
\Hook::set([
    'do.page.get',
    'do.page.set'
], __NAMESPACE__ . "\\form", 20);

// Make sure to run this hook before the page is deleted, because we need
// to get the `image` property to remove on page delete event
\Hook::set([
    'do.page.let'
], __NAMESPACE__ . "\\form", 9.9);
\Hook::set('_', __NAMESPACE__ . "\\tab", 20);
