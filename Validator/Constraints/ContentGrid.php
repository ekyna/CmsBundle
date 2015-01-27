<?php

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class ContentGrid
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class ContentGrid extends Constraint
{
    public $missing_row     = 'ekyna_cms.content.missing_row';
    public $columns_overlap = 'ekyna_cms.content.columns_overlap';
    public $row_too_large   = 'ekyna_cms.content.row_too_large';
    public $block_too_small = 'ekyna_cms.content.block_too_small';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
