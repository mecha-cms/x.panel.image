<?php namespace x\panel__image;

function _($_) {
    if (0 === \strpos($_['type'] . '/', 'page/')) {
        \extract($GLOBALS, \EXTR_SKIP);
        $key = $state->x->{'panel.image'}->key ?? 'image';
        $title = $state->x->{'panel.image'}->title ?? 'Image';
        $page = new \Page($_['file'] ?: null);
        if ($image = $page[$key] ?? 0) {
            $link = 0 === \strpos($image, '//') || false !== \strpos($image, '://') || 0 !== \strpos($image, '/lot/asset/');
            $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['page']['lot']['fields']['lot']['image'] = [
                'name' => 'page[' . $key . ']',
                'stack' => 15,
                'title' => $title,
                'type' => 'text',
                'value' => $image,
                'value-after' => [
                    '2' => [
                        'onclick' => $link ? 'window.open(' . \htmlspecialchars(\json_encode($image)) . ');' : 'window.location.href=' . \htmlspecialchars(\json_encode(\Hook::fire('link', [\x\panel\to\link([
                            'part' => 0,
                            'path' => \substr($image, \strlen('/lot/')),
                            'query' => [
                                'kick' => $url->current,
                                'token' => $_['token'],
                                'trash' => !empty($state->x->panel->trash) ? \date('Y-m-d-H-i-s') : null
                            ],
                            'task' => 'let'
                        ])]))) . ';',
                        'style' => 'cursor: pointer;',
                        'tabindex' => 0
                    ],
                    'icon' => $link ? 'M14,3V5H17.59L7.76,14.83L9.17,16.24L19,6.41V10H21V3M19,19H5V5H12V3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V12H19V19Z' : 'M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19M8,9H16V19H8V9M15.5,4L14.5,3H9.5L8.5,4H5V6H19V4H15.5Z'
                ],
                'width' => true
            ];
        } else {
            $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['page']['lot']['fields']['lot']['image'] = [
                'name' => 'page[' . $key . ']',
                'stack' => 15,
                'title' => $title,
                'type' => 'blob',
                'width' => true
            ];
        }
    }
    return $_;
}

function get($_) {
    if ('GET' === $_SERVER['REQUEST_METHOD'] && $_['file']) {
        \extract($GLOBALS, \EXTR_SKIP);
        $key = $state->x->{'panel.image'}->key ?? 'image';
        $page = \From::page(\file_get_contents($_['file']), false);
        if ($blob = $page[$key] ?? 0) {
            if (0 === \strpos($blob, '//') || false !== \strpos($blob, '://')) {
                return $_; // Ignore external image link
            }
            if (!\is_file($file = \PATH . \strtr($blob, ['/' => \D]))) {
                unset($page[$key]);
                if (\is_int(\file_put_contents($_['file'], \To::page($page)))) {
                    // \class_exists("\\Alert") && \Alert::info('Image data has been deleted automatically because the file no longer exists.');
                    // Redirect once!
                    \kick($url->current);
                }
            }
            return $_;
        }
    }
    return \x\panel__image\set($_);
}

function set($_) {
    $blob = $_POST['page'][$key = $state->x->{'panel.image'}->key ?? 'image'] ?? [];
    if ('POST' !== $_SERVER['REQUEST_METHOD'] || empty($blob)) {
        return $_;
    }
    \extract($GLOBALS, \EXTR_SKIP);
    // Get folder path to store the image or use folder `.\lot\asset\user\user-name` by default
    $folder = \rtrim(\strtr($state->x->{'panel.image'}->folder ?? ($d = \LOT . \D . 'asset' . \D . 'user' . \D . $user->name), ['/' => \D]), \D);
    // Make sure folder path is relative to the application root to prevent directory traversal attack
    if (0 === \strpos($folder . \D, \PATH . \D)) {
        // Create folder if it does not exist
        if (!\stream_resolve_include_path($folder)) {
            \mkdir($folder, 0775, true);
        }
    } else {
        $_['alert']['error'][$folder] = ['Could not create or use folder %s to store the image because it is not relative to the application root.', '<code>' . \x\panel\from\path($folder) . '</code>'];
        return $_;
    }
    // Upload an image
    if (is_array($blob)) {
        [$min, $max] = \array_replace(
            [0, 0],
            (array) ($state->x->panel->guard->file->size ?? []),
            (array) ($state->x->{'panel.image'}->guard->file->size ?? [])
        );
        $file = $folder . \D . ($name = \To::file($blob['name'] ?? ""));
        // Remove the image link
        if (!empty($blob['let'])) {
            unset($_POST['page']['image']);
            if (-2 === $blob['let'] && \is_file($file)) {
                if (\unlink($file)) {
                    $_['alert']['success'][$file] = ['%s %s successfully deleted.', ['Image', '<code>' . \x\panel\from\path($file) . '</code>']];
                } else {
                    $_['alert']['error'][$file] = ['Could not delete %s %s due to file system error.', ['image', '<code>' . \x\panel\from\path($file) . '</code>']];
                }
            }
            return $_;
        }
        if (!$name || !\is_string($name)) {
            $_['alert']['error'][$file] = 'The file you are about to upload doesn\'t seem to have a valid file name.';
            return $_;
        }
        if (\is_file($file)) {
            $_['alert']['info'][$file] = ['%s %s already exists.', ['Image', '<code>' . \x\panel\from\path($file) . '</code>']];
            $_POST['page'][$key] = \strtr($file, [
                \PATH . \D => '/',
                \D => '/'
            ]);
            return $_;
        }
        $x = \pathinfo($name, \PATHINFO_EXTENSION);
        if (false === \strpos(',apng,avif,gif,jpeg,jpg,png,svg,webp,', ',' . $x . ',')) {
            $_['alert']['error'][$file] = ['%s extension %s is not allowed.', ['Image', '<code>' . $x . '</code>']];
            return $_;
        }
        if (!isset($blob['type']) || 0 !== \strpos($blob['type'], 'image/')) {
            $_['alert']['error'][$file] = 'The file you are about to upload doesn\'t seem to be an image.';
            return $_;
        }
        if (!isset($blob['size']) || $blob['size'] > $max) {
            $_['alert']['error'][$file] = ['Maximum %s size allowed to upload is %s.', ['image', '<code>' . \size($max) . '</code>']];
            return $_;
        }
        if (!isset($blob['size']) || $blob['size'] < $min) {
            $_['alert']['error'][$file] = ['Minimum %s size allowed to upload is %s.', ['image', '<code>' . \size($min) . '</code>']];
            return $_;
        }
        if (\is_int($status = \store(\dirname($file), $blob, $name))) {
            $_['alert']['error'][$file] = 'Failed to upload with status code: ' . $status;
            return $_;
        }
        $_['alert']['success'][$file] = ['%s %s successfully uploaded.', ['Image', '<code>' . \x\panel\from\path($status) . '</code>']];
        $_POST['page'][$key] = \strtr($status, [
            \PATH . \D => '/',
            \D => '/'
        ]);
        return $_;
    }
    // Update the image link
    if (\is_string($blob)) {
        if (0 === \strpos($blob, '//') || false !== \strpos($blob, '://')) {
            return $_; // Ignore external image link
        }
        if (!\is_file($file = \PATH . \strtr($blob, ['/' => \D]))) {
            unset($_POST['page'][$key]);
        }
        return $_;
    }
    return $_;
}

\Hook::set('_', __NAMESPACE__ . "\\_", 20);
\Hook::set('do.page.get', __NAMESPACE__ . "\\get", 20);
\Hook::set('do.page.set', __NAMESPACE__ . "\\set", 20);