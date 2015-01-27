<?php

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

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
        /**
         * @var \Ekyna\Bundle\CmsBundle\Model\MenuInterface $menu
         * @var \Ekyna\Bundle\CmsBundle\Validator\Constraints\Menu $constraint
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
