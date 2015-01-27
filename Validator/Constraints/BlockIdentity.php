<?php

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class BlockIdentity
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class BlockIdentity extends Constraint
{
    public $message = 'ekyna_cms.block.identity';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
