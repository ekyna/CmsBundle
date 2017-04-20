<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class Tab
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class Tab extends Constraint
{
    public string $mediaOrAnchorButNotBoth = 'ekyna_cms.block.tab.media_or_anchor_but_not_both';


    /**
     * @inheritDoc
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
