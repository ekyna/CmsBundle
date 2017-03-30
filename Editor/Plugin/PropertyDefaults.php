<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin;

/**
 * Class PropertyDefaults
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
final class PropertyDefaults
{
    /**
     * Returns the default theme choices.
     *
     * @return array
     */
    static public function getDefaultThemeChoices()
    {
        return [
            'white' => 'White',
            'black' => 'Black',
        ];
    }

    /**
     * Returns the default style choices.
     *
     * @return array
     */
    static public function getDefaultStyleChoices()
    {
        return [
            'img-rounded'   => 'Rounded',
            'img-circle'    => 'Circle',
            'img-thumbnail' => 'Thumbnail',
        ];
    }

    /**
     * Returns the default choices.
     *
     * @return array
     */
    static public function getDefaultAnimationChoices()
    {
        return [
            // Fade
            'fade'            => 'Fade',
            'fade-up'         => 'Fade up',
            'fade-down'       => 'Fade down',
            'fade-left'       => 'Fade left',
            'fade-right'      => 'Fade right',
            'fade-up-right'   => 'Fade up right',
            'fade-up-left'    => 'Fade up left',
            'fade-down-right' => 'Fade down right',
            'fade-down-left'  => 'Fade down left',
            // Flip
            'flip-up'         => 'Flip up',
            'flip-down'       => 'Flip down',
            'flip-left'       => 'Flip left',
            'flip-right'      => 'Flip right',
            // Slide
            'slide-up'        => 'Slide up',
            'slide-down'      => 'Slide down',
            'slide-left'      => 'Slide left',
            'slide-right'     => 'Slide right',
            // Zoom
            'zoom-in'         => 'Zoom in',
            'zoom-in-up'      => 'Zoom in up',
            'zoom-in-down'    => 'Zoom in down',
            'zoom-in-left'    => 'Zoom in left',
            'zoom-in-right'   => 'Zoom in right',
            'zoom-out'        => 'Zoom out',
            'zoom-out-up'     => 'Zoom out up',
            'zoom-out-down'   => 'Zoom out down',
            'zoom-out-left'   => 'Zoom out left',
            'zoom-out-right'  => 'Zoom out right',
        ];
    }
}
