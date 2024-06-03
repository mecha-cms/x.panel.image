(function () {
    'use strict';
    var isArray = function isArray(x) {
        return Array.isArray(x);
    };
    var isDefined = function isDefined(x) {
        return 'undefined' !== typeof x;
    };
    var isInstance = function isInstance(x, of) {
        return x && isSet(of) && x instanceof of ;
    };
    var isNull = function isNull(x) {
        return null === x;
    };
    var isObject = function isObject(x, isPlain) {
        if (isPlain === void 0) {
            isPlain = true;
        }
        if ('object' !== typeof x) {
            return false;
        }
        return isPlain ? isInstance(x, Object) : true;
    };
    var isSet = function isSet(x) {
        return isDefined(x) && !isNull(x);
    };
    var toCount = function toCount(x) {
        return x.length;
    };
    var fromValue = function fromValue(x) {
        if (isArray(x)) {
            return x.map(function (v) {
                return fromValue(x);
            });
        }
        if (isObject(x)) {
            for (var k in x) {
                x[k] = fromValue(x[k]);
            }
            return x;
        }
        if (false === x) {
            return 'false';
        }
        if (null === x) {
            return 'null';
        }
        if (true === x) {
            return 'true';
        }
        return "" + x;
    };
    var D = document;
    var W = window;
    var getAttributes = function getAttributes(node, parseValue) {
        var attributes = node.attributes,
            value,
            values = {};
        for (var i = 0, j = attributes.length; i < j; ++i) {
            value = attributes[i].value;
            values[attributes[i].name] = value;
        }
        return values;
    };
    var getElement = function getElement(query, scope) {
        return (scope || D).querySelector(query);
    };
    var getElements = function getElements(query, scope) {
        return (D).querySelectorAll(query);
    };
    var getHTML = function getHTML(node, trim) {
        if (trim === void 0) {
            trim = true;
        }
        var state = 'innerHTML';
        if (!hasState(node, state)) {
            return false;
        }
        var content = node[state];
        content = trim ? content.trim() : content;
        return "" !== content ? content : null;
    };
    var getParent = function getParent(node, query) {
        {
            return node.closest(query) || null;
        }
    };
    var hasState = function hasState(node, state) {
        return state in node;
    };
    var letAttribute = function letAttribute(node, attribute) {
        return node.removeAttribute(attribute), node;
    };
    var setAttribute = function setAttribute(node, attribute, value) {
        if (true === value) {
            value = attribute;
        }
        return node.setAttribute(attribute, fromValue(value)), node;
    };
    var setAttributes = function setAttributes(node, attributes) {
        var value;
        for (var attribute in attributes) {
            value = attributes[attribute];
            if (value || "" === value || 0 === value) {
                setAttribute(node, attribute, value);
            } else {
                letAttribute(node, attribute);
            }
        }
        return node;
    };
    var setHTML = function setHTML(node, content, trim) {
        if (trim === void 0) {
            trim = true;
        }
        if (null === content) {
            return node;
        }
        var state = 'innerHTML';
        return hasState(node, state) && (node[state] = trim ? content.trim() : content), node;
    };
    var theHistory = W.history;
    var offEventDefault = function offEventDefault(e) {
        return e && e.preventDefault();
    };
    var onEvent = function onEvent(name, node, then, options) {
        if (options === void 0) {
            options = false;
        }
        node.addEventListener(name, then, options);
    };

    function onClickConfirm(toggle) {
        onEvent('click', toggle, function (e) {
            var $ = this;
            var fieldTarget = getParent($, '.content\\:field,.lot\\:field');
            if (!fieldTarget) {
                return;
            }
            var inputTarget = getElement('input[name]', fieldTarget),
                inputTargetName = inputTarget.name,
                inputToken = inputTarget.form.token,
                route;
            W.fetch(route = $.href).then(function (response) {
                return response.text();
            }).then(function (text) {
                var parser = new DOMParser();
                var doc = parser.parseFromString(text, 'text/html');
                var inputTargetNew = doc.forms.set[inputTargetName];
                var inputTokenNew = doc.forms.set.token;
                var fieldTargetNew = getParent(inputTargetNew, '.content\\:field,.lot\\:field');
                if (fieldTargetNew) {
                    setAttributes(fieldTarget, getAttributes(fieldTargetNew));
                    setHTML(fieldTarget, getHTML(fieldTargetNew));
                }
                if (inputTokenNew) {
                    inputToken.value = inputTokenNew.value;
                }
                theHistory.pushState({}, "", route);
                onChange();
            });
            offEventDefault(e);
        });
    }

    function onChange() {
        var toggles = getElements('.description a:where([href*="?image="],[href*="&image="])');
        toCount(toggles) && toggles.forEach(onClickConfirm);
    }
    onChange();
    _.on('change', onChange);
})();