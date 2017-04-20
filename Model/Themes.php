<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Bundle\ResourceBundle\Model\AbstractConstants;

/**
 * Class Themes
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
final class Themes extends AbstractConstants
{
    public const THEME_DEFAULT = 'default';
    public const THEME_PRIMARY = 'primary';
    public const THEME_SUCCESS = 'success';
    public const THEME_WARNING = 'warning';
    public const THEME_DANGER  = 'danger';


    /**
     * @inheritDoc
     */
    public static function getConfig(): array
    {
        $prefix = 'theme.';

        return [
            self::THEME_DEFAULT => [$prefix . self::THEME_DEFAULT],
            self::THEME_PRIMARY => [$prefix . self::THEME_PRIMARY],
            self::THEME_SUCCESS => [$prefix . self::THEME_SUCCESS],
            self::THEME_WARNING => [$prefix . self::THEME_WARNING],
            self::THEME_DANGER  => [$prefix . self::THEME_DANGER],
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getTheme(string $constant): ?string
    {
        self::isValid($constant, true);

        return $constant;
    }

    /**
     * @inheritDoc
     */
    public static function getTranslationDomain(): ?string
    {
        return 'EkynaCms';
    }
}
