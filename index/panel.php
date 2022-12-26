<?php

if (0 === strpos($_['type'] . '/', 'page/') && $site->is('page')) {
    require __DIR__ . DS . '..' . DS . 'engine' . DS . 'r' . DS . 'hook.php';
}