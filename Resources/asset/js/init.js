(function(window) {
    if (!!window.cookieChoices) {
        return window.cookieChoices;
    }

    var zIndex = 1050;
    var document = window.document;
    // IE8 does not support textContent, so we should fallback to innerText.
    var supportsTextContent = 'textContent' in document.body;

    var cookieChoices = (function() {

        var cookieName = 'cms-cookies-consent';
        var cookieConsentId = 'cms-cookies-consent';
        var contentId = 'cms-cookies-consent-content';
        var infoLinkId = 'cms-cookies-consent-info';
        var dismissLinkId = 'cms-cookies-consent-dismiss';

        function _createHeaderElement(cookieText, dismissText, linkText, linkHref) {
            var butterBarStyles = 'position:fixed;width:100%;background-color:#eee;' +
                'margin:0;left:0;top:0;padding:4px; z-index:' + zIndex + ';text-align:center;';

            var cookieConsentElement = document.createElement('div');
            cookieConsentElement.id = cookieConsentId;
            cookieConsentElement.style.cssText = butterBarStyles;

            var content = _createConsentText(cookieText);
            content.id = contentId;
            cookieConsentElement.appendChild(content);

            if (!!linkText && !!linkHref) {
                var infoLink = _createInformationLink(linkText, linkHref);
                infoLink.style.marginLeft = '16px';
                cookieConsentElement.appendChild(infoLink);
            }
            var dismissLink = _createDismissLink(dismissText);
            dismissLink.style.marginLeft = '16px';
            cookieConsentElement.appendChild(dismissLink);
            return cookieConsentElement;
        }

        function _createDialogElement(cookieText, dismissText, linkText, linkHref) {
            var glassStyle = 'position:fixed;width:100%;height:100%;z-index:' + zIndex + ';' +
                'top:0;left:0;opacity:0.5;filter:alpha(opacity=50);' +
                'background-color:#ccc;';
            var dialogStyle = 'z-index:' + (zIndex+1) + ';position:fixed;left:50%;top:50%';
            var contentStyle = 'position:relative;left:-50%;margin-top:-25%;' +
                'background-color:#fff;padding:20px;box-shadow:4px 4px 25px #888;';

            var cookieConsentElement = document.createElement('div');
            cookieConsentElement.id = cookieConsentId;

            var glassPanel = document.createElement('div');
            glassPanel.style.cssText = glassStyle;

            var container = document.createElement('div');
            container.style.cssText = contentStyle;

            var content = document.createElement('div');
            content.id = contentId;
            content.innerHTML = cookieText;

            var dialog = document.createElement('div');
            dialog.style.cssText = dialogStyle;

            var controls = document.createElement('p');
            controls.style.borderTop = '1px solid #ccc';
            controls.style.paddingTop = '10px';
            controls.style.marginBottom = '0';

            var dismissLink = _createDismissLink(dismissText);
            dismissLink.style.cssFloat = 'right';
            controls.appendChild(dismissLink);
            if (!!linkText && !!linkHref) {
                controls.appendChild(_createInformationLink(linkText, linkHref));
            }
            container.appendChild(content);
            container.appendChild(controls);
            dialog.appendChild(container);
            cookieConsentElement.appendChild(glassPanel);
            cookieConsentElement.appendChild(dialog);
            return cookieConsentElement;
        }

        function _setElementText(element, text) {
            if (supportsTextContent) {
                element.textContent = text;
            } else {
                element.innerText = text;
            }
        }

        function _createConsentText(cookieText) {
            var consentText = document.createElement('span');
            _setElementText(consentText, cookieText);
            return consentText;
        }

        function _createDismissLink(dismissText) {
            var dismissLink = document.createElement('a');
            _setElementText(dismissLink, dismissText);
            dismissLink.id = dismissLinkId;
            dismissLink.href = '#';
            return dismissLink;
        }

        function _createInformationLink(linkText, linkHref) {
            var infoLink = document.createElement('a');
            _setElementText(infoLink, linkText);
            infoLink.id = infoLinkId;
            infoLink.href = linkHref;
            infoLink.target = '_blank';
            return infoLink;
        }

        function _dismissLinkClick() {
            _saveUserPreference();
            _removeCookieConsent();
            return false;
        }

        function _showCookieConsent(cookieText, dismissText, linkText, linkHref, isDialog) {
            if (_shouldDisplayConsent()) {
                _removeCookieConsent();
                var consentElement = (isDialog) ?
                    _createDialogElement(cookieText, dismissText, linkText, linkHref) :
                    _createHeaderElement(cookieText, dismissText, linkText, linkHref);
                var fragment = document.createDocumentFragment();
                fragment.appendChild(consentElement);
                document.body.appendChild(fragment.cloneNode(true));
                document.getElementById(dismissLinkId).onclick = _dismissLinkClick;
            }
        }

        function showCookieConsentBar(cookieText, dismissText, linkText, linkHref) {
            _showCookieConsent(cookieText, dismissText, linkText, linkHref, false);
        }

        function showCookieConsentDialog(cookieText, dismissText, linkText, linkHref) {
            _showCookieConsent(cookieText, dismissText, linkText, linkHref, true);
        }

        function _removeCookieConsent() {
            var cookieChoiceElement = document.getElementById(cookieConsentId);
            if (cookieChoiceElement != null) {
                cookieChoiceElement.parentNode.removeChild(cookieChoiceElement);
            }
        }

        function _saveUserPreference() {
            // Set the cookie expiry to one year after today.
            var expiryDate = new Date();
            expiryDate.setFullYear(expiryDate.getFullYear() + 1);
            document.cookie = cookieName + '=y; expires=' + expiryDate.toGMTString();
        }

        function _shouldDisplayConsent() {
            // Display the header only if the cookie has not been set.
            return !document.cookie.match(new RegExp(cookieName + '=([^;]+)'));
        }

        var exports = {};
        exports.showCookieConsentBar = showCookieConsentBar;
        exports.showCookieConsentDialog = showCookieConsentDialog;
        exports.consentRequired = _shouldDisplayConsent;
        exports.saveUserPreference = _saveUserPreference;
        return exports;
    })();

    window.cookieChoices = cookieChoices;
    return cookieChoices;
})(this);

