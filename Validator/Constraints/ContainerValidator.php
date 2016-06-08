<?php

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Ekyna\Bundle\CmsBundle\Model\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class ContainerValidator
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class ContainerValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($container, Constraint $constraint)
    {
        if (!$constraint instanceof Container) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\Container');
        }
        if (!$container instanceof ContainerInterface) {
            throw new UnexpectedTypeException($container, 'Ekyna\Bundle\CmsBundle\Model\ContainerInterface');
        }

        /**
         * @var ContainerInterface $container
         * @var Container          $constraint
         */
        $content = $container->getContent();
        $name = $container->getName();

        // Checks that Content or Name is set, but not both.
        if ((null === $content && 0 === strlen($name)) || (null !== $content && 0 < strlen($name))) {
            $this->context->addViolation($constraint->contentOrNameButNotBoth);
        }
    }
}
