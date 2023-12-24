<?php namespace x\panel__image;

require __DIR__ . \D . '..' . \D . 'engine' . \D . 'f.php';

if (!empty($state->x->{'panel.image'}->description)) {
    $_['asset']['panel.image'] = [
        'id' => false,
        'path' => \stream_resolve_include_path(__DIR__ . \D . '..' . \D . 'index' . (\defined("\\TEST") && \TEST ? '.' : '.min.') . 'js'),
        'stack' => 30
    ];
}

if (!\array_key_exists('type', $_GET) && !isset($_['type']) && 'set' === $_['task'] && 0 === \strpos($_['path'] . '/', 'image/')) {
    $GLOBALS['_']['type'] = $_['type'] = 'blob/image';
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
        $active = !isset($state->x->{'panel.image'}->active) || $state->x->{'panel.image'}->active;
        $description = $state->x->{'panel.image'}->description ?? true;
        $key = $state->x->{'panel.image'}->key ?? 'image';
        $skip = !empty($state->x->{'panel.image'}->skip);
        $title = $state->x->{'panel.image'}->title ?? 'Image';
        $type = $_['query']['image'] ?? null;
        $vital = !empty($state->x->{'panel.image'}->vital);
        $page_image = isset($page) && $page instanceof \Page ? ($page[$key] ?? ('get' === $_['task'] ? ($page->{$key} ?? null) : null)) : false;
        if ($page_image && !$type) {
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
        $is_link = 0 === \strpos($test = (string) $page_image, '//') || 0 === \strpos($test, 'data:image/') || false !== \strpos($test, '://');
        if (isset($state->x->image) && \is_file($file = \To::path(\long($test)))) {
            $image = new \Image($file);
            $is_link = false;
        } else {
            $image = new \Image;
        }
        $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['page']['lot']['fields']['lot']['image'] = [
            'active' => $active,
            'description' => $description,
            'name' => 'page[' . $key . ']',
            'skip' => $skip,
            'stack' => 15,
            'state' => $is_link ? [
                'height' => 72,
                'image' => $page->{$key}(72, 72, 100),
                'link' => $page_image,
                'size' => '-',
                'type' => '-',
                'width' => 72
            ] : [
                'height' => 72,
                'image' => $page->{$key}(72, 72, 100),
                'path' => $image->path,
                'size' => $image->size,
                'type' => $image->type,
                'width' => 72
            ],
            'title' => $title,
            'type' => \trim('image/' . ($type ?? ""), '/'),
            'value' => "" !== $page_image ? $page_image : null,
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

function do__blob__set($_) {
    if ('POST' !== $_SERVER['REQUEST_METHOD']) {
        return $_;
    }
    if (0 !== \strpos(($_POST['type'] ?? \P) . '/', 'blob/image/')) {
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

function do__page__get($_) {
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
    return \x\panel__image\do__page__set($_);
}

function do__page__set($_) {
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
        $name = \To::file(\lcfirst($blob['name'] ?? ""), '.@_~');
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
    if ($x_image && \is_file($file) && false !== \strpos(',apng,avif,bmp,gif,jpeg,jpg,png,svg,webp,xbm,xpm,', ',' . $x . ',')) {
        if (0 === \strpos($_['type'] . '/', 'file/') && isset($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['file']['lot']['fields'])) {
            $link = \To::URL($file);
            if (0 === \strpos($link, $url . '/lot/image/')) {
                // Convert to proxy URL
                $link = \substr_replace($link, $url . '/' . $route . '/', 0, \strlen($url . '/lot/image/'));
            }
            $info = (array) \getimagesize($file);
            $ctime = \filectime($file);
            $mtime = \filemtime($file);
            $data = [
                'Create' => \date('Y-m-d H:i:s', \min($ctime, $mtime)),
                'Dimension' => ($width = $info[0] ?? 0) . ' &#xd7; ' . ($height = $info[1] ?? 0),
                'Name' => \basename($file),
                'Size' => \size(\filesize($file)),
                'Type' => $info['mime'] ?? \mime_content_type($file),
                'Update' => $ctime !== $mtime ? \date('Y-m-d H:i:s', \max($ctime, $mtime)) : null
            ];
            if ('image/svg+xml' === $data['Type'] && \preg_match('/<svg(\s[^>]*)?>([\s\S]*?)<\/svg>/i', \file_get_contents($file), $m)) {
                if (\preg_match('/<title>([\s\S]+?)<\/title>/i', $m[2], $mm)) {
                    $data['Title'] = \trim($mm[1]);
                }
                if (false !== \stripos($m[1], 'id=') && \preg_match('/\bid=([\'"]?)([^\'"]+?)\1/i', $m[1], $mm)) {
                    $data['ID'] = $mm[2];
                }
                if (false !== \stripos($m[1], 'height=') && \preg_match('/\bheight=([\'"]?)([^\'"]+?)\1/i', $m[1], $mm)) {
                    $height = (int) $mm[2];
                }
                if (false !== \stripos($m[1], 'width=') && \preg_match('/\bwidth=([\'"]?)([^\'"]+?)\1/i', $m[1], $mm)) {
                    $width = (int) $mm[2];
                }
                if ($height && $width) {
                    $data['Dimension'] = $width . ' &#xd7; ' . $height;
                }
            } else if (\function_exists("\\exif_read_data") && ('image/jpeg' == $data['Type'] || 'image/tiff' === $data['Type'])) {
                $info = \array_replace_recursive((array) \exif_read_data($file, 'IFD0'), (array) \exif_read_data($file, 'EXIF'));
                $data['Camera'] = "";
                if (isset($info['Make'])) {
                    $data['Camera'] .= '<span title="Make">' . $info['Make'] . '</span>';
                }
                if (isset($info['Model'])) {
                    $data['Camera'] .= ': <span title="Model">' . $info['Model'] . '</span>';
                }
                if (isset($info['ExposureTime'])) {
                    $data['Shutter'] = $info['ExposureTime'] . ' s';
                }
                if (isset($info['COMPUTED']['ApertureFNumber'])) {
                    $data['Aperture'] = $info['COMPUTED']['ApertureFNumber'];
                }
                if (isset($info['DateTimeOriginal'])) {
                    $data['Create'] = \date('Y-m-d H:i:s', \strtotime($info['DateTimeOriginal']));
                } else if (isset($info['DateTime'])) {
                    $data['Create'] = \date('Y-m-d H:i:s', \strtotime($info['DateTime']));
                }
                if (isset($info['ISOSpeedRatings'])) {
                    $data['ISO'] = $info['ISOSpeedRatings'];
                }
                if (isset($info['FocalLength'])) {
                    $v = \explode('/', $info['FocalLength']);
                    $data['Focal Length'] = (((int) $v[0]) / ((int) ($v[1] ?? 1))) . ' mm';
                }
                if (isset($info['FocalLengthIn35mmFilm'])) {
                    $data['3MM Focal Length'] = $info['FocalLengthIn35mmFilm'] . ' mm';
                }
                if (isset($info['UndefinedTag:0xA434'])) {
                    $data['Lens'] = $info['UndefinedTag:0xA434'];
                }
                if (isset($info['MeteringMode'])) {
                    $data['Metering Mode'] = \i(['Unknown', 'Average', 'Center Weighted Average', 'Spot', 'Multi Spot', 'Pattern', 'Partial', 'Other'][$info['MeteringMode']] ?? "");
                }
                if (isset($info['Flash'])) {
                    $types = [
                        '0' => 'No Flash',
                        '1' => 'Flash',
                        '5' => 'Flash, No Strobe Return',
                        '7' => 'Flash, Strobe Return',
                        '9' => 'Flash, Compulsory',
                        'd' => 'Flash, Compulsory, No Strobe Return',
                        'f' => 'Flash, Compulsory, Strobe Return',
                        '10' => 'No Flash, Compulsory',
                        '18' => 'No Flash, Auto',
                        '19' => 'Flash, Auto',
                        '1d' => 'Flash, Auto, No Strobe Return',
                        '1f' => 'Flash, Auto, Strobe Return',
                        '20' => 'No Flash Function',
                        '41' => 'Flash, Red-Eye',
                        '45' => 'Flash, Red-Eye, No Strobe Return',
                        '47' => 'Flash, Red-Eye, Strobe Return',
                        '49' => 'Flash, Compulsory, Red-Eye',
                        '4d' => 'Flash, Compulsory, Red-Eye, No Strobe Return',
                        '4f' => 'Flash, Compulsory, Red-Eye, Strobe Return',
                        '59' => 'Flash, Auto, Red-Eye',
                        '5d' => 'Flash, Auto, No Strobe Return, Red-Eye',
                        '5f' => 'Flash, Auto, Strobe Return, Red-Eye',
                    ];
                    $data['Flash'] = \i($types[\dechex($info['Flash'])] ?? "");
                }
            }
            $content = "";
            $content .= '<figure class="figure">';
            $content .= '<img' . (!empty($data['Height']) ? ' height="' . $data['Height'] . '"' : "") . ' alt="' . \eat(\i('Loading...')) . '" src="' . \eat($link) . '?v=' . \filemtime($file) . '"' . (!empty($data['Width']) ? ' width="' . $data['Width'] . '"' : "") . '>';
            $content .= '</figure>';
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
                    if ('Name' === $k) {
                        $content .= '<a href="' . \eat($link) . '" target="_blank" title="' . \eat(\i('Open in new window')) . '">';
                        $content .= $v;
                        $content .= '</a>';
                    } else {
                        $content .= $v;
                    }
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
        $_['lot']['desk']['lot']['form']['lot'][0]['lot']['tasks']['lot']['blob']['url']['query']['type'] = null;
        $_['lot']['desk']['lot']['form']['lot'][0]['lot']['tasks']['lot']['file']['skip'] = true; // Disable file button
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
                        'icon' => 'M14 3v2h3.59l-9.83 9.83l1.41 1.41L19 6.41V10h2V3m-2 16H5V5h7V3H5c-1.11 0-2 .89-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7h-2v7Z',
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
\Hook::set('do.blob.set', __NAMESPACE__ . "\\do__blob__set", 9.9);
\Hook::set('do.page.get', __NAMESPACE__ . "\\do__page__get", 0);
\Hook::set('do.page.set', __NAMESPACE__ . "\\do__page__set", 0);