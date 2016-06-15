<?php

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Ekyna\Bundle\CmsBundle\Model\RowInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class RowValidator
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class RowValidator extends ConstraintValidator
{
    public function validate($row, Constraint $constraint)
    {
        if (!$constraint instanceof Row) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\Row');
        }
        if (!$row instanceof RowInterface) {
            throw new UnexpectedTypeException($row, 'Ekyna\Bundle\CmsBundle\Model\RowInterface');
        }

        /**
         * @var RowInterface $row
         * @var Row          $constraint
         */

        // Check that Content or Name is set, but not both.
        $container = $row->getContainer();
        $name = $row->getName();
        if ((null === $container && 0 === strlen($name)) || (null !== $container && 0 < strlen($name))) {
            $this->context->addViolation($constraint->containerOrNameButNotBoth);
        }

        // Check layout
        $position = 0;
        $size = 0;
        foreach ($row->getBlocks() as $block) {
            if ($position != $block->getPosition()) {
                $this->context->addViolation($constraint->badColumnIndex);
            }
            $size += $block->getSize();
            $position++;
        }

        if (12 < $size) {
            $this->context->addViolation($constraint->tooLargeRow);
        } elseif (12 > $size) {
            $this->context->addViolation($constraint->tooSmallRow);
        }
    }
}
