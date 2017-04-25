<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Bundle\ResourceBundle\Model\AbstractConstants;

/**
 * Class Themes
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
final class Themes extends AbstractConstants
{
    const THEME_DEFAULT = 'default';
    const THEME_PRIMARY = 'primary';
    const THEME_SUCCESS = 'success';
    const THEME_WARNING = 'warning';
    const THEME_DANGER  = 'danger';


    /**
     * @inheritDoc
     */
    public static function getConfig()
    {
        $prefix = 'ekyna_cms.theme.';

        return [
            static::THEME_DEFAULT => [$prefix . static::THEME_DEFAULT],
            static::THEME_PRIMARY => [$prefix . static::THEME_PRIMARY],
            static::THEME_SUCCESS => [$prefix . static::THEME_SUCCESS],
            static::THEME_WARNING => [$prefix . static::THEME_WARNING],
            static::THEME_DANGER  => [$prefix . static::THEME_DANGER],
        ];
    }
}
