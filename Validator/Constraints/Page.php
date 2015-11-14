<?php

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class Page
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class Page extends Constraint
{
    public $titleIsMandatory      = 'ekyna_cms.page.title_is_mandatory';
    public $controllerIsMandatory = 'ekyna_cms.page.controller_is_mandatory';
    public $invalidController     = 'ekyna_cms.page.invalid_controller';

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'ekyna_cms.page';
    }
}
