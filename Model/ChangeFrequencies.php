<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Bundle\ResourceBundle\Model\AbstractConstants;

/**
 * Class ChangeFrequencies
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ChangeFrequencies extends AbstractConstants
{
    public const HOURLY  = 'hourly';
    public const MONTHLY = 'monthly';
    public const YEARLY  = 'yearly';


    /**
     * @inheritDoc
     */
    public static function getConfig(): array
    {
        $prefix = 'changefreq.';

        return [
            self::HOURLY  => [$prefix . self::HOURLY],
            self::MONTHLY => [$prefix . self::MONTHLY],
            self::YEARLY  => [$prefix . self::YEARLY],
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getTranslationDomain(): ?string
    {
        return 'EkynaUi';
    }

    /**
     * @inheritDoc
     */
    public static function getTheme(string $constant): ?string
    {
        return null;
    }
}
