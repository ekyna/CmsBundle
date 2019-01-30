define(['jquery'], function ($) {

    var Tabs = function($element) {
        this.$element = $element;
        this.$button = null;
        this.$current = null;
    };

    Tabs.prototype.updateButton = function($link) {
        if ($link.length && $link.data('label')) {
            this.$button.text($link.data('label'));
            this.$button.attr('href', $link.data('url'));
        } else {
            this.$button.text(this.$button.data('label'));
            this.$button.attr('href', this.$button.data('url'));
        }
    };

    Tabs.prototype.init = function() {
        this.$current = this.$element.find('.cms-tabs-media:visible');
        this.$button = this.$element.find('.cms-tabs-content > p > a');

        this.updateButton(this.$element.find('.cms-tabs-menu > ul > li.active > a'));

        var $video = this.$current.find('video');
        if (1 === $video.length) {
            $video[0].play();
        }

        var that = this;
        this.$element.find('.cms-tabs-menu > ul').on('click', '> li > a', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var $link = $(e.target),
                target = $link.data('target'),
                $target, $current;

            that.updateButton($link);

            if (!target) {
                $target = $($link.attr('href'));
                if (1 === $target.length) {
                    that.$element.find('.cms-tabs-menu > ul > li').removeClass('active');
                    $link.closest('li').addClass('active');

                    $('html, body').animate({
                        scrollTop: $target.offset().top
                    }, 300);
                }

                return false;
            }

            $target = that.$element.find('#' + target);
            $current = that.$current;
            if ((1 === $target.length) && ($target[0] !== $current[0])) {
                that.$element.find('.cms-tabs-menu > ul > li').removeClass('active');
                $link.closest('li').addClass('active');

                $current.fadeOut(200, function() {
                    $(this).hide();

                    $video = $current.find('video');
                    if (1 === $video.length) {
                        $video[0].pause();
                    }

                    $video = $target.find('video');
                    if (1 === $video.length) {
                        $video[0].play();
                    }

                    $target.fadeIn(200);
                });

                that.$current = $target;
            }

            return false;
        });
    };

    return {
        init: function ($element) {
            $element.each(function () {
                new Tabs($(this)).init();
            });
        }
    };
});