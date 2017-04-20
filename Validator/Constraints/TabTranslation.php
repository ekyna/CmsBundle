<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class TabTranslation
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class TabTranslation extends Constraint
{
    public string $labelAndUrlButNotBoth = 'ekyna_cms.block.tab.label_and_url_but_not_both';


    /**
     * @inheritDoc
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
