<?php namespace x\panel__image;

require __DIR__ . \D . '..' . \D . 'engine' . \D . 'f.php';

if (!empty($state->x->{'panel.image'}->description)) {
    $_['asset']['panel.image'] = [
        'id' => false,
        'path' => \stream_resolve_include_path(__DIR__ . \D . '..' . \D . 'index' . (\defined("\\TEST") && \TEST ? '.' : '.min.') . 'js'),
        'stack' => 30
    ];
}

function _($_) {
    if (0 === \strpos($_['type'] . '/', 'blob/image/')) {
        if (isset($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['blob']['lot']['fields']['lot']['blob'])) {
            $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['blob']['lot']['fields']['lot']['blob']['title'] = 'Image';
        }
        unset($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['blob']['lot']['fields']['lot']['options']['lot']['extract']);
        if (empty($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['blob']['lot']['fields']['lot']['options']['lot'])) {
            unset($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['blob']['lot']['fields']['lot']['options']);
        }
        return $_;
    }
    if (0 === \strpos($_['type'] . '/', 'page/')) {
        \extract($GLOBALS, \EXTR_SKIP);
        $page = new \Page($_['file'] ?: null);
        $active = !isset($state->x->{'panel.image'}->active) || $state->x->{'panel.image'}->active;
        $description = $state->x->{'panel.image'}->description ?? true;
        $image = $page[$key = $state->x->{'panel.image'}->key ?? 'image'] ?? "";
        $link = 0 === \strpos($image, '//') || 0 === \strpos($image, 'data:image/') || false !== \strpos($image, '://');
        $skip = !empty($state->x->{'panel.image'}->skip);
        $title = $state->x->{'panel.image'}->title ?? 'Image';
        $type = $_['query']['image'] ?? null;
        $vital = !empty($state->x->{'panel.image'}->vital);
        $file = $image && !$link && \is_file($f = \PATH . $image) ? \path($f) : false;
        if ($image && !\array_key_exists('image', (array) ($_['query'] ?? []))) {
            $type = 'link';
        }
        // Set default field description if `description` value is truthy but not translate-able
        if ($description && !\is_array($description) && !\is_string($description)) {
            if ('link' === $type) {
                $description = ['Paste an image link or %s to select an image file.', '<a aria-description="' . \eat(\i('This action will reload the page.')) . '" href="' . \eat($url->query(['image' => 'blob'])) . '">' . \i('click here') . '</a>'];
            } else {
                $description = ['Select an image file or %s to paste an image link.', '<a aria-description="' . \eat(\i('This action will reload the page.')) . '" href="' . \eat($url->query(['image' => 'link'])) . '">' . \i('click here') . '</a>'];
            }
        }
        $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['page']['lot']['fields']['lot']['image'] = [
            'active' => $active,
            'description' => $description,
            'file' => $_['file'] ?: null,
            'key' => $key,
            'name' => 'page[' . $key . ']',
            'skip' => $skip,
            'stack' => 15,
            'title' => $title,
            'type' => \trim('image/' . ($type ?? ""), '/'),
            'value' => "" !== $image ? $image : null,
            'vital' => $vital,
            'width' => true
        ];
        if ('link' !== $type) {
            if (isset($_['lot']['desk']['lot']['form']) && isset($state->x->image)) {
                foreach (['fit', 'height', 'width', 'x'] as $v) {
                    $_['lot']['desk']['lot']['form']['values']['page'][$key][$v] = $state->x->{'panel.image'}->{$v} ?? null;
                }
            }
        }
        // Make `image` query to be unset by default
        unset($_GET['image'], $GLOBALS['_']['query']['image'], $_['query']['image']);
    }
    return $_;
}

function blob($_) {
    if ('POST' !== $_SERVER['REQUEST_METHOD']) {
        return $_;
    }
    $folder = $_['folder'] ?? "";
    if (isset($_POST['blobs']) && \is_array($_POST['blobs'])) {
        foreach ($_POST['blobs'] as $k => $v) {
            if (!empty($v['status'])) {
                continue;
            }
            $x = \pathinfo($v['name'], \PATHINFO_EXTENSION);
            // Allow image file(s) only
            if (false === \strpos(',apng,avif,bmp,gif,jpeg,jpg,png,svg,webp,xbm,xpm,', ',' . $x . ',')) {
                $_['alert']['error'][$folder ?: \uniqid()] = 'Please upload image files only.';
            } else if (!isset($v['type']) || 0 !== \strpos($v['type'], 'image/')) {
                $_['alert']['error'][$folder ?: \uniqid()] = 'Please upload image files only.';
            }
        }
    }
    return $_;
}

