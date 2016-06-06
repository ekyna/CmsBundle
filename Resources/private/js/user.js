(function(root, factory) {
    "use strict";

    // CommonJS module is defined
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = factory(require('jquery'), require('routing'), require('ekyna-cms/cookie'));
    }
    // AMD module is defined
    else if (typeof define === 'function' && define.amd) {
        define('ekyna-cms/user', ['jquery', 'routing', 'ekyna-cms/cookie'], function($, Router, Cookie) {
            return factory($, Router, Cookie);
        });
    } else {
        // planted over the root!
        root.EkynaCmsUser = factory(root.jQuery, root.Routing, root.EkynaCmsCookie);
    }

}(this, function($, Router, Cookie) {
    "use strict";

    var EkynaCmsUser = function() {
    };

    EkynaCmsUser.prototype = {
        constructor: EkynaCmsUser,
        init: function() {
            var $flashesContainer = $('#cms-flashes');
            $.ajax({
                url: Router.generate('ekyna_cms_init'),
                data: {
                    flashes: $flashesContainer.length > 0 ? 1 : 0,
                    editor: $('.cms-editor-block').length > 0 ? 1 : 0,
                    cookie: Cookie.consentRequired() ? 1 : 0
                },
                dataType: 'xml',
                type: 'POST'
            })
            .done(function (xml) {
                var $xml = $(xml);

                // Flashes
                if ($flashesContainer.length > 0) {
                    var $flashes = $xml.find('flashes');
                    if ($flashes.size() > 0) {
                        $flashesContainer.html($flashes.text());
                    }
                }

                // Cookie consent
                var $cookie = $xml.find('cookie');
                if ($cookie.length == 1) {
                    if ($cookie.attr('disabled') == 'disabled') {
                        Cookie.saveUserPreference();
                    } else if ($cookie.attr('mode') == 'header') {
                        Cookie.showCookieConsentBar(
                            $cookie.text(),
                            $cookie.attr('close'),
                            $cookie.attr('learn'),
                            Router.generate('cookies_privacy_policy')
                        );
                    } else {
                        Cookie.showCookieConsentDialog(
                            $cookie.text(),
                            $cookie.attr('close'),
                            $cookie.attr('learn'),
                            Router.generate('cookies_privacy_policy')
                        );
                    }
                }
            });
        }
    };

    return new EkynaCmsUser;

}));
