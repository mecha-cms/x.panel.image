<?php

foreach ([
    'The file you are about to upload doesn\'t seem to be an image.' => "The file you are about to upload doesn\u{2019}t seem to be an image.",
    'The file you are about to upload doesn\'t seem to have a valid file name.' => "The file you are about to upload doesn\u{2019}t seem to have a valid file name."
] as $k => $v) {
    if (isset(lot('I')[$k])) {
        continue;
    }
    lot('I')[$k] = $v;
}