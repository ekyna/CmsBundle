<?php

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\Model\Tab as Model;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class TabValidator
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class TabValidator extends ConstraintValidator
{
    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof Model) {
            throw new UnexpectedTypeException($value, Model::class);
        }
        if (!$constraint instanceof Tab) {
            throw new UnexpectedTypeException($constraint, Tab::class);
        }

        if (is_null($value->getMedia()) xor empty($value->getAnchor())) {
            $this
                ->context
                ->buildViolation($constraint->media_or_anchor_but_not_both)
                ->atPath('anchor')
                ->addViolation();
        }
    }
}
