<?php

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class BlockValidator
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class BlockValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($block, Constraint $constraint)
    {
        if (!$constraint instanceof Block) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\Block');
        }
        if (!$block instanceof BlockInterface) {
            throw new UnexpectedTypeException($block, 'Ekyna\Bundle\CmsBundle\Model\BlockInterface');
        }

        /**
         * @var BlockInterface $block
         * @var Block          $constraint
         */
        $row = $block->getRow();
        $name = $block->getName();

        // Checks that Content or Name is set, but not both.
        if ((null === $row && 0 === strlen($name)) || (null !== $row && 0 < strlen($name))) {
            $this->context->addViolation($constraint->rowOrNameButNotBoth);
        }
    }
}
