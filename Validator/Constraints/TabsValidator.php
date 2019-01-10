<?php

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\Model\Tabs as Model;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class TabsValidator
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class TabsValidator extends ConstraintValidator
{
    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof Model) {
            throw new UnexpectedTypeException($value, Model::class);
        }
        if (!$constraint instanceof Tabs) {
            throw new UnexpectedTypeException($constraint, Tabs::class);
        }

        if (null !== $value->getMedia()) {
            foreach ($value->getTabs() as $tab) {
                if (null !== $tab->getMedia()) {
                    $this
                        ->context
                        ->buildViolation($constraint->media_must_be_null)
                        ->atPath('media')
                        ->addViolation();

                    break;
                }
            }
        }

        // TODO Tabs translations and each tab's translations must use the same locales.
    }
}
