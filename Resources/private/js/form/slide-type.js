define(['jquery', 'ekyna-cms/slide-show'], function($, SlideShow) {
    "use strict";

    return {
        init: function($element) {
            try {
                var slide = SlideShow.create({id: 'slide-show-type'});
            } catch(e) {
                return;
            }

            $element.each(function() {
                var $select = $(this),
                    $options = $select.children();

                if ($select.prop('disabled')) {
                    return;
                }

                function slideTo() {
                    $options.each(function(index) {
                        if ($(this).attr('value') === $select.val()) {
                            slide.transitionTo(index, true);
                            return false;
                        }
                    });
                }

                $select.on('change keyup', slideTo);

                slideTo();
            });
        }
    };
});
