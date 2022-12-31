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

const toggles = getElements('.description a[aria-description]:where([href*="?image="],[href*="&image="])');

function onClickConfirm(toggle) {
    onEvent('click', toggle, function (e) {
        _.dialog.confirm(getAttribute(this, 'aria-description')).then(value => {
            value && (theLocation.href = this.href);
        }).catch(e => 0);
        offEventDefault(e);
    });
}

toCount(toggles) && toggles.forEach(onClickConfirm);