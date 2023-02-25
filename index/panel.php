<?php namespace x\panel__image;

if (!empty($state->x->{'panel.image'}->description)) {
    $_['asset']['panel.image'] = [
        'id' => false,
        'path' => \stream_resolve_include_path(__DIR__ . \D . '..' . \D . 'index' . (\defined("\\TEST") && \TEST ? '.' : '.min.') . 'js'),
        'stack' => 30
    ];
}

function _($_) {
    if (0 === \strpos($_['type'] . '/', 'page/')) {
        \extract($GLOBALS, \EXTR_SKIP);
        $active = !isset($state->x->{'panel.image'}->active) || $state->x->{'panel.image'}->active;
        $description = $state->x->{'panel.image'}->description ?? true;
        $key = $state->x->{'panel.image'}->key ?? 'image';
        $skip = !empty($state->x->{'panel.image'}->skip);
        $title = $state->x->{'panel.image'}->title ?? 'Image';
        $type = $_GET['image'] ?? null;
        $vital = !empty($state->x->{'panel.image'}->vital);
        $page = new \Page($_['file'] ?: null);
        $image = $page[$key] ?? "";
        $file = $image && \is_file($f = \To::path(\PATH . $image)) ? $f : false;
        if (!\array_key_exists('image', $_GET)) {
            if ($image) {
                $type = 'link';
            }
        }
        // Make `image` query to be unset by default
        unset($_GET['image'], $GLOBALS['_']['query']['image'], $_['query']['image']);
        if ('link' === $type) {
            $link = 0 === \strpos($image, '//') || 0 === \strpos($image, 'data:image/') || false !== \strpos($image, '://') || 0 !== \strpos($image, '/lot/asset/');
            $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['page']['lot']['fields']['lot']['image'] = [
                'active' => $active,
                'description' => \is_array($description) || \is_string($description) ? $description : ($description ? ['Paste an image link or %s to select an image file.', '<a aria-description="' . \htmlspecialchars(\i('This action will reload the page.')) . '" href="' . \htmlspecialchars($url->query(['image' => 'blob'])) . '">' . \i('click here') . '</a>'] : null),
                'field-exit' => $image && isset($state->x->image) ? '
<table>
  <tbody>
    <tr>
      <td rowspan="4" style="padding: 0; width: calc(((calc(var(--y) / 4) * 6) * 3) + 4px);">
        <img alt="" height="111" src="' . $page->{$key}(111, 111, 100) . ($file ? '?v=' . \filemtime($file) : "") . '" style="display: block; height: 100%; width: 100%;" width="111">
      </td>
    </tr>
    <tr>
      <td>
        <b>' . \i('Name') . ':</b> <a href="' . \long($image ?: $page->{$key}) . '" target="_blank">' . ($file ? \basename($image) : \i('Unknown')) . '</a>
      </td>
    </tr>
    <tr>
      <td>
        <b>' . \i('Size') . ':</b> ' . ($file ? \image($file)->size : \i('Unknown')) . '
      </td>
    </tr>
    <tr>
      <td>
        <b>' . \i('Type') . ':</b> ' . ($file ? \image($file)->type : \i('Unknown')) . '
      </td>
    </tr>
  </tbody>
</table>
' : null,
                'hint' => \strtr(\strtr($state->x->{'panel.image'}->folder ?? '/lot/asset/user/' . $user->name, ['/' => \D]), [
                    \PATH . \D => '/',
                    \D => '/'
                ]) . '/image.jpg',
                'icon' => [null, $image && 'set' !== $_['task'] ? [
                    'd' => $link || $vital ? 'M14,3V5H17.59L7.76,14.83L9.17,16.24L19,6.41V10H21V3M19,19H5V5H12V3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V12H19V19Z' : 'M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19M8,9H16V19H8V9M15.5,4L14.5,3H9.5L8.5,4H5V6H19V4H15.5Z',
                    'description' => $link || $vital ? 'View' : 'Delete',
                    'link' => $link || $vital ? $image : null,
                    'url' => $link || $vital ? null : [
                        'part' => 0,
                        'path' => \substr($image, \strlen('/lot/')),
                        'query' => [
                            'kick' => $url->current,
                            'token' => $_['token'],
                            'trash' => !empty($state->x->panel->trash) ? \date('Y-m-d-H-i-s') : null
                        ],
                        'task' => 'let'
                    ]
                ] : null],
                'name' => 'page[' . $key . ']',
                'pattern' => "^(data:image/(apng|avif|gif|jpeg|png|svg\\+xml|webp);base64,|(https?:)?\\/\\/|[.]{0,2}\\/)[^\\/]\\S*$",
                'skip' => $skip,
                'stack' => 15,
                'title' => $title,
                'type' => 'u-r-l',
                'value' => "" !== $image ? $image : null,
                'vital' => $vital,
                'width' => true
            ];
        } else /* if ('blob' === $type) */ {
            if (isset($_['lot']['desk']['lot']['form']) && isset($state->x->image)) {
                foreach (['fit', 'height', 'width', 'x'] as $v) {
                    $_['lot']['desk']['lot']['form']['values']['page'][$key][$v] = $state->x->{'panel.image'}->{$v} ?? null;
                }
            }
            $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['page']['lot']['fields']['lot']['image'] = [
                'active' => $active,
                'description' => \is_array($description) || \is_string($description) ? $description : ($description ? ['Select an image file or %s to paste an image link.', '<a aria-description="' . \htmlspecialchars(\i('This action will reload the page.')) . '" href="' . \htmlspecialchars($url->query(['image' => 'link'])) . '">' . \i('click here') . '</a>'] : null),
                'field-exit' => $image && isset($state->x->image) ? '
<table>
  <tbody>
    <tr>
      <td rowspan="4" style="padding: 0; width: calc(((calc(var(--y) / 4) * 6) * 3) + 4px);">
        <img alt="" height="111" src="' . $page->{$key}(111, 111, 100) . ($file ? '?v=' . \filemtime($file) : "") . '" style="display: block; height: 100%; width: 100%;" width="111">
      </td>
    </tr>
    <tr>
      <td>
        <b>' . \i('Name') . ':</b> <a href="' . \long($image ?: $page->{$key}) . '" target="_blank">' . ($file ? \basename($image) : \i('Unknown')) . '</a>
      </td>
    </tr>
    <tr>
      <td>
        <b>' . \i('Size') . ':</b> ' . ($file ? \image($file)->size : \i('Unknown')) . '
      </td>
    </tr>
    <tr>
      <td>
        <b>' . \i('Type') . ':</b> ' . ($file ? \image($file)->type : \i('Unknown')) . '
      </td>
    </tr>
  </tbody>
</table>
' : null,
                'name' => 'page[' . $key . ']',
                'skip' => $skip,
                'stack' => 15,
                'title' => $title,
                'type' => 'blob',
                'vital' => $vital,
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
        if (false === \strpos(',apng,avif,gif,jpeg,jpg,png,svg,webp,', ',' . $x . ',')) {
            $_['alert']['error'][$file] = ['File extension %s is not allowed.', '<code>' . $x . '</code>'];
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
        if (isset($state->x->image)) {
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
    if (0 === \strpos($_['path'] . '/', 'image/')) {
        // $_['lot']['desk']['lot']['form']['lot'][0]['lot']['tasks']['lot']['blob']['url']['query']['type'] = 'blob/image';
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
            $route = \trim($state->x->image->route ?? 'image', '/');
            foreach ($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['files']['lot']['files']['lot'] as $k => &$v) {
                if (\is_file($k) && \is_string($v['link']) && 0 === \strpos($v['link'], $url . '/lot/image/')) {
                    $is_image = false !== \strpos(',apng,avif,bmp,gif,jpeg,jpg,png,webp,xbm,xpm,', ',' . \pathinfo($v['link'], \PATHINFO_EXTENSION) . ',');
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
    }
    return $_;
}, 10.1);

\Hook::set('_', __NAMESPACE__ . "\\_", 20);
\Hook::set('do.page.get', __NAMESPACE__ . "\\get", 0);
\Hook::set('do.page.set', __NAMESPACE__ . "\\set", 0);