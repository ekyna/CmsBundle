<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class Menu
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class Menu extends Constraint
{
    public $invalidRouting = 'ekyna_cms.menu.invalid_routing';

    /**
     * @inheritDoc
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    /**
     * @inheritDoc
     */
    /*public function validatedBy()
    {
        return MenuValidator::class;
    }*/
}
