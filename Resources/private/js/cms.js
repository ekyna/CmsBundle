define(['require', 'jquery', 'bootstrap'], function (require, $) {
    "use strict";

    let EkynaCms = function () {
    };

    EkynaCms.prototype.init = function () {
        // Editor tabs widgets
        let $tabsWidget = $('.cms-tabs');
        if (0 < $tabsWidget.length) {
            require(['ekyna-cms/cms/tabs'], function (Tabs) {
                Tabs.init($tabsWidget);
            });
        }

        // Slide shows
        let $slideShow = $('.cms-slide-show');
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

            let element = e.currentTarget,
                $element = $(element),
                url = window.location.protocol + '//'
                    + window.location.host
                    + window.location.pathname + '#'
                    + $(element).closest('.cms-container').attr('id');

            navigator.clipboard.writeText(url).then(() => {
                $element
                    .tooltip({
                        title: 'Copied to clipboard',
                        placement: 'left',
                        trigger: 'manual',
                        container: 'body'
                    })
                    .tooltip('show');

                setTimeout(function () {
                    $element.tooltip('hide');
                }, 1500);
            }, () => {
                /* clipboard write failed */
            });

            return false;
        });
    };

    return new EkynaCms;
});
