<?php

declare(strict_types=1);

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
    public function validate($translation, Constraint $constraint)
    {
        if (!$translation instanceof Model) {
            throw new UnexpectedTypeException($translation, Model::class);
        }
        if (!$constraint instanceof TabsTranslation) {
            throw new UnexpectedTypeException($constraint, TabsTranslation::class);
        }

        if (empty($translation->getButtonLabel()) xor empty($translation->getButtonUrl())) {
            $this
                ->context
                ->buildViolation($constraint->labelAndUrlButNotBoth)
                ->atPath('buttonUrl')
                ->addViolation();
        }
    }
}
