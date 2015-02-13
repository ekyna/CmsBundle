<?php

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class GalleryImage
 * @package Ekyna\Bundle\CoreBundle\Validator\Constraints
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class GalleryImage extends Constraint
{
    public $fileIsMandatory = 'ekyna_core.uploadable.file_is_mandatory';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}