<?php

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\Model;
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
    public function validate($tabs, Constraint $constraint)
    {
        if (!$tabs instanceof Model\Tabs) {
            throw new UnexpectedTypeException($tabs, Model\Tabs::class);
        }
        if (!$constraint instanceof Tabs) {
            throw new UnexpectedTypeException($constraint, Tabs::class);
        }

        $locales = array_keys($tabs->getTranslations()->toArray());

        foreach ($tabs->getTabs() as $index => $tab) {
            /** @var Model\TabTranslation[] $translations */
            $translations = $tab->getTranslations()->toArray();

            $l = array_keys($translations);
            if (!empty(array_diff($locales, $l)) or !empty(array_diff($l, $locales))) {
                $this
                    ->context
                    ->buildViolation($constraint->media_must_be_null)
                    ->atPath("tabs[$index].translations")
                    ->addViolation();
            }

            $hasMedia = null;
            foreach ($translations as $locale => $translation) {
                if (is_null($hasMedia)) {
                    $hasMedia = !is_null($translation->getMedia());

                    continue;
                }

                if ($hasMedia xor is_null($tab->getMedia())) {
                    $this
                        ->context
                        ->buildViolation($constraint->media_must_be_null)
                        ->atPath("tabs[$index].translations[$locale].media")
                        ->addViolation();
                }
            }
        }
    }
}
