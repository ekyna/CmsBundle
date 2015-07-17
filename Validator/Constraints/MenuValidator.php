<?php

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Ekyna\Bundle\CmsBundle\Model\MenuInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class ContentGridValidator
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MenuValidator extends ConstraintValidator
{
    /**
     * @var array
     */
    private $locales;


    /**
     * @param array $locales
     */
    public function __construct(array $locales)
    {
        $this->locales = $locales;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($menu, Constraint $constraint)
    {
        if (!$constraint instanceof Menu) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\Menu');
        }
        if (!$menu instanceof MenuInterface) {
            throw new UnexpectedTypeException($menu, 'Ekyna\Bundle\CmsBundle\Model\MenuInterface');
        }

        if (0 === $menu->getLevel() || $menu->getLocked()) {
            return;
        }

        /**
         * @var MenuInterface $menu
         * @var Menu $constraint
         */
        $page = null !== $menu->getPage();
        $route = 0 < strlen($menu->getRoute());

        $path = true;
        foreach ($this->locales as $locale) {
            if (0 === strlen($menu->translate($locale, true)->getPath())) {
                $path = false;
            }
        }

        if (!($page || $path || $route)) {
            $this->context->addViolation($constraint->invalid_routing);
        } elseif ($page && ($path || $route) || ($path && $route)) {
            $this->context->addViolation($constraint->invalid_routing);
        }
    }
}
