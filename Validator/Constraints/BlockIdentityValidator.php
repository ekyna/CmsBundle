<?php

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class BlockIdentityValidator
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class BlockIdentityValidator extends ConstraintValidator
{
    public function validate($block, Constraint $constraint)
    {
        if (!$constraint instanceof BlockIdentity) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\BlockIdentity');
        }
        if (!$constraint instanceof BlockInterface) {
            throw new UnexpectedTypeException($block, 'Ekyna\Bundle\CmsBundle\Model\BlockInterface');
        }

        /**
         * @var BlockInterface $block
         * @var BlockIdentity $constraint
         */
        $content = $block->getContent();
        $name    = $block->getName();

        // Checks that Content or Name is set, but not both.
        if ((null === $content && 0 === strlen($name)) || (null !== $content && 0 < strlen($name))) {
            $this->context->addViolation($constraint->message);
        }
    }
}
