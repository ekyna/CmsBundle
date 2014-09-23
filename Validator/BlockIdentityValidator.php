<?php

namespace Ekyna\Bundle\CmsBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class BlockIdentityValidator
 * @package Ekyna\Bundle\CmsBundle\Validator
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class BlockIdentityValidator extends ConstraintValidator
{
    public function validate($block, Constraint $constraint)
    {
        /**
         * @var \Ekyna\Bundle\CmsBundle\Entity\AbstractBlock $block
         * @var \Ekyna\Bundle\CmsBundle\Validator\BlockIdentity $constraint
         */
        $content = $block->getContent();
        $name    = $block->getName();

        // Checks that Content or Name is set, but not both.
        if ((null === $content && 0 === strlen($name)) || (null !== $content && 0 < strlen($name))) {
            $this->context->addViolation($constraint->message);
        }
    }
}
