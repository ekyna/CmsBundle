<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class Content
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class Content extends Constraint
{
//    public $missingRow     = 'ekyna_cms.content.missing_row';
//    public $columnsOverlap = 'ekyna_cms.content.columns_overlap';
//    public $rowTooLarge    = 'ekyna_cms.content.row_too_large';
//    public $blockTooSmall  = 'ekyna_cms.content.block_too_small';


    /**
     * @inheritDoc
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
