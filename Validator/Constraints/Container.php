<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class Container
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class Container extends Constraint
{
    public string $contentOrNameButNotBoth = 'ekyna_cms.container.content_or_name_but_not_both';
    public string $titleMustBeEmpty        = 'ekyna_cms.container.title_must_be_empty';
    public string $titleMustBeFilled       = 'ekyna_cms.container.title_must_be_filled';

    /**
     * @inheritDoc
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
