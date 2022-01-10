<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Ekyna\Bundle\CmsBundle\Model\MenuInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class ContentGridValidator
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MenuValidator extends ConstraintValidator
{
    private array $locales;


    /**
     * @param array $locales
     */
    public function __construct(array $locales)
    {
        $this->locales = $locales;
    }

    /**
     * @inheritDoc
     */
    public function validate($menu, Constraint $constraint)
    {
        if (!$constraint instanceof Menu) {
            throw new UnexpectedTypeException($constraint, Menu::class);
        }
        if (!$menu instanceof MenuInterface) {
            throw new UnexpectedTypeException($menu, MenuInterface::class);
        }

        if ((0 === $menu->getLevel()) || $menu->isLocked()) {
            return;
        }

        /**
         * @var MenuInterface $menu
         * @var Menu          $constraint
         */
        $page = null !== $menu->getPage();
        $route = !empty($menu->getRoute());

        $path = true;
        foreach ($this->locales as $locale) {
            if (!$menu->hasTranslationForLocale($locale)) {
                $path = false;
                break;
            }

            if (empty($menu->translate($locale)->getPath())) {
                $path = false;
                break;
            }
        }

        // One and only one of page, route or path
        if ((!$page && ($route ^ $path)) || ($page && !($route || $path))) {
            return;
        }

        $this->context->addViolation($constraint->invalidRouting);
    }
}
