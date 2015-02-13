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
     * {@inheritdoc}
     */
    public function validate($menu, Constraint $constraint)
    {
        if (!$constraint instanceof Menu) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\Menu');
        }
        if (!$constraint instanceof MenuInterface) {
            throw new UnexpectedTypeException($menu, 'Ekyna\Bundle\CmsBundle\Model\MenuInterface');
        }

        /**
         * @var MenuInterface $menu
         * @var Menu $constraint
         */
        $page = null !== $menu->getPage();
        $path = 0 < strlen($menu->getPath());
        $route = 0 < strlen($menu->getRoute());

        if (!($page || $path || $route )) {
            $this->context->addViolation($constraint->invalid_routing);
        } elseif ($page && ($path || $route) || ($path && $route) ) {
            $this->context->addViolation($constraint->invalid_routing);
        }
    }
}
