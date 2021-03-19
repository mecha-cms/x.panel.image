<?php namespace x\panel__image;

function image($image) {
    return $image ? \URL::long($image, false) : $image;
}

// This resolves the relative image URL
\Hook::set('page.image', __NAMESPACE__ . "\\image", 0);
