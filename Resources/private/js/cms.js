define(['require', 'jquery', 'routing', 'js-cookie', 'bootstrap'], function (require, $, Router, Cookies) {
    "use strict";

    var CookieConsent = function ($element) {
        this.$element = $element;
        this.name = 'cookie-consent';
    };

    CookieConsent.prototype = {
        _consentRequired: function () {
            return undefined === Cookies.get(this.name);
        },

        _saveUserPreference: function () {
            Cookies.set(this.name, 'y', {expires: 365});
        },

        init: function () {
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

                            that.$element.on('click', '.cookies-consent-dismiss', function (e) {
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


    var EkynaCms = function () {};

    EkynaCms.prototype.init = function () {
        var $cookieConsent = $('#cookies-consent');
        if (1 === $cookieConsent.size()) {
            var cookieConsent = new CookieConsent($cookieConsent);
            cookieConsent.init();
        }

        // Editor tabs widgets
        var $tabsWidget = $('.cms-tabs');
        if (0 < $tabsWidget.length) {
            require(['ekyna-cms/cms/tabs'], function (Tabs) {
                Tabs.init($tabsWidget);
            });
        }

        // Slide shows
        var $slideShow = $('.cms-slide-show');
        if (0 < $slideShow.length) {
            require(['ekyna-cms/slide-show/slide-show'], function (SlideShow) {
                $slideShow.each(function () {
                    SlideShow.create($(this).data('config'));
                });
            });
        }

        $(document).on('click', '.cms-container-anchor', function (e) {
            if (typeof window['ontouchstart'] !== 'undefined') {
                return true;
            }

            e.preventDefault();
            e.stopPropagation();

            var element = e.currentTarget;
            element.addEventListener('copy', function (event) {
                event.preventDefault();
                if (event.clipboardData) {
                    var url = window.location.protocol + '//'
                        + window.location.host
                        + window.location.pathname + '#'
                        + $(element).closest('.cms-container').attr('id');

                    event.clipboardData.setData("text/plain", url);

                    $(element)
                        .tooltip({
                            title: 'Copied to clipboard',
                            placement: 'left',
                            trigger: 'manual',
                            container: 'body'
                        })
                        .tooltip('show');

                    setTimeout(function () {
                        $(element).tooltip('hide');
                    }, 1500);
                }
            });

            document.execCommand("Copy");

            return false;
        });
    };

    return new EkynaCms;
});
