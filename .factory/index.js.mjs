import {
    W,
    getAttributes,
    getElement,
    getElements,
    getHTML,
    getParent,
    hasClass,
    setAttributes,
    setHTML,
    theHistory
} from '@taufik-nurrohman/document';

import {
    offEventDefault,
    onEvent
} from '@taufik-nurrohman/event';

import {
    toCount
} from '@taufik-nurrohman/to';

function onEventClickToggle(e) {
    const $ = this;
    const fieldTarget = getParent($, '.content\\:field,.lot\\:field');
    if (!fieldTarget) {
        return;
    }
    let inputTarget = getElement('input[name]', fieldTarget),
        inputTargetName = inputTarget.name,
        inputToken = inputTarget.form.token, route;
    W.fetch(route = $.href).then(response => response.text()).then(text => {
        const parser = new DOMParser;
        const doc = parser.parseFromString(text, 'text/html');
        const inputTargetNew = doc.forms.set[inputTargetName];
        const inputTokenNew = doc.forms.set.token;
        const fieldTargetNew = getParent(inputTargetNew, '.content\\:field,.lot\\:field');
        if (fieldTargetNew) {
            setAttributes(fieldTarget, getAttributes(fieldTargetNew, false));
            setHTML(fieldTarget, getHTML(fieldTargetNew));
        }
        if (inputTokenNew) {
            inputToken.value = inputTokenNew.value;
        }
        theHistory.pushState({}, "", route);
        onChange();
    });
    offEventDefault(e);
}

function setEventTo(toggle) {
    onEvent('click', toggle, onEventClickToggle);
}

function onChange() {
    const toggles = getElements('.description a:where([href*="?image="],[href*="&image="])');
    toCount(toggles) && toggles.forEach(setEventTo);
} onChange();

_.on('change', onChange);