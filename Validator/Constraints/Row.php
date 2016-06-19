<?php

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class Row
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class Row extends Constraint
{
    public $containerOrNameButNotBoth = 'ekyna_cms.row.container_or_name_but_not_both';

    public $badColumnIndex            = 'ekyna_cms.row.bad_column_index';

    public $tooSmallRow               = 'ekyna_cms.row.too_small_row';

    public $tooLargeRow               = 'ekyna_cms.row.too_large_row';

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
