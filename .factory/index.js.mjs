import {
    W,
    getAttribute,
    getElements,
    theLocation
} from '@taufik-nurrohman/document';

import {
    offEventDefault,
    onEvent
} from '@taufik-nurrohman/event';

import {
    toCount
} from '@taufik-nurrohman/to';

function onClickConfirm(toggle) {
    onEvent('click', toggle, function (e) {
        _.dialog.confirm(getAttribute(this, 'aria-description')).then(value => {
            value && (theLocation.href = this.href);
        }).catch(e => 0);
        offEventDefault(e);
    });
}

function onChange() {
    const toggles = getElements('.description a[aria-description]:where([href*="?image="],[href*="&image="])');
    toCount(toggles) && toggles.forEach(onClickConfirm);
} onChange();

_.on('change', onChange);