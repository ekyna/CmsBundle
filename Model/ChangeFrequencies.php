<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Bundle\ResourceBundle\Model\AbstractConstants;

/**
 * Class ChangeFrequencies
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ChangeFrequencies extends \Ekyna\Bundle\ResourceBundle\Model\AbstractConstants
{
    const HOURLY  = 'hourly';
    const MONTHLY = 'monthly';
    const YEARLY  = 'yearly';

    public static function getConfig()
    {
        $prefix = 'ekyna_core.changefreq.';

        return [
            self::HOURLY  => [$prefix . self::HOURLY],
            self::MONTHLY => [$prefix . self::MONTHLY],
            self::YEARLY  => [$prefix . self::YEARLY],
        ];
    }
}
