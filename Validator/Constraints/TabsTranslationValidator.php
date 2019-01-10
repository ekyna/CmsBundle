<?php

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\Model\TabsTranslation as Model;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class TabsTranslationValidator
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class TabsTranslationValidator extends ConstraintValidator
{
    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof Model) {
            throw new UnexpectedTypeException($value, Model::class);
        }
        if (!$constraint instanceof TabsTranslation) {
            throw new UnexpectedTypeException($constraint, TabsTranslation::class);
        }

        if (empty($value->getButtonLabel()) xor !empty($value->getButtonUrl())) {
            $this
                ->context
                ->buildViolation($constraint->label_and_url_but_not_both)
                ->atPath('anchor')
                ->addViolation();
        }
    }
}
