<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class Row
 * @package      Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author       Étienne Dauvergne <contact@ekyna.com>
 */
class Row extends Constraint
{
    public string $containerOrNameButNotBoth = 'ekyna_cms.row.container_or_name_but_not_both';


    /**
     * @inheritDoc
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