function get($_) {
    if ('GET' === $_SERVER['REQUEST_METHOD'] && $_['file']) {
        \extract($GLOBALS, \EXTR_SKIP);
        $key = $state->x->{'panel.image'}->key ?? 'image';
        $page = \From::page(\file_get_contents($_['file']), true);
        if ($blob = (string) ($page[$key] ?? "")) {
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
    \extract($GLOBALS, \EXTR_SKIP);
    $blob = $_POST['page'][$key = $state->x->{'panel.image'}->key ?? 'image'] ?? [];
    $vital = !empty($state->x->{'panel.image'}->vital);
    if ('POST' !== $_SERVER['REQUEST_METHOD']) {
        return $_;
    }
    if (empty($blob)) {
        if ($vital) {
            $_['alert']['error'][] = 'Missing image data.';
        }
        return $_;
    }
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
    if (\is_array($blob)) {
        [$min, $max] = \array_replace(
            [0, 0],
            (array) ($state->x->panel->guard->file->size ?? []),
            (array) ($state->x->{'panel.image'}->guard->file->size ?? [])
        );
        if (!empty($blob['status'])) {
            $status = $blob['status'];
            // No file was uploaded
            if (\UPLOAD_ERR_NO_FILE === $status && $vital) {
                // $_['alert']['error'][$folder] = 'Failed to upload with status code: ' . \s($blob['status']);
                $_['alert']['error'][$folder] = 'Please upload an image.';
            }
            unset($_POST['page'][$key]);
            return $_;
        }
        $n = $state->x->{'panel.image'}->name ?? "";
        $name = \To::file($blob['name'] ?? "");
        if (!\is_string($name)) {
            $_['alert']['error'][$folder] = 'The file you are about to upload doesn\'t seem to have a valid file name.';
            return $_;
        }
        $x = \pathinfo($name, \PATHINFO_EXTENSION);
        if (\is_file($file = $folder . \D . ($name = $n ? $n . '.' . $x : $name))) {
            $_['alert']['info'][$file] = ['File %s already exists.', '<code>' . \x\panel\from\path($file) . '</code>'];
            $_POST['page'][$key] = \strtr($file, [
                \PATH . \D => '/',
                \D => '/'
            ]);
            return $_;
        }
        if (false === \strpos(',apng,avif,bmp,gif,jpeg,jpg,png,svg,webp,xbm,xpm,', ',' . $x . ',')) {
            $_['alert']['error'][$file] = 'Please upload an image.';
            return $_;
        }
        if (!isset($blob['type']) || 0 !== \strpos($blob['type'], 'image/')) {
            $_['alert']['error'][$file] = 'The file you are about to upload doesn\'t seem to be an image.';
            return $_;
        }
        if (!isset($blob['size']) || $blob['size'] > $max) {
            $_['alert']['error'][$file] = ['Maximum file size allowed to upload is %s.', '<code>' . \size($max) . '</code>'];
            return $_;
        }
        if (!isset($blob['size']) || $blob['size'] < $min) {
            $_['alert']['error'][$file] = ['Minimum file size allowed to upload is %s.', '<code>' . \size($min) . '</code>'];
            return $_;
        }
        if (\is_int($status = \store(\dirname($file), $blob, $name))) {
            $_['alert']['error'][$file] = 'Failed to upload with status code: ' . \s($status);
            return $_;
        }
        // Allow to manipulate the image file if the feature is available and it supports the image format
        if (isset($state->x->image) && \is_callable("\\x\\image\\from\\" . $x)) {
            $image = new \Image($file);
            // Prioritize `Image::fit()` over `Image::crop()`
            if (isset($blob['fit']) && (\is_array($blob['fit']) || \is_int($blob['fit']))) {
                if (\is_int($blob['fit'])) {
                    $blob['fit'] = [$blob['fit'], $blob['fit']];
                } else if (isset($blob['fit'][0]) && \is_int($blob['fit'][0]) || isset($blob['fit']['width']) && \is_int($blob['fit']['width'])) {
                    if (!isset($blob['fit'][1]) || !\is_int($blob['fit'][1])) {
                        $blob['fit']['height'] = $blob['fit'][0] ?? $blob['fit']['width'];
                    }
                    if (!isset($blob['fit']['height']) || !\is_int($blob['fit']['height'])) {
                        $blob['fit']['height'] = $blob['fit'][0] ?? $blob['fit']['width'];
                    }
                }
                $image->fit($blob['fit'][0] ?? $blob['fit']['width'], $blob['fit'][1] ?? $blob['fit']['height']);
            } else if (isset($blob['width']) && \is_int($blob['width'])) {
                if (!isset($blob['height']) || !\is_int($blob['height'])) {
                    $blob['height'] = $blob['width'];
                }
                $image->crop($blob['width'], $blob['height']);
            }
            try {
                $folder = \dirname($file);
                $name = \pathinfo($file, \PATHINFO_FILENAME);
                $x = isset($blob['x']) && \is_string($blob['x']) ? $blob['x'] : \pathinfo($file, \PATHINFO_EXTENSION);
                $image->blob($store = $folder . \D . '~' . $name . '.' . $x, 100);
                if (!\rename($folder . \D . '~' . $name . '.' . $x, $folder . \D . $name . '.' . $x)) {
                    $_['alert']['error'][$store] = 'Could not rename the modified image file due to file system error.';
                }
            } catch (\Throwable $e) {
                $_['alert']['error'][$file] = (string) $e;
            }
        }
        $_['alert']['success'][$file] = ['File %s successfully uploaded.', '<code>' . \x\panel\from\path($file) . '</code>'];
        $_POST['page'][$key] = \strtr($file, [
            \PATH . \D => '/',
            \D => '/'
        ]);
        return $_;
    }
    // Update the image link
    if (\is_string($blob)) {
        if (0 === \strpos($blob, '//') || 0 === \strpos($blob, 'data:image/') || false !== \strpos($blob, '://')) {
            return $_; // Ignore external image link
        }
        if (!\is_file($file = \PATH . \strtr($blob, ['/' => \D]))) {
            unset($_POST['page'][$key]);
        }
        return $_;
    }
    return $_;
}

\Hook::set('_', function ($_) {
    if (isset($_['lot']['bar']['lot'][0]['lot']['folder']['lot']['image'])) {
        $_['lot']['bar']['lot'][0]['lot']['folder']['lot']['image']['icon'] = 'M19,19H5V5H19M19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M13.96,12.29L11.21,15.83L9.25,13.47L6.5,17H17.5L13.96,12.29Z';
    }
    \extract($GLOBALS, \EXTR_SKIP);
    $x_image = isset($state->x->image);
    $file = $_['file'] ?? \P;
    $route = \trim($state->x->image->route ?? 'image', '/');
    $x = \pathinfo($file, \PATHINFO_EXTENSION);
    if (\is_file($file) && false !== \strpos(',apng,avif,bmp,gif,jpeg,jpg,png,svg,webp,xbm,xpm,', ',' . $x . ',')) {
        if (0 === \strpos($_['type'] . '/', 'file/') && isset($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['file']['lot']['fields'])) {
            $link = \To::URL($file);
            if (0 === \strpos($link, $url . '/lot/image/')) {
                // Convert to proxy URL
                $link = \substr_replace($link, $url . '/' . $route . '/', 0, \strlen($url . '/lot/image/'));
            }
            $content = "";
            $content .= '<figure class="figure">';
            $content .= '<a href="' . \eat($link) . '" target="_blank" title="' . \eat(\i('Open in new window')) . '">';
            $content .= '<img alt="' . \eat(\i('Loading...')) . '" src="' . \eat($link) . '?v=' . \filemtime($file) . '">';
            $content .= '</a>';
            $content .= '</figure>';
            $info = (array) \getimagesize($file);
            $data = [
                'Height' => $info[1] ?? null,
                'Name' => \basename($file),
                'Size' => \size(\filesize($file)),
                'Type' => $info['mime'] ?? \mime_content_type($file),
                'Update' => \date('Y-m-d H:i:s', \filemtime($file)),
                'Width' => $info[0] ?? null,
            ];
            if ('image/svg+xml' === $data['Type'] && \preg_match('/<svg(\s[^>]*)?>([\s\S]*?)<\/svg>/i', \file_get_contents($file), $m)) {
                if (\preg_match('/<title>([\s\S]+?)<\/title>/i', $m[2], $mm)) {
                    $data['Title'] = \trim($mm[1]);
                }
                foreach ([
                    'Height' => 'height',
                    'ID' => 'id',
                    'Width' => 'width'
                ] as $k => $v) {
                    if (\preg_match('/\b' . \x($v) . '=([\'"]?)([^\'"]+?)\1/i', $m[1], $mm)) {
                        $data[$k] = $mm[2];
                    }
                }
            } else if ('image/jpeg' == $data['Type'] && \function_exists("\\exif_read_data")) {
                $info = \exif_read_data($file);
                foreach ([
                    'Create' => 'DateTime',
                    'Focal Length' => 'FocalLength'
                ] as $k => $v) {
                    if (isset($info[$v])) {
                        $data[$k] = $info[$v];
                    }
                }
                if (isset($info['COMPUTED'])) {
                    foreach ([
                        'Aperture' => 'ApertureFNumber',
                        'Comment' => 'UserComment',
                        'Copyright' => 'Copyright'
                    ] as $k => $v) {
                        if (isset($info['COMPUTED'][$v])) {
                            $data[$k] = $info['COMPUTED'][$v];
                        }
                    }
                }
                $data['Camera'] = \trim(($info['Make'] ?? "") . ' ' . ($info['Model'] ?? ""));
                if (isset($data['Create'])) {
                    $data['Create'] = \date('Y-m-d H:i:s', \strtotime($data['Create']));
                }
            }
            if ($data = \array_filter($data)) {
                \ksort($data);
                $content .= '<table>';
                $content .= '<tbody>';
                foreach ($data as $k => $v) {
                    $content .= '<tr>';
                    $content .= '<th scope="row">';
                    $content .= \i($k);
                    $content .= '</th>';
                    $content .= '<td>';
                    $content .= $v;
                    $content .= '</td>';
                    $content .= '</tr>';
                }
                $content .= '</tbody>';
                $content .= '</table>';
            }
            $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['file']['lot']['image'] = [
                'content' => $content,
                'stack' => 9.9,
                'type' => 'content'
            ];
            // Disable auto-focus on file name
            unset($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['file']['lot']['fields']['lot']['name']['focus']);
        }
    }
    if (0 === \strpos($_['path'] . '/', 'image/')) {
        $_['lot']['desk']['lot']['form']['lot'][0]['lot']['tasks']['lot']['blob']['icon'] = 'M13 19C13 19.7 13.13 20.37 13.35 21H5C3.9 21 3 20.11 3 19V5C3 3.9 3.9 3 5 3H19C20.11 3 21 3.9 21 5V13.35C20.37 13.13 19.7 13 19 13V5H5V19H13M13.96 12.29L11.21 15.83L9.25 13.47L6.5 17H13.35C13.75 15.88 14.47 14.91 15.4 14.21L13.96 12.29M20 18V15H18V18H15V20H18V23H20V20H23V18H20Z';
        $_['lot']['desk']['lot']['form']['lot'][0]['lot']['tasks']['lot']['blob']['url']['query']['type'] = 'blob/image';
        $_['lot']['desk']['lot']['form']['lot'][0]['lot']['tasks']['lot']['file']['skip'] = true; // Disable file button
        if (isset($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['files'])) {
            $_['lot']['desk']['lot']['form']['lot'][0]['title'] = 'Image';
            $_['lot']['desk']['lot']['form']['lot'][0]['description'] = ['This folder is intended to store image files only. They cannot be accessed directly due to the default folder permissions. You can make a proxy to allow people to access them, or you can store them in %s instead.', '<a href="' . \x\panel\to\link([
                'hash' => null,
                'part' => 1,
                'path' => 'asset',
                'query' => null,
                'task' => 'get'
            ]) . '">this folder</a>'];
        }
        if (
            !empty($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['files']['lot']['files']['lot']) &&
            !empty($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['files']['lot']['files']['type']) &&
            'files' === $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['files']['lot']['files']['type']
        ) {
            foreach ($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['files']['lot']['files']['lot'] as $k => &$v) {
                if (\is_file($k) && \is_string($v['link']) && 0 === \strpos($v['link'], $url . '/lot/image/')) {
                    $is_image = false !== \strpos(',apng,avif,bmp,gif,jpeg,jpg,png,svg,webp,xbm,xpm,', ',' . \pathinfo($v['link'], \PATHINFO_EXTENSION) . ',');
                    $v['tasks']['proxy'] = [
                        'active' => $is_image && $x_image,
                        'description' => 'View image via proxy link',
                        'icon' => 'M19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M19,19H5V5H19V19M11,16H10C8.39,16 6,14.94 6,12C6,9.07 8.39,8 10,8H11V10H10C9.54,10 8,10.17 8,12C8,13.9 9.67,14 10,14H11V16M14,16H13V14H14C14.46,14 16,13.83 16,12C16,10.1 14.33,10 14,10H13V8H14C15.61,8 18,9.07 18,12C18,14.94 15.61,16 14,16M15,13H9V11H15V13Z',
                        'link' => \substr_replace($v['link'], $url . '/' . $route . '/', 0, \strlen($url . '/lot/image/')),
                        'stack' => 10.1,
                        'title' => 'View',
                        'type' => 'link'
                    ];
                }
            }
            unset($v);
        }
        return $_;
    }
    return $_;
}, 10.1);

\Hook::set('_', __NAMESPACE__ . "\\_", 20);
\Hook::set('do.blob.set', __NAMESPACE__ . "\\blob", 9.9);
\Hook::set('do.page.get', __NAMESPACE__ . "\\get", 0);
\Hook::set('do.page.set', __NAMESPACE__ . "\\set", 0);