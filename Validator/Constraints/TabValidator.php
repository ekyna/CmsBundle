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
    public function validate($tab, Constraint $constraint)
    {
        if (!$tab instanceof Model) {
            throw new UnexpectedTypeException($tab, Model::class);
        }
        if (!$constraint instanceof Tab) {
            throw new UnexpectedTypeException($constraint, Tab::class);
        }

        $mediaRequired = empty($tab->getAnchor());

        foreach ($tab->getTranslations() as $translation) {
            if (is_null($translation->getMedia()) xor $mediaRequired) {
                $this
                    ->context
                    ->buildViolation($constraint->media_or_anchor_but_not_both)
                    ->atPath('anchor')
                    ->addViolation();

                break;
            }
        }
    }
}
