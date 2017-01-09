<?php

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Ekyna\Bundle\CmsBundle\Model\ContentInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class ContentValidator
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class ContentValidator extends ConstraintValidator
{
    public function validate($content, Constraint $constraint)
    {
        if (!$constraint instanceof Content) {
            throw new UnexpectedTypeException($constraint, Content::class);
        }
        if (!$content instanceof ContentInterface) {
            throw new UnexpectedTypeException($content, ContentInterface::class);
        }
    }
}
