<?php

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\Model\TabTranslation as Model;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class TabTranslationValidator
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class TabTranslationValidator extends ConstraintValidator
{
    /**
     * @inheritDoc
     */
    public function validate($translation, Constraint $constraint)
    {
        if (!$translation instanceof Model) {
            throw new UnexpectedTypeException($translation, Model::class);
        }
        if (!$constraint instanceof TabsTranslation) {
            throw new UnexpectedTypeException($constraint, TabTranslation::class);
        }

        if (empty($translation->getButtonLabel()) xor empty($translation->getButtonUrl())) {
            $this
                ->context
                ->buildViolation($constraint->label_and_url_but_not_both)
                ->atPath('buttonUrl')
                ->addViolation();
        }
    }
}