(function(doc, $, router) {
    $(doc).ready(function() {

        var $flashesContainer = $('#cms-flashes');

        var initXrh = $.ajax({
            url: router.generate('ekyna_cms_init'),
            data: {
                flashes: $flashesContainer.length > 0 ? 1 : 0,
                editor: $('.cms-editor-block').length > 0 ? 1 : 0,
                cookie: cookieChoices.consentRequired() ? 1 : 0
            },
            dataType: 'xml',
            type: 'POST'
        });

        initXrh.done(function (xml) {
            var $xml = $(xml);
            // Flashes
            if ($flashesContainer.length > 0) {
                var flashes = $xml.find('flashes').text();
                $flashesContainer.html($(flashes));
            }

            // Editor
            var editor = $xml.find('editor').text();
            $('body').append($(editor));

            // Cookie consent
            var $cookie = $xml.find('cookie');
            if ($cookie.length == 1) {
                if ($cookie.attr('disabled') == 'disabled') {
                    cookieChoices.saveUserPreference();
                } else if ($cookie.attr('mode') == 'header') {
                    cookieChoices.showCookieConsentBar(
                        $cookie.text(),
                        $cookie.attr('close'),
                        $cookie.attr('learn'),
                        router.generate('cookies_informations')
                    );
                } else {
                    cookieChoices.showCookieConsentDialog(
                        $cookie.text(),
                        $cookie.attr('close'),
                        $cookie.attr('learn'),
                        router.generate('cookies_informations')
                    );
                }
            }
        });
    });
})(document, jQuery, Routing);