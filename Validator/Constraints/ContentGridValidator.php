<?php

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Ekyna\Bundle\CmsBundle\Model\ContentInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class ContentGridValidator
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class ContentGridValidator extends ConstraintValidator
{
    public function validate($content, Constraint $constraint)
    {
        if (!$constraint instanceof ContentGrid) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\ContentGrid');
        }
        if (!$constraint instanceof ContentInterface) {
            throw new UnexpectedTypeException($content, 'Ekyna\Bundle\CmsBundle\Model\ContentInterface');
        }

        /**
         * @var ContentInterface $content
         * @var ContentGrid $constraint
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
