<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Ekyna\Bundle\CmsBundle\Editor\Model\RowInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class RowValidator
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class RowValidator extends ConstraintValidator
{
    /**
     * @inheritDoc
     */
    public function validate($row, Constraint $constraint)
    {
        if (!$constraint instanceof Row) {
            throw new UnexpectedTypeException($constraint, Row::class);
        }
        if (!$row instanceof RowInterface) {
            throw new UnexpectedTypeException($row, RowInterface::class);
        }

        // Check that Content or Name is set, but not both.
        $container = $row->getContainer();
        $name = $row->getName();
        if ((null === $container && empty($name)) || (null !== $container && !empty($name))) {
            $this->context->addViolation($constraint->containerOrNameButNotBoth);
        }
    }
}
