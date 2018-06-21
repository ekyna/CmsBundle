<?php

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class Container
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class Container extends Constraint
{
    public $contentOrNameButNotBoth = 'ekyna_cms.container.content_or_name_but_not_both';
    public $titleMustBeEmpty        = 'ekyna_cms.container.title_must_be_empty';
    public $titleMustBeFilled       = 'ekyna_cms.container.title_must_be_filled';

    /**
     * @inheritdoc
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
