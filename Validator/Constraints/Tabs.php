<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class Tabs
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class Tabs extends Constraint
{
    public string $mediaMustBeNull  = 'ekyna_cms.block.tab.media_must_be_null';
    public string $localesMissMatch = 'ekyna_cms.block.tab.locales_miss_match';


    /**
     * @inheritDoc
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
