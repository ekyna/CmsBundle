(function(root, factory) {
    "use strict";

    if (typeof module !== 'undefined' && module.exports) {
        module.exports = factory(require('jquery'), require('routing'), require('js-cookie'));
    }
    else if (typeof define === 'function' && define.amd) {
        define(['jquery', 'routing', 'js-cookie'], function($, Router, Cookies) {
            return factory($, Router, Cookies);
        });
    } else {
        root.EkynaCms = factory(root.jQuery, root.Routing, root.Cookies);
    }

}(this, function($, Router, Cookies) {
    "use strict";

    var CookieConsent = function($element) {
        this.$element = $element;
        this.name = 'cookie-consent';
    };

    CookieConsent.prototype = {
        _consentRequired: function() {
            return undefined === Cookies.get(this.name);
        },

        _saveUserPreference: function() {
            Cookies.set(this.name, 'y', {expires: 365});
        },

        init: function() {
            var that = this;

            if (this._consentRequired()) {
                $.ajax({
                    url: Router.generate('ekyna_cms_cookie_consent'),
                    dataType: 'xml',
                    type: 'GET'
                })
                .done(function (xml) {
                    var $xml = $(xml);

                    // Cookie consent
                    var $response = $xml.find('response');
                    if (1 === $response.size()) {
                        that.$element.html($response.text()).show();

                        that.$element.on('click', '.cookies-consent-dismiss', function(e) {
                            e.preventDefault();
                            e.stopPropagation();

                            that._saveUserPreference();
                            that.$element.hide();

                            return false;
                        });
                    }
                });
            }
        }
    };


    var EkynaCms = function() {};

    EkynaCms.prototype = {
        init: function() {
            var $cookieConsent = $('#cookies-consent');
            if (1 === $cookieConsent.size()) {
                var cookieConsent = new CookieConsent($cookieConsent);
                cookieConsent.init();
            }
        }
    };

    return new EkynaCms;

}));
