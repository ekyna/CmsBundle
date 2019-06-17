define(['require', 'jquery', 'routing', 'bootstrap'], function (require, $, Router) {
    "use strict";

    var EkynaCms = function () {};

    EkynaCms.prototype.init = function () {
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
