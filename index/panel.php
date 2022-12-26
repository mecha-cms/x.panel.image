<?php namespace x\panel__image;

function _($_) {
    if (0 === \strpos($_['type'] . '/', 'page/')) {
        \extract($GLOBALS, EXTR_SKIP);
        if ('get' === $_['task'] && $_['file']) {
            $page = new \Page($_['file']);
            $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['page']['lot']['fields']['lot']['image'] = [
                'keep' => 1 !== $user->status ? '@' : null,
                'name' => 'page[image]',
                'stack' => 15,
                'type' => 'path',
                'value' => $page['image'],
                'width' => true
            ];
        } else {
            $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['page']['lot']['fields']['lot']['image'] = [
                'name' => 'page[image]',
                'stack' => 15,
                'type' => 'blob',
                'width' => true
            ];
        }
    }
    return $_;
}

function get($_) {
    return \x\panel__image\set($_);
}

function set($_) {
    if ('POST' === $_SERVER['REQUEST_METHOD'] && isset($_POST['page']['image'])) {
        \extract($GLOBALS, \EXTR_SKIP);
        $blob = $_POST['page']['image'];
        // Upload an image
        if (is_array($blob)) {
            $file = \LOT . \D . 'asset' . \D . (1 === $user->status ? \D : \D . $user->user . \D) . ($name = \To::file($blob['name'] ?? ""));
            [$min, $max] = (array) (\State::get('x.panel.guard.file.size', true) ?? [0, 0]);
            if (!$name || !\is_string($name)) {
                $_['alert']['error'][$file] = 'The file you are about to upload doesn\'t seem to have a valid file name.';
                return $_;
            }
            if (\is_file($file)) {
                $_['alert']['error'][$file] = ['%s %s already exists.', ['Image', '<code>' . \x\panel\from\path($file) . '</code>']];
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
            $_POST['page']['image'] = \strtr($status, [
                \PATH . \D => '/',
                \D => '/'
            ]);
            return $_;
        }
        // Update the image link
        if (is_string($blob)) {
            if (!\is_file($file = \PATH . \strtr($blob, ['/' => \D]))) {
                unset($_POST['page']['image']);
                $_['alert']['info'][$file] = ['Image data has been removed because image %s no longer exists.', '<code>' . \x\panel\from\path($file) . '</code>'];
            }
            return $_;
        }
        // Remove the image link
        if (-1 === $blob) {
            return $_;
        }
        if (-2 === $blob) {
            return $_;
        }
    }
    return $_;
}

\Hook::set('_', __NAMESPACE__ . "\\_", 20);
\Hook::set('do.page.get', __NAMESPACE__ . "\\get", 20);
\Hook::set('do.page.set', __NAMESPACE__ . "\\set", 20);