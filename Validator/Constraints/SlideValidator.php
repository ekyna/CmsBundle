<?php

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Ekyna\Bundle\CmsBundle\Entity\Slide as Entity;
use Ekyna\Bundle\CmsBundle\SlideShow\TypeRegistryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class SlideValidator
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class SlideValidator extends ConstraintValidator
{
    /**
     * @var TypeRegistryInterface
     */
    private $registry;


    /**
     * Constructor.
     *
     * @param TypeRegistryInterface $registry
     */
    public function __construct(TypeRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @inheritdoc
     */
    public function validate($slide, Constraint $constraint)
    {
        if (!$constraint instanceof Slide) {
            throw new UnexpectedTypeException($constraint, Slide::class);
        }
        if (!$slide instanceof Entity) {
            throw new UnexpectedTypeException($slide, Entity::class);
        }

        // Type validation
        $type = $this->registry->get($slide->getType());
        //$type->validate($slide, $this->context);
    }
}
