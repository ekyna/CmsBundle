<?php

namespace Ekyna\Bundle\CmsBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class ContentGridValidator
 * @package Ekyna\Bundle\CmsBundle\Validator
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class ContentGridValidator extends ConstraintValidator
{
    public function validate($content, Constraint $constraint)
    {
        /**
         * @var \Ekyna\Bundle\CmsBundle\Entity\Content $content
         * @var \Ekyna\Bundle\CmsBundle\Validator\ContentGrid $constraint
         */
        $currentColumn = 1;
        $currentRow = 1;
        $columnSize = 0;

        foreach($content->getBlocks() as $block) {
            if ($currentRow != $block->getRow()) {
                // Row is missing
                $this->context->addViolation($constraint->missing_row);
                return;
            }
            if ($currentColumn != $block->getColumn()) {
                // Columns overlap
                $this->context->addViolation($constraint->columns_overlap);
                return;
            }
            $columnSize += $block->getSize();
            if ($columnSize > 12) {
                // Row is too large
                $this->context->addViolation($constraint->row_too_large);
                return;
            } elseif ($columnSize == 12) {
                $columnSize = 0;
                $currentColumn = 1;
                $currentRow++;
            } else {
                $currentColumn += $block->getSize();
            }
        }

        if ($columnSize != 0) {
            // Last block is too small
            $this->context->addViolation($constraint->block_too_small);
        }
    }
}
